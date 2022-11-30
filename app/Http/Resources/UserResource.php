<?php

namespace App\Http\Resources;

use App\Models\UserType;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'status'    => $this->status,
            'user_type' => $this->UserType,
            'user_details' => $this->UserDetails
        ];
    }
}
