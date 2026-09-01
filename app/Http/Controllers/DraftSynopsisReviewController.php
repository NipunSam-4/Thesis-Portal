<?php

namespace App\Http\Controllers;

use App\Models\DraftSynopsisCirculation;
use App\Models\DraftSynopsisComment;
use App\Services\PtsDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Purify\Facades\Purify;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DraftSynopsisReviewController extends Controller
{
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Display the review page for academic authorities to inspect draft synopsis & submit feedback.
    public function show(DraftSynopsisCirculation $draftSynopsisCirculation)
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

        $comments = $circulation->getOrderedComments();

        return view('draft_synopsis.review', compact('circulation', 'user', 'student', 'thesis', 'authorityInfo', 'userComment', 'comments'));
    }

    // Submit or update an authority's feedback comment.
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

        // Validate that total size of all attached images in the comment does not exceed 10 MB (10,485,760 bytes)
        preg_match_all('/([A-Za-z0-9_]+_Img_[A-Za-z0-9_]+\.(?:jpe?g|png|gif|webp))/i', $validated['comment'], $matches);
        $imageFilenames = array_unique($matches[1] ?? []);

        if (!empty($imageFilenames)) {
            $totalBytes = 0;
            $disk = Storage::disk('local');
            
            foreach ($imageFilenames as $imgName) {
                $candidates = [
                    "students/{$student->roll_number}/draft_synopsis/comment_images/{$user->id}/{$imgName}",
                    "students/{$student->roll_number}/draft_synopsis/comment_images/{$imgName}",
                ];
                foreach ($candidates as $path) {
                    if ($disk->exists($path)) {
                        $totalBytes += $disk->size($path);
                        break;
                    }
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

        // Normalize all image src paths to the canonical URL /draft-synopsis/{id}/comment-images/{userId}/{filename}
        foreach ($imageFilenames as $imgName) {
            $canonicalUrl = route('draft_synopsis.serve_comment_image', [
                'id' => $circulation->id,
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

    // Upload an image embedded in TinyMCE editor.
    public function uploadCommentImage(Request $request, DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('comment', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);
        $student = $circulation->thesis?->student;
        $user = auth()->user();

        if (!$student && isset($circulation->student_id)) {
            $student = \App\Models\Student::find($circulation->student_id);
        }

        if (!$student) {
            abort(404, 'Student profile not found for this circulation.');
        }

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $filename = $this->ptsDocService->storeDraftSynopsisCommentImage($request->file('file'), $student->roll_number, $circulation->thesis_id, $user->id);

        // Return root-relative URL with user ID subfolder
        return response()->json([
            'location' => route('draft_synopsis.serve_comment_image', [
                'id' => $circulation->id,
                'userId' => $user->id,
                'filename' => $filename
            ], false),
        ]);
    }

    // Serve comment image uploaded in TinyMCE securely.
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
        $candidates = [
            "students/{$rollNumber}/thesis_{$thesisId}/draft_synopsis/comment_images/{$targetUserId}/{$actualFilename}",
            "students/{$rollNumber}/draft_synopsis/comment_images/{$targetUserId}/{$actualFilename}",
            "students/{$rollNumber}/draft_synopsis/comment_images/{$actualFilename}",
            "draft_synopsis_documents/{$actualFilename}",
        ];

        $foundPath = null;
        foreach ($candidates as $cand) {
            if ($disk->exists($cand)) {
                $foundPath = $cand;
                break;
            }
        }

        if (!$foundPath) {
            abort(404, 'Comment image not found on server storage.');
        }

        return response()->file($disk->path($foundPath));
    }

    // Serve the uploaded draft synopsis document securely.
    public function serveDocument(DraftSynopsisCirculation $draftSynopsisCirculation)
    {
        $this->authorize('view', $draftSynopsisCirculation);

        $circulation = $draftSynopsisCirculation->loadMissing(['thesis.student']);

        if (!Storage::disk('local')->exists($circulation->draft_synopsis_doc_path)) {
            abort(404, 'Draft synopsis document file not found.');
        }

        return response()->file(Storage::disk('local')->path($circulation->draft_synopsis_doc_path));
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

        if ($user->isAcademicOffice()) {
            return [
                'role' => 'academic_office',
                'label' => 'Academic Office',
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
