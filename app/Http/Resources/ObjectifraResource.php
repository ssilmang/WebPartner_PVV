<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ObjectifResource;
class ObjectifraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "value"=>$this->value,
            "realisation"=>$this->realisation,
            "taux"=>$this->taux,
            "objectifs"=>ObjectifResource::make($this->objectif),
            
        ];
    }
}
