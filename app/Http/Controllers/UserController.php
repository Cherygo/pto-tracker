<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Promote a user to a manager role.
     */
    public function promoteToManager(Request $request, User $user): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        // Only admins can promote users
        if (!$authUser->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $user->update([
            'role' => 'manager',
        ]);

        return back()->with('success', 'User successfully promoted to manager.');
    }
}
