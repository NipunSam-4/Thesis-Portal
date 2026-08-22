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
            'pspcMembers',
            'theses.draftSynopsisCirculation',
            'theses.pts1Form',
            'theses.pts2Form',
            'theses.pts2Extension'
        ])->firstOrFail();

        $activeThesis = $student->theses->firstWhere('status', 'in_progress') ?? $student->theses->last();
        $latestThesis = $activeThesis;
        $draftSynopsis = $activeThesis ? $activeThesis->draftSynopsisCirculation : null;
        $pts1Form = $activeThesis ? $activeThesis->pts1Form : null;
        $pts2Form = $activeThesis ? $activeThesis->pts2Form : null;
        $pts2Extension = $activeThesis ? $activeThesis->pts2Extension : null;

        // PTS-2 is unlocked when PTS-1 is approved (status === 'approved')
        $pts1Approved = $pts1Form && $pts1Form->status === 'approved';

        // Fetch rejected forms
        $rejectedForms = collect();
        if ($activeThesis) {
            $rejectedPts1 = $activeThesis->pts1Forms()->where('status', 'rejected')->get();
            $rejectedPts2 = $activeThesis->pts2Forms()->where('status', 'rejected')->get();
            $rejectedPts2Ext = $activeThesis->pts2Extensions()->where('status', 'rejected')->get();
            $rejectedForms = $rejectedPts1->concat($rejectedPts2)->concat($rejectedPts2Ext)->sortByDesc('created_at');
        }

        return view('student.dashboard', compact('student', 'activeThesis', 'latestThesis', 'draftSynopsis', 'pts1Form', 'pts2Form', 'pts2Extension', 'pts1Approved', 'rejectedForms'));
    }
}