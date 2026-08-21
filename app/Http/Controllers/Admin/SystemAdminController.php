<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Thesis;
use Illuminate\Support\Facades\Hash;

class SystemAdminController extends Controller
{
    // 1. Load the Dashboard
    public function index()
    {
        $departments = Department::all();
        $userCount = User::count() + Admin::count();

        return view('admin.dashboard', compact('departments', 'userCount'));
    }

    // 2. Department Management
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:20|unique:departments',
            'is_active' => 'required|boolean',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Department created successfully!');
    }

    public function manageDepartments()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        return back()->with('success', 'Department updated successfully!');
    }

    public function toggleDepartmentStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Department $status successfully!");
    }

    // 3. Core Admins Management (Separate Admin Model)
    public function manageCoreAdmins()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $admins = Admin::with('department')->orderBy('name')->get();

        return view('admin.core_admins.index', compact('admins', 'departments'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'role' => 'required|string|in:system_admin,super_admin',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($request->role === 'super_admin' && !$request->department_id) {
            return back()->withErrors(['department_id' => 'A department must be selected for a Department Super Admin.'])->withInput();
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'admin_type' => $request->role,
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);

        return back()->with('success', $admin->name . ' registered successfully as ' . str_replace('_', ' ', $request->role) . '!');
    }

    public function updateUser(Request $request, Admin $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Admin details updated successfully!');
    }

    public function toggleUserStatus(Admin $user)
    {
        if (auth('admin')->id() === $user->id) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account!']);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Admin account $status successfully!");
    }

    // 4. Department Authorities Management
    public function manageDeptAuthorities()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $authorities = User::whereIn('role', ['hod', 'dpgc', 'section_officer', 'faculty'])
            ->with(['facultyProfile.department', 'deptAuthorityProfile.department'])
            ->orderBy('name')
            ->get();

        return view('admin.dept_authorities.index', compact('authorities', 'departments'));
    }

    public function storeDeptAuthority(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'role' => 'required|string|in:hod,dpgc,section_officer,faculty',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($request->role === 'hod') {
            $existingHod = User::where('role', 'hod')
                ->where(function($query) use ($request) {
                    $query->whereHas('deptAuthorityProfile', function($q) use ($request) {
                        $q->where('department_id', $request->department_id);
                    })->orWhereHas('facultyProfile', function($q) use ($request) {
                        $q->where('department_id', $request->department_id);
                    });
                })->exists();

            if ($existingHod) {
                return back()->withErrors(['role' => 'This department already has a registered HoD!']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => $request->role,
            'is_active' => true,
        ]);

        if (in_array($request->role, ['hod', 'dpgc'], true)) {
            $user->deptAuthorityProfile()->create([
                'department_id' => $request->department_id,
            ]);
        } else {
            $user->facultyProfile()->create([
                'department_id' => $request->department_id,
            ]);
        }

        return back()->with('success', $user->name . ' registered successfully as ' . strtoupper(str_replace('_', ' ', $request->role)) . '!');
    }

    // 5. Global Authorities Management (including AR Academic)
    public function manageGlobalAuthorities()
    {
        $authorities = User::whereIn('role', ['doaa', 'adoaa', 'senate_chairperson', 'ar'])
            ->orderBy('name')
            ->get();

        return view('admin.global_authorities.index', compact('authorities'));
    }

    public function storeGlobalAuthority(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'role' => 'required|string|in:doaa,adoaa,senate_chairperson,ar',
        ]);

        if (User::where('role', $request->role)->exists()) {
            return back()->withErrors(['role' => 'There is already an active user assigned to this position in the system!']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return back()->with('success', $user->name . ' registered successfully!');
    }

    // 6. PhD Students Management
    public function manageStudents()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $facultyUsers = User::where('role', 'faculty')->with('facultyProfile.department')->orderBy('name')->get();

        $students = User::where('role', 'phd_student')
            ->with(['student.department', 'student.supervisors', 'student.pspcMembers'])
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students', 'departments', 'facultyUsers'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'roll_number' => 'required|string|max:50|unique:students',
            'department_id' => 'required|exists:departments,id',
            'admission_category' => 'nullable|string|max:100',
            'course_credits_earned' => 'nullable|numeric|min:0',
            'thesis_title' => 'nullable|string|max:255',
            'main_supervisor_id' => 'required|exists:users,id',
            'co_supervisor_ids' => 'nullable|array',
            'co_supervisor_ids.*' => 'exists:users,id',
            'pspc_member_ids' => 'nullable|array',
            'pspc_member_ids.*' => 'exists:users,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'phd_student',
            'is_active' => true,
        ]);

        $student = $user->student()->create([
            'roll_number' => strtoupper($request->roll_number),
            'department_id' => $request->department_id,
            'admission_category' => $request->admission_category ?? 'TA',
            'course_credits_earned' => $request->course_credits_earned ?? 0,
        ]);

        $thesis = $student->theses()->create([
            'title' => $request->thesis_title ?? 'PhD Thesis Research',
            'status' => 'in_progress',
        ]);

        // Attach Main Supervisor to Student
        $student->supervisors()->attach($request->main_supervisor_id, ['supervisor_type' => 'main']);

        // Attach Co-Supervisors to Student
        if (!empty($request->co_supervisor_ids)) {
            foreach ($request->co_supervisor_ids as $coSupId) {
                $student->supervisors()->attach($coSupId, ['supervisor_type' => 'co']);
            }
        }

        // Attach PSPC Members to Student
        if (!empty($request->pspc_member_ids)) {
            $student->pspcMembers()->attach($request->pspc_member_ids);
        }

        return back()->with('success', 'Student ' . $user->name . ' registered successfully with thesis record!');
    }
}