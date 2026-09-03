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
        $ext = $file->getClientOriginalExtension() ?: 'pdf';
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
     * Handle document upload or retention for an in-progress form.
     * - If a new file is uploaded ($file): saves it fresh into in_progress/
     * - If no new file is uploaded ($file is null): checks $existingPath. If $existingPath is in reverted/ or elsewhere outside in_progress/, copies it into in_progress/. If already in in_progress/, keeps it.
     * - Returns the final in_progress storage path (or null if no file).
     */
    public function handleInProgressFile(
        ?UploadedFile $file,
        ?string $existingPath,
        string $rollNumber,
        int $thesisId,
        string $formType,          // 'pts1', 'pts2', 'pts3', 'pts4'
        string $docKey,            // 'Draft_Synopsis', 'Publication_List', 'Synopsis_Report', etc.
        string $origin = 'Student' // 'Student' or 'Supervisor_Modified'
    ): ?string {
        $disk = Storage::disk('local');
        $dir = "students/{$rollNumber}/thesis_{$thesisId}/{$formType}/in_progress";
        $formPrefix = strtoupper($formType);

        // Case 1: Fresh file uploaded in request
        if ($file instanceof UploadedFile) {
            $ext = $file->getClientOriginalExtension() ?: 'pdf';
            $filename = "{$rollNumber}_{$formPrefix}_{$docKey}_{$origin}.{$ext}";
            return $file->storeAs($dir, $filename, 'local');
        }

        // Case 2: Existing file retention (e.g. student resubmitting after reversion without re-uploading)
        if ($existingPath && $disk->exists($existingPath)) {
            $ext = pathinfo($existingPath, PATHINFO_EXTENSION) ?: 'pdf';
            $filename = "{$rollNumber}_{$formPrefix}_{$docKey}_{$origin}.{$ext}";
            $targetPath = "{$dir}/{$filename}";

            // If already in target in_progress directory, keep it
            if ($existingPath === $targetPath) {
                return $existingPath;
            }

            if (!$disk->exists($dir)) {
                $disk->makeDirectory($dir);
            }

            // Copy from reverted/archived folder into in_progress
            $disk->copy($existingPath, $targetPath);
            return $targetPath;
        }

        return null;
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
     * Move all files in a source directory or referenced on form to target directory and update DB path attributes.
     */
    protected function moveDirectoryContents(string $sourceDir, string $targetDir, object $form): void
    {
        $disk = Storage::disk('local');
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
            'thesis_doc_path',
            'main_supervisor_thesis_doc_path',
        ];

        $updatedAttributes = [];

        // 1. Move any files sitting in source directory (e.g. in_progress)
        if ($disk->exists($sourceDir)) {
            $files = $disk->files($sourceDir);
            if (!empty($files)) {
                if (!$disk->exists($targetDir)) {
                    $disk->makeDirectory($targetDir);
                }

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
            }
        }

        // 2. Check all document path attributes on the form directly
        // (In case the form was resubmitted without re-uploading, so file path points to a historical folder)
        foreach ($docPathAttributes as $attr) {
            if (isset($form->$attr) && !empty($form->$attr)) {
                $oldPath = $form->$attr;
                // If file exists on disk and is NOT already inside $targetDir
                if ($disk->exists($oldPath) && strpos($oldPath, $targetDir) === false) {
                    if (!$disk->exists($targetDir)) {
                        $disk->makeDirectory($targetDir);
                    }

                    $filename = basename($oldPath);
                    $newPath = "{$targetDir}/{$filename}";

                    // If file is in a historical folder (reverted/rejected/approved), copy it so past history isn't broken
                    if (strpos($oldPath, '/reverted/') !== false || strpos($oldPath, '/rejected/') !== false || strpos($oldPath, '/approved/') !== false) {
                        $disk->copy($oldPath, $newPath);
                    } else {
                        try {
                            $disk->move($oldPath, $newPath);
                        } catch (\Throwable $e) {
                            $disk->copy($oldPath, $newPath);
                        }
                    }

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
