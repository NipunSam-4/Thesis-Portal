<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentThesisController;
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
    Route::post('/thesis/store', [StudentThesisController::class, 'store'])->name('student.thesis.store');
    
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
    Route::post('/pts1/{pts1}/update', [FacultyPts1Controller::class, 'update'])->name('faculty.pts1.update');
    
    Route::get('/pts2/{pts2}/review', [FacultyPts2Controller::class, 'edit'])->name('faculty.pts2.edit');
    Route::post('/pts2/{pts2}/update', [FacultyPts2Controller::class, 'update'])->name('faculty.pts2.update');
});

/*
|--------------------------------------------------------------------------
| Head of Department (HOD) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('hod')->middleware(['auth', 'role:hod'])->group(function () {
    Route::get('/dashboard', [HodDashboardController::class, 'index'])->name('hod.dashboard');
});

/*
|--------------------------------------------------------------------------
| Department Postgraduate Committee (DPGC) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dpgc')->middleware(['auth', 'role:dpgc'])->group(function () {
    Route::get('/dashboard', [DpgcDashboardController::class, 'index'])->name('dpgc.dashboard');
});

/*
|--------------------------------------------------------------------------
| Institute Global Authorities Routes (DOAA, ADoAA, Senate Chair, Section Officer)
|--------------------------------------------------------------------------
*/
Route::prefix('global-authorities')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GlobalAuthorityDashboardController::class, 'index'])->name('global_authorities.dashboard');
});

/*
|--------------------------------------------------------------------------
| System Admin Routes (System Admin Model Guard)
|--------------------------------------------------------------------------
*/
Route::prefix('system-admin')->name('system_admin.')->middleware(['auth:system_admin'])->group(function () {
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