<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\AllComplexityPresent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** @psalm-suppress PropertyNotSetInConstructor */
class ExpertTestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->test_category_id && User::isExpert($this->test_category_id);
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'Ця назва вже зайнята.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expert_tests')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where('test_category_id', $this->test_category_id);
                    }
                )->whereNull('deleted_at')
            ],
            'is_published' => ['boolean', Rule::in([0])],
            'test_category_id' => 'required|exists:test_categories,id,deleted_at,NULL'
        ];
    }
}
