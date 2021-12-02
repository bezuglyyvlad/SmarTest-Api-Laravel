<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** @psalm-suppress PropertyNotSetInConstructor */
class TestCategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // admin for basic category or ancestor expert for nested
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return (User::isAdmin() && !$this->parent_id) ||
            ($this->parent_id && User::isExpert($this->parent_id));
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title.unique' => 'Ця назва вже зайнята.',
            'user_email.exists' =>
                'Вибрана електронна
                адреса користувача недійсна.'
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
                // unique among nesting level
                Rule::unique('test_categories')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where(['parent_id' => $this->parent_id]);
                    }
                )->whereNull('deleted_at')
            ],
            'parent_id' => [
                'nullable',
                'exists:test_categories,id,deleted_at,NULL'
            ],
            'user_email' => 'nullable|string|email|max:255|exists:users,email',
        ];
    }
}
