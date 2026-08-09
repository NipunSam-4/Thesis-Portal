<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Show the form for creating a PTS-2 submission.
     */
    public function createPts2(Request $request)
    {
        $user = $request->user();
        $student = $user->student()->with('theses.submissions')->firstOrFail();

        $latestThesis = $student->theses->firstWhere('status', 'in_progress');
        $submissions = $latestThesis ? $latestThesis->submissions : collect();
        $pts1Approved = $submissions->where('form_type', 'PTS-1')->whereIn('status', ['Accepted', 'Approved'])->isNotEmpty();
        $pts3Approved = $submissions->where('form_type', 'PTS-3')->whereIn('status', ['Accepted', 'Approved'])->isNotEmpty();
        $pts2Submitted = $submissions->where('form_type', 'PTS-2')->isNotEmpty();

        if (!$latestThesis || !$pts1Approved || !$pts3Approved || $pts2Submitted) {
            return redirect()->route('student.dashboard')->with('error', 'You are not eligible to submit PTS-2 at this time.');
        }

        return view('student.thesis.submit', compact('student', 'latestThesis'));
    }
}
