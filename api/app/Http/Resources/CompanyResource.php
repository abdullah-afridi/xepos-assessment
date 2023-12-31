<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'email' => $this->email,
            'logo' => $this->logo,
            'website' => $this->website,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'creator' => $this->creator,
            'employees' => $this->employees ?? null,
            
        ];
    }
}
