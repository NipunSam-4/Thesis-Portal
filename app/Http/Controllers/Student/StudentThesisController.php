<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class StudentThesisController extends Controller
{
    // Store a newly created Thesis title for the authenticated student.
    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $isReinitiation = $student->canReinitiateThesis();

        // Check if student is allowed to initiate or re-initiate a thesis
        if (!$student->canInitiateThesis() && !$isReinitiation) {
            if ($student->hasActiveThesis()) {
                return redirect()->route('student.dashboard')->with('warning', 'You cannot re-initiate a new thesis while your current thesis submission is in progress.');
            }
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('warning', 'Your thesis has already been completed and approved.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'You cannot initiate a thesis at this time.');
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
            ? 'PhD Thesis submission re-initiated successfully!'
            : 'PhD Thesis title registered successfully!';

        return redirect()->route('student.dashboard')->with('success', $msg);
    }
}
