<?php

namespace App\Http\Controllers;

use App\Models\DraftSynopsisCirculation;
use App\Models\DraftSynopsisComment;
use App\Models\Student;
use App\Models\User;
use App\Services\PtsDocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Purify\Facades\Purify;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DraftSynopsisController extends Controller
{
    use AuthorizesRequests;
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // ==========================================
    // 1. STUDENT ACTIONS
    // ==========================================

    /**
     * Display the student draft synopsis circulation form and comments trail.
     */
    public function studentShow(?DraftSynopsisCirculation $draftSynopsisCirculation = null)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        if ($draftSynopsisCirculation) {
            if ($draftSynopsisCirculation->thesis?->student_id !== $student->id) {
                abort(403, 'Unauthorized to view this draft synopsis circulation.');
            }
            $circulation = $draftSynopsisCirculation;
            $thesis = $circulation->thesis;
        } else {
            $thesis = $student->theses()->where('status', 'in_progress')->latest()->first()
                ?? $student->theses()->where('status', 'completed')->latest()->first();

            if (!$thesis) {
                return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
            }

            $circulation = $thesis->draftSynopsisCirculation;
        }

        $pts1Approved = $thesis->pts1Form && $thesis->pts1Form->status === 'approved';

        // If PTS-1 is approved and student never submitted draft synopsis, disallow access
        if ($pts1Approved && !$circulation) {
            return redirect()->route('student.dashboard')->with('warning', 'Draft Synopsis Circulation is closed as your PTS-1 form has already been approved.');
        }

        $comments = $circulation ? $circulation->getOrderedComments() : collect();

        return view('student.draft_synopsis.index', compact('user', 'student', 'thesis', 'circulation', 'pts1Approved', 'comments'));
    }

    /**
     * Store or update draft synopsis circulation (Student).
     */
    public function studentStore(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('error', 'This thesis has already been completed.');
            }
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

        return redirect()->route('dashboard')->with('success', "Draft Synopsis {$verb} successfully and shared with all Supervisors and PSPC Members!");
    }

    // ==========================================
    // 2. AUTHORITY REVIEW & COMMENT ACTIONS
    // ==========================================

    /**
     * Display the review page for academic authorities to inspect draft synopsis & submit feedback.
     */
    public function review(DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('view', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student.user']);
        $user = auth()->user();
        $thesis = $circulation->thesis;
        $student = $thesis->student;

        $authorityInfo = $this->resolveAuthorityInfo($user, $student);

        // Fetch existing comment by this user if any
        $userComment = DraftSynopsisComment::where('draft_synopsis_circulation_id', $circulation->id)
            ->where('user_id', $user->id)
            ->first();

        // Auto-cleanup any unsubmitted/abandoned comment images left over from previous uncommitted editing sessions
        if ($student && $user && $thesis) {
            $activeFilenames = [];
            if ($userComment && !empty($userComment->comment)) {
                preg_match_all('/([A-Za-z0-9_]+_Img_[A-Za-z0-9_]+\.(?:jpe?g|png|gif|webp))/i', $userComment->comment, $matches);
                $activeFilenames = array_unique($matches[1] ?? []);
            }
            $this->ptsDocService->cleanupOrphanedCommentImages($student->roll_number, $thesis->id, $user->id, $activeFilenames);
        }

        $comments = $circulation->getOrderedComments();

        return view('draft_synopsis.review', compact('circulation', 'user', 'student', 'thesis', 'authorityInfo', 'userComment', 'comments'));
    }

    /**
     * Submit or update an authority's feedback comment.
     */
    public function comment(Request $request, DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('comment', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);
        $user = auth()->user();
        $student = $circulation->thesis->student;

        $pts1Form = $circulation->thesis?->pts1Form;
        if ($pts1Form && $pts1Form->status === 'approved') {
            return back()->with('error', 'Draft synopsis circulation is closed because PTS-1 form has already been approved.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:15000',
        ]);

        // Extract all attached image filenames from comment HTML
        preg_match_all('/([A-Za-z0-9_]+_Img_[A-Za-z0-9_]+\.(?:jpe?g|png|gif|webp))/i', $validated['comment'], $matches);
        $imageFilenames = array_unique($matches[1] ?? []);

        // Validate that total number of attached images does not exceed 10
        if (count($imageFilenames) > 10) {
            return back()->withInput()->withErrors([
                'comment' => 'A maximum of 10 images can be attached to a comment. Please remove excess images.'
            ]);
        }

        // Validate that total size of all attached images in the comment does not exceed 10 MB
        if (!empty($imageFilenames)) {
            $totalBytes = 0;
            $disk = Storage::disk('local');
            
            foreach ($imageFilenames as $imgName) {
                $path = "students/{$student->roll_number}/thesis_{$circulation->thesis_id}/draft_synopsis/comment_images/{$user->id}/{$imgName}";
                if ($disk->exists($path)) {
                    $totalBytes += $disk->size($path);
                }
            }

            $maxTotalBytes = 10 * 1024 * 1024; // 10 MB
            if ($totalBytes > $maxTotalBytes) {
                $formattedSize = round($totalBytes / (1024 * 1024), 2);
                return back()->withInput()->withErrors([
                    'comment' => "Total size of all attached images ({$formattedSize} MB) in this comment exceeds the allowed limit of 10 MB."
                ]);
            }
        }

        // Clean up orphaned / replaced images from previous edits of this user's comment
        $this->ptsDocService->cleanupOrphanedCommentImages($student->roll_number, $circulation->thesis_id, $user->id, $imageFilenames);

        $authorityInfo = $this->resolveAuthorityInfo($user, $student);
        $sanitizedComment = Purify::clean($validated['comment']);

        // Normalize all image src paths to the canonical URL
        foreach ($imageFilenames as $imgName) {
            $canonicalUrl = route('draft_synopsis.serve_comment_image', [
                'draftSynopsisCirculation' => $circulation->id,
                'userId' => $user->id,
                'filename' => $imgName,
            ], false);
            
            $sanitizedComment = preg_replace(
                '/src=["\'][^"\']*' . preg_quote($imgName, '/') . '["\']/i',
                'src="' . $canonicalUrl . '"',
                $sanitizedComment
            );
        }

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

    /**
     * Upload an image embedded in TinyMCE editor.
     */
    public function uploadCommentImage(Request $request, DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('comment', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);
        $student = $circulation->thesis?->student;
        $user = auth()->user();

        if (!$student && isset($circulation->student_id)) {
            $student = Student::find($circulation->student_id);
        }

        if (!$student) {
            abort(404, 'Student profile not found for this circulation.');
        }

        // Guardrail: Enforce a hard cap of maximum 10 images per user for this comment
        $disk = Storage::disk('local');
        $commentImagesDir = "students/{$student->roll_number}/thesis_{$circulation->thesis_id}/draft_synopsis/comment_images/{$user->id}";
        if ($disk->exists($commentImagesDir)) {
            $existingCount = count($disk->files($commentImagesDir));
            if ($existingCount >= 10) {
                return response()->json([
                    'error' => 'You cannot upload more than 10 images for this comment. Please remove an image before uploading another.'
                ], 422);
            }
        }

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $filename = $this->ptsDocService->storeDraftSynopsisCommentImage($request->file('file'), $student->roll_number, $circulation->thesis_id, $user->id);

        return response()->json([
            'location' => route('draft_synopsis.serve_comment_image', [
                'draftSynopsisCirculation' => $circulation->id,
                'userId' => $user->id,
                'filename' => $filename
            ], false),
        ]);
    }

    /**
     * Serve comment image uploaded in TinyMCE securely.
     */
    public function serveCommentImage(DraftSynopsisCirculation $draftSynopsisCirculation, $userId, $filename)
    {
        $this->authorize('view', $draftSynopsisCirculation);

        $actualFilename = basename($filename);
        if (empty($actualFilename)) {
            abort(404, 'Invalid image filename.');
        }

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);
        $student = $circulation->thesis?->student;
        if (!$student) {
            abort(404, 'Student not found.');
        }

        $rollNumber = $student->roll_number;
        $thesisId = $circulation->thesis_id;
        $targetUserId = (int)$userId;

        $disk = Storage::disk('local');
        $imagePath = "students/{$rollNumber}/thesis_{$thesisId}/draft_synopsis/comment_images/{$targetUserId}/{$actualFilename}";

        if (!$disk->exists($imagePath)) {
            abort(404, 'Comment image not found on server storage.');
        }

        return response()->file($disk->path($imagePath));
    }

    /**
     * Serve the uploaded draft synopsis document securely.
     */
    public function serveDocument(DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('view', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);

        if (!Storage::disk('local')->exists($circulation->draft_synopsis_doc_path)) {
            abort(404, 'Draft synopsis document file not found.');
        }

        return response()->file(Storage::disk('local')->path($circulation->draft_synopsis_doc_path));
    }

    /**
     * Resolve the authority role & label for the given user relative to the student.
     */
    private function resolveAuthorityInfo(User $user, Student $student): array
    {
        if ($student->isMainSupervisor($user)) {
            return [
                'role' => 'main_supervisor',
                'label' => 'Main Supervisor (' . $user->name . ')',
            ];
        }

        if ($student->isExternalSupervisor($user)) {
            $roleTitle = $student->getSupervisorRoleTitle($user);
            $inst = $user->externalSupervisorProfile?->affiliated_institute ? ' - ' . $user->externalSupervisorProfile->affiliated_institute : '';
            return [
                'role' => 'external_supervisor',
                'label' => $roleTitle . ' (' . $user->name . $inst . ')',
            ];
        }

        if ($student->isInternalCoSupervisor($user)) {
            $roleTitle = $student->getSupervisorRoleTitle($user);
            return [
                'role' => 'co_supervisor',
                'label' => $roleTitle . ' (' . $user->name . ')',
            ];
        }

        if ($student->isPspcMember($user)) {
            return [
                'role' => 'pspc_member',
                'label' => 'PSPC Member (' . $user->name . ')',
            ];
        }

        return [
            'role' => 'authority',
            'label' => 'Academic Reviewer (' . $user->name . ')',
        ];
    }
}
