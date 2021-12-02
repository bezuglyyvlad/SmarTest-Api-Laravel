<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ExpertPanelBreadcrumbsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->test_category_id && User::isExpert($this->test_category_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'test_category_id' => 'required|exists:test_categories,id,deleted_at,NULL',
            'expert_test_id' => 'nullable|exists:expert_tests,id,deleted_at,NULL'
        ];
    }
}
