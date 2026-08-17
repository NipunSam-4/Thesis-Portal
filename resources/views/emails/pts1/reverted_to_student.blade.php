@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-amber">Action Required &bull; Reverted</span>
    
    <h2 class="email-title">PTS-1 Form Reverted for Revisions</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        Your PTS-1 Form has been reverted by <strong>{{ strtoupper(str_replace('_', ' ', $revertedByRole ?? 'Academic Authority')) }}</strong>.
        Please review the authority's remarks, make the requested modifications, and resubmit your form.
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Reverted By' => strtoupper(str_replace('_', ' ', $revertedByRole ?? 'Academic Authority')),
            'Authority Remarks / Comments' => $reversionComment ?? 'Please check comments on portal.',
            'Thesis Title' => $thesisTitle,
            'Reverted Date' => $revertedDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.pts1.edit'),
        'actionText' => 'Edit & Resubmit PTS-1 Form'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Please make the necessary corrections promptly to avoid workflow delays.<br><br>
        Regards,<br>
        <strong>PhD Thesis Portal System</strong>
    </p>
@endsection
