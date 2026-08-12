<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class FacultyDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Students where faculty is Main Supervisor
        $mainStudents = Student::whereHas('supervisors', function ($query) use ($user) {
            $query->where('users.id', $user->id)->where('supervisor_type', 'main');
        })
        ->orderBy('roll_number', 'asc')
        ->with([
            'user',
            'department',
            'supervisors',
            'pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])
        ->get();

        // 2. Students where faculty is Co-Supervisor
        $coStudents = Student::whereHas('supervisors', function ($query) use ($user) {
            $query->where('users.id', $user->id)->where('supervisor_type', 'co');
        })
        ->orWhereHas('theses.pts1Form', function ($query) use ($user) {
            $query->where('co_supervisor_1_id', $user->id)
                ->orWhere('co_supervisor_2_id', $user->id)
                ->orWhere('co_supervisor_3_id', $user->id);
        })
        ->orderBy('roll_number', 'asc')
        ->with([
            'user',
            'department',
            'supervisors',
            'pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])
        ->get();

        // 3. Students where faculty is PSPC Member
        $pspcStudents = Student::whereHas('pspcMembers', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->orWhereHas('theses.pts1Form', function ($query) use ($user) {
            $query->where('pspc_member_1_id', $user->id)
                ->orWhere('pspc_member_2_id', $user->id)
                ->orWhere('pspc_member_3_id', $user->id);
        })
        ->orderBy('roll_number', 'asc')
        ->with([
            'user',
            'department',
            'supervisors',
            'pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])
        ->get();

        return view('faculty.dashboard', compact('user', 'mainStudents', 'coStudents', 'pspcStudents'));
    }
}
