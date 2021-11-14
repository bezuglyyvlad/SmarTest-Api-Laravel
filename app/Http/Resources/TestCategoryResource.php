<?php

namespace App\Http\Resources;

use App\Models\TestCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'parent_id' => $this->parent_id,
            'user' => $this->when(
                User::isAdmin() || User::isExpert($this->id),
                new UserResource($this->whenLoaded('user'))
            ),
            'has_children' => !!TestCategory::setParentKeyName('parent_id')::find($this->id)
                ->children()
                ->count(),
        ];
    }
}
