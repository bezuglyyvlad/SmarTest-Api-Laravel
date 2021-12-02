<?php

namespace App\Http\Requests;

use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AnswerIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $question = Question::findOrFail($this->question_id);
        $expertTest = ExpertTest::findOrFail($question->expert_test_id);
        return User::isExpert($expertTest->test_category_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question_id' => 'required|exists:questions,id,deleted_at,NULL'
        ];
    }
}
