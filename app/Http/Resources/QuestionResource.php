<?php

namespace App\Http\Resources;

use App\Helpers\Image;
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
            'type' => $this->type,
            'image' => $this->image ? Image::getImageUrl('question', $this->image) : null,
            'expert_test_id' => $this->expert_test_id,
            'condComplexity' => $this->whenAppended('condComplexity')
        ];
    }
}
