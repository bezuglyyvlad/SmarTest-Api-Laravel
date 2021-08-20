<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** @psalm-suppress MissingConstructor */
class TestCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [];

        for ($i = 1; $i <= 50; $i++) {
            $title = 'Категория №' . $i;
            $categories[] = [
                'title' => $title,
                'parent_id' => rand(0, $i - 1),
                'user_id' => rand(1, 10),
            ];
        }

        DB::table('test_categories')->insert($categories);
    }
}
