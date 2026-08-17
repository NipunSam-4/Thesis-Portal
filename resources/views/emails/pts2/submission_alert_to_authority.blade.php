@extends('emails.layouts.master')

@section('content')
    <span class="badge badge-blue">Action Required</span>
    
    <h2 class="email-title">PTS-2 Synopsis Form Submitted &bull; Pending Review</h2>

    <p>Dear <strong>{{ $recipientName ?? 'Professor' }}</strong>,</p>

    <p>
        Research Scholar <strong>{{ $studentName }}</strong> (Roll No: <strong>{{ $rollNumber ?? 'N/A' }}</strong>) 
        has submitted their <strong>PTS-2 (Ph.D. Synopsis Evaluation &amp; Panel Approval)</strong> form for review.
    </p>

    <p>Please log in to the PhD Thesis Portal to review the synopsis submission, proposed examiner panels, and submit your endorsement.</p>

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
