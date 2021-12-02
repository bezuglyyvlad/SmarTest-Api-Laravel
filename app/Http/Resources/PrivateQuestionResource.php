<?php

namespace App\Http\Resources;

use App\Helpers\Image;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateQuestionResource extends JsonResource
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
            'complexity' => $this->complexity,
            'significance' => $this->significance,
            'relevance' => $this->relevance,
            'quality_coef' => $this->quality_coef,
            'type' => $this->type,
            'description' => $this->description,
            'image' => $this->image ? Image::getImageUrl('question', $this->image) : null,
            'expert_test_id' => $this->expert_test_id,
            'condComplexity' => $this->whenAppended('condComplexity'),
            'answers' => PrivateAnswerResource::collection($this->whenLoaded('answers'))
        ];
    }
}
