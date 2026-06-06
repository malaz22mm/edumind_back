<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentSkillProgressResource extends JsonResource
{
    public function toArray(Request $request): array|null
    {
        return [
            'id' => $this->id??null,
            'skill_id' => $this->skill_id??null,
            'status' => $this->status, // not_started | in_progress | completed
            'score' => $this->score,
            'attempts_count' => $this->attempts_count,
            'skill' => $this->skill ? new SkillResource($this->skill) : null,
        ];
    }
}