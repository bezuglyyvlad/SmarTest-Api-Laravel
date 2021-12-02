<?php

namespace App\Models;

use App\Helpers\TestHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Test extends Model
{
    use HasFactory;

    public $timestamps = false;
    public const MAX_CORRECTION_COEF = 100 / 109.99;

    /**
     * @return BelongsTo
     */
    public function expert_test(): BelongsTo // phpcs:ignore
    {
        return $this->belongsTo(ExpertTest::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function test_category(): BelongsTo // phpcs:ignore
    {
        return $this->belongsTo(TestCategory::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function test_results(): HasMany // phpcs:ignore
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * @return bool
     */
    public function testIsFinished(): bool
    {
        return Carbon::now()->gte(Carbon::createFromFormat('c', $this->finish_date));
    }

    /**
     * @param int $id
     * @param bool $onlyIsPublishedChanged
     * @return \Illuminate\Support\Collection
     * @throws ValidationException
     */
    public static function getActiveTestIdsByExpertTest(int $id): \Illuminate\Support\Collection
    {
        return Test::where('expert_test_id', $id)
            ->get()
            ->filter(function (Test $test) {
                return !$test->testIsFinished();
            })->pluck('id');
    }

    public static function validateNobodyPassesExpertTest($errorCondition): bool
    {
        if ($errorCondition) {
            throw ValidationException::withMessages([
                'expert_test_id' => [
                    'Хтось ще проходить тест. Закрийте його та дочекайтеся поки всі закінчать, після чого виконайте операцію знову.'
                ]
            ]);
        }
        return true;
    }
}
