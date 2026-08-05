<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $supervisor = $user->supervisor()->with(['students', 'coSupervisedTheses.student'])->firstOrFail();

        return view('supervisor.dashboard', compact('supervisor'));
    }
}
