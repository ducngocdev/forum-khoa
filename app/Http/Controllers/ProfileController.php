<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AcceptedAnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'books' => $request->user()->books()->latest()->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['avatar'], $data['academic_year'], $data['remove_avatar']);
        $user = $request->user();
        $oldAvatarPath = $user->avatar_path;

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $extension = strtolower($avatar->getClientOriginalExtension());
            $directory = storage_path('app/public/avatars');
            File::ensureDirectoryExists($directory);
            $filename = Str::random(40) . '.' . $extension;
            $avatar->move($directory, $filename);
            $data['avatar_path'] = 'avatars/' . $filename;
        } elseif ($request->boolean('remove_avatar')) {
            $data['avatar_path'] = null;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $newAvatarPath = array_key_exists('avatar_path', $data) ? $data['avatar_path'] : $oldAvatarPath;
        if ($oldAvatarPath && $oldAvatarPath !== $newAvatarPath) {
            if (str_starts_with($oldAvatarPath, 'avatars/') && basename($oldAvatarPath) !== '') {
                File::delete(storage_path('app/public/' . $oldAvatarPath));
            }
        }

        return Redirect::route('users.show', $user)->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        AcceptedAnswerService::clearForUser($user);
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
