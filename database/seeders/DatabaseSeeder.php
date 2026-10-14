<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

/*         app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);



        $user =  User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => 123,
        ]);


        $user->assignRole($role); */

        $this->call([PaymentMethodSeeder::class]);
    }
}
