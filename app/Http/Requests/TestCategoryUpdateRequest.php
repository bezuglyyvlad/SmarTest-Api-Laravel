<?php

namespace App\Http\Requests;

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
        $user = Auth::user();
        // active record and admin or this category expert
        // or parent_id ancestor expert (if move to not your category)
        /**
         * @psalm-suppress PossiblyNullPropertyFetch
         * @psalm-suppress PossiblyInvalidPropertyFetch
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return $this->route('test_category')->active_record === 1
            && (
                $user->isAdmin()
                || $user->isExpert($this->route('test_category')->id)
                || $this->parent_id
                && $user->isExpert($this->parent_id)
            );
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
                // unique among active records and nesting level ignore this resource
                Rule::unique('test_categories')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where(['parent_id' => $this->parent_id, 'active_record' => 1]);
                    }
                )->ignore($this->test_category->id)
            ],
            'parent_id' => [
                'nullable',
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
            ],
            'user_email' => 'nullable|string|email|max:255|exists:users,email',
        ];
    }
}
