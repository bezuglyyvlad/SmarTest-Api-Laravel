<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            /** @phpstan-ignore-next-line */
            'email' => $this->email,
        ];
    }
}
