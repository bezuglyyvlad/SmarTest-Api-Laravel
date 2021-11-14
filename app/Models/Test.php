<?php

namespace App\Models;

use App\Helpers\TestHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsTo(ExpertTest::class);
    }

    /**
     * @return BelongsTo
     */
    public function test_category(): BelongsTo // phpcs:ignore
    {
        return $this->belongsTo(TestCategory::class);
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
     * @return float|int
     */
    public function getScoreCorrectionCoef()
    {
        return $this->max_score >= 100 ? 100 / $this->max_score : self::MAX_CORRECTION_COEF;
    }
}
