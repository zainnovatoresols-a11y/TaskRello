<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => [
                'required',
                'string',
            ],
        ]);

        $user       = $request->user();
        $base64Data = $request->avatar;

        if (str_contains($base64Data, ';base64,')) {
            $base64Data = explode(';base64,', $base64Data)[1];
        }

        $imageData = base64_decode($base64Data);

        if (!$imageData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image data.',
            ], 422);
        }

        $finfo = finfo_open();
        $mime  = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image type.',
            ], 422);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $path = 'avatars/' . $user->id . '_' . time() . '.' . $extension;
        Storage::disk('public')->put($path, $imageData);

        $user->update(['avatar' => $path]);

        return response()->json([
            'success'=> true,
            'message'=> 'Avatar updated.',
            'avatar_url'=> Storage::url($path),
        ]);
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar removed.',
        ]);
    }
}
