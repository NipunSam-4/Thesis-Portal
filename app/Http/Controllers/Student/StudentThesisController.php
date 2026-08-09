<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class StudentThesisController extends Controller
{
    /**
     * Store a newly created Thesis title for the authenticated student.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $isReinitiation = $student->theses()->exists();

        // Check if an active thesis already exists in progress
        if ($student->hasActiveThesis()) {
            return redirect()->route('student.dashboard')->with('warning', 'You cannot re-initiate a new thesis while your current thesis submission is in progress.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Thesis::create([
            'student_id' => $student->id,
            'title' => $validated['title'],
            'status' => 'in_progress',
        ]);

        $msg = $isReinitiation 
            ? 'PhD Thesis submission re-initiated successfully! You can now submit your new PTS-1 Form.'
            : 'PhD Thesis title registered successfully! You can now submit your PTS-1 Form.';

        return redirect()->route('student.dashboard')->with('success', $msg);
    }
}
