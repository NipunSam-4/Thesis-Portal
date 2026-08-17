@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-purple">Feedback Received</span>
    
    <h2 class="email-title">Feedback Posted on Draft Synopsis</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        <strong>{{ $authorityLabel ?? 'An Academic Authority' }}</strong> has reviewed your circulated Draft Synopsis 
        and posted feedback comments.
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Reviewer' => $authorityLabel ?? 'Academic Authority',
            'Thesis Title' => $thesisTitle,
            'Date' => $commentDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.draft_synopsis.show'),
        'actionText' => 'Read Feedback Comments'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Please review the reviewer remarks and discuss with your supervisor if necessary.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
