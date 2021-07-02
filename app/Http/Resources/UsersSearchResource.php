<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**Searches don't need created at and updated at time */
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

        ];;
    }
}
