<?php

use App\Models\Tenant;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('admin sees admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('role', 'admin')
        ->has('users')
    );
});

test('manager sees manager dashboard', function () {
    $tenant = Tenant::factory()->create();
    $manager = User::factory()->create(['role' => 'manager', 'tenant_id' => $tenant->id]);
    $this->actingAs($manager);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('role', 'manager')
        ->has('requests')
    );
});

test('employee sees employee dashboard', function () {
    $tenant = Tenant::factory()->create();
    $employee = User::factory()->create(['role' => 'employee', 'tenant_id' => $tenant->id]);
    $this->actingAs($employee);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('role', 'employee')
        ->has('requests')
    );
});
