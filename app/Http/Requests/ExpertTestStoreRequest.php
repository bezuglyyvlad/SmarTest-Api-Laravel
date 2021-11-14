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
    public function authorize()
    {
        return User::isExpert($this->test_category_id);
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
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // unique among active records
                Rule::unique('expert_tests')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where('active_record', 1);
                    }
                )
            ],
//            'is_published' => ['boolean', new AllComplexityPresent(null)],
            'test_category_id' => [
                'required',
                // exists among test_categories_id and active records
                Rule::exists('test_categories', 'id')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where('active_record', 1);
                    }
                )
            ]
        ];
    }
}
