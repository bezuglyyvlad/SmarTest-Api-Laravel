<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serial_number,
            'is_correct_answer' => $this->is_correct_answer,
            'score' => $this->score,
            'user_answer' => $this->user_answer,
            'answer_ids' => $this->answer_ids,
            'test_id' => $this->test_id,
            'question_id' => $this->question_id,
            'question' =>  new QuestionResource($this->whenLoaded('question'))
        ];
    }
}
