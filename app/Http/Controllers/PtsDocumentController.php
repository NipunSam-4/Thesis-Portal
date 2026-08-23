<?php

namespace App\Http\Controllers;

use App\Models\Pts1Form;
use App\Models\Pts2Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PtsDocumentController extends Controller
{
    // Securely serve a document stored in private local storage.
    public function serveDocument(Request $request, string $formType, int $id, string $field): BinaryFileResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $filePath = null;
        $thesis = null;

        if ($formType === 'pts1') {
            $form = Pts1Form::with('thesis.student')->findOrFail($id);
            $thesis = $form->thesis;
            $allowedFields = [
                'publication_approval_doc_path',
                'min_time_approval_doc_path',
                'draft_synopsis_report_doc_path',
                'publication_list_doc_path',
                'main_supervisor_publication_approval_doc_path',
                'main_supervisor_min_time_approval_doc_path',
                'main_supervisor_draft_synopsis_report_doc_path',
                'main_supervisor_publication_list_doc_path',
            ];

            if (!in_array($field, $allowedFields, true)) {
                abort(400, 'Invalid document type');
            }

            $filePath = $form->{$field};
        } elseif ($formType === 'pts2') {
            $form = Pts2Form::with('thesis.student')->findOrFail($id);
            $thesis = $form->thesis;
            $allowedFields = ['synopsis_report_doc_path'];

            if (!in_array($field, $allowedFields, true)) {
                abort(400, 'Invalid document type');
            }

            $filePath = $form->{$field};
        } else {
            abort(404, 'Form type not supported');
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'Document file not found in private storage');
        }

        // Authorization check: Is current user Student, Supervisor, PSPC member, HOD/DPGC, or Global Authority?
        $isAuthorized = false;

        // 1. Check if user is the student
        if ($thesis && $thesis->student && $thesis->student->user_id === $user->id) {
            $isAuthorized = true;
        }

        // 2. Check if user is an assigned supervisor or PSPC member
        if (!$isAuthorized && $thesis && $thesis->student) {
            if ($thesis->student->supervisors()->where('faculty_user_id', $user->id)->exists() || 
                $thesis->student->pspcMembers()->where('faculty_user_id', $user->id)->exists()) {
                $isAuthorized = true;
            }
        }

        // 3. Check if user is HOD/DPGC (same department), Global Authority, or Acting/Vested DOAA
        if (!$isAuthorized) {
            if (in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'ar', 'section_officer'], true)) {
                $isAuthorized = true;
            } elseif ($user->isActingApprovalAuthority() && ($form->acting_doaa_email === $user->email || $form->vested_doaa_email === $user->email)) {
                $isAuthorized = true;
            } elseif (in_array($user->role, ['hod', 'dpgc'], true)) {
                $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
                if ($userDeptId && $thesis->student && $thesis->student->department_id === $userDeptId) {
                    $isAuthorized = true;
                }
            }
        }

        if (!$isAuthorized) {
            abort(403, 'You are not authorized to view this document');
        }

        $absolutePath = Storage::disk('local')->path($filePath);

        return response()->file($absolutePath);
    }
}
