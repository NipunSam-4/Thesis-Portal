<?php

namespace App\Http\Controllers\Dpgc;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class DpgcDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->facultyProfile?->department_id;

        // Fetch all students belonging to DPGC's department with their theses and PTS forms
        $departmentStudents = Student::where('department_id', $departmentId)
            ->with([
                'user',
                'department',
                'theses.supervisors',
                'theses.pts1Form',
                'theses.pts2Form'
            ])
            ->get();

        return view('dept_authorities.dashboard', compact('user', 'departmentStudents'));
    }
}
