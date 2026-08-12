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

        // Fetch all institute students with eager loaded relationships
        $allStudents = Student::with([
            'user',
            'department',
            'supervisors',
            'pspcMembers',
            'theses.pts1Form',
            'theses.pts2Form'
        ])->get();

        $phdStudents = $allStudents->filter(fn($s) => $s->isPhd());
        $msrStudents = $allStudents->filter(fn($s) => $s->isMsr());

        return view('global_authorities.dashboard', compact('user', 'phdStudents', 'msrStudents'));
    }
}
