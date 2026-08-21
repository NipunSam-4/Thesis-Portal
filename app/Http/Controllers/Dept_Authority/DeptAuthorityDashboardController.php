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
        $departmentId = $user->deptAuthorityProfile?->department_id;

        // Fetch all students belonging to the department with their theses and PTS forms
        $departmentStudents = Student::where('department_id', $departmentId)
            ->with([
                'user',
                'department',
                'supervisors',
                'pspcMembers',
                'theses.pts1Form',
                'theses.pts2Form',
                'theses.pts2Extension'
            ])
            ->get();

        // Sort hierarchy: Tier 1 (Action Required) -> Tier 2 (In-Progress) -> Tier 3 (Reverted/Rejected/Approved) -> Tier 4 (Pending), tie-break by roll_number
        $sortCallback = function ($a, $b) use ($user) {
            $scoreA = $a->getAuthoritySortScore($user);
            $scoreB = $b->getAuthoritySortScore($user);
            if ($scoreA !== $scoreB) {
                return $scoreA <=> $scoreB;
            }
            return strnatcasecmp($a->roll_number ?? '', $b->roll_number ?? '');
        };

        $phdStudents = $departmentStudents->filter(fn($s) => $s->isPhd())->sort($sortCallback)->values();
        $msrStudents = $departmentStudents->filter(fn($s) => $s->isMsr())->sort($sortCallback)->values();

        return view('dept_authorities.dashboard', compact('user', 'phdStudents', 'msrStudents'));
    }
}
