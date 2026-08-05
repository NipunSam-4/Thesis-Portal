<?php

namespace App\Http\Controllers\Adoaa;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class AdoaaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allTheses = Thesis::with(['student.user', 'student.department'])->get();

        return view('adoaa.dashboard', compact('user', 'allTheses'));
    }
}
