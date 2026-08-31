<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // All system permissions
        $permissions = [
            // User Management
            'create-users',
            'edit-users',
            'delete-users',

            // Employee / Contingent Management
            'access-to-contingent',
            'access-to-contingent-management',
            'access-to-moodle-contingent-sync',
            'access-to-student-grades',
            'access-to-student-academic-leave-calendar',
            'access-to-student-document-order-issuances',
            'access-to-generate-spt-groups',
            'access-to-admission-campaign',
            'show-operating-activity-and-decisions-rectorate-and-council',
            'access-to-regulatory-documents',
            'access-to-uvr',
            'access-to-student-social-passport-review',
            'access-to-military',

            // Document Retrieval Requests
            'access-to-student-requests-for-certificates-full',
            'access-to-student-requests-for-certificates-spo',
            'access-to-student-requests-for-certificates-vo',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // Give all permissions to Admin & Employee
        $adminRole->syncPermissions(Permission::all());
        $employeeRole->syncPermissions(Permission::all());
    }
}
