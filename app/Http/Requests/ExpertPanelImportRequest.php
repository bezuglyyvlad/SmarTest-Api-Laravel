<?php

namespace App\Http\Requests;

use App\Models\ExpertTest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ExpertPanelImportRequest extends FormRequest
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
    public function rules()
    {
        return [
            'importFile' => 'required|file|mimes:xml',
            'expert_test_id' => [
                'required',
                'exists:expert_tests,id,deleted_at,NULL'
            ],
        ];
    }
}
