<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
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
            'file_where_calling' => $this->file_where_calling,
            'data' => $this->data,
            'file_where_defined' => $this->file_where_defined,
            'class' => $this->class,
            'changed_properties' => $this->changed_properties,
            'all_properties' => $this->all_properties,
            'calling_line' => $this->calling_line,
            'level' => $this->level
        ];
    }
}
