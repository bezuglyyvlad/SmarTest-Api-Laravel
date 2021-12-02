<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Answer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'text',
        'is_correct',
        'question_id'
    ];
}
