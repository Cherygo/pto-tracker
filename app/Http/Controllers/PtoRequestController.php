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
     * Update a pending PTO request.
     */
    public function update(StorePtoRequest $request, PtoRequest $ptoRequest): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        if ($ptoRequest->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($ptoRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be modified.']);
        }

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();

        // Calculate working days
        $requestedDays = $startDate->diffInWeekdays($endDate) + ($startDate->isWeekday() ? 1 : 0);

        if ($requestedDays <= 0) {
            return back()->withErrors(['start_date' => 'The selected date range must include at least one working day.']);
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

        $ptoRequest->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'PTO Request updated successfully.');
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

    /**
     * Cancel a PTO request.
     */
    public function cancel(Request $request, PtoRequest $ptoRequest): RedirectResponse
    {
        $authUser = Auth::user();

        if ($ptoRequest->user_id !== $authUser->id) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($ptoRequest->status, ['denied', 'cancelled'])) {
            return back()->withErrors(['error' => 'This request cannot be cancelled.']);
        }

        if ($ptoRequest->status === 'approved') {
            // Refund the days
            $startDate = Carbon::parse($ptoRequest->start_date);
            $endDate = Carbon::parse($ptoRequest->end_date);
            $requestedDays = $startDate->diffInWeekdays($endDate) + ($startDate->isWeekday() ? 1 : 0);

            $balance = PtoBalance::where('user_id', $ptoRequest->user_id)->where('year', $startDate->year)->first();
            if ($balance) {
                $balance->decrement('used_days', $requestedDays);
            }
        }

        $ptoRequest->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'PTO Request cancelled successfully.');
    }
}
