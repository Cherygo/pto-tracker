<?php

namespace App\Http\Controllers;

use App\Models\PtoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();

        // Get requests for the whole tenant
        $requests = PtoRequest::with('user:id,name')
            ->where('tenant_id', $user->tenant_id)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        return Inertia::render('Calendar', [
            'requests' => $requests,
        ]);
    }
}
