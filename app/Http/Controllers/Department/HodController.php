<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;

class HodController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->facultyProfile?->department_id;

        $departmentTheses = Thesis::whereHas('student', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->with(['student.user', 'student.supervisors'])->get();

        return view('dept_authorities.dashboard', compact('departmentTheses'));
    }
}
