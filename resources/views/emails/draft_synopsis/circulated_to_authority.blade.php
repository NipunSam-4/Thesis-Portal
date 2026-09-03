@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-blue">Action Required &bull; Review</span>
    
    <h2 class="email-title">Draft Synopsis Report Circulated</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Professor' }}</strong>,</p>

    <p>
        Research Scholar <strong>{{ $studentName }}</strong> (Roll No: <strong>{{ $rollNumber ?? 'N/A' }}</strong>) 
        has {{ $actionVerb ?? 'circulated' }} their <strong>Draft Synopsis Report</strong> for thesis <em>"{{ $thesisTitle }}"</em>.
    </p>

    <p>Please log in to the portal to download the document and provide your feedback comments or approval.</p>

    @include('emails.components.details_box', [
        'details' => [
            'Student Name' => $studentName,
            'Roll Number' => $rollNumber ?? 'N/A',
            'Department' => $department ?? 'N/A',
            'Thesis Title' => $thesisTitle,
            'Circulated Date' => $circulationDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('login'),
        'actionText' => 'Review Draft Synopsis in Portal'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Regards,<br>
        <strong>Academic Affairs Office &amp; PTS Desk</strong>
    </p>
@endsection
