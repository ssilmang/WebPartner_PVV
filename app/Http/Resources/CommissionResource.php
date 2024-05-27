<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
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
            "commission"=>$this->commission,
            "mois"=>MoisResource::make($this->mois),
            "annee"=>SemestreResource::make($this->annee),

        ];
    }
}
