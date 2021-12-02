<?php

namespace App\Http\Requests;

use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpertPanelStoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $testCategoryId = ExpertTest::findOrFail($this->expert_test_id)->test_category_id;
        return User::isExpert($testCategoryId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => 'nullable|image|max:512',
            'expert_test_id' => [
                'required',
                'exists:expert_tests,id,deleted_at,NULL'
            ],
            'answers' => ['required', 'array', 'min:2'],
            'answers.*.text' => ['required', 'string', 'max:5000', 'distinct'],
            'answers.*.is_correct' => ['required', 'boolean']
        ];
    }
}
