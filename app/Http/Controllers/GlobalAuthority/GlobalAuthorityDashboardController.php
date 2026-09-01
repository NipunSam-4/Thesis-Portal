<?php

namespace App\Http\Controllers\GlobalAuthority;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;

class GlobalAuthorityDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch all active departments for filtering
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        // Fetch all institute students with eager loaded relationships
        $allStudents = Student::with([
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
        ])->get();

        // Sort hierarchy: Tier 1 (Action Required) -> Tier 2 (In-Progress) -> Tier 3 (Reverted/Rejected/Approved) -> Tier 4 (Pending), tie-break by roll_number
        $sortCallback = function ($a, $b) use ($user) {
            $scoreA = $a->getAuthoritySortScore($user);
            $scoreB = $b->getAuthoritySortScore($user);
            if ($scoreA !== $scoreB) {
                return $scoreA <=> $scoreB;
            }
            return strnatcasecmp($a->roll_number ?? '', $b->roll_number ?? '');
        };

        $phdStudents = $allStudents->filter(fn($s) => $s->isPhd())->sort($sortCallback)->values();
        $msrStudents = $allStudents->filter(fn($s) => $s->isMsr())->sort($sortCallback)->values();

        return view('global_authorities.dashboard', compact('user', 'phdStudents', 'msrStudents', 'departments'));
    }
}
