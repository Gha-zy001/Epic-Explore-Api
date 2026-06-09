<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'admin']);

        $permissions = [
            'view users', 'create users', 'update users', 'delete users',
            'view places', 'create places', 'update places', 'delete places', 'import places',
            'view hotels', 'create hotels', 'update hotels', 'delete hotels', 'import hotels',
            'view restaurants', 'create restaurants', 'update restaurants', 'delete restaurants', 'import restaurants',
            'view banks', 'create banks', 'update banks', 'delete banks', 'import banks',
            'view trips', 'delete trips',
            'view reviews', 'delete reviews',
            'view quests', 'create quests', 'update quests', 'delete quests',
            'view reward logs', 'view visits',
            'view otps',
            'manage contacts', 'approve contacts',
            'view favorites',
            'manage admins', 'manage roles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'admin']);
        }

        $superAdmin->syncPermissions(Permission::all());
        $adminRole->syncPermissions(Permission::all());
        $moderator->syncPermissions([
            'view users', 'view places', 'view hotels', 'view restaurants', 'view banks',
            'view reviews', 'delete reviews', 'view quests', 'view reward logs', 'view visits',
            'manage contacts', 'approve contacts', 'view favorites',
        ]);

        $superAdminUser = Admin::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@epicexplore.test')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdminUser->assignRole('super-admin');
    }
}
