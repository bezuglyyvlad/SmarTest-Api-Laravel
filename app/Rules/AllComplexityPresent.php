<?php

namespace App\Rules;

use App\Models\Question;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AllComplexityPresent implements Rule
{
    /**
     * @var int
     */
    private $expert_test_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->expert_test_id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value) {
            $countOfEasyQuestions = Question::where(
                [
                    'expert_test_id' => $this->expert_test_id, 'active_record' => 1
                ]
            )->get()->groupBy('complexity')->map(function (Collection $i): int {
                return $i->count();
            })->count();
            return $countOfEasyQuestions === 3;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return
            'Тест не може бути відкритий.
            Повинні бути питання всіх складностей.';
    }
}
