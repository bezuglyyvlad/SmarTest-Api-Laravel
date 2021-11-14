<?php

namespace App\Http\Requests;

use App\Helpers\TestHelper;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/** @psalm-suppress PropertyNotSetInConstructor */
class TestNextQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userAnswer = $this->answer;
        $test = Test::where(['id' => $this->test_id, 'user_id' => Auth::id()])->first();
        $correctAnswer = true;
        $lastQuestion = $test ? TestResult::where('test_id', $test->id)
            ->with('question')
            ->orderByDesc('serial_number')
            ->first()->question : null;
        if (
            $lastQuestion &&
            in_array($lastQuestion->type, Question::TYPES_WITH_ONE_ASWER) &&
            count($userAnswer) > 1
        ) {
            $correctAnswer = false;
        }
        return ($test && !$test->testIsFinished()) &&
            ($userAnswer && $correctAnswer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'test_id' => 'required|exists:tests,id'
        ];
    }
}
