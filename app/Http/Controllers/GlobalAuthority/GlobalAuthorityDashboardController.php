<?php

namespace App\Http\Controllers\GlobalAuthority;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class GlobalAuthorityDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch all institute students with eager loaded theses and PTS forms
        $allStudents = Student::with([
            'user',
            'department',
            'supervisors',
            'theses.pts1Form',
            'theses.pts2Form'
        ])
        ->get();

        return view('global_authorities.dashboard', compact('user', 'allStudents'));
    }
}
