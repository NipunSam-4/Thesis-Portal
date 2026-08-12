<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        $student = $user->student()->with([
            'department', 
            'supervisors',
            'pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])->firstOrFail();

        $activeThesis = $student->theses->firstWhere('status', 'in_progress');
        $latestThesis = $activeThesis;
        $pts1Form = $activeThesis ? $activeThesis->pts1Form : null;
        $pts2Form = $activeThesis ? $activeThesis->pts2Form : null;

        // PTS-2 is unlocked when PTS-1 is approved (status === 'accepted')
        $pts1Approved = $pts1Form && $pts1Form->status === 'accepted';

        // Fetch rejected forms
        $rejectedForms = collect();
        if ($activeThesis) {
            $rejectedPts1 = $activeThesis->pts1Forms()->where('status', 'rejected')->get();
            $rejectedPts2 = $activeThesis->pts2Forms()->where('status', 'rejected')->get();
            $rejectedForms = $rejectedPts1->concat($rejectedPts2)->sortByDesc('created_at');
        }

        return view('student.dashboard', compact('student', 'activeThesis', 'latestThesis', 'pts1Form', 'pts2Form', 'pts1Approved', 'rejectedForms'));
    }
}