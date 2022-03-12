<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'item'              => new GiftResource($this->attachable),
            'qty'               => $this->qty,
            'status'            => $this->status,
            'createdAt'         => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt'         => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
