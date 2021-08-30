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
        $expertTests = [];
        /** @phpstan-ignore-next-line */
        $countOfTestCategories = TestCategory::count();

        for ($i = 1; $i <= 50; $i++) {
            $number_of_questions = rand(10, 30);
            $expertTests[] = [
                'title' => 'Розділ №' . $i,
                'time' => (int)($number_of_questions * 1.25),
                'number_of_questions' => $number_of_questions,
                'is_published' => rand(0, 1),
                'created_at' => Carbon::now()->floorMonth(),
                'updated_at' => Carbon::now()->floorDays(rand(10, 30)),
                'deleted_at' => !($i % 10) ? Carbon::now()->floorDays(rand(1, 10)) : null,
                'test_category_id' => rand(1, $countOfTestCategories),
            ];
        }

        DB::table('expert_tests')->insert($expertTests);
    }
}
