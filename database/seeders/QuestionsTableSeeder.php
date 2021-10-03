<?php

namespace Database\Seeders;

use App\Models\ExpertTest;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [];
        $faker = Factory::create();
        $countOfExpertTests = ExpertTest::count();

        for ($i = 1; $i <= 1000; $i++) {
            $questions[] = [
                'text' => $faker->realText(),
                'lvl' => rand(1, 3),
                'type' => rand(1, 2),
                'created_at' => Carbon::now()->floorMonth(),
                'updated_at' => Carbon::now()->floorDays(rand(10, 30)),
                'deleted_at' => !($i % 10) ? Carbon::now()->floorDays(rand(1, 10)) : null,
                'expert_test_id' => rand(1, $countOfExpertTests),
            ];
        }

        DB::table('questions')->insert($questions);
    }
}
