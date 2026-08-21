<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Display the user's profile form.
    public function edit(Request $request): View
    {
        $user = $request->user();
        $student = null;

        if ($user->isStudent()) {
            $user->load(['student.department', 'student.supervisors', 'student.pspcMembers']);
            $student = $user->student;
        }

        return view('profile.edit', [
            'user' => $user,
            'student' => $student,
        ]);
    }

    // Update the user's profile information.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            return Redirect::route('profile.edit')->with('warning', 'Student profile details are managed by Academic Office and cannot be edited.');
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Delete the user's account.
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            return Redirect::route('profile.edit')->with('warning', 'Student accounts are managed by Academic Administration and cannot be deleted.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
