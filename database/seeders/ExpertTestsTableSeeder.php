<?php

namespace Database\Seeders;

use App\Models\TestCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpertTestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'title' => 'jQuery',
                'is_published' => true,
                'test_category_id' => 1
            ],
            [
                'title' => 'WebGL',
                'is_published' => true,
                'test_category_id' => 1
            ],
            [
                'title' => 'JSON',
                'is_published' => true,
                'test_category_id' => 2
            ],
            [
                'title' => 'AJAX',
                'is_published' => true,
                'test_category_id' => 2
            ],
            [
                'title' => 'Тест №1 - Введення в JavaScript',
                'is_published' => true,
                'test_category_id' => 9
            ],
            [
                'title' => 'Тест №2 - Змінні та константи',
                'is_published' => true,
                'test_category_id' => 9
            ],
            [
                'title' => 'Тест №3 - Типи даних',
                'is_published' => true,
                'test_category_id' => 9
            ],
            [
                'title' => 'Тест №4 - Умовні оператори',
                'is_published' => false,
                'test_category_id' => 9
            ],
            [
                'title' => 'Тест №5 - Цикли',
                'is_published' => true,
                'test_category_id' => 9
            ],
            [
                'title' => 'Тест №6 - Перетворення даних',
                'is_published' => true,
                'test_category_id' => 9
            ],
        ];

        $expertTests = [];

        for ($i = 0; $i < count($data); $i++) {
            $delete_at = !(($i + 1) % 10) ? Carbon::now()->subDays(rand(1, 10)) : null;
            $expertTests[] = [
                'title' => $data[$i]['title'],
                'is_published' => $data[$i]['is_published'],
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subDays(rand(10, 30)),
                'deleted_at' => $delete_at,
                'test_category_id' => $data[$i]['test_category_id'],
            ];
        }

        DB::table('expert_tests')->insert($expertTests);
    }
}
