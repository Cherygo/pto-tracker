<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePtoRequest;
use App\Models\PtoBalance;
use App\Models\PtoRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PtoRequestController extends Controller
{
    /**
     * Store a newly created PTO request in storage.
     */
    public function store(StorePtoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();

        // Calculate working days (excluding weekends)
        $requestedDays = $startDate->diffInWeekdays($endDate) + ($startDate->isWeekday() ? 1 : 0);

        if ($requestedDays <= 0) {
            return back()->withErrors(['start_date' => 'The selected date range must include at least one working day.']);
        }

        // Check overlapping requests
        $hasOverlap = PtoRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();

        if ($hasOverlap) {
            return back()->withErrors(['start_date' => 'You already have an overlapping PTO request for these dates.']);
        }

        // Check balance
        $balance = PtoBalance::where('user_id', $user->id)->where('year', date('Y'))->first();
        if (! $balance) {
            return back()->withErrors(['start_date' => 'No PTO balance found for the current year.']);
        }

        $remainingDays = $balance->allocated_days - $balance->used_days;

        if ($requestedDays > $remainingDays) {
            return back()->withErrors(['end_date' => "You are requesting {$requestedDays} days, but only have {$remainingDays} days remaining."]);
        }

        // Create the request
        PtoRequest::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'PTO Request submitted successfully.');
    }

    /**
     * Approve a pending PTO request.
     */
    public function approve(Request $request, PtoRequest $ptoRequest): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if (! $authUser->isManager()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ptoRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be approved.']);
        }

        // Calculate days to deduct
        $startDate = Carbon::parse($ptoRequest->start_date);
        $endDate = Carbon::parse($ptoRequest->end_date);
        $requestedDays = $startDate->diffInWeekdays($endDate) + ($startDate->isWeekday() ? 1 : 0);

        // Deduct from balance
        $balance = PtoBalance::where('user_id', $ptoRequest->user_id)->where('year', $startDate->year)->first();
        if ($balance) {
            $balance->increment('used_days', $requestedDays);
        }

        $ptoRequest->update([
            'status' => 'approved',
            'reviewer_id' => $authUser->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'PTO Request approved.');
    }

    /**
     * Deny a pending PTO request.
     */
    public function deny(Request $request, PtoRequest $ptoRequest): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if (! $authUser->isManager()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ptoRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be denied.']);
        }

        $ptoRequest->update([
            'status' => 'denied',
            'reviewer_id' => $authUser->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'PTO Request denied.');
    }
}
