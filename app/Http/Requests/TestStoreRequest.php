<?php

namespace App\Http\Requests;

use App\Helpers\TestHelper;
use App\Models\Test;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** @psalm-suppress PropertyNotSetInConstructor */
class TestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $test = Test::where(['expert_test_id' => $this->expert_test_id, 'user_id' => Auth::id()])
            ->orderByDesc('id')
            ->first();

        return !$test || $test->testIsFinished();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'expert_test_id' => [
                'required',
                // exists among test_categories_id and active records
                Rule::exists('expert_tests', 'id')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where([
                            'deleted_at' => null,
                            'active_record' => 1,
                            'is_published' => 1,
                        ]);
                    }
                )
            ]
        ];
    }
}
