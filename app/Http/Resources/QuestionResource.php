<?php

namespace App\Http\Resources;

use App\Models\ExpertTest;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'quality_coef' => $this->quality_coef,
            'type' => $this->type,
            'description' => $this->when(
                User::isExpert(
                    ExpertTest::where('id', $this->expert_test_id)->first()->test_category_id
                ) || TestResult::with(['test' => function (Builder $q) {
                    $q->where('user_id', Auth::id());
                }])->where('question_id', $this->id)->count() !== 0,
                $this->description
            ), //for expert this and ancestors tests
            'image' => $this->image,
            'expert_test_id' => $this->expert_test_id,
        ];
    }
}
