<?php

namespace Database\Seeders;

use App\Models\PtoBalance;
use App\Models\PtoRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Platform Admin
        User::factory()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@pto-tracker.com',
            'role' => 'admin',
            'tenant_id' => null,
        ]);

        User::factory()->create([
            'name' => 'Cherygo Admin',
            'email' => 'cherygo@pto-tracker.com',
            'password' => 'cherygo',
            'role' => 'admin',
            'tenant_id' => null,
        ]);

        // 2. Create a Tenant
        $tenant = Tenant::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        // 3. Create a Tenant Manager
        $manager = User::factory()->create([
            'name' => 'Jane Manager',
            'email' => 'manager@acmecorp.com',
            'role' => 'manager',
            'tenant_id' => $tenant->id,
        ]);

        // 4. Create a Tenant Employee reporting to the Manager
        $employee = User::factory()->create([
            'name' => 'John Employee',
            'email' => 'employee@acmecorp.com',
            'role' => 'employee',
            'tenant_id' => $tenant->id,
            'manager_id' => $manager->id,
        ]);

        // 5. Create a PTO Balance for the Employee
        PtoBalance::create([
            'tenant_id' => $tenant->id,
            'user_id' => $employee->id,
            'year' => date('Y'),
            'allocated_days' => 20.00,
            'used_days' => 5.00,
        ]);

        // 6. Create a PTO Request for the Employee (Approved)
        PtoRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $employee->id,
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'approved',
            'reason' => 'Summer vacation',
            'reviewer_id' => $manager->id,
            'reviewed_at' => now(),
            'reviewer_notes' => 'Have fun!',
        ]);

        // 7. Create a PTO Request for the Employee (Pending)
        PtoRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $employee->id,
            'start_date' => now()->addDays(30)->format('Y-m-d'),
            'end_date' => now()->addDays(32)->format('Y-m-d'),
            'status' => 'pending',
            'reason' => 'Doctor appointments',
        ]);
    }
}
