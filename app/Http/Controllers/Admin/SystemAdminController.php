<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Models\Admin;
use App\Models\Student;
use App\Models\FacultyProfile;
use App\Models\Thesis;
use App\Models\ActingDoaa;
use App\Models\VestedDoaa;
use App\Services\AdminActivityLogger;
use Illuminate\Support\Facades\Hash;

class SystemAdminController extends Controller
{
    // 1. Dashboard
    public function index()
    {
        $admin = auth('admin')->user();

        if ($admin && $admin->isSuperAdmin()) {
            $department = Department::find($admin->department_id);

            return view('admin.dashboard', compact('admin', 'department'));
        }

        return view('admin.dashboard', compact('admin'));
    }

    // 2. Department Management (System Admin Only)
    public function manageDepartments(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Department management is restricted to System Administrators.']);
        }

        $query = Department::query();

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
            });
        }

        $departments = $query->orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Department creation is restricted to System Administrators.']);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:20|unique:departments',
            'is_active' => 'required|boolean',
        ]);

        $dept = Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'is_active' => $request->is_active,
        ]);

        AdminActivityLogger::log("Created Department", "Department #{$dept->id} ({$dept->code})", [
            'name' => $dept->name,
            'code' => $dept->code,
            'is_active' => $dept->is_active
        ]);

        return back()->with('success', 'Department created successfully!');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Department updating is restricted to System Administrators.']);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
        ]);

        $oldData = ['name' => $department->name, 'code' => $department->code];

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        AdminActivityLogger::log("Updated Department", "Department #{$department->id} ({$department->code})", [
            'name' => ['old' => $oldData['name'], 'new' => $department->name],
            'code' => ['old' => $oldData['code'], 'new' => $department->code]
        ]);

        return back()->with('success', 'Department updated successfully!');
    }

    public function toggleDepartmentStatus(Department $department)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Department status modification is restricted to System Administrators.']);
        }

        $oldStatus = $department->is_active;

        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'activated' : 'deactivated';

        AdminActivityLogger::log("Toggled Department Status ({$status})", "Department #{$department->id} ({$department->code})", [
            'is_active' => ['old' => $oldStatus, 'new' => $department->is_active]
        ]);

        return back()->with('success', "Department $status successfully!");
    }

    // 3. Core Admins Management (System Admin Only)
    public function manageCoreAdmins(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Admin management is restricted to System Administrators.']);
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $query = Admin::with('department');

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('department_id') && $request->department_id !== 'all') {
            $query->where('department_id', $request->department_id);
        }

        $admins = $query->orderBy('name')->get();

        return view('admin.core_admins.index', compact('admins', 'departments'));
    }

    public function storeAdmin(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Registering admin accounts is restricted to System Administrators.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:super_admin',
            'department_id' => 'required|exists:departments,id',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_type' => 'super_admin',
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);

        AdminActivityLogger::log("Created Super Admin", "Admin #{$admin->id} ({$admin->email})", [
            'name' => $admin->name,
            'email' => $admin->email,
            'department_id' => $admin->department_id
        ]);

        return back()->with('success', $admin->name . ' registered successfully as Department Super Admin!');
    }

    public function updateAdmin(Request $request, Admin $admin)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Updating admin accounts is restricted to System Administrators.']);
        }

        if ($admin->isSystemAdmin()) {
            return back()->withErrors(['error' => 'System Administrator accounts cannot be edited via UI.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
        ]);

        $oldData = ['name' => $admin->name, 'email' => $admin->email];

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        AdminActivityLogger::log("Updated Super Admin", "Admin #{$admin->id} ({$admin->email})", [
            'name' => ['old' => $oldData['name'], 'new' => $admin->name],
            'email' => ['old' => $oldData['email'], 'new' => $admin->email]
        ]);

        return back()->with('success', 'Department Super Admin details updated successfully!');
    }

    public function toggleAdminStatus(Admin $admin)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Deactivating admin accounts is restricted to System Administrators.']);
        }

        if ($admin->isSystemAdmin()) {
            return back()->withErrors(['error' => 'System Administrator accounts cannot be modified via UI.']);
        }

        if (auth('admin')->id() === $admin->id) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account!']);
        }

        $oldStatus = $admin->is_active;

        $admin->update([
            'is_active' => !$admin->is_active
        ]);

        $status = $admin->is_active ? 'activated' : 'deactivated';

        AdminActivityLogger::log("Toggled Super Admin Status ({$status})", "Admin #{$admin->id} ({$admin->email})", [
            'is_active' => ['old' => $oldStatus, 'new' => $admin->is_active]
        ]);

        return back()->with('success', "Admin account $status successfully!");
    }

    // 4. Department Authorities Management
    public function manageDeptAuthorities(Request $request)
    {
        $admin = auth('admin')->user();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $query = User::whereIn('role', ['hod', 'dpgc']);

        if ($admin->isSuperAdmin()) {
            $departments = Department::where('id', $admin->department_id)->get();
            $query->where(function($q) use ($admin) {
                $q->whereHas('deptAuthorityProfile', fn($sq) => $sq->where('department_id', $admin->department_id));
            });
        } elseif ($request->filled('department_id') && $request->department_id !== 'all') {
            $query->where(function($q) use ($request) {
                $q->whereHas('deptAuthorityProfile', fn($sq) => $sq->where('department_id', $request->department_id));
            });
        }

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $authorities = $query->with(['deptAuthorityProfile.department'])
            ->orderBy('name')
            ->get();

        return view('admin.dept_authorities.index', compact('authorities', 'departments'));
    }

    public function storeDeptAuthority(Request $request)
    {
        $admin = auth('admin')->user();

        if ($admin->isSuperAdmin()) {
            $request->merge(['department_id' => $admin->department_id]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'role' => 'required|string|in:hod,dpgc',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($request->role === 'hod') {
            $existingHod = User::where('role', 'hod')
                ->whereHas('deptAuthorityProfile', function($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                })->exists();

            if ($existingHod) {
                return back()->withErrors(['role' => 'An active Head of Department already exists for this department!']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => $request->role,
            'is_active' => true,
        ]);

        $user->deptAuthorityProfile()->create([
            'department_id' => $request->department_id,
        ]);

        AdminActivityLogger::log("Created Department Authority", "User #{$user->id} ({$user->email})", [
            'name' => $user->name,
            'role' => $user->role,
            'department_id' => $request->department_id
        ]);

        return back()->with('success', $user->name . ' registered successfully as ' . strtoupper(str_replace('_', ' ', $request->role)) . '!');
    }

    // 5. Global Authorities Management (System Admin Only)
    public function manageGlobalAuthorities(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Global Authorities management is restricted to System Administrators.']);
        }

        $query = User::whereIn('role', ['doaa', 'adoaa', 'senate_chairperson', 'academic_office', 'ar', 'dr']);

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $authorities = $query->orderBy('name')->get();

        return view('admin.global_authorities.index', compact('authorities'));
    }

    public function storeGlobalAuthority(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Registering global authorities is restricted to System Administrators.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'role' => 'required|string|in:doaa,adoaa,senate_chairperson,academic_office,ar,dr',
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

        AdminActivityLogger::log("Created Global Authority", "User #{$user->id} ({$user->email})", [
            'name' => $user->name,
            'role' => $user->role
        ]);

        return back()->with('success', $user->name . ' registered successfully!');
    }

    // 6. Faculties & External Supervisors Management
    public function manageFaculties(Request $request)
    {
        $admin = auth('admin')->user();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        if ($admin->isSuperAdmin()) {
            $departments = Department::where('id', $admin->department_id)->get();
            $query = User::where('role', 'faculty')
                ->whereHas('facultyProfile', fn($sq) => $sq->where('department_id', $admin->department_id));
        } else {
            $query = User::whereIn('role', ['faculty', 'external_supervisor']);
            if ($request->filled('department_id') && $request->department_id !== 'all') {
                $query->where(function($q) use ($request) {
                    $q->whereHas('facultyProfile', fn($sq) => $sq->where('department_id', $request->department_id))
                      ->orWhere('role', 'external_supervisor');
                });
            }
        }

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $faculties = $query->with(['facultyProfile.department', 'externalSupervisorProfile'])
            ->orderBy('name')
            ->get();

        return view('admin.faculties.index', compact('faculties', 'departments'));
    }

    // 7. External Examiners Management
    public function manageExaminers(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: External Examiners management is restricted to System Administrators.']);
        }

        $query = User::where('role', 'external_examiner');

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $examiners = $query->orderBy('name')->get();

        return view('admin.examiners.index', compact('examiners'));
    }

    // 8. Students Management (System Admin & Super Admin, PhD vs MSR tabs)
    public function manageStudents(Request $request)
    {
        $admin = auth('admin')->user();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $query = User::where('role', 'student');

        if ($admin->isSuperAdmin()) {
            $departments = Department::where('id', $admin->department_id)->get();
            $query->whereHas('student', fn($q) => $q->where('department_id', $admin->department_id));
        } elseif ($request->filled('department_id') && $request->department_id !== 'all') {
            $query->whereHas('student', fn($q) => $q->where('department_id', $request->department_id));
        }

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('student', fn($sq) => $sq->whereRaw('LOWER(roll_number) LIKE ?', ["%{$search}%"]));
            });
        }

        $students = $query->with([
                'student.department',
                'student.supervisors',
                'student.mainSupervisors',
                'student.coSupervisors',
                'student.externalSupervisors.externalSupervisorProfile',
                'student.pspcMembers',
                'student.theses.pts1Form',
                'student.theses.pts2Form',
                'student.theses.pts2Extension',
                'student.theses.pts3Form',
                'student.theses.pts4Form',
                'student.theses.pts4Extension',
                'student.theses.pts5Form',
                'student.theses.pts6Form',
                'student.theses.draftSynopsisCirculation'
            ])
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students', 'departments', 'admin'));
    }

    // 9. Name-Only Update for Authorities, Faculty, Examiners, and Students
    public function updateAuthorityName(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldName = $user->name;

        $user->update([
            'name' => $request->name,
        ]);

        AdminActivityLogger::log("Updated User Name", "User #{$user->id} ({$user->email})", [
            'name' => ['old' => $oldName, 'new' => $user->name]
        ]);

        return back()->with('success', "Full name for {$user->email} updated successfully!");
    }

    // 10. General User Status Toggle (Students, Authorities, Faculty, Examiners)
    public function toggleUserStatus(User $user)
    {
        $newStatus = !$user->is_active;

        // Deactivation Guardrails
        if ($user->is_active && !$newStatus) {
            // 1. Main Supervisor Guardrail
            $isMainSupForActiveStudent = \DB::table('student_supervisor')
                ->where('faculty_user_id', $user->id)
                ->where('supervisor_type', 'main')
                ->whereExists(function ($q) {
                    $q->select(\DB::raw(1))
                        ->from('students')
                        ->join('users', 'users.id', '=', 'students.user_id')
                        ->whereColumn('students.id', 'student_supervisor.student_id')
                        ->where('users.is_active', true);
                })
                ->exists();

            if ($isMainSupForActiveStudent) {
                return back()->withErrors(['error' => "Cannot deactivate {$user->name}: This faculty member is currently assigned as a Main Supervisor for an active student. Please reassign a new Main Supervisor to the student first before deactivating."]);
            }

            // 2. Department HOD & DPGC Guardrail
            if (in_array($user->role, ['hod', 'dpgc'], true)) {
                $deptId = $user->deptAuthorityProfile?->department_id;
                if ($deptId) {
                    $activeCount = User::where('role', $user->role)
                        ->where('is_active', true)
                        ->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $deptId))
                        ->count();

                    if ($activeCount <= 1) {
                        $roleLabel = $user->role === 'hod' ? 'HOD' : 'DPGC Convener';
                        $deptCode = $user->deptAuthorityProfile?->department?->code ?? 'department';
                        return back()->withErrors(['error' => "Cannot deactivate {$user->name}: At least one active {$roleLabel} must remain for {$deptCode}."]);
                    }
                }
            }

            // 3. Global Authorities Guardrail
            if (in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'academic_office', 'ar', 'dr'], true)) {
                $activeCount = User::where('role', $user->role)->where('is_active', true)->count();
                if ($activeCount <= 1) {
                    $roleLabel = strtoupper(str_replace('_', ' ', $user->role));
                    return back()->withErrors(['error' => "Cannot deactivate {$user->name}: At least one active user must exist for global role {$roleLabel}."]);
                }
            }
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => $newStatus]);

        $statusLabel = $newStatus ? 'activated' : 'deactivated';

        AdminActivityLogger::log("Toggled User Status ({$statusLabel})", "User #{$user->id} ({$user->email})", [
            'is_active' => ['old' => $oldStatus, 'new' => $newStatus]
        ]);

        return back()->with('success', "Account for {$user->name} {$statusLabel} successfully!");
    }

    // 11. DOAA Pool & Delegation Management (Three Sections: Pool, Acting DOAA, Vested DOAA)
    public function manageDoaaDelegation(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: DOAA Delegation management is restricted to System Administrators.']);
        }

        $query = User::where('role', 'acting_approval_authority');

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $doaaPoolUsers = $query->orderBy('name')->get();

        $activeActingUserIds = ActingDoaa::where('is_acting_doaa', true)->pluck('user_id')->toArray();
        $vestedUserId = VestedDoaa::where('is_active', true)->value('user_id');
        $vestedUser = $vestedUserId ? User::find($vestedUserId) : null;

        return view('admin.doaa_delegation.index', compact('doaaPoolUsers', 'activeActingUserIds', 'vestedUserId', 'vestedUser'));
    }

    public function storeActingDoaaUser(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Adding acting authority users is restricted to System Administrators.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'acting_approval_authority',
            'is_active' => true,
        ]);

        ActingDoaa::firstOrCreate(
            ['user_id' => $user->id],
            ['is_acting_doaa' => true]
        );

        AdminActivityLogger::log("Added User to DOAA Pool", "User #{$user->id} ({$user->email})", [
            'name' => $user->name,
            'role' => $user->role
        ]);

        return back()->with('success', $user->name . ' (' . $user->email . ') assigned to DOAA Pool as Acting Approval Authority!');
    }

    public function saveDoaaPoolMembers(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Updating pool members is restricted to System Administrators.']);
        }

        $activeUserIds = $request->input('active_user_ids', []);

        $doaaPoolUsers = User::where('role', 'acting_approval_authority')->get();

        foreach ($doaaPoolUsers as $user) {
            $shouldBeActive = in_array((string)$user->id, array_map('strval', $activeUserIds), true);
            if ($user->is_active !== $shouldBeActive) {
                $old = $user->is_active;
                $user->update(['is_active' => $shouldBeActive]);
                AdminActivityLogger::log("Updated DOAA Pool Member Status", "User #{$user->id} ({$user->email})", [
                    'is_active' => ['old' => $old, 'new' => $shouldBeActive]
                ]);
            }
        }

        return back()->with('success', 'DOAA Pool members selection updated successfully!');
    }

    public function saveActingDoaaVisibility(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Updating Acting DOAA visibility is restricted to System Administrators.']);
        }

        $actingUserIds = $request->input('acting_user_ids', []);

        $doaaPoolUsers = User::where('role', 'acting_approval_authority')->get();

        foreach ($doaaPoolUsers as $user) {
            $isVisible = in_array((string)$user->id, array_map('strval', $actingUserIds), true);
            $actingDoaa = ActingDoaa::firstOrNew(['user_id' => $user->id]);
            $oldVis = $actingDoaa->is_acting_doaa;
            if ($oldVis !== $isVisible) {
                $actingDoaa->is_acting_doaa = $isVisible;
                $actingDoaa->save();
                AdminActivityLogger::log("Updated Acting DOAA Dropdown Visibility", "User #{$user->id} ({$user->email})", [
                    'is_acting_doaa' => ['old' => $oldVis, 'new' => $isVisible]
                ]);
            }
        }

        return back()->with('success', 'Acting DOAA dropdown selections saved successfully for Verifying Officers!');
    }

    public function updateVestedDoaa(Request $request)
    {
        if (auth('admin')->user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Access denied: Managing Vested DOAA is restricted to System Administrators.']);
        }

        $request->validate([
            'vested_user_id' => 'required|string',
        ]);

        if ($request->vested_user_id === 'none') {
            VestedDoaa::query()->update(['is_active' => false]);
            $this->updateInformedVestedDoaaEmail(null);
            AdminActivityLogger::log("Removed Vested DOAA", "None", ['vested_doaa_email' => null]);
            return back()->with('success', 'Vested DOAA removed! All in-progress forms now have Vested DOAA set to null.');
        }

        $user = User::findOrFail($request->vested_user_id);

        if ($user->role !== 'acting_approval_authority') {
            return back()->withErrors(['error' => 'Selected user must have the Acting Approval Authority role in the DOAA Pool.']);
        }

        VestedDoaa::query()->update(['is_active' => false]);
        $vested = VestedDoaa::firstOrNew(['user_id' => $user->id]);
        $vested->is_active = true;
        $vested->save();

        $this->updateInformedVestedDoaaEmail($user->email);

        AdminActivityLogger::log("Updated Vested DOAA", "User #{$user->id} ({$user->email})", [
            'vested_doaa_email' => $user->email
        ]);

        return back()->with('success', "Designated {$user->name} ({$user->email}) as active Vested DOAA. Auto-updated all in-progress forms with this email!");
    }

    private function updateInformedVestedDoaaEmail(?string $email)
    {
        if (class_exists(\App\Models\Pts1Form::class)) {
            \App\Models\Pts1Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts2Form::class)) {
            \App\Models\Pts2Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts2Extension::class)) {
            \App\Models\Pts2Extension::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts3Form::class)) {
            \App\Models\Pts3Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts4Form::class)) {
            \App\Models\Pts4Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts4Extension::class)) {
            \App\Models\Pts4Extension::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts5Form::class)) {
            \App\Models\Pts5Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
        if (class_exists(\App\Models\Pts6Form::class)) {
            \App\Models\Pts6Form::where('status', 'in_progress')->update(['vested_doaa_email' => $email]);
        }
    }

    // Admin Profile Management
    public function editProfile()
    {
        $admin = auth('admin')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account password.'])->withInput();
            }
            $admin->password = Hash::make($request->password);
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        return back()->with('success', 'Admin profile details updated successfully!');
    }
}