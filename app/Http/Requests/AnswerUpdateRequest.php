<?php

namespace App\Http\Requests;

use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnswerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $question = Question::findOrFail($this->answer->question_id);
        $expertTest = ExpertTest::findOrFail($question->expert_test_id);
        return User::isExpert($expertTest->test_category_id);
    }

    public function messages(): array
    {
        return [
            'text.unique' => 'Вже є відповідь з таким текстом.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => [
                'required',
                'string',
                'max:5000',
                // unique among current question answers
                Rule::unique('answers')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where(['question_id' => $this->answer->question_id]);
                    }
                )->ignore($this->answer->id)->whereNull('deleted_at')
            ],
            'is_correct' => ['required', 'boolean']
        ];
    }
}
