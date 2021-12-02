<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ExpertTest extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'is_published',
        'test_category_id'
    ];

    /** @var string $parentKey */
    protected static $parentKey;

    /**
     * @return string
     */
    public function getParentKeyName(): string
    {
        return static::$parentKey;
    }

    /**
     * @psalm-suppress UnsafeInstantiation
     * @param string $parentKey
     * @return ExpertTest
     */
    public static function setParentKeyName(string $parentKey): ExpertTest
    {
        static::$parentKey = $parentKey;

        return new static();
    }

    /**
     * @param int $expertTestId
     * @return array
     */
    public static function getExpertTestHistoryRecordIds(int $expertTestId): array
    {
        return ExpertTest
            ::setParentKeyName('modified_records_parent_id')
            ::findOrFail($expertTestId)
            ->ancestorsAndSelf()
            ->withTrashed()
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function validateExpertTestNotPublished(): bool
    {
        if ($this->is_published) {
            throw ValidationException::withMessages([
                'expert_test_id' => [
                    'Неможливо виконати операцію, якщо тест відкрито.'
                ]
            ]);
        }
        return true;
    }
}
