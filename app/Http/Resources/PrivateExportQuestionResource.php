<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateExportQuestionResource extends JsonResource
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
            'text' => $this->text,
            'description' => $this->when(!is_null($this->description), $this->description),
            'complexity' => $this->complexity,
            'significance' => $this->significance,
            'relevance' => $this->relevance,
            'type' => $this->type,
            'answers' => PrivateExportAnswerResource::collection($this->whenLoaded('answers'))
        ];
    }
}
