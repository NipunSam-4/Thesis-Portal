<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThesisController extends Controller
{
    /**
     * Single unified stage mapping for all forms across the entire portal.
     * Configure all stage label displays here in one single place.
     */
    public static array $stageMappings = [
        'main_supervisor' => 'Main Supervisor',
        'co_supervisors'  => 'Co-Supervisors',
        'pspc_members'    => 'PSPC Members',
        'dpgc'            => 'DPGC',
        'hod'             => 'HOD',
        'section_officer' => 'Section Officer',
        'adoaa'           => 'ADoAA',
        'doaa'            => 'DOAA',
        'completed'       => 'Approved',
        'rejected'        => 'Rejected',
        'reverted'        => 'Reverted',
    ];

    /**
     * Get the mapped stage label for a given stage code.
     *
     * @param string|null $stage e.g. 'main_supervisor', 'dpgc', 'adoaa', etc.
     * @return string
     */
    public static function getStageLabel(?string $stage): string
    {
        if (!$stage) {
            return 'N/A';
        }

        return self::$stageMappings[$stage] ?? ucwords(str_replace('_', ' ', $stage));
    }
}
