<?php

namespace App\Http\Controllers\ActingApprovalAuthority;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;

class ActingApprovalAuthorityDashboardController extends Controller
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

        $hasActingForm = function ($student) use ($user) {
            return $student->theses->contains(function ($thesis) use ($user) {
                return ($thesis->pts1Form && $thesis->pts1Form->acting_doaa_email === $user->email)
                    || ($thesis->pts2Form && $thesis->pts2Form->acting_doaa_email === $user->email)
                    || ($thesis->pts2Extension && $thesis->pts2Extension->acting_doaa_email === $user->email)
                    || ($thesis->pts3Form && $thesis->pts3Form->acting_doaa_email === $user->email)
                    || ($thesis->pts4Form && $thesis->pts4Form->acting_doaa_email === $user->email)
                    || ($thesis->pts4Extension && $thesis->pts4Extension->acting_doaa_email === $user->email)
                    || ($thesis->pts5Form && $thesis->pts5Form->acting_doaa_email === $user->email)
                    || ($thesis->pts6Form && $thesis->pts6Form->acting_doaa_email === $user->email);
            });
        };

        $hasVestedForm = function ($student) use ($user) {
            return $student->theses->contains(function ($thesis) use ($user) {
                return ($thesis->pts1Form && $thesis->pts1Form->vested_doaa_email === $user->email)
                    || ($thesis->pts2Form && $thesis->pts2Form->vested_doaa_email === $user->email)
                    || ($thesis->pts2Extension && $thesis->pts2Extension->vested_doaa_email === $user->email)
                    || ($thesis->pts3Form && $thesis->pts3Form->vested_doaa_email === $user->email)
                    || ($thesis->pts4Form && $thesis->pts4Form->vested_doaa_email === $user->email)
                    || ($thesis->pts4Extension && $thesis->pts4Extension->vested_doaa_email === $user->email)
                    || ($thesis->pts5Form && $thesis->pts5Form->vested_doaa_email === $user->email)
                    || ($thesis->pts6Form && $thesis->pts6Form->vested_doaa_email === $user->email);
            });
        };

        $actingPhdStudents = $allStudents->filter(fn($s) => $s->isPhd() && $hasActingForm($s))->sort($sortCallback)->values();
        $actingMsrStudents = $allStudents->filter(fn($s) => $s->isMsr() && $hasActingForm($s))->sort($sortCallback)->values();

        $vestedPhdStudents = $allStudents->filter(fn($s) => $s->isPhd() && $hasVestedForm($s))->sort($sortCallback)->values();
        $vestedMsrStudents = $allStudents->filter(fn($s) => $s->isMsr() && $hasVestedForm($s))->sort($sortCallback)->values();

        return view('acting_approval_authority.dashboard', compact(
            'user',
            'actingPhdStudents',
            'actingMsrStudents',
            'vestedPhdStudents',
            'vestedMsrStudents',
            'departments'
        ));
    }
}
