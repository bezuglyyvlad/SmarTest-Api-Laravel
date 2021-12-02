<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** @psalm-suppress PropertyNotSetInConstructor */
class TestCategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
//        // swap subcategory
//        // if no loop
//        // admin for basic category
//        // or parent_id ancestor expert
//        return $this->test_category->id !== (int)$this->parent_id &&
//            ((User::isAdmin() && !$this->parent_id) ||
//            ($this->test_category->parent_id &&
//                User::isExpert($this->test_category->id) &&
//                $this->parent_id &&
//                User::isExpert($this->parent_id)));
        // no swap subcategory
        return (User::isAdmin() && !$this->test_category->parent_id) ||
            ($this->test_category->parent_id &&
                User::isExpert($this->test_category->id));
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title.unique' => 'Ця назва вже зайнята.',
            'user_email.exists' =>
                'Вибрана електронна адреса користувача недійсна.'
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
                // unique among nesting level ignore this resource
                Rule::unique('test_categories')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where(['parent_id' => $this->test_category->parent_id]);
                    }
                )->ignore($this->test_category->id)->whereNull('deleted_at')
            ],
//            'parent_id' => [
//                'nullable',
//                'exists:test_categories,id,deleted_at,NULL'
//            ],
            'user_email' => 'nullable|string|email|max:255|exists:users,email',
        ];
    }
}
