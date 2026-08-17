@extends('emails.layouts.master')

@section('content')
    @php
        $isAccepted = ($status === 'accepted');
        $badgeClass = $isAccepted ? 'badge-green' : 'badge-blue';
    @endphp

    <span class="badge {{ $badgeClass }}">
        {{ $isAccepted ? 'Official Approval' : 'Stage Endorsement' }}
    </span>
    
    <h2 class="email-title">
        @if($isAccepted)
            PTS-2 Synopsis Form Approved by DOAA
        @else
            PTS-2 Synopsis Form Advanced to {{ strtoupper(str_replace('_', ' ', $currentStage)) }}
        @endif
    </h2>

    <p>Dear <strong>{{ $recipientName ?? 'Scholar' }}</strong>,</p>

    <p>
        @if($isAccepted)
            Congratulations! Your PTS-2 Synopsis Form has been officially approved by the <strong>Dean of Academic Affairs (DOAA)</strong>. The examiner panel and synopsis evaluation stage is complete.
        @else
            Your PTS-2 Synopsis Form was endorsed and has advanced to stage: <strong>{{ strtoupper(str_replace('_', ' ', $currentStage)) }}</strong>.
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
        'actionText' => 'View Student Dashboard'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Regards,<br>
        <strong>Office of the Dean of Academic Affairs (DOAA) &amp; PTS Desk</strong>
    </p>
@endsection
