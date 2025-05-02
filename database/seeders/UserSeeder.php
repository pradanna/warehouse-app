<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Ramsey\Uuid\Guid\Guid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'superadmin', 'guard_name' => 'web'],
            ['id' => Str::uuid()->toString()]
        );

        $user = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => bcrypt('@Superadmin123'),
                'id' => Str::uuid()->toString(),
            ]
        );

        if (!$user->hasRole('superadmin')) {
            $user->assignRole($role);
        }
    }
}
