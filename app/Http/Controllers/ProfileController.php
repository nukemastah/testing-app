<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $data = $request->only('name', 'email');

        // Update attributes explicitly (email_verified_at is not fillable)
        $user->name = $data['name'];
        // If the email changed, reset email_verified_at to null
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
        } else {
            $user->email = $data['email'];
        }

        $user->save();

        // Redirect to profile page to match test expectations
        return redirect('/profile')->with('status', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            // Use the 'userDeletion' error bag to match tests that expect this bag
            return back()->withErrors(['password' => 'Password salah.'], 'userDeletion');
        }

        // To avoid issues deleting the currently authenticated model instance,
        // capture the id first, log out, then delete by query.
        $userId = $user->id;

        // Log out and invalidate session before removing the DB record
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \App\Models\User::where('id', $userId)->delete();

        return redirect('/')->with('status', 'Akun berhasil dihapus.');
    }
}
