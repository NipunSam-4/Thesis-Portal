<?php

namespace App\Services;

use App\Mail\PtsWorkflowAlert;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PtsMailService
{
    // Send a custom Mailable to a student recipient using student_mailer.
    public function sendToStudent(string|array $recipient, Mailable $mailable): bool
    {
        try {
            Mail::mailer('student_mailer')
                ->to($recipient)
                ->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error('PtsMailService: Failed sending email via student_mailer to ' . json_encode($recipient) . '. Error: ' . $e->getMessage());
            return false;
        }
    }

    // Send a custom Mailable to an authority recipient using authority_mailer.
    public function sendToAuthority(string|array $recipient, Mailable $mailable): bool
    {
        try {
            Mail::mailer('authority_mailer')
                ->to($recipient)
                ->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error('PtsMailService: Failed sending email via authority_mailer to ' . json_encode($recipient) . '. Error: ' . $e->getMessage());
            return false;
        }
    }

    // Send a generic structured communication/alert to a student using student_mailer.
    public function notifyStudent(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $title,
        string $message,
        array $details = [],
        ?string $actionUrl = null,
        ?string $actionText = 'View Portal Details',
        string $view = 'emails.pts_alert',
        array $viewData = []
    ): bool {
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            title: $title,
            messageContent: $message,
            recipientName: $recipientName,
            senderDeskName: $senderDeskName,
            details: $details,
            actionUrl: $actionUrl,
            actionText: $actionText,
            view: $view,
            viewData: $viewData
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Send a generic structured notification/alert to an authority using authority_mailer.
    public function notifyAuthority(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $title,
        string $message,
        array $details = [],
        ?string $actionUrl = null,
        ?string $actionText = 'Review Submission',
        string $view = 'emails.pts_alert',
        array $viewData = []
    ): bool {
        $senderDeskName = config('mail.mailers.authority_mailer.from.name') ?: 'PTS Authority Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            title: $title,
            messageContent: $message,
            recipientName: $recipientName,
            senderDeskName: $senderDeskName,
            details: $details,
            actionUrl: $actionUrl,
            actionText: $actionText,
            view: $view,
            viewData: $viewData
        );

        return $this->sendToAuthority($recipientEmail, $mailable);
    }

    // =========================================================================
    // PTS-1 WORKFLOW SPECIFIC EMAILS
    // =========================================================================

    // Notify Main Supervisor / Authorities that a student submitted or resubmitted PTS-1.
    public function sendPts1SubmittedToAuthority(
        string $recipientEmail,
        string $recipientName,
        string $studentName,
        ?string $rollNumber,
        ?string $department,
        string $thesisTitle,
        string $actionVerb = 'submitted',
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-1 Form " . ucfirst($actionVerb) . ": {$studentName}";
        $senderDeskName = config('mail.mailers.authority_mailer.from.name') ?: 'PTS Authority Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts1.submission_alert_to_authority',
            viewData: [
                'recipientName' => $recipientName,
                'studentName' => $studentName,
                'rollNumber' => $rollNumber,
                'department' => $department,
                'thesisTitle' => $thesisTitle,
                'actionVerb' => $actionVerb,
                'submissionDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('faculty.dashboard'),
            ]
        );

        return $this->sendToAuthority($recipientEmail, $mailable);
    }

    // Send submission receipt to student for PTS-1.
    public function sendPts1SubmittedConfirmationToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $actionVerb = 'submitted',
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-1 Form " . ucfirst($actionVerb) . " Confirmation";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts1.submission_confirmation_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'actionVerb' => $actionVerb,
                'submissionDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.dashboard'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student that Main Supervisor reviewed and forwarded PTS-1.
    public function sendPts1SupervisorReviewedToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $workStatus,
        string $nextStage,
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-1 Form Status Update: Reviewed by Main Supervisor";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts1.supervisor_reviewed_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'workStatus' => $workStatus,
                'nextStage' => $nextStage,
                'reviewDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.dashboard'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student of PTS-1 stage advancement, acceptance, or rejection.
    public function sendPts1StageUpdatedToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $currentStage,
        string $status,
        string $endorsedStage,
        ?string $actionUrl = null
    ): bool {
        $isFinal = in_array($status, ['approved', 'rejected']);
        $subjectStatus = $isFinal ? ucfirst($status) : 'Endorsed';
        $subject = "PTS-1 Form Status Update: {$subjectStatus}";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts1.stage_updated_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'currentStage' => $currentStage,
                'status' => $status,
                'endorsedStage' => $endorsedStage,
                'updatedDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.dashboard'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student when PTS-1 is reverted for revisions.
    public function sendPts1RevertedToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $revertedByRole,
        string $reversionComment,
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-1 Form Reverted for Resubmission";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts1.reverted_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'revertedByRole' => $revertedByRole,
                'reversionComment' => $reversionComment,
                'revertedDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.pts1.edit'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // =========================================================================
    // PTS-2 WORKFLOW SPECIFIC EMAILS
    // =========================================================================

    // Notify Main Supervisor that student submitted PTS-2 form.
    public function sendPts2SubmittedToAuthority(
        string $recipientEmail,
        string $recipientName,
        string $studentName,
        ?string $rollNumber,
        ?string $department,
        string $thesisTitle,
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-2 Synopsis Form Submitted: {$studentName}";
        $senderDeskName = config('mail.mailers.authority_mailer.from.name') ?: 'PTS Authority Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts2.submission_alert_to_authority',
            viewData: [
                'recipientName' => $recipientName,
                'studentName' => $studentName,
                'rollNumber' => $rollNumber,
                'department' => $department,
                'thesisTitle' => $thesisTitle,
                'submissionDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('faculty.dashboard'),
            ]
        );

        return $this->sendToAuthority($recipientEmail, $mailable);
    }

    // Send submission confirmation to student for PTS-2.
    public function sendPts2SubmittedConfirmationToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-2 Synopsis Form Submission Confirmation";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts2.submission_confirmation_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'submissionDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.dashboard'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student of PTS-2 stage update or final approval.
    public function sendPts2StageUpdatedToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $currentStage,
        string $status,
        ?string $actionUrl = null
    ): bool {
        $isApproved = ($status === 'approved');
        $subjectStatus = $isApproved ? 'Approved' : 'Endorsed';
        $subject = "PTS-2 Synopsis Form Status Update: {$subjectStatus}";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts2.stage_updated_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'currentStage' => $currentStage,
                'status' => $status,
                'updatedDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.dashboard'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student when PTS-2 form is reverted.
    public function sendPts2RevertedToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $revertedByRole,
        string $reversionComment,
        ?string $actionUrl = null
    ): bool {
        $subject = "PTS-2 Synopsis Form Reverted for Resubmission";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.pts2.reverted_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'revertedByRole' => $revertedByRole,
                'reversionComment' => $reversionComment,
                'revertedDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.pts2.create'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // =========================================================================
    // DRAFT SYNOPSIS WORKFLOW SPECIFIC EMAILS
    // =========================================================================

    // Notify authorities that a Draft Synopsis was circulated.
    public function sendDraftSynopsisCirculatedToAuthority(
        string $recipientEmail,
        string $recipientName,
        string $studentName,
        ?string $rollNumber,
        ?string $department,
        string $thesisTitle,
        string $actionVerb = 'circulated',
        ?string $actionUrl = null
    ): bool {
        $subject = "Draft Synopsis " . ucfirst($actionVerb) . ": {$studentName}";
        $senderDeskName = config('mail.mailers.authority_mailer.from.name') ?: 'PTS Authority Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.draft_synopsis.circulated_to_authority',
            viewData: [
                'recipientName' => $recipientName,
                'studentName' => $studentName,
                'rollNumber' => $rollNumber,
                'department' => $department,
                'thesisTitle' => $thesisTitle,
                'actionVerb' => $actionVerb,
                'circulationDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('login'),
            ]
        );

        return $this->sendToAuthority($recipientEmail, $mailable);
    }

    // Send circulation confirmation to student for Draft Synopsis.
    public function sendDraftSynopsisConfirmationToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $actionVerb = 'circulated',
        ?string $actionUrl = null
    ): bool {
        $subject = "Draft Synopsis " . ucfirst($actionVerb) . " Confirmation";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.draft_synopsis.circulation_confirmation_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'actionVerb' => $actionVerb,
                'circulationDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.draft_synopsis.show'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }

    // Notify student when an authority posts feedback comment on Draft Synopsis.
    public function sendDraftSynopsisFeedbackToStudent(
        string $recipientEmail,
        string $recipientName,
        string $thesisTitle,
        string $authorityLabel,
        ?string $actionUrl = null
    ): bool {
        $subject = "New Feedback on Draft Synopsis";
        $senderDeskName = config('mail.mailers.student_mailer.from.name') ?: 'PTS Student Desk';

        $mailable = new PtsWorkflowAlert(
            subject: $subject,
            senderDeskName: $senderDeskName,
            view: 'emails.draft_synopsis.feedback_received_to_student',
            viewData: [
                'recipientName' => $recipientName,
                'thesisTitle' => $thesisTitle,
                'authorityLabel' => $authorityLabel,
                'commentDate' => now()->format('d M Y, h:i A'),
                'actionUrl' => $actionUrl ?: route('student.draft_synopsis.show'),
            ]
        );

        return $this->sendToStudent($recipientEmail, $mailable);
    }
}
