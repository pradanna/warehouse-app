<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        DB::beginTransaction();
        try {
            $roleId = "b572ea4e-a0b3-444a-96f0-e97077379175";
            $role = Role::where('id', $roleId)->first();
            if (!$role) {
                Log::error("role not found...");
                DB::rollBack();
            }

            $user = User::firstOrCreate(
                ['username' => 'superadmin'],
                [
                    'password' => bcrypt('@Superadmin123'),
                    'id' => Str::uuid()->toString(),
                ]
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("error seed users" . $th->getMessage());
        }
    }
}
