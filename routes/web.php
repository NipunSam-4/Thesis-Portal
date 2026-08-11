<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentThesisController;
use App\Http\Controllers\Student\StudentPts1Controller;
use App\Http\Controllers\Student\StudentPts2Controller;
use App\Http\Controllers\PtsDocumentController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\FacultyPts2Controller;
use App\Http\Controllers\Pts1Controller;
use App\Http\Controllers\Pts2Controller;
use App\Http\Controllers\Dept_Authority\DeptAuthorityDashboardController;
use App\Http\Controllers\GlobalAuthority\GlobalAuthorityDashboardController;
use App\Http\Controllers\ProfileController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Profile, Dashboard Dispatcher & Private File & Endorsement Routes
Route::get('/dashboard', function () {
    if (auth('admin')->check()) {
        return redirect()->route('system_admin.dashboard');
    }

    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    }
    if ($user->isFaculty()) {
        return redirect()->route('faculty.dashboard');
    }
    if ($user->isHod()) {
        return redirect()->route('hod.dashboard');
    }
    if ($user->isDpgc()) {
        return redirect()->route('dpgc.dashboard');
    }
    if ($user->isSectionOfficer() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic()) {
        return redirect()->route('global_authorities.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth:web,admin'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Secure Private Document Streaming Route
    Route::get('/pts/document/{formType}/{id}/{field}', [PtsDocumentController::class, 'serveDocument'])->name('pts.document.serve');

    // Universal Dedicated Review & Endorsement Full-Page Views
    Route::get('/pts1/{pts1}/show', [Pts1Controller::class, 'show'])->name('pts1.show');
    Route::get('/pts1/{pts1}/review-endorse', [Pts1Controller::class, 'showReview'])->name('pts1.review_endorse');
    Route::get('/pts2/{pts2}/review-endorse', [Pts2Controller::class, 'showReview'])->name('pts2.review_endorse');

    // Universal PTS-1 Endorsement & Reversion Action Routes
    Route::post('/pts1/{pts1}/endorse', [Pts1Controller::class, 'endorse'])->name('pts1.endorse');
    Route::post('/pts1/{pts1}/revert', [Pts1Controller::class, 'revert'])->name('pts1.revert');

    // Universal PTS-2 Endorsement & Reversion Action Routes
    Route::post('/pts2/{pts2}/endorse', [Pts2Controller::class, 'endorse'])->name('pts2.endorse');
    Route::post('/pts2/{pts2}/revert', [Pts2Controller::class, 'revert'])->name('pts2.revert');
});

// Student Routes
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/thesis/store', [StudentThesisController::class, 'store'])->name('student.thesis.store');
    
    Route::get('/pts1/create', [StudentPts1Controller::class, 'create'])->name('student.pts1.create');
    Route::post('/pts1/store', [StudentPts1Controller::class, 'store'])->name('student.pts1.store');
    Route::get('/pts1/template/download', [StudentPts1Controller::class, 'downloadTemplate'])->name('student.pts1.template.download');
    
    // PTS-2 Synopsis Form Routes
    Route::get('/pts2/create', [StudentPts2Controller::class, 'create'])->name('student.pts2.create');
    Route::post('/pts2/store', [StudentPts2Controller::class, 'store'])->name('student.pts2.store');
});

// Faculty Routes (Supervisors & PSPC)
Route::prefix('faculty')->middleware(['auth', 'role:faculty'])->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('faculty.dashboard');
    Route::get('/pts1/{pts1}/review', [Pts1Controller::class, 'edit'])->name('faculty.pts1.edit');
    Route::match(['post', 'put'], '/pts1/{pts1}/update', [Pts1Controller::class, 'update'])->name('faculty.pts1.update');
    
    Route::get('/pts2/{pts2}/review', [FacultyPts2Controller::class, 'edit'])->name('faculty.pts2.edit');
    Route::match(['post', 'put'], '/pts2/{pts2}/update', [FacultyPts2Controller::class, 'update'])->name('faculty.pts2.update');
});

// Departmental Authorities Routes (HOD & DPGC)
Route::prefix('hod')->middleware(['auth', 'role:hod'])->group(function () {
    Route::get('/dashboard', [DeptAuthorityDashboardController::class, 'index'])->name('hod.dashboard');
});

Route::prefix('dpgc')->middleware(['auth', 'role:dpgc'])->group(function () {
    Route::get('/dashboard', [DeptAuthorityDashboardController::class, 'index'])->name('dpgc.dashboard');
});

// Institute Global Authorities Routes (DOAA, ADoAA, Senate Chair, Section Officer)
Route::prefix('global-authorities')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GlobalAuthorityDashboardController::class, 'index'])->name('global_authorities.dashboard');
});

// System Admin Routes (System Admin Model Guard)
Route::prefix('system-admin')->name('system_admin.')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [SystemAdminController::class, 'dashboard'])->name('dashboard');

    // Admin & Global Authority User Management
    Route::get('/users', [SystemAdminController::class, 'manageUsers'])->name('users.index');
    Route::post('/users', [SystemAdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/toggle', [SystemAdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::delete('/users/{user}', [SystemAdminController::class, 'deleteUser'])->name('users.destroy');

    // Department Management
    Route::get('/departments', [SystemAdminController::class, 'departmentsIndex'])->name('departments.index');
    Route::post('/departments', [SystemAdminController::class, 'storeDepartment'])->name('departments.store');

    // Core Admin Accounts Management
    Route::get('/admins', [SystemAdminController::class, 'adminsIndex'])->name('admins.index');
    Route::post('/admins', [SystemAdminController::class, 'storeAdmin'])->name('admins.store');

    // PhD Student Management
    Route::get('/students', [SystemAdminController::class, 'studentsIndex'])->name('students.index');
    Route::post('/students', [SystemAdminController::class, 'storeStudent'])->name('students.store');
});

require __DIR__.'/auth.php';