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

        // Check if thesis already exists
        if ($student->theses()->exists()) {
            return redirect()->route('student.dashboard')->with('info', 'Thesis title has already been registered.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Thesis::create([
            'student_id' => $student->id,
            'title' => $validated['title'],
            'current_status' => 'Thesis Registered',
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Ph.D. Thesis title registered successfully. You can now submit your PTS-1 Form.');
    }
}
