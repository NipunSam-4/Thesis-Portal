@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-green">Circulation Confirmed</span>
    
    <h2 class="email-title">Draft Synopsis {{ ucfirst($actionVerb ?? 'Circulated') }} Successfully</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your <strong>Draft Synopsis Report</strong> for thesis <em>"{{ $thesisTitle }}"</em> has been {{ $actionVerb ?? 'circulated' }} 
        and shared with your Supervisor, Co-Supervisors, PSPC Members, and Department Authorities for review.
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Thesis Title' => $thesisTitle,
            'Status' => 'Circulated for Review',
            'Date' => $circulationDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.draft_synopsis.show'),
        'actionText' => 'View Draft Synopsis Status'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        You will receive notifications whenever an authority posts feedback comments on your draft synopsis.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
