<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            //code...
            $path = database_path('seeders/data/item.json');
            $json = File::get($path);
            $data = json_decode($json, true);
            foreach ($data as $item) {
                Item::updateOrCreate(
                    ['id' => $item['id']],
                    $item
                );
            }
        } catch (\Exception $e) {
            Log::error("error seed categories " . $e->getMessage());
        }
    }
}
