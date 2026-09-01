<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    // Display the Student Dashboard.
    public function index()
    {
        $user = Auth::user();
        
        $student = $user->student()->with([
            'department', 
            'supervisors',
            'mainSupervisors',
            'coSupervisors',
            'externalSupervisors.externalSupervisorProfile',
            'pspcMembers',
            'theses.draftSynopsisCirculation',
            'theses.pts1Form',
            'theses.pts2Form',
            'theses.pts2Extension',
            'theses.pts3Form',
            'theses.pts4Form',
            'theses.pts4Extension',
            'theses.pts5Form',
            'theses.pts6Form'
        ])->firstOrFail();

        // Active thesis is the current in_progress thesis or completed thesis
        $activeThesis = $student->theses->firstWhere('status', 'in_progress') 
            ?? $student->theses->firstWhere('status', 'completed');

        $latestThesis = $activeThesis;
        $draftSynopsis = $activeThesis ? $activeThesis->draftSynopsisCirculation : null;
        $pts1Form = $activeThesis ? $activeThesis->pts1Form : null;
        $pts2Form = $activeThesis ? $activeThesis->pts2Form : null;
        $pts2Extension = $activeThesis ? $activeThesis->pts2Extension : null;
        $pts3Form = $activeThesis ? $activeThesis->pts3Form : null;
        $pts4Form = $activeThesis ? $activeThesis->pts4Form : null;
        $pts4Extension = $activeThesis ? $activeThesis->pts4Extension : null;
        $pts5Form = $activeThesis ? $activeThesis->pts5Form : null;
        $pts6Form = $activeThesis ? $activeThesis->pts6Form : null;

        // PTS-2 is unlocked when PTS-1 is approved (status === 'approved')
        $pts1Approved = $pts1Form && $pts1Form->status === 'approved';

        // PTS-3 and PTS-4 are unlocked when PTS-2 is approved (status === 'approved')
        $pts2Approved = $pts2Form && $pts2Form->status === 'approved';

        $pts3Approved = $pts3Form && $pts3Form->status === 'approved';
        $pts4Approved = $pts4Form && $pts4Form->status === 'approved';
        $pts5Approved = $pts5Form && $pts5Form->status === 'approved';
        $pts6Approved = $pts6Form && $pts6Form->status === 'approved';

        // 1. Current Rejected Forms (for the current in-progress/active thesis)
        $currentRejectedForms = collect();
        if ($activeThesis && $activeThesis->status === 'in_progress') {
            $rejectedPts1 = $activeThesis->pts1Forms()->where('status', 'rejected')->get();
            $rejectedPts2 = $activeThesis->pts2Forms()->where('status', 'rejected')->get();
            $rejectedPts2Ext = $activeThesis->pts2Extensions()->where('status', 'rejected')->get();
            $rejectedPts3 = $activeThesis->pts3Forms()->where('status', 'rejected')->get();
            $rejectedPts4 = $activeThesis->pts4Forms()->where('status', 'rejected')->get();
            $rejectedPts4Ext = $activeThesis->pts4Extensions()->where('status', 'rejected')->get();
            $rejectedPts5 = $activeThesis->pts5Forms()->where('status', 'rejected')->get();
            $rejectedPts6 = $activeThesis->pts6Forms()->where('status', 'rejected')->get();
            $currentRejectedForms = $rejectedPts1->concat($rejectedPts2)->concat($rejectedPts2Ext)->concat($rejectedPts3)->concat($rejectedPts4)->concat($rejectedPts4Ext)->concat($rejectedPts5)->concat($rejectedPts6)->sortByDesc('created_at');
        }

        // 2. Rejected Theses History (all past theses with status 'rejected')
        $rejectedTheses = $student->theses()
            ->where('status', 'rejected')
            ->with([
                'draftSynopsisCirculation.comments',
                'pts1Form',
                'pts2Form',
                'pts2Extension',
                'pts3Form',
                'pts4Form',
                'pts4Extension',
                'pts5Form',
                'pts6Form',
                'pts1Forms',
                'pts2Forms',
                'pts3Forms',
                'pts4Forms',
                'pts4Extensions',
                'pts5Forms',
                'pts6Forms'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $rejectedForms = $currentRejectedForms;

        return view('student.dashboard', compact(
            'student', 
            'activeThesis', 
            'latestThesis', 
            'draftSynopsis', 
            'pts1Form', 
            'pts2Form', 
            'pts2Extension', 
            'pts3Form', 
            'pts4Form',
            'pts4Extension',
            'pts5Form',
            'pts6Form',
            'pts1Approved', 
            'pts2Approved', 
            'pts3Approved',
            'pts4Approved',
            'pts5Approved',
            'pts6Approved',
            'currentRejectedForms',
            'rejectedTheses',
            'rejectedForms'
        ));
    }
}