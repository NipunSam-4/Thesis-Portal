<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PtsDocumentService
{
    /**
     * Store Draft Synopsis Circulation Document.
     * Path: students/{rollNumber}/thesis_{thesisId}/draft_synopsis/{rollNumber}_Draft_Synopsis_Circulation.{ext}
     */
    public function storeDraftSynopsis(UploadedFile $file, string $rollNumber, int $thesisId): string
    {
        $ext = $file->getClientOriginalExtension();
        $filename = "{$rollNumber}_Draft_Synopsis_Circulation.{$ext}";
        $dir = "students/{$rollNumber}/thesis_{$thesisId}/draft_synopsis";

        return $file->storeAs($dir, $filename, 'local');
    }

    /**
     * Store an inline image uploaded in TinyMCE draft synopsis comments under user ID subfolder.
     * Path: students/{rollNumber}/thesis_{thesisId}/draft_synopsis/comment_images/{userId}/{rollNumber}_{userId}_Img_{timestamp}_{uniqid}.{ext}
     */
    public function storeDraftSynopsisCommentImage(UploadedFile $file, string $rollNumber, int $thesisId, int $userId): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $unique = time() . '_' . substr(md5(uniqid('', true)), 0, 6);
        $filename = "{$rollNumber}_{$userId}_Img_{$unique}.{$ext}";
        $dir = "students/{$rollNumber}/thesis_{$thesisId}/draft_synopsis/comment_images/{$userId}";

        $file->storeAs($dir, $filename, 'local');
        return $filename;
    }

    /**
     * Clean up orphaned/replaced images for a specific user's comment.
     */
    public function cleanupOrphanedCommentImages(string $rollNumber, int $thesisId, int $userId, array $activeFilenames): void
    {
        $disk = Storage::disk('local');
        $dir = "students/{$rollNumber}/thesis_{$thesisId}/draft_synopsis/comment_images/{$userId}";

        if (!$disk->exists($dir)) {
            return;
        }

        $existingFiles = $disk->files($dir);
        foreach ($existingFiles as $file) {
            $basename = basename($file);
            if (!in_array($basename, $activeFilenames, true)) {
                $disk->delete($file);
            }
        }
    }

    /**
     * Store an in-progress document for PTS-1, PTS-2, etc.
     * Path: students/{rollNumber}/thesis_{thesisId}/{formType}/in_progress/{rollNumber}_{FORM}_{DocKey}_{Origin}.{ext}
     */
    public function storeInProgressDocument(
        UploadedFile $file,
        string $rollNumber,
        int $thesisId,
        string $formType,      // 'pts1', 'pts2'
        string $docKey,        // 'Draft_Synopsis', 'Publication_List', 'Min_Time_Approval', etc.
        string $origin = 'Student' // 'Student' or 'Supervisor_Modified'
    ): string {
        $ext = $file->getClientOriginalExtension();
        $formPrefix = strtoupper($formType);
        $filename = "{$rollNumber}_{$formPrefix}_{$docKey}_{$origin}.{$ext}";
        $dir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/in_progress";

        return $file->storeAs($dir, $filename, 'local');
    }

    /**
     * Move in_progress files to reverted/{formId}/ when reverted.
     */
    public function moveToReverted(object $form, string $formType): void
    {
        $rollNumber = $this->getRollNumber($form);
        $thesisId = $this->getThesisId($form);
        if (!$rollNumber || !$thesisId) return;

        $sourceDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/in_progress";
        $targetDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/reverted/{$form->id}";

        $this->moveDirectoryContents($sourceDir, $targetDir, $form);
    }

    /**
     * Move in_progress files to rejected/{formId}/ when rejected.
     */
    public function moveToRejected(object $form, string $formType): void
    {
        $rollNumber = $this->getRollNumber($form);
        $thesisId = $this->getThesisId($form);
        if (!$rollNumber || !$thesisId) return;

        $sourceDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/in_progress";
        $targetDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/rejected/{$form->id}";

        $this->moveDirectoryContents($sourceDir, $targetDir, $form);
    }

    /**
     * Move in_progress files to approved/ on final approval.
     */
    public function moveToApproved(object $form, string $formType): void
    {
        $rollNumber = $this->getRollNumber($form);
        $thesisId = $this->getThesisId($form);
        if (!$rollNumber || !$thesisId) return;

        $sourceDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/in_progress";
        $targetDir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/approved";

        $this->moveDirectoryContents($sourceDir, $targetDir, $form);
    }

    /**
     * Extract student roll number from a form model.
     */
    protected function getRollNumber(object $form): ?string
    {
        if (isset($form->thesis) && isset($form->thesis->student)) {
            return $form->thesis->student->roll_number;
        }

        if (isset($form->student)) {
            return $form->student->roll_number;
        }

        return null;
    }

    /**
     * Extract thesis ID from a form model.
     */
    protected function getThesisId(object $form): ?int
    {
        if (isset($form->thesis_id)) {
            return (int) $form->thesis_id;
        }

        if (isset($form->thesis) && isset($form->thesis->id)) {
            return (int) $form->thesis->id;
        }

        return null;
    }

    /**
     * Move all files in a source directory to target directory and update DB path attributes.
     */
    protected function moveDirectoryContents(string $sourceDir, string $targetDir, object $form): void
    {
        $disk = Storage::disk('local');
        if (!$disk->exists($sourceDir)) {
            return;
        }

        $files = $disk->files($sourceDir);
        if (empty($files)) {
            return;
        }

        if (!$disk->exists($targetDir)) {
            $disk->makeDirectory($targetDir);
        }

        $docPathAttributes = [
            'draft_synopsis_report_doc_path',
            'publication_list_doc_path',
            'publication_approval_doc_path',
            'min_time_approval_doc_path',
            'main_supervisor_draft_synopsis_report_doc_path',
            'main_supervisor_publication_list_doc_path',
            'main_supervisor_publication_approval_doc_path',
            'main_supervisor_min_time_approval_doc_path',
            'synopsis_report_doc_path',
            'main_supervisor_synopsis_report_doc_path',
            'draft_synopsis_doc_path',
        ];

        $updatedAttributes = [];

        foreach ($files as $oldPath) {
            $filename = basename($oldPath);
            $newPath = "{$targetDir}/{$filename}";

            // Move the file on disk
            $disk->move($oldPath, $newPath);

            // Update any model attributes pointing to the old path
            foreach ($docPathAttributes as $attr) {
                if (isset($form->$attr) && $form->$attr === $oldPath) {
                    $updatedAttributes[$attr] = $newPath;
                    $form->$attr = $newPath;
                }
            }
        }

        if (!empty($updatedAttributes)) {
            $form->update($updatedAttributes);
        }
    }
}
