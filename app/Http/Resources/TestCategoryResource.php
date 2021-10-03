<?php

namespace App\Http\Resources;

use App\Models\TestCategory;
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
        $user = Auth::user();
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'parent_id' => $this->parent_id,
            'user' => $this->when(
                $user->isAdmin() || $user->isExpert($this->id),
                new UserResource($this->user)
            ),
            'has_children' => !!TestCategory::setParentKeyName('parent_id')::find($this->id)
                ->children()
                ->count(),
        ];
    }
}
