<?php

namespace App\Http\Resources;

use App\Helpers\TestHelper;
use App\Models\Test;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ExpertTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $test = Test::where(['expert_test_id' => $this->id, 'user_id' => Auth::id()])
            ->orderByDesc('id')->first();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_published' => $this->is_published,
            'test_category_id' => $this->test_category_id,
            // for continue test
            'test' => $this->when(
                $test && !$test->testIsFinished(),
                new TestResource($test)
            ),
            'test_category' => new TestCategoryResource($this->whenLoaded('test_category'))
        ];
    }
}
