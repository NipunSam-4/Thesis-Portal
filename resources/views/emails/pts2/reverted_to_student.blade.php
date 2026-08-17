@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-amber">Action Required &bull; Reverted</span>
    
    <h2 class="email-title">PTS-2 Synopsis Form Reverted for Revisions</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your PTS-2 Synopsis Form has been reverted by <strong>{{ strtoupper(str_replace('_', ' ', $revertedByRole ?? 'Academic Authority')) }}</strong> with comments for resubmission.
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Reverted By' => strtoupper(str_replace('_', ' ', $revertedByRole ?? 'Academic Authority')),
            'Authority Remarks / Comments' => $reversionComment ?? 'Please check remarks on the portal.',
            'Thesis Title' => $thesisTitle,
            'Reverted Date' => $revertedDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.pts2.create'),
        'actionText' => 'Edit & Resubmit PTS-2 Form'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Please make the requested updates and resubmit promptly.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
