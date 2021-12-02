<?php

namespace App\Http\Requests;

use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $expertTest = ExpertTest::findOrFail($this->question->expert_test_id);
        return User::isExpert($expertTest->test_category_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $typeOfQuestions = Question::TYPE_OF_QUESTIONS;
        return [
            'text' => [
                'required',
                'string',
                'max:5000'
            ],
            'complexity' => ['required', 'integer', 'between:0,10'],
            'significance' => ['required', 'integer', 'between:0,10'],
            'relevance' => ['required', 'integer', 'between:0,10'],
            'type' => ['required', 'numeric', "between:1,{$typeOfQuestions}"],
            'description' => ['nullable', 'string', 'max:10000']
        ];
    }
}
