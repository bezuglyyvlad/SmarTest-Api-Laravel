<?php
// phpcs:ignoreFile

namespace App\Helpers;

use App\Models\Answer;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TestHelper
{
    /**
     * @return array
     */
    public static function getUserTestIds(): array
    {
        return Test::where('user_id', Auth::id())
            ->get()
            ->filter(function (Test $test) {
                return $test->testIsFinished();
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @param Collection $testResult
     * @return Collection
     */
    public static function getAnswerIds(Collection $testResult): Collection
    {
        return $testResult->pluck('answer_ids')->map(function (string $item): array {
            return json_decode($item);
        })->collapse();
    }

    /**
     * @param int $testId
     * @param array $answerSelect
     * @return array
     */
    public static function getLastQuestionAndAnswers(int $testId, array $answerSelect = ['*']): array
    {
        $test_result = TestResult::where('test_id', $testId)
            ->with('question')
            ->orderByDesc('serial_number')
            ->first();
        $lastQuestion = $test_result->question;
        $answer_ids = self::getAnswerIds(
            $test_result->where('question_id', $lastQuestion->id)->get()
        );
        $answers = Answer::select($answerSelect)->whereIn('id', $answer_ids)->get();
        return ['test_tesult' => $test_result, 'lastQuestion' => $lastQuestion, 'answers' => $answers];
    }

    /**
     * @param TestResult $testResult
     * @param Collection $answers
     * @param array $userAnswer
     * @param Question $lastQuestion
     * @param Test $test
     * @return bool
     */
    public static function updateTestStatistics(
        TestResult $testResult,
        Collection $answers,
        array      $userAnswer,
        Question   $lastQuestion,
        Test       $test
    ): bool {
        // save user answer
        $validUserAnswer = array_values(array_intersect($answers->pluck('id')->toArray(), $userAnswer));
        $countOfIncorrectUserAnswer = array_intersect(
            $answers->where('is_correct', 0)->pluck('id')->toArray(),
            $validUserAnswer
        );

        // calc question score
        $poinst = $lastQuestion->quality_coef * Question::BASIC_POINTS;
        $correctAnswerIds = $answers->where('is_correct', 1)->pluck('id');

        $testResult->score = $correctAnswerIds->count() !== 0 && count($countOfIncorrectUserAnswer) === 0 ?
            $poinst * count($validUserAnswer) / $correctAnswerIds->count() :
            0;
        $testResult->user_answer = json_encode($validUserAnswer);
        $testResult->is_correct_answer = $poinst === $testResult->score;
        $testResult->score = $testResult->score * Test::MAX_CORRECTION_COEF;
        $testResult->max_score = $poinst * Test::MAX_CORRECTION_COEF;
        $testResult->save();

        // update test score
        $test->max_score += $poinst;
        $test->score += $testResult->score;
        if ($test->max_score >= 100) {
            $test->score = ($test->score / Test::MAX_CORRECTION_COEF) * 100 / $test->max_score;
            TestResult::where('test_id', $test->id)->get()->each(function (TestResult $item) use ($test) {
                $item->score = ($item->score / Test::MAX_CORRECTION_COEF) * 100 / $test->max_score;
                $item->max_score = ($item->max_score / Test::MAX_CORRECTION_COEF) * 100 / $test->max_score;
                $item->save();
            });
            $test->finish_date = Carbon::now()->toIso8601String();
        }
        return $test->save();
    }

    /**
     * @psalm-suppress PossiblyInvalidMethodCall
     * @param array $coefRange
     * @param int $expertTestId
     * @param int $testId
     * @return Question
     */
    private static function selectNewQuestion(array $coefRange, int $expertTestId, int $testId): Question
    {
        $questions = Question::where(['expert_test_id' => $expertTestId])
            ->whereBetween('quality_coef', $coefRange)->get();
        $test_result = TestResult::with('question')
            ->where('test_id', $testId)
            ->get()->pluck('question');
        $uniqQueestions = $questions->diff($test_result);
        return $uniqQueestions->all() ? $uniqQueestions->random() : $questions->random();
    }

    /**
     * @param int $expertTestId
     * @param int $testId
     * @param int $serialNumber
     * @param array $coefRange
     * @return array
     */
    public static function generateNewQuestion(
        int   $expertTestId,
        int   $testId,
        int   $serialNumber,
        array $coefRange
    ): array {
        $question = self::selectNewQuestion($coefRange, $expertTestId, $testId);
        $answers = Answer::select(['id', 'text'])
            ->where(['question_id' => $question->id])
            ->get()->shuffle();
        $testResult = new TestResult();
        $testResult->serial_number = $serialNumber;
        $testResult->answer_ids = $answers->pluck('id')->toJson();
        $testResult->test_id = $testId;
        $testResult->question_id = $question->id;
        $testResult->save();
        $testResult->load('question');
        return ['test_result' => $testResult, 'answers' => $answers];
    }

    /**
     * @param int $expertTestId
     * @return Test
     */
    public static function startNewTest(int $expertTestId): Test
    {
        $currentDate = Carbon::now();
        $newTest = new Test();
        $newTest->start_date = $currentDate->toIso8601String();
        $newTest->finish_date = $currentDate->addHour()->toIso8601String();
        $newTest->user_id = Auth::id();
        $newTest->expert_test_id = $expertTestId;
        $newTest->test_category_id = ExpertTest::findOrFail($expertTestId)->test_category_id;

        $newTest->save();
        return $newTest;
    }

    /**
     * @param int $testId
     * @return array
     */
    public static function getTestComponentsForResult(int $testId): array
    {
        $questions = TestResult::where('test_id', $testId)->with('question')
            ->get();
        $answer_ids = self::getAnswerIds($questions);
        $answers = Answer::withTrashed()->select(['id', 'text', 'is_correct', 'question_id'])
            ->whereIn('id', $answer_ids)
            ->get();

        return ['questions' => $questions, 'answers' => $answers];
    }

    /**
     * @param int $testCategoryId
     * @return array
     */
    public static function getExpertTestIds(int $testCategoryId): array
    {
        $testCategoryHistoryRecordIds = TestCategory
            ::setParentKeyName('parent_id')
            ::findOrFail($testCategoryId)
            ->descendantsAndSelf()
            ->withTrashed()
            ->pluck('id')
            ->toArray();
        return ExpertTest::whereIn('test_category_id', $testCategoryHistoryRecordIds)
            ->pluck('id')->toArray();
    }

    /**
     * @return Collection
     */
    public static function getBasicTestCategories(): \Illuminate\Support\Collection
    {
        return TestCategory::select(['id', 'title'])
            ->where(['parent_id' => null])
            ->get();
    }
}
