<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\SubmissionController;
use App\Http\Controllers\Student\Pts1Controller;
use App\Http\Controllers\Student\StudentPts2Controller;
use App\Http\Controllers\PtsDocumentController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\FacultyPts1Controller;
use App\Http\Controllers\Faculty\FacultyPts2Controller;
use App\Http\Controllers\Pts1EndorsementController;
use App\Http\Controllers\Pts2EndorsementController;
use App\Http\Controllers\Hod\HodDashboardController;
use App\Http\Controllers\Dpgc\DpgcDashboardController;
use App\Http\Controllers\GlobalAuthority\GlobalAuthorityDashboardController;
use App\Http\Controllers\ProfileController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Profile, Dashboard Dispatcher & Private File & Endorsement Routes
Route::middleware('auth')->group(function () {
    // Central Role-Based Dashboard Dispatcher
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        } elseif ($user->isFaculty()) {
            return redirect()->route('faculty.dashboard');
        } elseif ($user->isHod()) {
            return redirect()->route('hod.dashboard');
        } elseif ($user->isDpgc()) {
            return redirect()->route('dpgc.dashboard');
        } elseif ($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || $user->isSectionOfficer()) {
            return redirect()->route('global_authority.dashboard');
        }

        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Secure Private Document Streaming Route
    Route::get('/pts/document/{formType}/{id}/{field}', [PtsDocumentController::class, 'serveDocument'])->name('pts.document.serve');

    // Universal Dedicated Review & Endorsement Full-Page Views
    Route::get('/pts1/{pts1}/review-endorse', [Pts1EndorsementController::class, 'showReview'])->name('pts1.review_endorse');
    Route::get('/pts2/{pts2}/review-endorse', [Pts2EndorsementController::class, 'showReview'])->name('pts2.review_endorse');

    // Universal PTS-1 Endorsement & Reversion Action Routes
    Route::post('/pts1/{pts1}/endorse', [Pts1EndorsementController::class, 'endorse'])->name('pts1.endorse');
    Route::post('/pts1/{pts1}/revert', [Pts1EndorsementController::class, 'revert'])->name('pts1.revert');

    // Universal PTS-2 Endorsement & Reversion Action Routes
    Route::post('/pts2/{pts2}/endorse', [Pts2EndorsementController::class, 'endorse'])->name('pts2.endorse');
    Route::post('/pts2/{pts2}/revert', [Pts2EndorsementController::class, 'revert'])->name('pts2.revert');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/pts1/create', [Pts1Controller::class, 'create'])->name('student.pts1.create');
    Route::post('/pts1/store', [Pts1Controller::class, 'store'])->name('student.pts1.store');
    Route::get('/pts1/template/download', [Pts1Controller::class, 'downloadTemplate'])->name('student.pts1.template.download');
    
    // PTS-2 Synopsis Form Routes
    Route::get('/pts2/create', [StudentPts2Controller::class, 'create'])->name('student.pts2.create');
    Route::post('/pts2/store', [StudentPts2Controller::class, 'store'])->name('student.pts2.store');
    Route::get('/thesis/submit', [SubmissionController::class, 'createPts2'])->name('student.thesis.submit');
});

/*
|--------------------------------------------------------------------------
| Faculty Routes (Supervisors & PSPC)
|--------------------------------------------------------------------------
*/
Route::prefix('faculty')->middleware(['auth', 'role:faculty'])->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('faculty.dashboard');
    Route::get('/pts1/{pts1}/review', [FacultyPts1Controller::class, 'edit'])->name('faculty.pts1.edit');
    Route::put('/pts1/{pts1}', [FacultyPts1Controller::class, 'update'])->name('faculty.pts1.update');

    // Co-Supervisor PTS-1 Review Routes
    Route::get('/pts1/{pts1}/co-review', [FacultyPts1Controller::class, 'coEdit'])->name('faculty.pts1.co_edit');
    Route::put('/pts1/{pts1}/co-update', [FacultyPts1Controller::class, 'coUpdate'])->name('faculty.pts1.co_update');

    // PTS-2 Faculty Review & Update Routes
    Route::get('/pts2/{pts2}/review', [FacultyPts2Controller::class, 'edit'])->name('faculty.pts2.edit');
    Route::put('/pts2/{pts2}', [FacultyPts2Controller::class, 'update'])->name('faculty.pts2.update');
});

/*
|--------------------------------------------------------------------------
| HoD Routes
|--------------------------------------------------------------------------
*/
Route::prefix('hod')->middleware(['auth', 'role:hod'])->group(function () {
    Route::get('/dashboard', [HodDashboardController::class, 'index'])->name('hod.dashboard');
});

/*
|--------------------------------------------------------------------------
| DPGC Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dpgc')->middleware(['auth', 'role:dpgc'])->group(function () {
    Route::get('/dashboard', [DpgcDashboardController::class, 'index'])->name('dpgc.dashboard');
});

/*
|--------------------------------------------------------------------------
| Global Authorities Routes (DOAA, Section Officer, etc.)
|--------------------------------------------------------------------------
*/
Route::prefix('global-authority')->middleware(['auth', 'role:doaa,adoaa,senate_chairperson,ar_academic,section_officer'])->group(function () {
    Route::get('/dashboard', [GlobalAuthorityDashboardController::class, 'index'])->name('global_authority.dashboard');
});

/*
|--------------------------------------------------------------------------
| System Admin Routes (Guard: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [SystemAdminController::class, 'index'])->name('system_admin.dashboard');
    
    // Core Admins Management
    Route::get('/core-admins/manage', [SystemAdminController::class, 'manageCoreAdmins'])->name('system_admin.admins.index');
    
    // Global Authorities Management
    Route::get('/global-authorities/manage', [SystemAdminController::class, 'manageGlobalAuthorities'])->name('system_admin.global_authorities.index');
    Route::post('/global-authorities', [SystemAdminController::class, 'storeGlobalAuthority'])->name('system_admin.global_authorities.store');

    // Departments Management
    Route::get('/departments/manage', [SystemAdminController::class, 'manageDepartments'])->name('system_admin.departments.index');
    Route::post('/departments', [SystemAdminController::class, 'storeDepartment'])->name('system_admin.departments.store');
    Route::put('/departments/{department}', [SystemAdminController::class, 'updateDepartment'])->name('system_admin.departments.update');
    Route::patch('/departments/{department}/toggle', [SystemAdminController::class, 'toggleDepartmentStatus'])->name('system_admin.departments.toggle');

    // Department Authorities Management
    Route::get('/dept-authorities/manage', [SystemAdminController::class, 'manageDeptAuthorities'])->name('system_admin.dept_authorities.index');
    Route::post('/dept-authorities', [SystemAdminController::class, 'storeDeptAuthority'])->name('system_admin.dept_authorities.store');

    // Users (Faculty/Staff) Management
    Route::get('/users/manage', [SystemAdminController::class, 'manageUsers'])->name('system_admin.users.index');
    Route::post('/users', [SystemAdminController::class, 'storeUser'])->name('system_admin.users.store');
    Route::put('/users/{user}', [SystemAdminController::class, 'updateUser'])->name('system_admin.users.update');
    Route::patch('/users/{user}/toggle', [SystemAdminController::class, 'toggleUserStatus'])->name('system_admin.users.toggle');

    // Students Management
    Route::get('/students/manage', [SystemAdminController::class, 'manageStudents'])->name('system_admin.students.index');
    Route::post('/students', [SystemAdminController::class, 'storeStudent'])->name('system_admin.students.store');
});

require __DIR__.'/auth.php';