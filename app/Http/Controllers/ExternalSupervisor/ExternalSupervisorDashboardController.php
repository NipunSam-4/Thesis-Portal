<?php

namespace App\Http\Controllers\ExternalSupervisor;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class ExternalSupervisorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch students supervised by this external supervisor with eager loaded relationships
        $allStudents = $user->externalSupervisedStudents()->with([
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
            'theses.pts5Form',
            'theses.pts6Form'
        ])->get();

        // Fetch only relevant departments where this supervisor has assigned students
        $departments = Department::whereIn('id', $allStudents->pluck('department_id')->filter()->unique())->orderBy('name')->get();

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

        return view('external_supervisor.dashboard', compact('user', 'phdStudents', 'msrStudents', 'departments'));
    }
}
