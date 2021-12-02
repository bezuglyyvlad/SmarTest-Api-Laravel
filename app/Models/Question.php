<?php

namespace App\Models;

use App\Helpers\Image;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'text',
        'complexity',
        'significance',
        'relevance',
        'type',
        'description',
        'image',
        'expert_test_id'
    ];

    public const LOWER_LIMIT_QUALITY_COEF = 0.5;
    public const UPPER_LIMIT_QUALITY_COEF = 1.5;
    public const GAIN_OF_QUALITY_COEF = (self::UPPER_LIMIT_QUALITY_COEF - self::LOWER_LIMIT_QUALITY_COEF) / 3;

    public const LOWER_EASY_QUESTION_COEF = self::LOWER_LIMIT_QUALITY_COEF;
    public const UPPER_EASY_QUESTION_COEF = self::LOWER_LIMIT_QUALITY_COEF + self::GAIN_OF_QUALITY_COEF;
    public const LOWER_MED_QUESTION_COEF = self::UPPER_EASY_QUESTION_COEF + 0.001;
    public const UPPER_MED_QUESTION_COEF = self::LOWER_LIMIT_QUALITY_COEF + self::GAIN_OF_QUALITY_COEF * 2;
    public const LOWER_HARD_QUESTION_COEF = self::UPPER_MED_QUESTION_COEF + 0.001;
    public const UPPER_HARD_QUESTION_COEF = self::LOWER_LIMIT_QUALITY_COEF + self::GAIN_OF_QUALITY_COEF * 3;

    public const BASIC_POINTS = 6.66;

    public const TYPES_WITH_ONE_ASWER = [1];
    public const TYPE_OF_QUESTIONS = 2;

    /**
     * @return int
     */
    public function getCondComplexityAttribute(): int
    {
        if (
            $this->attributes['quality_coef'] >= Question::LOWER_EASY_QUESTION_COEF &&
            $this->attributes['quality_coef'] <= Question::UPPER_EASY_QUESTION_COEF
        ) {
            return 1;
        } elseif (
            $this->attributes['quality_coef'] >= Question::LOWER_MED_QUESTION_COEF &&
            $this->attributes['quality_coef'] <= Question::UPPER_MED_QUESTION_COEF
        ) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * @param float $score
     * @return array
     */
    public static function selectCoefRange(float $score): array
    {
        $coefRange = [self::LOWER_EASY_QUESTION_COEF, self::UPPER_EASY_QUESTION_COEF];
        if ($score >= 64 && $score < 82) {
            $coefRange = [self::LOWER_MED_QUESTION_COEF, self::UPPER_MED_QUESTION_COEF];
        } elseif ($score >= 82) {
            $coefRange = [self::LOWER_HARD_QUESTION_COEF, self::UPPER_HARD_QUESTION_COEF];
        }
        return $coefRange;
    }

    /**
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return float
     */
    public function getQualityCoefByFuzzyLogic(): float
    {
        $criteriaArray = [
            'complexity' => $this->complexity,
            'significance' => $this->significance,
            'relevance' => $this->relevance
        ];

        $similarQuestion = Question::where($criteriaArray)->first();
        $qualityCoef = $similarQuestion ? $similarQuestion->quality_coef : null;

        if (!$qualityCoef) {
            $process = new Process(
                ['python', 'PythonScripts/fuzzyLogic.py'],
                null,
                $criteriaArray
            );
            $process->run();

            // error handling
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return (float)$process->getOutput();
        }

        return (float)$qualityCoef;
    }
}
