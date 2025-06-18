<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle photo upload if present
        if ($request->hasFile('photo')) {
            // Delete old profile photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store the new photo
            $photoPath = $request->file('photo')->store(
                'profile-photos/' . $user->id,
                'public'
            );

            $validated['profile_photo_path'] = $photoPath;
        } else if ($request->removePhoto) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $validated['profile_photo_path'] = null;
            }
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'language_preference' => ['required', 'string', 'size:2'],
        ]);

        $request->user()->update($validated);

        return back();
    }

    /**
     * Serve a profile photo
     */
    public function getPhoto(Request $request, $userId, $filename)
    {
        $path = "profile-photos/{$userId}/{$filename}";
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        $filePath = Storage::disk('public')->path($path);
        
        return response()->file($filePath, [
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
        ]);
    }
}
