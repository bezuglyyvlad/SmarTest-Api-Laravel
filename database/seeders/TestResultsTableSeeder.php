<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestResultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testResults = [];
        $countOfTests = Test::count();
        $countOfQuestions = Question::count();
        $lastSerialNumbers = [];

        for ($i = 1; $i <= $countOfTests * 10; $i++) {
            $question_id = rand(1, $countOfQuestions);
            $test_id = rand(1, $countOfTests);
            $answers = Answer::where('question_id', $question_id);
            $answer_ids = $answers->pluck('id')->toJson();
            $user_answer = $answers->inRandomOrder()->limit(rand(1, 2))->pluck('id')->toJson();
            $lastSerialNumbers[$test_id] = array_key_exists($test_id, $lastSerialNumbers)
                ? $lastSerialNumbers[$test_id] + 1
                : 1;
            $testResults[] = [
                'serial_number' => $lastSerialNumbers[$test_id],
                'is_correct_answer' => rand(0, 1),
                'score' => rand(80, 100),
                'user_answer' => $user_answer,
                'answer_ids' => $answer_ids,
                'test_id' => $test_id,
                'question_id' => $question_id,
            ];
        }

        DB::table('test_results')->insert($testResults);
    }
}
