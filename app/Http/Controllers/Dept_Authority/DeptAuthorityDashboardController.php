<?php

namespace App\Http\Controllers\Dept_Authority;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class DeptAuthorityDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->facultyProfile?->department_id;

        // Fetch all students belonging to the department with their theses and PTS forms
        $departmentStudents = Student::where('department_id', $departmentId)
            ->with([
                'user',
                'department',
                'supervisors',
                'pspcMembers',
                'theses.draftSynopsisCirculation.comments',
                'theses.pts1Form',
                'theses.pts2Form',
                'theses.pts2Extension'
            ])
            ->get();

        $phdStudents = $departmentStudents->filter(fn($s) => $s->isPhd());
        $msrStudents = $departmentStudents->filter(fn($s) => $s->isMsr());

        return view('dept_authorities.dashboard', compact('user', 'phdStudents', 'msrStudents'));
    }
}
