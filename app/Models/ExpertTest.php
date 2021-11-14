<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ExpertTest extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

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
}
