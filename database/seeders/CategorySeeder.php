<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            //code...
            $path = database_path('seeders/data/category.json');
            $json = File::get($path);
            $data = json_decode($json, true);
            foreach ($data as $item) {
                Category::updateOrCreate(
                    ['id' => $item['id']],
                    $item
                );
            }
        } catch (\Exception $e) {
            Log::error("error seed categories " . $e->getMessage());
        }
    }
}
