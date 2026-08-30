<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DraftSynopsisCirculation;
use App\Services\PtsDocumentService;
use Illuminate\Http\Request;

class StudentDraftSynopsisController extends Controller
{
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }
    // Display the student draft synopsis circulation form and comments trail.
    public function show()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $pts1Approved = $thesis->pts1Form && $thesis->pts1Form->status === 'approved';
        $circulation = $thesis->draftSynopsisCirculation;

        // If PTS-1 is approved and student never submitted draft synopsis, disallow access
        if ($pts1Approved && !$circulation) {
            return redirect()->route('student.dashboard')->with('warning', 'Draft Synopsis Circulation is closed as your PTS-1 form has already been approved.');
        }

        $comments = $circulation ? $circulation->getOrderedComments() : collect();

        return view('student.draft_synopsis.index', compact('user', 'student', 'thesis', 'circulation', 'pts1Approved', 'comments'));
    }

    // Store or update draft synopsis circulation.
    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('error', 'Active thesis registration not found.');
        }

        $pts1Approved = $thesis->pts1Form && $thesis->pts1Form->status === 'approved';
        $circulation = $thesis->draftSynopsisCirculation;

        if ($pts1Approved && !$circulation) {
            return redirect()->route('student.dashboard')->with('error', 'Draft Synopsis Circulation is closed as your PTS-1 form has already been approved.');
        }

        if ($circulation) {
            return redirect()->route('student.draft_synopsis.show')->with('warning', 'Draft Synopsis has already been submitted and circulated.');
        }

        $validated = $request->validate([
            'thesis_title' => 'required|string|max:1000',
            'draft_synopsis_report' => [
                $circulation && $circulation->draft_synopsis_doc_path ? 'nullable' : 'required',
                'file', 'mimes:pdf,docx', 'max:10240'
            ],
        ]);

        // Update thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        // Handle private local file upload
        if ($request->hasFile('draft_synopsis_report')) {
            $docPath = $this->ptsDocService->storeDraftSynopsis($request->file('draft_synopsis_report'), $student->roll_number, $thesis->id);
        } else {
            $docPath = $circulation->draft_synopsis_doc_path;
        }

        DraftSynopsisCirculation::updateOrCreate(
            ['thesis_id' => $thesis->id],
            [
                'student_id' => $student->id,
                'thesis_title' => $validated['thesis_title'],
                'draft_synopsis_doc_path' => $docPath,
                'status' => 'circulated',
            ]
        );

        $verb = $circulation ? 'updated' : 'circulated';

        return redirect()->route('student.draft_synopsis.show')->with('success', "Draft Synopsis {$verb} successfully and shared with all academic authorities!");
    }
}
