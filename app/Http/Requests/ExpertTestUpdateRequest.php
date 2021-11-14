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
        return $this->expert_test->active_record === 1 &&
            User::isExpert($this->expert_test->test_category_id);
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'Ця назва вже зайнята.'
        ];
    }

    /**
     * @param int $id
     * @param bool $onlyIsPublishedChanged
     * @return \Illuminate\Support\Collection
     * @throws ValidationException
     */
    public static function validateExpertTestId(int $id, bool $onlyIsPublishedChanged): \Illuminate\Support\Collection
    {
        $activeTestId = Test::where('expert_test_id', $id)
            ->get()
            ->filter(function (Test $test) {
                return !$test->testIsFinished();
            })->pluck('id');
        // deny if users passing test
        if ($activeTestId->count() > 0 && !$onlyIsPublishedChanged) {
            throw ValidationException::withMessages([
                'id' => [
                    'Хтось ще проходить тест. Закрийте його,
                    щоб більше ніхто його не розпочав та
                    дочекайтеся поки всі його закінчать.'
                ]
            ]);
        }
        return $activeTestId;
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
                // unique among active records
                Rule::unique('expert_tests')->where(
                /**
                 * @psalm-suppress MissingClosureReturnType
                 * @psalm-suppress MissingClosureParamType
                 */
                    function ($query) {
                        return $query->where('active_record', 1);
                    }
                )->ignore($this->expert_test->id)
            ],
            'is_published' => ['boolean', new AllComplexityPresent($this->expert_test->id)],
        ];
    }
}
