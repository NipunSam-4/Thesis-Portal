@extends('emails.layouts.master')

@section('content')
    @php
        $isAccepted = ($status === 'accepted');
        $isRejected = ($status === 'rejected');
        $badgeClass = $isAccepted ? 'badge-green' : ($isRejected ? 'badge-rose' : 'badge-blue');
    @endphp

    <span class="badge {{ $badgeClass }}">
        {{ $isAccepted ? 'Official Approval' : ($isRejected ? 'Application Rejected' : 'Stage Endorsement') }}
    </span>
    
    <h2 class="email-title">
        @if($isAccepted)
            PTS-1 Form Approved by DOAA
        @elseif($isRejected)
            PTS-1 Form Rejected
        @else
            PTS-1 Form Advanced to {{ strtoupper(str_replace('_', ' ', $currentStage)) }}
        @endif
    </h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        @if($isAccepted)
            Congratulations! Your PTS-1 Form has received final approval from the <strong>Dean of Academic Affairs (DOAA)</strong>. Your Ph.D. registration / plan of research is officially confirmed.
        @elseif($isRejected)
            Your PTS-1 Form has been rejected during the academic review workflow.
        @else
            Your PTS-1 Form has been endorsed at stage <strong>{{ strtoupper(str_replace('_', ' ', $endorsedStage)) }}</strong> and has advanced to <strong>{{ strtoupper(str_replace('_', ' ', $currentStage)) }}</strong> for subsequent evaluation.
        @endif
    </p>

    @include('emails.components.details_box', [
        'details' => [
            'Thesis Title' => $thesisTitle,
            'Current Stage' => strtoupper(str_replace('_', ' ', $currentStage)),
            'Overall Status' => ucfirst($status),
            'Updated At' => $updatedDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('student.dashboard'),
        'actionText' => 'Open Student Dashboard'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Regards,<br>
        <strong>Office of the Dean of Academic Affairs (DOAA) &amp; PTS Desk</strong>
    </p>
@endsection
