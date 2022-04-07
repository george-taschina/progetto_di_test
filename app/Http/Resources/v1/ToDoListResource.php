<?php
namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\UserResource as UserResource;
use App\Http\Resources\v1\TaskResource as TaskResource;

class ToDoListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'creator' => new UserResource($this->user),
            'tasks' => TaskResource::collection($this->tasks),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
