<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\V1\TestController;
use App\Http\Requests\TestStoreRequest;
use App\Models\Answer;
use App\Models\Test;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class TestProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @psalm-suppress PossiblyUndefinedMethod
     * @psalm-suppress PossiblyInvalidMethodCall
     * @psalm-suppress PossiblyNullArgument
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $countOfUsers = User::count();
        for ($j = 1; $j <= $countOfUsers; $j++) {
            // login
            $user = User::findOrFail($j);

            // $countOfTests = rand(1, 3);
            $countOfTests = 1;

            for ($i = 0; $i < $countOfTests; $i++) {
                // disable rate limit
                Artisan::call('cache:clear');
                Auth::setUser($user);

                // start test
                $startTestRequest = Request::create(
                    'api/V1/tests',
                    'POST',
                    [
                        'expert_test_id' => 5,
                    ]
                );
                $startTestRequest->headers->set('Authorization', 'Bearer ' . 'TestProcessSeeder');
                $jsonResponseStartTest = app()->handle($startTestRequest);
                $startTestData = json_decode($jsonResponseStartTest->getContent());

                $answers = $startTestData->answers;
                $questionType = $startTestData->question->question->type;
                $testId = $startTestData->test->id;

                echo "test_id: " . $testId;
                echo "\n";

                $nextQuestionData = [0];
                while (count($nextQuestionData)) {
                    // select answers
                    $answerIds = array_column($answers, 'id');
                    $answersWithIsCorrect = Answer::whereIn('id', $answerIds)->get();
                    if ($questionType === 1) {
                        $sortedAnswer = $answersWithIsCorrect
                            ->sortByDesc('is_correct');
                        // 80% answer will be correct, 20% - 50/50
                        $userAnswer = rand(1, 5) <= 4 ? [$sortedAnswer->first()->id] : [$sortedAnswer
                            ->take(2)
                            ->pluck('id')
                            ->random()];
                    } else {
                        $countOfCorrect = $answersWithIsCorrect
                            ->where('is_correct', 1)
                            ->count();
                        if ($countOfCorrect / count($answersWithIsCorrect) >= 0.5) {
                            // select randomly from all answers
                            $userAnswer = $answersWithIsCorrect
                                ->pluck('id')
                                ->random(rand(1, count($answersWithIsCorrect)))->toArray();
                        } else {
                            // select only from correct answer
                            $userAnswer = $answersWithIsCorrect
                                ->pluck('id')
                                ->random(rand(1, $countOfCorrect))->toArray();
                        }
                    }

                    // next question
                    $nextQuestionRequest = Request::create(
                        'api/V1/tests/nextQuestion',
                        'POST',
                        [
                            'test_id' => $testId,
                            'answer' => $userAnswer
                        ]
                    );
                    $nextQuestionRequest->headers->set('Authorization', 'Bearer ' . 'TestProcessSeeder');
                    $jsonResponseNextQuestion = app()->handle($nextQuestionRequest);
                    $nextQuestionData = json_decode($jsonResponseNextQuestion->getContent(), true);

                    $questionType = $nextQuestionData['question']['question']['type'] ?? null;
                    $answers = $nextQuestionData['answers'] ?? null;
                }
            }
        }
        Test::all()->each(function (Test $item) {
            $item->update([
                'start_date' => Carbon::now()->subDays(1)->toIso8601String(),
                'finish_date' => Carbon::now()->subDays(1)
                    ->addSeconds(rand(600, 3300))
                    ->toIso8601String(),
            ]);
        });
    }
}
