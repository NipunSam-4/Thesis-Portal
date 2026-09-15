<?php

namespace App\Http\Controllers;

use App\Models\Pts3Form;
use App\Models\Pts3Examiner;
use App\Models\Pts3OebChairperson;
use App\Models\Pts3Draft;
use App\Models\Pts3ExaminerDraft;
use App\Models\Pts3OebChairpersonDraft;
use App\Models\Student;
use App\Models\VestedDoaa;
use App\Models\User;
use App\Models\Thesis;
use App\Http\Requests\Pts3\StorePts3Request;
use App\Http\Requests\Pts3\UpdatePts3Request;
use App\Http\Requests\Pts3\RevertPts3Request;
use App\Services\PtsDocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Pts3Controller extends Controller
{
    use AuthorizesRequests;

    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Show Main Supervisor initiation form
    public function create(Student $student)
    {
        $user = auth()->user();
        if (!$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 initiation.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('faculty.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'approved')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-3 Examiner Panel Form is locked until the PTS-1 Form is fully approved.');
        }

        // Check if a reverted PTS-3 form exists for this student
        $reverted = Pts3Form::where('thesis_id', $thesis->id)
            ->where('status', 'reverted')
            ->first();

        if ($reverted) {
            return redirect()->route('faculty.pts3.edit', $reverted->id);
        }

        // Check if a PTS-3 form already exists and is in progress or approved
        $existing = Pts3Form::where('thesis_id', $thesis->id)
            ->whereNotIn('status', ['rejected', 'reverted'])
            ->first();

        if ($existing) {
            return redirect()->route('pts3.show', $existing)->with('warning', 'A PTS-3 submission already exists for this student.');
        }

        $studentUser = $student->user;
        $draft = Pts3Draft::with(['examinerDrafts', 'oebChairpersonDrafts'])
            ->where('thesis_id', $thesis->id)
            ->where('user_id', $user->id)
            ->first();

        return view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'draft'));
    }

    // Save draft for PTS-3 creation
    public function saveDraft(Request $request, Student $student)
    {
        $user = auth()->user();
        if (!$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to save PTS-3 draft.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('faculty.dashboard')->with('error', 'No active thesis found for this student.');
        }

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('faculty.dashboard')->with('error', 'PTS-1 must be approved before saving a PTS-3 draft.');
        }

        $request->validate([
            'thesis_title' => 'nullable|string|max:1000',
            'indian_examiners' => 'nullable|array',
            'indian_examiners.*.consent_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'international_examiners' => 'nullable|array',
            'international_examiners.*.consent_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'oeb_chairpersons' => 'nullable|array',
        ]);

        $draft = Pts3Draft::firstOrNew([
            'thesis_id' => $thesis->id,
            'user_id' => $user->id,
        ]);

        $draft->thesis_title = $request->input('thesis_title');

        $examinerDrafts = [];
        $oebDrafts = [];

        // Indian Examiners (1 to 4)
        $indianExaminers = $request->input('indian_examiners', []);
        if (is_array($indianExaminers)) {
            foreach ($indianExaminers as $index => $examinerData) {
                $slot = $index + 1;
                if ($slot > 4) break;

                $docPath = $draft->{"indian_examiner_{$slot}_consent_doc_path"} ?? null;
                if ($request->hasFile("indian_examiners.{$index}.consent_doc")) {
                    $docPath = $this->ptsDocService->handleInProgressFile(
                        $request->file("indian_examiners.{$index}.consent_doc"),
                        null,
                        $student->roll_number,
                        $thesis->id,
                        'pts3_draft',
                        "Draft_Consent_indian_{$slot}",
                        'Main_Supervisor'
                    );
                } elseif (!empty($examinerData['consent_doc_path'])) {
                    $docPath = $examinerData['consent_doc_path'];
                }

                $draft->{"indian_examiner_{$slot}_email"} = $examinerData['email'] ?? null;
                $draft->{"indian_examiner_{$slot}_has_consent"} = isset($examinerData['has_consent']) ? (bool)$examinerData['has_consent'] : null;
                $draft->{"indian_examiner_{$slot}_consent_doc_path"} = $docPath;

                $examinerDrafts[] = [
                    'slot' => $slot,
                    'type' => 'indian',
                    'name' => $examinerData['name'] ?? null,
                    'designation' => $examinerData['designation'] ?? null,
                    'organization' => $examinerData['organization'] ?? null,
                    'postal_address' => $examinerData['postal_address'] ?? null,
                    'email' => $examinerData['email'] ?? null,
                    'phone_number' => $examinerData['phone_number'] ?? null,
                    'phone_country_code' => $examinerData['phone_country_code'] ?? '+91',
                    'phone_iso2' => $examinerData['phone_iso2'] ?? 'in',
                    'website' => $examinerData['website'] ?? null,
                    'research_area' => $examinerData['research_area'] ?? null,
                ];
            }
            for ($s = count($indianExaminers) + 1; $s <= 4; $s++) {
                $draft->{"indian_examiner_{$s}_email"} = null;
                $draft->{"indian_examiner_{$s}_has_consent"} = null;
                $draft->{"indian_examiner_{$s}_consent_doc_path"} = null;
            }
        }

        // International Examiners (1 to 4)
        $intlExaminers = $request->input('international_examiners', []);
        if (is_array($intlExaminers)) {
            foreach ($intlExaminers as $index => $examinerData) {
                $slot = $index + 1;
                if ($slot > 4) break;

                $docPath = $draft->{"international_examiner_{$slot}_consent_doc_path"} ?? null;
                if ($request->hasFile("international_examiners.{$index}.consent_doc")) {
                    $docPath = $this->ptsDocService->handleInProgressFile(
                        $request->file("international_examiners.{$index}.consent_doc"),
                        null,
                        $student->roll_number,
                        $thesis->id,
                        'pts3_draft',
                        "Draft_Consent_international_{$slot}",
                        'Main_Supervisor'
                    );
                } elseif (!empty($examinerData['consent_doc_path'])) {
                    $docPath = $examinerData['consent_doc_path'];
                }

                $draft->{"international_examiner_{$slot}_email"} = $examinerData['email'] ?? null;
                $draft->{"international_examiner_{$slot}_has_consent"} = isset($examinerData['has_consent']) ? (bool)$examinerData['has_consent'] : null;
                $draft->{"international_examiner_{$slot}_consent_doc_path"} = $docPath;

                $examinerDrafts[] = [
                    'slot' => $slot,
                    'type' => 'international',
                    'name' => $examinerData['name'] ?? null,
                    'designation' => $examinerData['designation'] ?? null,
                    'organization' => $examinerData['organization'] ?? null,
                    'postal_address' => $examinerData['postal_address'] ?? null,
                    'email' => $examinerData['email'] ?? null,
                    'phone_number' => $examinerData['phone_number'] ?? null,
                    'phone_country_code' => $examinerData['phone_country_code'] ?? '+1',
                    'phone_iso2' => $examinerData['phone_iso2'] ?? 'us',
                    'website' => $examinerData['website'] ?? null,
                    'research_area' => $examinerData['research_area'] ?? null,
                ];
            }
            for ($s = count($intlExaminers) + 1; $s <= 4; $s++) {
                $draft->{"international_examiner_{$s}_email"} = null;
                $draft->{"international_examiner_{$s}_has_consent"} = null;
                $draft->{"international_examiner_{$s}_consent_doc_path"} = null;
            }
        }

        // OEB Chairpersons (1 to 4)
        $oebList = $request->input('oeb_chairpersons', []);
        if (is_array($oebList)) {
            foreach ($oebList as $index => $oebData) {
                $slot = $index + 1;
                if ($slot > 4) break;

                $draft->{"oeb_chairperson_{$slot}_email"} = $oebData['email'] ?? null;

                $oebDrafts[] = [
                    'slot' => $slot,
                    'name' => $oebData['name'] ?? null,
                    'designation' => $oebData['designation'] ?? null,
                    'department' => $oebData['department'] ?? null,
                    'email' => $oebData['email'] ?? null,
                ];
            }
            for ($s = count($oebList) + 1; $s <= 4; $s++) {
                $draft->{"oeb_chairperson_{$s}_email"} = null;
            }
        }

        DB::beginTransaction();
        try {
            $draft->save();

            // Replace child drafts
            $draft->examinerDrafts()->delete();
            foreach ($examinerDrafts as $ex) {
                $draft->examinerDrafts()->create($ex);
            }

            $draft->oebChairpersonDrafts()->delete();
            foreach ($oebDrafts as $oeb) {
                $draft->oebChairpersonDrafts()->create($oeb);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving draft: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('faculty.pts3.create', $student->id)
            ->with('success', 'PTS-3 draft saved successfully at ' . now()->format('h:i A, d M Y') . '.');
    }

    // Discard draft for PTS-3
    public function discardDraft(Student $student)
    {
        $user = auth()->user();
        if (!$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if ($thesis) {
            $draft = Pts3Draft::where('thesis_id', $thesis->id)->where('user_id', $user->id)->first();
            if ($draft) {
                $draft->delete();
            }
            $draftDir = "students/{$student->roll_number}/thesis_{$thesis->id}/pts3_draft";
            if (Storage::disk('local')->exists($draftDir)) {
                Storage::disk('local')->deleteDirectory($draftDir);
            }
        }

        return redirect()->route('faculty.pts3.create', $student->id)
            ->with('info', 'PTS-3 draft has been discarded.');
    }

    // Process initial PTS-3 form submission by Main Supervisor
    public function store(StorePts3Request $request, Student $student)
    {
        $user = auth()->user();
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('faculty.dashboard')->with('error', 'This thesis has already been completed.');
            }
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('faculty.dashboard')->with('error', 'PTS-1 must be approved before submitting PTS-3.');
        }

        $validated = $request->validated();
        $thesis->update(['title' => $validated['thesis_title']]);

        // Map active co-supervisors
        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
        }

        $nextStage = count($coSupervisors) > 0 ? 'co_supervisors' : 'dpgc';

        $draft = Pts3Draft::where('thesis_id', $thesis->id)->where('user_id', $user->id)->first();

        // Prepare slot-based columns for PTS-3 Form
        $slotData = [];
        $examinersToUpsert = [];
        $oebToUpsert = [];

        // Indian Examiners (1 to 4)
        foreach ($validated['indian_examiners'] as $index => $examinerData) {
            $slot = $index + 1;
            $docPath = null;
            $existingDocPath = $examinerData['consent_doc_path'] ?? ($draft?->{"indian_examiner_{$slot}_consent_doc_path"});
            if ($request->hasFile("indian_examiners.{$index}.consent_doc")) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    $request->file("indian_examiners.{$index}.consent_doc"),
                    null,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_indian_" . $slot,
                    'Main_Supervisor'
                );
            } elseif (!empty($existingDocPath) && Storage::disk('local')->exists($existingDocPath)) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    null,
                    $existingDocPath,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_indian_" . $slot,
                    'Main_Supervisor'
                );
            }

            $slotData["indian_examiner_{$slot}_email"] = $examinerData['email'];
            $slotData["indian_examiner_{$slot}_has_consent"] = $examinerData['has_consent'];
            $slotData["indian_examiner_{$slot}_consent_doc_path"] = $docPath;

            $examinersToUpsert[] = [
                'type' => 'indian',
                'name' => $examinerData['name'],
                'designation' => $examinerData['designation'],
                'organization' => $examinerData['organization'],
                'postal_address' => $examinerData['postal_address'],
                'email' => $examinerData['email'],
                'phone_number' => $examinerData['phone_number'],
                'phone_country_code' => $examinerData['phone_country_code'] ?? '+91',
                'phone_iso2' => $examinerData['phone_iso2'] ?? 'in',
                'website' => $examinerData['website'] ?? null,
                'research_area' => $examinerData['research_area'] ?? null,
            ];
        }

        // International Examiners (1 to 4)
        foreach ($validated['international_examiners'] as $index => $examinerData) {
            $slot = $index + 1;
            $docPath = null;
            $existingDocPath = $examinerData['consent_doc_path'] ?? ($draft?->{"international_examiner_{$slot}_consent_doc_path"});
            if ($request->hasFile("international_examiners.{$index}.consent_doc")) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    $request->file("international_examiners.{$index}.consent_doc"),
                    null,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_international_" . $slot,
                    'Main_Supervisor'
                );
            } elseif (!empty($existingDocPath) && Storage::disk('local')->exists($existingDocPath)) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    null,
                    $existingDocPath,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_international_" . $slot,
                    'Main_Supervisor'
                );
            }

            $slotData["international_examiner_{$slot}_email"] = $examinerData['email'];
            $slotData["international_examiner_{$slot}_has_consent"] = $examinerData['has_consent'];
            $slotData["international_examiner_{$slot}_consent_doc_path"] = $docPath;

            $examinersToUpsert[] = [
                'type' => 'international',
                'name' => $examinerData['name'],
                'designation' => $examinerData['designation'],
                'organization' => $examinerData['organization'],
                'postal_address' => $examinerData['postal_address'],
                'email' => $examinerData['email'],
                'phone_number' => $examinerData['phone_number'],
                'phone_country_code' => $examinerData['phone_country_code'] ?? '+1',
                'phone_iso2' => $examinerData['phone_iso2'] ?? 'us',
                'website' => $examinerData['website'] ?? null,
                'research_area' => $examinerData['research_area'] ?? null,
            ];
        }

        // OEB Chairpersons (1 to 4)
        foreach ($validated['oeb_chairpersons'] as $index => $oebData) {
            $slot = $index + 1;
            $slotData["oeb_chairperson_{$slot}_email"] = $oebData['email'];

            $oebToUpsert[] = [
                'name' => $oebData['name'],
                'designation' => $oebData['designation'],
                'department' => $oebData['department'],
                'email' => $oebData['email'],
            ];
        }

        DB::beginTransaction();
        try {
            $pts3 = Pts3Form::create(array_merge([
                'thesis_id' => $thesis->id,
                'thesis_title' => $validated['thesis_title'],
                
                'main_supervisor_id' => $user->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => now(),
                'vested_doaa_email' => VestedDoaa::getActiveVestedEmail(),
                
                'current_stage' => $nextStage,
                'status' => 'in_progress',
            ], $coSupData, $slotData));

            // Save / update Examiners linked to this form
            foreach ($examinersToUpsert as $ex) {
                Pts3Examiner::updateOrCreate(
                    ['pts3_form_id' => $pts3->id, 'email' => $ex['email']],
                    $ex
                );
            }

            // Save / update OEB Chairpersons linked to this form
            foreach ($oebToUpsert as $oeb) {
                Pts3OebChairperson::updateOrCreate(
                    ['pts3_form_id' => $pts3->id, 'email' => $oeb['email']],
                    $oeb
                );
            }

            // Clean up saved draft and its temporary directory if exists
            if ($draft) {
                $draft->delete();
            }
            $draftDir = "students/{$student->roll_number}/thesis_{$thesis->id}/pts3_draft";
            if (Storage::disk('local')->exists($draftDir)) {
                Storage::disk('local')->deleteDirectory($draftDir);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error initiating PTS-3 form: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-3 initiated successfully.');
    }

    // Show edit/resubmission form for reverted PTS-3 form
    public function edit(Pts3Form $pts3)
    {
        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$student || !$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 edit.');
        }

        if ($thesis?->status === 'completed') {
            return redirect()->route('pts3.show', $pts3->id)->with('info', 'This thesis has already been completed.');
        }

        if ($pts3->status !== 'reverted') {
            return redirect()->route('pts3.show', $pts3->id)->with('info', 'Only reverted PTS-3 forms can be edited.');
        }

        $studentUser = $student->user;

        return view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'pts3'));
    }

    // Process update and resubmission of reverted PTS-3 form
    public function update(UpdatePts3Request $request, Pts3Form $pts3)
    {
        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$student || !$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 update.');
        }

        if ($thesis?->status === 'completed') {
            return redirect()->route('pts3.show', $pts3->id)->with('error', 'This thesis has already been completed.');
        }

        if ($pts3->status !== 'reverted') {
            return redirect()->route('pts3.show', $pts3->id)->with('error', 'Only reverted PTS-3 forms can be updated.');
        }

        $validated = $request->validated();
        $thesis->update(['title' => $validated['thesis_title']]);

        // Map active co-supervisors
        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
            $coSupData["co_supervisor_{$i}_recommendation"] = null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = null;
        }

        $nextStage = count($coSupervisors) > 0 ? 'co_supervisors' : 'dpgc';

        // Prepare slot-based columns for PTS-3 Form
        $slotData = [];
        $examinersToUpsert = [];
        $oebToUpsert = [];

        // Indian Examiners (1 to 4)
        foreach ($validated['indian_examiners'] as $index => $examinerData) {
            $slot = $index + 1;
            $docPath = null;
            $existingDocPath = $examinerData['consent_doc_path'] ?? null;
            if ($request->hasFile("indian_examiners.{$index}.consent_doc")) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    $request->file("indian_examiners.{$index}.consent_doc"),
                    null,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_indian_" . $slot,
                    'Main_Supervisor'
                );
            } elseif (!empty($existingDocPath)) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    null,
                    $existingDocPath,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_indian_" . $slot,
                    'Main_Supervisor'
                );
            }

            $slotData["indian_examiner_{$slot}_email"] = $examinerData['email'];
            $slotData["indian_examiner_{$slot}_has_consent"] = $examinerData['has_consent'];
            $slotData["indian_examiner_{$slot}_consent_doc_path"] = $docPath;

            $examinersToUpsert[] = [
                'type' => 'indian',
                'name' => $examinerData['name'],
                'designation' => $examinerData['designation'],
                'organization' => $examinerData['organization'],
                'postal_address' => $examinerData['postal_address'],
                'email' => $examinerData['email'],
                'phone_number' => $examinerData['phone_number'],
                'phone_country_code' => $examinerData['phone_country_code'] ?? '+91',
                'phone_iso2' => $examinerData['phone_iso2'] ?? 'in',
                'website' => $examinerData['website'] ?? null,
                'research_area' => $examinerData['research_area'] ?? null,
            ];
        }

        // International Examiners (1 to 4)
        foreach ($validated['international_examiners'] as $index => $examinerData) {
            $slot = $index + 1;
            $docPath = null;
            $existingDocPath = $examinerData['consent_doc_path'] ?? null;
            if ($request->hasFile("international_examiners.{$index}.consent_doc")) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    $request->file("international_examiners.{$index}.consent_doc"),
                    null,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_international_" . $slot,
                    'Main_Supervisor'
                );
            } elseif (!empty($existingDocPath)) {
                $docPath = $this->ptsDocService->handleInProgressFile(
                    null,
                    $existingDocPath,
                    $student->roll_number,
                    $thesis->id,
                    'pts3',
                    "Consent_international_" . $slot,
                    'Main_Supervisor'
                );
            }

            $slotData["international_examiner_{$slot}_email"] = $examinerData['email'];
            $slotData["international_examiner_{$slot}_has_consent"] = $examinerData['has_consent'];
            $slotData["international_examiner_{$slot}_consent_doc_path"] = $docPath;

            $examinersToUpsert[] = [
                'type' => 'international',
                'name' => $examinerData['name'],
                'designation' => $examinerData['designation'],
                'organization' => $examinerData['organization'],
                'postal_address' => $examinerData['postal_address'],
                'email' => $examinerData['email'],
                'phone_number' => $examinerData['phone_number'],
                'phone_country_code' => $examinerData['phone_country_code'] ?? '+1',
                'phone_iso2' => $examinerData['phone_iso2'] ?? 'us',
                'website' => $examinerData['website'] ?? null,
                'research_area' => $examinerData['research_area'] ?? null,
            ];
        }

        // OEB Chairpersons (1 to 4)
        foreach ($validated['oeb_chairpersons'] as $index => $oebData) {
            $slot = $index + 1;
            $slotData["oeb_chairperson_{$slot}_email"] = $oebData['email'];

            $oebToUpsert[] = [
                'name' => $oebData['name'],
                'designation' => $oebData['designation'],
                'department' => $oebData['department'],
                'email' => $oebData['email'],
            ];
        }

        DB::beginTransaction();
        try {
            // Create a brand new PTS-3 form row, preserving historical reverted submissions
            $newPts3 = Pts3Form::create(array_merge([
                'thesis_id' => $thesis->id,
                'thesis_title' => $validated['thesis_title'],
                'main_supervisor_id' => $user->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => now(),
                'current_stage' => $nextStage,
                'status' => 'in_progress',
            ], $coSupData, $slotData));

            // Create Examiners for the new form
            foreach ($examinersToUpsert as $ex) {
                Pts3Examiner::updateOrCreate(
                    ['pts3_form_id' => $newPts3->id, 'email' => $ex['email']],
                    $ex
                );
            }

            // Create OEB Chairpersons for the new form
            foreach ($oebToUpsert as $oeb) {
                Pts3OebChairperson::updateOrCreate(
                    ['pts3_form_id' => $newPts3->id, 'email' => $oeb['email']],
                    $oeb
                );
            }

            // Clean up any lingering draft and its temporary directory
            $draft = Pts3Draft::where('thesis_id', $thesis->id)->where('user_id', $user->id)->first();
            if ($draft) {
                $draft->delete();
            }
            $draftDir = "students/{$student->roll_number}/thesis_{$thesis->id}/pts3_draft";
            if (Storage::disk('local')->exists($draftDir)) {
                Storage::disk('local')->deleteDirectory($draftDir);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error resubmitting PTS-3 form: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-3 resubmitted successfully.');
    }

    // Display dedicated evaluation/review portal for active authority
    public function review(Pts3Form $pts3)
    {
        $this->authorize('review', $pts3);

        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if ($student && $student->isMainSupervisor($user) && $pts3->current_stage === 'main_supervisor') {
            return redirect()->route('faculty.pts3.edit', $pts3->id);
        }

        $student?->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student?->user;

        $userRank = $this->getUserRank($user, $pts3);
        $coSupervisors = $pts3->getCoSupervisors();

        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        // Fetch examiners & OEB members ordered by evaluating authority's role
        $indianExaminers = $this->getOrderedExaminers($pts3, $userRank, 'indian');
        $internationalExaminers = $this->getOrderedExaminers($pts3, $userRank, 'international');
        $oebMembers = $this->getOrderedOebMembers($pts3, $userRank);

        return view('pts3.review', compact(
            'pts3',
            'thesis',
            'student',
            'studentUser',
            'user',
            'userRank',
            'coSupervisors',
            'actingDoaaUsers',
            'indianExaminers',
            'internationalExaminers',
            'oebMembers'
        ));
    }

    // Endorse action for active evaluating authority
    public function endorse(Request $request, Pts3Form $pts3)
    {
        $this->authorize('review', $pts3);

        $request->validate([
            'declaration' => 'required|accepted',
        ], [
            'declaration.required' => 'Please confirm the declaration checkbox before submitting.',
            'declaration.accepted' => 'Please confirm the declaration checkbox before submitting.',
        ]);

        $user = auth()->user();
        $stage = $pts3->current_stage;
        $userRank = $this->getUserRank($user, $pts3);

        DB::beginTransaction();
        try {
            switch ($stage) {
                case 'co_supervisors':
                    for ($i = 1; $i <= 10; $i++) {
                        if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                            $pts3->{"co_supervisor_{$i}_recommendation"} = true;
                            $pts3->{"co_supervisor_{$i}_submitted_at"} = now();
                            break;
                        }
                    }

                    // Check if all co-supervisors have submitted
                    $allSubmitted = true;
                    for ($i = 1; $i <= 10; $i++) {
                        $coId = $pts3->{"co_supervisor_{$i}_id"};
                        if ($coId && $pts3->{"co_supervisor_{$i}_recommendation"} === null) {
                            $allSubmitted = false;
                            break;
                        }
                    }

                    if ($allSubmitted) {
                        $pts3->co_supervisors_submitted_at = now();
                        $pts3->current_stage = 'dpgc';
                    }
                    $pts3->save();
                    break;

                case 'dpgc':
                    $pts3->dpgc_recommendation = true;
                    $pts3->dpgc_submitted_at = now();
                    $pts3->dpgc_user_id = $user->id;
                    $pts3->current_stage = 'hod';
                    $pts3->save();
                    break;

                case 'hod':
                    $pts3->hod_recommendation = true;
                    $pts3->hod_submitted_at = now();
                    $pts3->hod_user_id = $user->id;
                    $pts3->current_stage = 'academic_office';
                    $pts3->save();
                    break;

                case 'academic_office':
                    $pts3->academic_office_is_verified = true;
                    $pts3->academic_office_verification_remark = $request->input('academic_office_verification_remark');
                    $pts3->acting_doaa_email = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;
                    $pts3->academic_office_submitted_at = now();
                    $pts3->academic_office_user_id = $user->id;
                    $pts3->current_stage = 'doaa';

                    // Save any Academic Office examiner/OEB remarks if provided
                    if ($request->has('examiner_remarks')) {
                        $examiners = $pts3->examiners->keyBy('id');
                        foreach ($request->input('examiner_remarks') as $exId => $remark) {
                            $examiner = $examiners->get($exId) ?? Pts3Examiner::find($exId);
                            if ($examiner && $examiner->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"{$examiner->type}_examiner_{$i}_email"} === $examiner->email) {
                                        $pts3->{"{$examiner->type}_examiner_{$i}_academic_office_remark"} = $remark;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($request->has('oeb_remarks')) {
                        $oebs = $pts3->oebChairpersons->keyBy('id');
                        foreach ($request->input('oeb_remarks') as $oebId => $remark) {
                            $oeb = $oebs->get($oebId) ?? Pts3OebChairperson::find($oebId);
                            if ($oeb && $oeb->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"oeb_chairperson_{$i}_email"} === $oeb->email) {
                                        $pts3->{"oeb_chairperson_{$i}_academic_office_remark"} = $remark;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $pts3->save();
                    break;

                case 'doaa':
                    // Validate Priority Orders for Indian Examiners, International Examiners, and OEB Chairpersons
                    $indianExIds = $pts3->examiners()->where('type', 'indian')->pluck('id')->toArray();
                    $intlExIds = $pts3->examiners()->where('type', 'international')->pluck('id')->toArray();
                    $oebIds = $pts3->oebChairpersons()->pluck('id')->toArray();

                    $submittedExPriorities = $request->input('doaa_examiner_priority', []);
                    $submittedOebPriorities = $request->input('doaa_oeb_priority', []);

                    $indianPriorities = [];
                    foreach ($indianExIds as $id) {
                        $indianPriorities[$id] = $submittedExPriorities[$id] ?? 0;
                    }

                    $intlPriorities = [];
                    foreach ($intlExIds as $id) {
                        $intlPriorities[$id] = $submittedExPriorities[$id] ?? 0;
                    }

                    $oebPriorities = [];
                    foreach ($oebIds as $id) {
                        $oebPriorities[$id] = $submittedOebPriorities[$id] ?? 0;
                    }

                    $priorityErrors = [];
                    if ($err = $this->validatePanelPriorities($indianPriorities, 'Indian Examiners Panel')) {
                        $priorityErrors[] = $err;
                    }
                    if ($err = $this->validatePanelPriorities($intlPriorities, 'International Examiners Panel')) {
                        $priorityErrors[] = $err;
                    }
                    if ($err = $this->validatePanelPriorities($oebPriorities, 'Oral Examination Board (OEB) Chairpersons')) {
                        $priorityErrors[] = $err;
                    }

                    if (!empty($priorityErrors)) {
                        DB::rollBack();
                        return back()->with('error', implode(' ', $priorityErrors))->withInput();
                    }

                    $pts3->doaa_is_verified = true;
                    $pts3->doaa_verification_remark = $request->input('doaa_verification_remark');
                    $pts3->doaa_submitted_at = now();
                    $pts3->doaa_user_id = $user->id;
                    $pts3->current_stage = 'senate_chairperson';

                    // Save DOAA priorities and remarks to slot columns
                    $examiners = $pts3->examiners->keyBy('id');
                    if ($request->has('doaa_examiner_priority')) {
                        foreach ($request->input('doaa_examiner_priority') as $exId => $prio) {
                            $examiner = $examiners->get($exId) ?? Pts3Examiner::find($exId);
                            if ($examiner && $examiner->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"{$examiner->type}_examiner_{$i}_email"} === $examiner->email) {
                                        $pts3->{"{$examiner->type}_examiner_{$i}_doaa_priority"} = ($prio !== null && $prio !== '') ? (int)$prio : null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($request->has('doaa_examiner_remarks')) {
                        foreach ($request->input('doaa_examiner_remarks') as $exId => $remark) {
                            $examiner = $examiners->get($exId) ?? Pts3Examiner::find($exId);
                            if ($examiner && $examiner->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"{$examiner->type}_examiner_{$i}_email"} === $examiner->email) {
                                        $pts3->{"{$examiner->type}_examiner_{$i}_doaa_remark"} = $remark;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $oebs = $pts3->oebChairpersons->keyBy('id');
                    if ($request->has('doaa_oeb_priority')) {
                        foreach ($request->input('doaa_oeb_priority') as $oebId => $prio) {
                            $oeb = $oebs->get($oebId) ?? Pts3OebChairperson::find($oebId);
                            if ($oeb && $oeb->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"oeb_chairperson_{$i}_email"} === $oeb->email) {
                                        $pts3->{"oeb_chairperson_{$i}_doaa_priority"} = ($prio !== null && $prio !== '') ? (int)$prio : null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($request->has('doaa_oeb_remarks')) {
                        foreach ($request->input('doaa_oeb_remarks') as $oebId => $remark) {
                            $oeb = $oebs->get($oebId) ?? Pts3OebChairperson::find($oebId);
                            if ($oeb && $oeb->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"oeb_chairperson_{$i}_email"} === $oeb->email) {
                                        $pts3->{"oeb_chairperson_{$i}_doaa_remark"} = $remark;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $pts3->save();
                    break;

                case 'senate_chairperson':
                    $isApproved = $request->input('recommendation') === '1' || $request->input('decision') === 'approve';
                    $approvalRemark = $request->input('senate_chairperson_approval_remark') ?? $request->input('confidential_remark');

                    if (!$isApproved && empty(trim($approvalRemark ?? ''))) {
                        DB::rollBack();
                        return back()->with('error', 'Non-approval remark is mandatory when not approving the form.')->withInput();
                    }

                    if ($isApproved) {
                        // Validate Priority Orders for Indian Examiners, International Examiners, and OEB Chairpersons
                        $indianExIds = $pts3->examiners()->where('type', 'indian')->pluck('id')->toArray();
                        $intlExIds = $pts3->examiners()->where('type', 'international')->pluck('id')->toArray();
                        $oebIds = $pts3->oebChairpersons()->pluck('id')->toArray();

                        $submittedSenateExPriorities = $request->input('senate_examiner_priority', []);
                        $submittedSenateOebPriorities = $request->input('senate_oeb_priority', []);

                        $indianPriorities = [];
                        foreach ($indianExIds as $id) {
                            $indianPriorities[$id] = $submittedSenateExPriorities[$id] ?? 0;
                        }

                        $intlPriorities = [];
                        foreach ($intlExIds as $id) {
                            $intlPriorities[$id] = $submittedSenateExPriorities[$id] ?? 0;
                        }

                        $oebPriorities = [];
                        foreach ($oebIds as $id) {
                            $oebPriorities[$id] = $submittedSenateOebPriorities[$id] ?? 0;
                        }

                        $priorityErrors = [];
                        if ($err = $this->validatePanelPriorities($indianPriorities, 'Indian Examiners Panel')) {
                            $priorityErrors[] = $err;
                        }
                        if ($err = $this->validatePanelPriorities($intlPriorities, 'International Examiners Panel')) {
                            $priorityErrors[] = $err;
                        }
                        if ($err = $this->validatePanelPriorities($oebPriorities, 'Oral Examination Board (OEB) Chairpersons')) {
                            $priorityErrors[] = $err;
                        }

                        if (!empty($priorityErrors)) {
                            DB::rollBack();
                            return back()->with('error', implode(' ', $priorityErrors))->withInput();
                        }
                    }

                    $pts3->senate_chairperson_approval = $isApproved;
                    $pts3->senate_chairperson_submitted_at = now();
                    $pts3->senate_chairperson_user_id = $user->id;
                    $pts3->senate_chairperson_approval_remark = $approvalRemark;
                    $pts3->senate_chairperson_confidential_remark = $request->input('senate_chairperson_confidential_remark');
                    $pts3->status = $isApproved ? 'approved' : 'rejected';
                    $pts3->current_stage = 'completed';
                    $pts3->approved_by_id = $isApproved ? $user->id : null;

                    // Save Senate Chairperson priorities to slot columns
                    $examiners = $pts3->examiners->keyBy('id');
                    if ($request->has('senate_examiner_priority')) {
                        foreach ($request->input('senate_examiner_priority') as $exId => $prio) {
                            $examiner = $examiners->get($exId) ?? Pts3Examiner::find($exId);
                            if ($examiner && $examiner->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"{$examiner->type}_examiner_{$i}_email"} === $examiner->email) {
                                        $pts3->{"{$examiner->type}_examiner_{$i}_senate_chairperson_priority"} = ($prio !== null && $prio !== '') ? (int)$prio : null;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $oebs = $pts3->oebChairpersons->keyBy('id');
                    if ($request->has('senate_oeb_priority')) {
                        foreach ($request->input('senate_oeb_priority') as $oebId => $prio) {
                            $oeb = $oebs->get($oebId) ?? Pts3OebChairperson::find($oebId);
                            if ($oeb && $oeb->pts3_form_id == $pts3->id) {
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($pts3->{"oeb_chairperson_{$i}_email"} === $oeb->email) {
                                        $pts3->{"oeb_chairperson_{$i}_senate_chairperson_priority"} = ($prio !== null && $prio !== '') ? (int)$prio : null;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $pts3->save();

                    if ($isApproved) {
                        $this->ptsDocService->moveToApproved($pts3, 'pts3');
                    } else {
                        $this->ptsDocService->moveToRejected($pts3, 'pts3');
                    }
                    break;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving evaluation: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'PTS-3 evaluation submitted successfully.');
    }

    // Revert form back to Main Supervisor
    public function revert(RevertPts3Request $request, Pts3Form $pts3)
    {
        $user = auth()->user();

        if ($pts3->status !== 'in_progress') {
            return back()->with('error', 'Cannot revert a PTS-3 form that is not currently in progress.');
        }

        $thesis = $pts3->thesis;
        $student = $thesis?->student;
        $stage = $pts3->current_stage;
        $validated = $request->validated();
        $revertedRole = null;

        if ($stage === 'dpgc') {
            if (!$user->isDpgc() || ($student && $user->deptAuthorityProfile && $user->deptAuthorityProfile->department_id !== $student->department_id)) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'dpgc';
        } elseif ($stage === 'hod') {
            if (!$user->isHod() || ($student && $user->deptAuthorityProfile && $user->deptAuthorityProfile->department_id !== $student->department_id)) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'hod';
        } elseif ($stage === 'academic_office') {
            if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'academic_office';
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts3->acting_doaa_email === $user->email || $pts3->vested_doaa_email === $user->email)) || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email))) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'doaa';
        } elseif ($stage === 'senate_chairperson') {
            if (!$user->isSenateChairperson()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'senate_chairperson';
        } else {
            return back()->with('error', 'Invalid stage for reversion. Only evaluating authorities (DPGC, HOD, Academic Office, DOAA, Senate Chairperson) can revert.');
        }

        $pts3->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        $this->ptsDocService->moveToReverted($pts3, 'pts3');

        return redirect()->route('dashboard')->with('warning', 'PTS-3 form has been reverted to the Main Supervisor.');
    }

    // Unified Confidential View Action (Handles in_progress, approved, rejected, reverted)
    public function show(Pts3Form $pts3)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Students are strictly barred from viewing confidential PTS-3 examiners
        if ($user->isStudent()) {
            return redirect()->route('student.dashboard')->with('error', 'Students are not authorized to access confidential PTS-3 form details.');
        }

        $userRank = $this->getUserRank($user, $pts3);

        if ($userRank === 0) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
        }

        // 1. If form is in_progress: authority can view show ONLY if they have already given their recommendation
        if ($pts3->status === 'in_progress') {
            $accessStatus = $pts3->getUserSubmissionAccessStatus($user);

            // If active review is pending for this user, redirect to review portal
            if ($accessStatus === 'pending_endorsement') {
                return redirect()->route('pts3.review', $pts3->id);
            }

            // If the form has not reached their stage yet, disallow viewing
            if ($accessStatus === 'not_reached') {
                return redirect()->route('dashboard')->with('error', 'This submission has not reached your review stage yet.');
            }

            if ($accessStatus !== 'allowed') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
            }
        }

        // 2. If form is reverted: check reverted trail visibility
        if ($pts3->status === 'reverted') {
            if (!$pts3->canUserViewRevertedForm($user)) {
                return redirect()->route('dashboard')->with('error', 'You are not authorized to view this reverted PTS-3 form trail.');
            }
        }

        // 3. If form is approved or rejected: check general viewing authorization
        if (in_array($pts3->status, ['approved', 'rejected'])) {
            if (!$pts3->canUserView($user)) {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
            }
        }

        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$thesis || !$student) {
            return redirect()->route('dashboard')->with('error', 'Associated thesis or student record not found.');
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;
        $coSupervisors = $pts3->getCoSupervisors();

        // Fetch examiners & OEB members ordered by viewing authority's role
        $indianExaminers = $this->getOrderedExaminers($pts3, $userRank, 'indian');
        $internationalExaminers = $this->getOrderedExaminers($pts3, $userRank, 'international');
        $oebMembers = $this->getOrderedOebMembers($pts3, $userRank);

        return view('pts3.show', compact(
            'pts3',
            'thesis',
            'student',
            'studentUser',
            'user',
            'userRank',
            'coSupervisors',
            'indianExaminers',
            'internationalExaminers',
            'oebMembers'
        ));
    }


    // Helper: Determine integer rank of logged-in user for trail visibility
    protected function getUserRank(User $user, Pts3Form $pts3): int
    {
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        // 1. Main Supervisor
        if ($student && $student->isMainSupervisor($user)) {
            return 1;
        }

        // 2. Co-Supervisor / External Supervisor
        if ($student && $student->isCoSupervisor($user)) {
            return 2;
        }

        // 3. DPGC
        if ($user->isDpgc() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 3;
        }

        // 4. HOD
        if ($user->isHod() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 4;
        }

        // 5. Academic Office
        if ($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority() && !$user->isDoaa() && !$user->isSenateChairperson())) {
            return 5;
        }

        // 6. DOAA / Vested DOAA / Acting DOAA
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts3->acting_doaa_email === $user->email || $pts3->vested_doaa_email === $user->email)) || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email) || ($pts3->acting_doaa_email && $user->email === $pts3->acting_doaa_email)) {
            return 6;
        }

        // 7. Senate Chairperson
        if ( $user->isSenateChairperson()) {
            return 7;
        }

        return 0; // Unauthorized
    }

    // Helper: Determine role code for reversion tracking
    protected function getUserRoleCode(User $user, Pts3Form $pts3): string
    {
        $rank = $this->getUserRank($user, $pts3);
        return match ($rank) {
            1 => 'main_supervisor',
            2 => $user->isExternalSupervisor() ? 'external_supervisor' : 'co_supervisor',
            3 => 'dpgc',
            4 => 'hod',
            5 => 'academic_office',
            6 => 'doaa',
            7 => 'senate_chairperson',
            default => 'authority',
        };
    }

    // Helper: Check if user is currently authorized to review/endorse
    protected function canUserReview(User $user, Pts3Form $pts3): bool
    {
        if ($pts3->status !== 'in_progress') {
            return false;
        }

        $stage = $pts3->current_stage;
        $userRank = $this->getUserRank($user, $pts3);

        return match ($stage) {
            'main_supervisor' => ($userRank === 1),
            'co_supervisors' => ($userRank === 2 && $this->isCoSupervisorPending($user, $pts3)),
            'dpgc' => ($userRank === 3),
            'hod' => ($userRank === 4),
            'academic_office' => ($userRank === 5),
            'doaa' => ($userRank === 6),
            'senate_chairperson' => ($userRank === 7),
            default => false,
        };
    }

    protected function isCoSupervisorPending(User $user, Pts3Form $pts3): bool
    {
        for ($i = 1; $i <= 10; $i++) {
            if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                return $pts3->{"co_supervisor_{$i}_recommendation"} === null;
            }
        }
        return false;
    }

    // Helper: Get examiners in original submission order as filled by Main Supervisor
    protected function getOrderedExaminers(Pts3Form $pts3, int $userRank, string $type)
    {
        $examiners = $type === 'indian' ? $pts3->getIndianExaminers() : $pts3->getInternationalExaminers();
        if ($userRank === 7 && $pts3->senate_chairperson_submitted_at) {
            return $examiners->sortBy(function ($ex) {
                return ($ex->senate_chairperson_priority && $ex->senate_chairperson_priority > 0) ? $ex->senate_chairperson_priority : 999;
            })->values();
        }
        if ($userRank >= 6 && $pts3->doaa_submitted_at) {
            return $examiners->sortBy(function ($ex) {
                return ($ex->doaa_priority && $ex->doaa_priority > 0) ? $ex->doaa_priority : 999;
            })->values();
        }
        return $examiners;
    }

    // Helper: Get OEB members in original submission order as filled by Main Supervisor
    protected function getOrderedOebMembers(Pts3Form $pts3, int $userRank)
    {
        $oebs = $pts3->getOebChairpersons();
        if ($userRank === 7 && $pts3->senate_chairperson_submitted_at) {
            return $oebs->sortBy(function ($oeb) {
                return ($oeb->senate_chairperson_priority && $oeb->senate_chairperson_priority > 0) ? $oeb->senate_chairperson_priority : 999;
            })->values();
        }
        if ($userRank >= 6 && $pts3->doaa_submitted_at) {
            return $oebs->sortBy(function ($oeb) {
                return ($oeb->doaa_priority && $oeb->doaa_priority > 0) ? $oeb->doaa_priority : 999;
            })->values();
        }
        return $oebs;
    }

    /**
     * Validate that an array of priorities for a panel has exactly one of each required priority (1..min(4, count)),
     * no duplicates of positive priorities, and all remaining items set to 0.
     *
     * @param array $priorities Map of id => priority value
     * @param string $panelName Name of the panel for error message
     * @return string|null Error message if invalid, null if valid
     */
    protected function validatePanelPriorities(array $priorities, string $panelName): ?string
    {
        $totalMembers = count($priorities);
        if ($totalMembers === 0) {
            return null;
        }

        $values = array_map(function ($val) {
            return is_numeric($val) ? (int)$val : 0;
        }, array_values($priorities));

        $counts = array_count_values($values);

        // Check for invalid numbers (anything not in 0..4)
        foreach ($counts as $num => $freq) {
            if (!in_array($num, [0, 1, 2, 3, 4], true)) {
                return "Priority order for {$panelName} contains invalid priority value ({$num}). Only 0, 1, 2, 3, and 4 are permitted.";
            }
        }

        // Required priorities are 1 through min(4, totalMembers)
        $maxRequired = min(4, $totalMembers);
        $requiredPriorities = range(1, $maxRequired);

        $missing = [];
        $duplicates = [];

        foreach ($requiredPriorities as $p) {
            $freq = $counts[$p] ?? 0;
            if ($freq === 0) {
                $missing[] = $p;
            } elseif ($freq > 1) {
                $duplicates[] = $p;
            }
        }

        // Also check if any priority > $maxRequired was selected
        for ($p = $maxRequired + 1; $p <= 4; $p++) {
            if (($counts[$p] ?? 0) > 0) {
                $duplicates[] = $p;
            }
        }

        $errorParts = [];
        if (!empty($missing)) {
            $errorParts[] = "missing priority: " . implode(', ', $missing);
        }
        if (!empty($duplicates)) {
            $errorParts[] = "duplicate/excess priority: " . implode(', ', array_unique($duplicates));
        }

        if (!empty($errorParts)) {
            return "Priority order for {$panelName} is invalid. Each priority from 1 to {$maxRequired} must be assigned exactly once (multiple 0s allowed for remaining). Issues: " . implode('; ', $errorParts) . ".";
        }

        return null;
    }
}
