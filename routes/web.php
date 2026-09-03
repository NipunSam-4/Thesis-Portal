<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentThesisController;
use App\Http\Controllers\PtsDocumentController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Pts1Controller;
use App\Http\Controllers\Pts2Controller;
use App\Http\Controllers\Pts4Controller;
use App\Http\Controllers\Dept_Authority\DeptAuthorityDashboardController;
use App\Http\Controllers\GlobalAuthority\GlobalAuthorityDashboardController;
use App\Http\Controllers\ActingApprovalAuthority\ActingApprovalAuthorityDashboardController;
use App\Http\Controllers\DraftSynopsisController;
use App\Http\Controllers\Pts2ExtensionController;
use App\Http\Controllers\Pts4ExtensionController;
use App\Http\Controllers\ExternalSupervisor\ExternalSupervisorDashboardController;
use App\Http\Controllers\ProfileController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Profile, Dashboard Dispatcher
Route::get('/dashboard', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
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
    if ($user->isGlobalAuthority()) {
        return redirect()->route('global_authorities.dashboard');
    }
    if ($user->isActingApprovalAuthority()) {
        return redirect()->route('acting_approval_authority.dashboard');
    }
    if ($user->isExternalSupervisor()) {
        return redirect()->route('external_supervisor.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth:web,admin'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Secure Private Document Streaming
    Route::get('/pts/document/{formType}/{id}/{field}', [PtsDocumentController::class, 'serveDocument'])->name('pts.document.serve');

    // ==========================================
    // PTS-1 (Comprehensive Exam) Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts1/create', [Pts1Controller::class, 'create'])->name('student.pts1.create');
    Route::get('/student/pts1/edit', [Pts1Controller::class, 'edit'])->name('student.pts1.edit');
    Route::post('/student/pts1/store', [Pts1Controller::class, 'store'])->name('student.pts1.store');
    Route::get('/student/pts1/template/download', [Pts1Controller::class, 'downloadTemplate'])->name('student.pts1.template.download');

    // Faculty Edit & Supervisor Modification Actions
    Route::get('/faculty/pts1/{pts1}/edit', [Pts1Controller::class, 'mainSupervisorEdit'])->name('faculty.pts1.edit');
    Route::match(['post', 'put'], '/faculty/pts1/{pts1}/update', [Pts1Controller::class, 'update'])->name('faculty.pts1.update');

    // Universal Viewing & Endorsement Full-Page Views
    Route::get('/pts1/{pts1}/show', [Pts1Controller::class, 'show'])->name('pts1.show');
    Route::get('/pts1/{pts1}/submitted', [Pts1Controller::class, 'submitted'])->name('pts1.submitted');
    Route::get('/pts1/{pts1}/reverted', [Pts1Controller::class, 'reverted'])->name('pts1.reverted');
    Route::get('/pts1/{pts1}/review', [Pts1Controller::class, 'review'])->name('pts1.review');
    Route::post('/pts1/{pts1}/review', [Pts1Controller::class, 'endorse'])->name('pts1.endorse');
    Route::post('/pts1/{pts1}/revert', [Pts1Controller::class, 'revert'])->name('pts1.revert');


    // ==========================================
    // PTS-2 (Synopsis Submission) Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts2/create', [Pts2Controller::class, 'create'])->name('student.pts2.create');
    Route::get('/student/pts2/edit', [Pts2Controller::class, 'edit'])->name('student.pts2.edit');
    Route::post('/student/pts2/store', [Pts2Controller::class, 'store'])->name('student.pts2.store');

    // Faculty Edit & Supervisor Modification Actions
    Route::get('/faculty/pts2/{pts2}/edit', [Pts2Controller::class, 'mainSupervisorEdit'])->name('faculty.pts2.edit');
    Route::match(['post', 'put'], '/faculty/pts2/{pts2}/update', [Pts2Controller::class, 'update'])->name('faculty.pts2.update');

    // Universal Viewing & Endorsement Full-Page Views
    Route::get('/pts2/{pts2}/show', [Pts2Controller::class, 'show'])->name('pts2.show');
    Route::get('/pts2/{pts2}/submitted', [Pts2Controller::class, 'submitted'])->name('pts2.submitted');
    Route::get('/pts2/{pts2}/reverted', [Pts2Controller::class, 'reverted'])->name('pts2.reverted');
    Route::get('/pts2/{pts2}/review', [Pts2Controller::class, 'review'])->name('pts2.review');
    Route::post('/pts2/{pts2}/review', [Pts2Controller::class, 'endorse'])->name('pts2.endorse');
    Route::post('/pts2/{pts2}/revert', [Pts2Controller::class, 'revert'])->name('pts2.revert');


    // ==========================================
    // PTS-3 (Panel of Examiners) Form Routes
    // ==========================================
    // Main Supervisor Form Actions
    Route::get('/faculty/pts3/create/{student}', [\App\Http\Controllers\Pts3Controller::class, 'create'])->name('faculty.pts3.create');
    Route::get('/faculty/pts3/{pts3}/edit', [\App\Http\Controllers\Pts3Controller::class, 'edit'])->name('faculty.pts3.edit');
    Route::post('/faculty/pts3/store/{student}', [\App\Http\Controllers\Pts3Controller::class, 'store'])->name('faculty.pts3.store');
    Route::match(['post', 'put'], '/faculty/pts3/{pts3}/update', [\App\Http\Controllers\Pts3Controller::class, 'update'])->name('faculty.pts3.update');

    // Single Universal Viewing & Evaluation Routes for PTS-3
    Route::get('/pts3/{pts3}/show', [\App\Http\Controllers\Pts3Controller::class, 'show'])->name('pts3.show');
    Route::post('/pts3/{pts3}/endorse', [\App\Http\Controllers\Pts3Controller::class, 'endorse'])->name('pts3.endorse');
    Route::post('/pts3/{pts3}/revert', [\App\Http\Controllers\Pts3Controller::class, 'revert'])->name('pts3.revert');


    // ==========================================
    // PTS-4 (Thesis Submission) Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts4/create', [Pts4Controller::class, 'create'])->name('student.pts4.create');
    Route::get('/student/pts4/edit', [Pts4Controller::class, 'edit'])->name('student.pts4.edit');
    Route::post('/student/pts4/store', [Pts4Controller::class, 'store'])->name('student.pts4.store');

    // Faculty Edit & Supervisor Modification Actions
    Route::get('/faculty/pts4/{pts4}/edit', [Pts4Controller::class, 'mainSupervisorEdit'])->name('faculty.pts4.edit');
    Route::match(['post', 'put'], '/faculty/pts4/{pts4}/update', [Pts4Controller::class, 'update'])->name('faculty.pts4.update');

    // Universal Viewing & Endorsement Full-Page Views
    Route::get('/pts4/{pts4}/show', [Pts4Controller::class, 'show'])->name('pts4.show');
    Route::get('/pts4/{pts4}/submitted', [Pts4Controller::class, 'submitted'])->name('pts4.submitted');
    Route::get('/pts4/{pts4}/reverted', [Pts4Controller::class, 'reverted'])->name('pts4.reverted');
    Route::get('/pts4/{pts4}/review', [Pts4Controller::class, 'review'])->name('pts4.review');
    Route::post('/pts4/{pts4}/review', [Pts4Controller::class, 'endorse'])->name('pts4.endorse');
    Route::post('/pts4/{pts4}/revert', [Pts4Controller::class, 'revert'])->name('pts4.revert');


    // ==========================================
    // PTS-2 Extension Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts2-extension/create', [Pts2ExtensionController::class, 'create'])->name('student.pts2_extension.create');
    Route::post('/student/pts2-extension/store', [Pts2ExtensionController::class, 'store'])->name('student.pts2_extension.store');

    // Universal Viewing & Review Actions
    Route::get('/pts2-extension/{pts2Extension}/show', [Pts2ExtensionController::class, 'show'])->name('pts2_extension.show');
    Route::get('/pts2-extension/{pts2Extension}/review', [Pts2ExtensionController::class, 'review'])->name('pts2_extension.review');
    Route::post('/pts2-extension/{pts2Extension}/review', [Pts2ExtensionController::class, 'endorse'])->name('pts2_extension.endorse');
    Route::post('/pts2-extension/{pts2Extension}/revert', [Pts2ExtensionController::class, 'revert'])->name('pts2_extension.revert');

    // ==========================================
    // PTS-4 Extension Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts4-extension/create', [Pts4ExtensionController::class, 'create'])->name('student.pts4_extension.create');
    Route::post('/student/pts4-extension/store', [Pts4ExtensionController::class, 'store'])->name('student.pts4_extension.store');

    // Universal Viewing & Review Actions
    Route::get('/pts4-extension/{pts4Extension}/show', [Pts4ExtensionController::class, 'show'])->name('pts4_extension.show');
    Route::get('/pts4-extension/{pts4Extension}/review', [Pts4ExtensionController::class, 'review'])->name('pts4_extension.review');
    Route::post('/pts4-extension/{pts4Extension}/review', [Pts4ExtensionController::class, 'endorse'])->name('pts4_extension.endorse');
    Route::post('/pts4-extension/{pts4Extension}/revert', [Pts4ExtensionController::class, 'revert'])->name('pts4_extension.revert');


    // ==========================================
    // Draft Synopsis Circulation Routes
    // ==========================================
    // Student Circulation Submission
    Route::get('/student/draft-synopsis/{draftSynopsisCirculation?}', [DraftSynopsisController::class, 'studentShow'])->name('student.draft_synopsis.show');
    Route::post('/student/draft-synopsis/store', [DraftSynopsisController::class, 'studentStore'])->name('student.draft_synopsis.store');

    // Authority Review, Comments & Image Streaming
    Route::get('/draft-synopsis/{draftSynopsisCirculation}/review', [DraftSynopsisController::class, 'review'])->name('draft_synopsis.review');
    Route::post('/draft-synopsis/{draftSynopsisCirculation}/comment', [DraftSynopsisController::class, 'comment'])->name('draft_synopsis.comment');
    Route::post('/draft-synopsis/{draftSynopsisCirculation}/upload-comment-image', [DraftSynopsisController::class, 'uploadCommentImage'])
        ->middleware('throttle:30,1')
        ->name('draft_synopsis.upload_comment_image');
    Route::get('/draft-synopsis/{draftSynopsisCirculation}/comment-images/{userId}/{filename}', [DraftSynopsisController::class, 'serveCommentImage'])->name('draft_synopsis.serve_comment_image');
    Route::get('/draft-synopsis/{draftSynopsisCirculation}/document', [DraftSynopsisController::class, 'serveDocument'])->name('draft_synopsis.document.serve');
});

// Role-Based Dashboards & General Actions
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/thesis/store', [StudentThesisController::class, 'store'])->name('student.thesis.store');
});

Route::prefix('faculty')->middleware(['auth', 'role:faculty'])->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('faculty.dashboard');
});

Route::prefix('hod')->middleware(['auth', 'role:hod'])->group(function () {
    Route::get('/dashboard', [DeptAuthorityDashboardController::class, 'index'])->name('hod.dashboard');
});

Route::prefix('dpgc')->middleware(['auth', 'role:dpgc'])->group(function () {
    Route::get('/dashboard', [DeptAuthorityDashboardController::class, 'index'])->name('dpgc.dashboard');
});

Route::prefix('global-authorities')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GlobalAuthorityDashboardController::class, 'index'])->name('global_authorities.dashboard');
});

Route::prefix('acting-approval-authority')->middleware(['auth', 'role:acting_approval_authority'])->group(function () {
    Route::get('/dashboard', [ActingApprovalAuthorityDashboardController::class, 'index'])->name('acting_approval_authority.dashboard');
});

Route::prefix('external-supervisor')->middleware(['auth', 'role:external_supervisor'])->group(function () {
    Route::get('/dashboard', [ExternalSupervisorDashboardController::class, 'index'])->name('external_supervisor.dashboard');
});

// System Admin Routes (System Admin Model Guard)
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [SystemAdminController::class, 'index'])->name('dashboard');

    // Department Management
    Route::get('/departments', [SystemAdminController::class, 'manageDepartments'])->name('departments.index');
    Route::post('/departments', [SystemAdminController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [SystemAdminController::class, 'updateDepartment'])->name('departments.update');
    Route::post('/departments/{department}/toggle', [SystemAdminController::class, 'toggleDepartmentStatus'])->name('departments.toggle');

    // Core Admin Accounts Management
    Route::get('/admins', [SystemAdminController::class, 'manageCoreAdmins'])->name('admins.index');
    Route::post('/admins', [SystemAdminController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{admin}', [SystemAdminController::class, 'updateAdmin'])->name('admins.update');
    Route::post('/admins/{admin}/toggle', [SystemAdminController::class, 'toggleAdminStatus'])->name('admins.toggle');

    // General User Status Toggle & Name Update
    Route::post('/users/{user}/toggle', [SystemAdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::put('/users/{user}/name', [SystemAdminController::class, 'updateAuthorityName'])->name('users.update');

    // Department & Global Authorities Management
    Route::get('/dept-authorities', [SystemAdminController::class, 'manageDeptAuthorities'])->name('dept_authorities.index');
    Route::post('/dept-authorities', [SystemAdminController::class, 'storeDeptAuthority'])->name('dept_authorities.store');
    Route::get('/global-authorities', [SystemAdminController::class, 'manageGlobalAuthorities'])->name('global_authorities.index');
    Route::post('/global-authorities', [SystemAdminController::class, 'storeGlobalAuthority'])->name('global_authorities.store');

    // Faculties & External Supervisors Management
    Route::get('/faculties', [SystemAdminController::class, 'manageFaculties'])->name('faculties.index');

    // External Examiners Management
    Route::get('/examiners', [SystemAdminController::class, 'manageExaminers'])->name('examiners.index');

    // Student Management
    Route::get('/students', [SystemAdminController::class, 'manageStudents'])->name('students.index');

    // DOAA Delegation & Pool Management
    Route::get('/doaa-delegation', [SystemAdminController::class, 'manageDoaaDelegation'])->name('doaa_delegation.index');
    Route::post('/doaa-delegation/user', [SystemAdminController::class, 'storeActingDoaaUser'])->name('doaa_delegation.user.store');
    Route::post('/doaa-delegation/pool-save', [SystemAdminController::class, 'saveDoaaPoolMembers'])->name('doaa_delegation.pool.save');
    Route::post('/doaa-delegation/visibility-save', [SystemAdminController::class, 'saveActingDoaaVisibility'])->name('doaa_delegation.visibility.save');
    Route::post('/doaa-delegation/vested', [SystemAdminController::class, 'updateVestedDoaa'])->name('doaa_delegation.vested.update');

    // Admin Profile Management
    Route::get('/profile', [SystemAdminController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [SystemAdminController::class, 'updateProfile'])->name('profile.update');
});

require __DIR__.'/auth.php';