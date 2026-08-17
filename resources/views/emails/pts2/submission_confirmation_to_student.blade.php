@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-green">Submission Received</span>
    
    <h2 class="email-title">PTS-2 Synopsis Form Submitted Successfully</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your <strong>PTS-2 (Ph.D. Synopsis Evaluation &amp; Panel Approval)</strong> form has been successfully submitted and forwarded to your <strong>Main Supervisor</strong> for evaluation.
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Thesis Title' => $thesisTitle,
            'Current Stage' => 'Main Supervisor Review',
            'Submission Date' => $submissionDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.dashboard'),
        'actionText' => 'Track Synopsis Status'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        You will receive real-time notifications as your PTS-2 form is reviewed by your committee and department authorities.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
