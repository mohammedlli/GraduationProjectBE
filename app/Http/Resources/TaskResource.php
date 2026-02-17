<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'question' =>$this->question,
            'description'=>$this->description,
          'stage' => new StageResource($this->stage),
          'language'=> $this->language,
'answers' => AnswerResource::collection($this->answers)

        ];
    }
}
