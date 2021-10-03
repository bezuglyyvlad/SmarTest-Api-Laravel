<?php

namespace Database\Seeders;

use App\Models\Question;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answers = [];
        $faker = Factory::create();
        $countOfQuestions = Question::count();

        for ($i = 1; $i <= 5000; $i++) {
            $answers[] = [
                'text' => $faker->realText(100),
                'is_correct' => rand(0, 1),
                'created_at' => Carbon::now()->floorMonth(),
                'updated_at' => Carbon::now()->floorDays(rand(10, 30)),
                'deleted_at' => !($i % 10) ? Carbon::now()->floorDays(rand(1, 10)) : null,
                'question_id' => rand(1, $countOfQuestions),
            ];
        }

        DB::table('answers')->insert($answers);
    }
}
