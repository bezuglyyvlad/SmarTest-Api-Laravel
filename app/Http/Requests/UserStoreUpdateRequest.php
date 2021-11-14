<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Ця електронна адреса вже зайнята.'
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
            'name' => 'required|string|min:2|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore(Auth::id())
            ],
            'password' => 'required|string|min:6|max:255|confirmed',
        ];
    }
}
