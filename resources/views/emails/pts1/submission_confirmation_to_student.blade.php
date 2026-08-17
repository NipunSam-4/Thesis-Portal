@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-green">Submission Received</span>
    
    <h2 class="email-title">PTS-1 Form {{ ucfirst($actionVerb ?? 'Submitted') }} Successfully</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your <strong>PTS-1 (Ph.D. Thesis Registration / Plan of Research)</strong> form has been successfully {{ $actionVerb ?? 'submitted' }} 
        and queued for institutional approval.
    </p>

    <p>It has been forwarded to your <strong>Main Supervisor</strong> for initial evaluation and recommendations.</p>

    @include('emails.components.details_box', [
        'details' => [
            'Thesis Title' => $thesisTitle,
            'Current Stage' => 'Main Supervisor Review',
            'Submission Date' => $submissionDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.dashboard'),
        'actionText' => 'Track Form on Dashboard'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        You will receive real-time notifications as your form progresses through subsequent committee stages.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
