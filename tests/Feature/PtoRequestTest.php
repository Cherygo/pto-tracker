<?php

use App\Models\PtoBalance;
use App\Models\PtoRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;

test('employee can submit a pto request', function () {
    $tenant = Tenant::factory()->create();
    $employee = User::factory()->create(['role' => 'employee', 'tenant_id' => $tenant->id]);

    PtoBalance::create([
        'tenant_id' => $tenant->id,
        'user_id' => $employee->id,
        'year' => date('Y'),
        'allocated_days' => 20,
        'used_days' => 0,
    ]);

    $this->actingAs($employee);

    $response = $this->post(route('pto-requests.store'), [
        'start_date' => Carbon::now()->addDays(2)->toDateString(),
        'end_date' => Carbon::now()->addDays(4)->toDateString(),
        'reason' => 'Vacation',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertDatabaseHas('pto_requests', [
        'user_id' => $employee->id,
        'status' => 'pending',
    ]);
});

test('manager can approve a request and deduct balance', function () {
    $tenant = Tenant::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'tenant_id' => $tenant->id]);
    $employee = User::factory()->create(['role' => 'employee', 'tenant_id' => $tenant->id]);

    $balance = PtoBalance::create([
        'tenant_id' => $tenant->id,
        'user_id' => $employee->id,
        'year' => date('Y'),
        'allocated_days' => 20,
        'used_days' => 0,
    ]);

    $start = Carbon::now()->next(Carbon::MONDAY);
    $end = $start->copy()->addDays(1); // Mon-Tue = 2 days

    $request = PtoRequest::create([
        'tenant_id' => $tenant->id,
        'user_id' => $employee->id,
        'start_date' => $start,
        'end_date' => $end,
        'status' => 'pending',
        'reason' => 'Vacation',
    ]);

    $this->actingAs($manager);

    $response = $this->patch(route('pto-requests.approve', $request->id));

    $response->assertSessionHasNoErrors();

    expect($request->fresh()->status)->toBe('approved');
    expect($balance->fresh()->used_days)->toBe(2);
});

test('manager can deny a request', function () {
    $tenant = Tenant::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'tenant_id' => $tenant->id]);
    $employee = User::factory()->create(['role' => 'employee', 'tenant_id' => $tenant->id]);

    $request = PtoRequest::create([
        'tenant_id' => $tenant->id,
        'user_id' => $employee->id,
        'start_date' => Carbon::now()->addDays(1),
        'end_date' => Carbon::now()->addDays(3),
        'status' => 'pending',
        'reason' => 'Vacation',
    ]);

    $this->actingAs($manager);

    $response = $this->patch(route('pto-requests.deny', $request->id));

    $response->assertSessionHasNoErrors();

    expect($request->fresh()->status)->toBe('denied');
});
