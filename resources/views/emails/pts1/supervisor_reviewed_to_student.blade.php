@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-purple">Supervisor Evaluated</span>
    
    <h2 class="email-title">PTS-1 Form Reviewed by Main Supervisor</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your Main Supervisor has reviewed your PTS-1 submission for <strong>"{{ $thesisTitle }}"</strong> 
        and submitted their evaluation.
    </p>

    <p>The form has now been advanced to the next review stage: <strong>{{ strtoupper(str_replace('_', ' ', $nextStage)) }}</strong>.</p>

    @include('emails.components.details_box', [
        'details' => [
            'Thesis Title' => $thesisTitle,
            'Work Status Evaluation' => ucfirst($workStatus ?? 'Adequate'),
            'Advanced To Stage' => strtoupper(str_replace('_', ' ', $nextStage)),
            'Review Date' => $reviewDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.dashboard'),
        'actionText' => 'View Student Dashboard'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
