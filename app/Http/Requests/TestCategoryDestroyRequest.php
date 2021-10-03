<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/** @psalm-suppress PropertyNotSetInConstructor */
class TestCategoryDestroyRequest extends FormRequest
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
        /**
         * @psalm-suppress PossiblyNullPropertyFetch
         * @psalm-suppress PossiblyInvalidPropertyFetch
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress PossiblyNullReference
         */
        return $this->route('test_category')->active_record === 1
            && ($user->isAdmin() || $user->isExpert($this->route('test_category')->id));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
