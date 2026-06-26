<?php

namespace App\Http\Controllers;

use App\Models\PtoBalance;
use App\Models\PtoRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the role-based dashboard.
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return Inertia::render('Dashboard', [
                'role' => 'admin',
                'total_companies' => Tenant::count(),
                'users' => User::where('role', '!=', 'admin')->with('tenant')->get(),
            ]);
        }

        $balance = PtoBalance::where('user_id', $user->id)
            ->where('year', date('Y'))
            ->first();

        if ($user->isManager()) {
            return Inertia::render('Dashboard', [
                'role' => 'manager',
                'balance' => $balance,
                'requests' => PtoRequest::with('user')->latest()->get(),
            ]);
        }

        // Employee
        return Inertia::render('Dashboard', [
            'role' => 'employee',
            'balance' => $balance,
            'requests' => PtoRequest::where('user_id', $user->id)->latest()->get(),
        ]);
    }
}
