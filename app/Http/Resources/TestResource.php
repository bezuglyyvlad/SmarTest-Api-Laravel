<?php

namespace App\Http\Resources;

use App\Helpers\TestHelper;
use App\Models\Test;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'score' => round($this->score, 2),
            //'max_score' => round($this->max_score, 2),
            'is_finished' => Test::findOrFail($this->id)->testIsFinished(),
            'user_id' => $this->user_id,
            'expert_test_id' => $this->expert_test_id,
            'test_category_id' => $this->expert_test_id,
            'expert_test' => new ExpertTestResource($this->whenLoaded('expert_test')),
            'test_category' => new TestCategoryResource($this->whenLoaded('test_category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'test_results' => TestResultResource::collection($this->whenLoaded('test_results'))
        ];
    }
}
