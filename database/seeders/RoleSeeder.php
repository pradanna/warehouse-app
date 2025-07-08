<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $jsonPath = database_path('seeders/data/roles.json');
            $json = File::get($jsonPath);
            $data = json_decode($json, true);
            foreach ($data as $datum) {
                $role = Role::updateOrCreate(
                    ['id' => $datum['id']],
                    $datum
                );
                // $role->syncPermissions(App::permissions());
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("error seed roles" . $th->getMessage());
        }
    }
}
