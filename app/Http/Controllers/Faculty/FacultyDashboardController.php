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
            'mainSupervisors',
            'coSupervisors',
            'externalSupervisors.externalSupervisorProfile',
            'pspcMembers',
            'theses.draftSynopsisCirculation.comments',
            'theses.pts1Form',
            'theses.pts2Form',
            'theses.pts2Extension',
            'theses.pts3Form',
            'theses.pts4Form',
            'theses.pts4Extension',
            'theses.pts5Form',
            'theses.pts6Form'
        ])
        ->get();

        // 2. Students where faculty is Co-Supervisor
        $coStudents = Student::whereHas('supervisors', function ($query) use ($user) {
            $query->where('users.id', $user->id)->where('supervisor_type', 'co');
        })
        ->orderBy('roll_number', 'asc')
        ->with([
            'user',
            'department',
            'supervisors',
            'mainSupervisors',
            'coSupervisors',
            'externalSupervisors.externalSupervisorProfile',
            'pspcMembers',
            'theses.draftSynopsisCirculation.comments',
            'theses.pts1Form',
            'theses.pts2Form',
            'theses.pts2Extension',
            'theses.pts3Form',
            'theses.pts4Form',
            'theses.pts4Extension',
            'theses.pts5Form',
            'theses.pts6Form'
        ])
        ->get();

        // 3. Students where faculty is PSPC Member
        $pspcStudents = Student::whereHas('pspcMembers', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->orderBy('roll_number', 'asc')
        ->with([
            'user',
            'department',
            'supervisors',
            'mainSupervisors',
            'coSupervisors',
            'externalSupervisors.externalSupervisorProfile',
            'pspcMembers',
            'theses.draftSynopsisCirculation.comments',
            'theses.pts1Form',
            'theses.pts2Form',
            'theses.pts2Extension',
            'theses.pts3Form',
            'theses.pts4Form',
            'theses.pts4Extension',
            'theses.pts5Form',
            'theses.pts6Form'
        ])
        ->get();

        return view('faculty.dashboard', compact('user', 'mainStudents', 'coStudents', 'pspcStudents'));
    }
}
