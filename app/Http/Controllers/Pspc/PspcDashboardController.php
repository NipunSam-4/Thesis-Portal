<?php

namespace App\Http\Controllers\Pspc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PspcDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pspcMember = $user->pspcMember; // Assuming relationship exists

        // Fetch pending synopsis reviews for PSPC
        $pendingSynopsis = collect(); // Placeholder for actual query

        return view('pspc.dashboard', compact('pspcMember', 'pendingSynopsis'));
    }
}
