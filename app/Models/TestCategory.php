<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TestCategory extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'parent_id',
        'user_id'
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
     * @return TestCategory
     */
    public static function setParentKeyName(string $parentKey): TestCategory
    {
        static::$parentKey = $parentKey;

        return new static();
    }

    /**
     * @return array
     */
    public function getCustomPaths(): array
    {
        return [
            [
                'name' => 'custom_path',
                'column' => 'title',
                'separator' => '/',
            ],
        ];
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param int $testCategoryId
     * @return array
     */
    public static function getHistoryRecordIds(int $testCategoryId): array
    {
        return TestCategory
            ::setParentKeyName('modified_records_parent_id')
            ->findOrFail($testCategoryId)
            ->ancestorsAndSelf()
            ->withTrashed()
            ->pluck('id')
            ->toArray();
    }
}
