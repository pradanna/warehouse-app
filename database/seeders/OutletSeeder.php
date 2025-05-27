<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            //code...
            $path = database_path('seeders/data/outlet.json');
            $json = File::get($path);
            $data = json_decode($json, true);
            foreach ($data as $item) {
                Outlet::updateOrCreate(
                    ['id' => $item['id']],
                    $item
                );
            }
        } catch (\Exception $e) {
            Log::error("error seed units " . $e->getMessage());
        }
    }
}
