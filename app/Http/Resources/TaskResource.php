<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $description
 * @property-read Carbon|null $completed_at
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
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
            'id'         => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'completed'    => (bool)$this->completed_at,
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString()
        ];
    }
}
