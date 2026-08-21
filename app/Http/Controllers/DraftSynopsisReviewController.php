<?php

namespace App\Http\Controllers;

use App\Models\DraftSynopsisCirculation;
use App\Models\DraftSynopsisComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Purify\Facades\Purify;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DraftSynopsisReviewController extends Controller
{
    // Display the review page for academic authorities to inspect draft synopsis & submit feedback.
    public function show($id)
    {
        $circulation = DraftSynopsisCirculation::with(['thesis.student.user'])->findOrFail($id);
        $user = auth()->user();
        $thesis = $circulation->thesis;
        $student = $thesis->student;

        $authorityInfo = $this->resolveAuthorityInfo($user, $student);

        // Fetch existing comment by this user if any
        $userComment = DraftSynopsisComment::where('draft_synopsis_circulation_id', $circulation->id)
            ->where('user_id', $user->id)
            ->first();

        $comments = $circulation->getOrderedComments();

        return view('draft_synopsis.review', compact('circulation', 'user', 'student', 'thesis', 'authorityInfo', 'userComment', 'comments'));
    }

    // Submit or update an authority's feedback comment.
    public function comment(Request $request, $id)
    {
        $circulation = DraftSynopsisCirculation::with('thesis.student')->findOrFail($id);
        $user = auth()->user();
        $student = $circulation->thesis->student;

        $validated = $request->validate([
            'comment' => 'required|string|max:15000',
        ]);

        $authorityInfo = $this->resolveAuthorityInfo($user, $student);
        $sanitizedComment = Purify::clean($validated['comment']);

        DraftSynopsisComment::updateOrCreate(
            [
                'draft_synopsis_circulation_id' => $circulation->id,
                'user_id' => $user->id,
            ],
            [
                'authority_role' => $authorityInfo['role'],
                'authority_label' => $authorityInfo['label'],
                'comment' => $sanitizedComment,
            ]
        );

        return back()->with('success', 'Your feedback comment on the Draft Synopsis has been submitted successfully!');
    }

    // Serve the uploaded draft synopsis document securely.
    public function serveDocument($id)
    {
        $circulation = DraftSynopsisCirculation::findOrFail($id);
        $user = auth()->user();

        if (!Storage::disk('local')->exists($circulation->draft_synopsis_doc_path)) {
            abort(404, 'Draft synopsis document file not found.');
        }

        return response()->file(storage_path('app/private/' . $circulation->draft_synopsis_doc_path));
    }

    // Resolve the authority role & label for the given user relative to the student.
    private function resolveAuthorityInfo($user, $student): array
    {
        if ($student->isMainSupervisor($user)) {
            return [
                'role' => 'main_supervisor',
                'label' => 'Main Supervisor (' . $user->name . ')',
            ];
        }

        if ($student->isCoSupervisor($user)) {
            return [
                'role' => 'co_supervisor',
                'label' => 'Co-Supervisor (' . $user->name . ')',
            ];
        }

        if ($student->isPspcMember($user)) {
            return [
                'role' => 'pspc_member',
                'label' => 'PSPC Member (' . $user->name . ')',
            ];
        }

        if ($user->isDpgc()) {
            return [
                'role' => 'dpgc',
                'label' => 'Department Postgraduate Committee (DPGC)',
            ];
        }

        if ($user->isHod()) {
            return [
                'role' => 'hod',
                'label' => 'Head of Department (HOD)',
            ];
        }

        if ($user->isSectionOfficer()) {
            return [
                'role' => 'section_officer',
                'label' => 'Section Officer',
            ];
        }

        if ($user->isDoaa() || $user->isAdoaa()) {
            return [
                'role' => 'doaa',
                'label' => 'Dean of Academic Affairs (DOAA)',
            ];
        }

        return [
            'role' => 'authority',
            'label' => 'Academic Reviewer (' . $user->name . ')',
        ];
    }
}
