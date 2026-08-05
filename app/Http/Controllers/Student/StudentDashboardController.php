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
            'theses.supervisors',
            'theses.pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])->firstOrFail();

        $latestThesis = $student->theses->last();
        $pts1Form = $latestThesis ? $latestThesis->pts1Form : null;
        $pts2Form = $latestThesis ? $latestThesis->pts2Form : null;

        // PTS-2 is unlocked when PTS-1 is approved (status === 'accepted')
        $pts1Approved = $pts1Form && $pts1Form->status === 'accepted';

        return view('student.dashboard', compact('student', 'latestThesis', 'pts1Form', 'pts2Form', 'pts1Approved'));
    }
}