<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            /** @phpstan-ignore-next-line */
            'id' => $this->id,
            /** @phpstan-ignore-next-line */
            'title' => $this->title,
            /** @phpstan-ignore-next-line */
            'parent_id' => $this->parent_id,
            /** @phpstan-ignore-next-line */
            'user_id' => $this->user_id,
        ];
    }
}
