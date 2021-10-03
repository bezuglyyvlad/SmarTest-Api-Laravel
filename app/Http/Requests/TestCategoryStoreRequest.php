<?php

namespace App\Http\Requests;

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
        $user = Auth::user();
        // admin or ancestor expert
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return $user->isAdmin() || $this->parent_id && $user->isExpert($this->parent_id);
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
                // unique among active records and nesting level
                Rule::unique('test_categories')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where('parent_id', $this->parent_id);
                    }
                )
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
