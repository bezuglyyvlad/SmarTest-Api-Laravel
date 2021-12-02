<?php

namespace App\Http\Requests;

use App\Helpers\TestHelper;
use App\Models\Test;
use App\Models\User;
use App\Rules\AllComplexityPresent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/** @psalm-suppress PropertyNotSetInConstructor */
class ExpertTestUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return User::isExpert($this->expert_test->test_category_id);
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
                        return $query->where('test_category_id', $this->expert_test->test_category_id);
                    }
                )->ignore($this->expert_test->id)->whereNull('deleted_at')
            ],
            'is_published' => ['boolean', new AllComplexityPresent($this->expert_test->id)],
        ];
    }
}
