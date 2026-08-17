@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-blue">Action Required</span>
    
    <h2 class="email-title">PTS-1 Form {{ ucfirst($actionVerb ?? 'Submitted') }} &bull; Pending Your Review</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Professor' }}</strong>,</p>

    <p>
        Research Scholar <strong>{{ $studentName }}</strong> (Roll No: <strong>{{ $rollNumber ?? 'N/A' }}</strong>) 
        has {{ $actionVerb ?? 'submitted' }} their <strong>PTS-1 (Ph.D. Thesis Registration / Plan of Research)</strong> form for thesis evaluation.
    </p>

    <p>Please log in to the PhD Thesis Portal to review the research topic, coursework credentials, committee nominations, and provide your formal endorsement.</p>

    @include('emails.components.details_box', [
        'details' => [
            'Student Name' => $studentName,
            'Roll Number' => $rollNumber ?? 'N/A',
            'Department' => $department ?? 'N/A',
            'Thesis Title' => $thesisTitle,
            'Submission Date' => $submissionDate ?? now()->format('d M Y, h:i A'),
        ]
    ])

    @include('emails.components.action_button', [
        'actionUrl' => $actionUrl ?? route('faculty.dashboard'),
        'actionText' => 'Open Faculty Dashboard to Review'
    ])

    <div class="divider"></div>

    <p style="font-size: 13px; color: #64748b;">
        Regards,<br>
        <strong>Academic Affairs Office &amp; PTS Desk</strong>
    </p>
@endsection
