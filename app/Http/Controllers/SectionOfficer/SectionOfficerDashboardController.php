<?php

namespace App\Http\Controllers\SectionOfficer;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class SectionOfficerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allTheses = Thesis::with(['student.user', 'student.department'])->get();

        return view('section_officer.dashboard', compact('user', 'allTheses'));
    }
}
