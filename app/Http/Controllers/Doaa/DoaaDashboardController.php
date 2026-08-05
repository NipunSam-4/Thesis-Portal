<?php

namespace App\Http\Controllers\Doaa;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class DoaaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allTheses = Thesis::with(['student.user', 'student.department'])->get();

        return view('doaa.dashboard', compact('user', 'allTheses'));
    }
}
