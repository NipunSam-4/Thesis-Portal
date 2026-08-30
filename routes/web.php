<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentThesisController;
use App\Http\Controllers\Student\StudentPts1Controller;
use App\Http\Controllers\Student\StudentPts2Controller;
use App\Http\Controllers\PtsDocumentController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Pts1Controller;
use App\Http\Controllers\Pts2Controller;
use App\Http\Controllers\Dept_Authority\DeptAuthorityDashboardController;
use App\Http\Controllers\GlobalAuthority\GlobalAuthorityDashboardController;
use App\Http\Controllers\ActingApprovalAuthority\ActingApprovalAuthorityDashboardController;
use App\Http\Controllers\Student\StudentDraftSynopsisController;
use App\Http\Controllers\DraftSynopsisReviewController;
use App\Http\Controllers\Pts2ExtensionController;
use App\Http\Controllers\ExternalSupervisor\ExternalSupervisorDashboardController;
use App\Http\Controllers\ProfileController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Profile, Dashboard Dispatcher
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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Secure Private Document Streaming
    Route::get('/pts/document/{formType}/{id}/{field}', [PtsDocumentController::class, 'serveDocument'])->name('pts.document.serve');

    // ==========================================
    // PTS-1 (Comprehensive Exam) Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts1/create', [StudentPts1Controller::class, 'create'])->name('student.pts1.create');
    Route::get('/student/pts1/edit', [StudentPts1Controller::class, 'edit'])->name('student.pts1.edit');
    Route::post('/student/pts1/store', [StudentPts1Controller::class, 'store'])->name('student.pts1.store');
    Route::get('/student/pts1/template/download', [StudentPts1Controller::class, 'downloadTemplate'])->name('student.pts1.template.download');

    // Faculty Review & Supervisor Modification Actions
    Route::get('/faculty/pts1/{pts1}/review', [Pts1Controller::class, 'review'])->name('faculty.pts1.review');
    Route::match(['post', 'put'], '/faculty/pts1/{pts1}/update', [Pts1Controller::class, 'update'])->name('faculty.pts1.update');

    // Universal Viewing & Endorsement Full-Page Views
    Route::get('/pts1/{pts1}/show', [Pts1Controller::class, 'show'])->name('pts1.show');
    Route::get('/pts1/{pts1}/submitted', [Pts1Controller::class, 'submitted'])->name('pts1.submitted');
    Route::get('/pts1/{pts1}/reverted', [Pts1Controller::class, 'reverted'])->name('pts1.reverted');
    Route::get('/pts1/{pts1}/review', [Pts1Controller::class, 'reviewEndorse'])->name('pts1.review');
    Route::post('/pts1/{pts1}/review', [Pts1Controller::class, 'endorse'])->name('pts1.endorse');
    Route::post('/pts1/{pts1}/revert', [Pts1Controller::class, 'revert'])->name('pts1.revert');


    // ==========================================
    // PTS-2 (Synopsis Submission) Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts2/create', [StudentPts2Controller::class, 'create'])->name('student.pts2.create');
    Route::get('/student/pts2/edit', [StudentPts2Controller::class, 'edit'])->name('student.pts2.edit');
    Route::post('/student/pts2/store', [StudentPts2Controller::class, 'store'])->name('student.pts2.store');

    // Faculty Review & Supervisor Modification Actions
    Route::get('/faculty/pts2/{pts2}/review', [Pts2Controller::class, 'review'])->name('faculty.pts2.review');
    Route::match(['post', 'put'], '/faculty/pts2/{pts2}/update', [Pts2Controller::class, 'update'])->name('faculty.pts2.update');

    // Universal Viewing & Endorsement Full-Page Views
    Route::get('/pts2/{pts2}/show', [Pts2Controller::class, 'show'])->name('pts2.show');
    Route::get('/pts2/{pts2}/submitted', [Pts2Controller::class, 'submitted'])->name('pts2.submitted');
    Route::get('/pts2/{pts2}/reverted', [Pts2Controller::class, 'reverted'])->name('pts2.reverted');
    Route::get('/pts2/{pts2}/review', [Pts2Controller::class, 'reviewEndorse'])->name('pts2.review');
    Route::post('/pts2/{pts2}/review', [Pts2Controller::class, 'endorse'])->name('pts2.endorse');
    Route::post('/pts2/{pts2}/revert', [Pts2Controller::class, 'revert'])->name('pts2.revert');


    // ==========================================
    // PTS-2 Extension Form Routes
    // ==========================================
    // Student Form Actions
    Route::get('/student/pts2-extension/create', [Pts2ExtensionController::class, 'create'])->name('student.pts2_extension.create');
    Route::post('/student/pts2-extension/store', [Pts2ExtensionController::class, 'store'])->name('student.pts2_extension.store');

    // Universal Viewing & Review Actions
    Route::get('/pts2-extension/{id}/show', [Pts2ExtensionController::class, 'show'])->name('pts2_extension.show');
    Route::get('/pts2-extension/{id}/review', [Pts2ExtensionController::class, 'review'])->name('pts2_extension.review');
    Route::post('/pts2-extension/{id}/review', [Pts2ExtensionController::class, 'submitReview'])->name('pts2_extension.endorse');


    // ==========================================
    // Draft Synopsis Circulation Routes
    // ==========================================
    // Student Circulation Submission
    Route::get('/student/draft-synopsis', [StudentDraftSynopsisController::class, 'show'])->name('student.draft_synopsis.show');
    Route::post('/student/draft-synopsis/store', [StudentDraftSynopsisController::class, 'store'])->name('student.draft_synopsis.store');

    // Authority Review, Comments & Image Streaming
    Route::get('/draft-synopsis/{id}/review', [DraftSynopsisReviewController::class, 'show'])->name('draft_synopsis.review');
    Route::post('/draft-synopsis/{id}/comment', [DraftSynopsisReviewController::class, 'comment'])->name('draft_synopsis.comment');
    Route::post('/draft-synopsis/{id}/upload-comment-image', [DraftSynopsisReviewController::class, 'uploadCommentImage'])->name('draft_synopsis.upload_comment_image');
    Route::get('/draft-synopsis/{id}/comment-images/{userId}/{filename}', [DraftSynopsisReviewController::class, 'serveCommentImage'])->name('draft_synopsis.serve_comment_image');
    Route::get('/draft-synopsis/{id}/document', [DraftSynopsisReviewController::class, 'serveDocument'])->name('draft_synopsis.document.serve');
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
Route::prefix('system-admin')->name('system_admin.')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [SystemAdminController::class, 'index'])->name('dashboard');

    // Department Management
    Route::get('/departments', [SystemAdminController::class, 'manageDepartments'])->name('departments.index');
    Route::post('/departments', [SystemAdminController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [SystemAdminController::class, 'updateDepartment'])->name('departments.update');
    Route::post('/departments/{department}/toggle', [SystemAdminController::class, 'toggleDepartmentStatus'])->name('departments.toggle');

    // Core Admin Accounts Management
    Route::get('/admins', [SystemAdminController::class, 'manageCoreAdmins'])->name('admins.index');
    Route::post('/users', [SystemAdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [SystemAdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/toggle', [SystemAdminController::class, 'toggleUserStatus'])->name('users.toggle');

    // Department Authorities Management
    Route::get('/dept-authorities', [SystemAdminController::class, 'manageDeptAuthorities'])->name('dept_authorities.index');
    Route::post('/dept-authorities', [SystemAdminController::class, 'storeDeptAuthority'])->name('dept_authorities.store');

    // Global Authorities Management
    Route::get('/global-authorities', [SystemAdminController::class, 'manageGlobalAuthorities'])->name('global_authorities.index');
    Route::post('/global-authorities', [SystemAdminController::class, 'storeGlobalAuthority'])->name('global_authorities.store');

    // PhD Student Management
    Route::get('/students', [SystemAdminController::class, 'manageStudents'])->name('students.index');
    Route::post('/students', [SystemAdminController::class, 'storeStudent'])->name('students.store');
});

require __DIR__.'/auth.php';