<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SemestreResource;
use App\Http\Resources\ObjectifccResource;

class StockccResource extends JsonResource
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
            "realisation"=>$this->realisation,
            "value"=>$this->value,
            "taux"=>$this->taux,
            "commission"=>$this->commission,
            "statut"=>$this->statut,
            "mois"=>SemestreResource::make($this->mois),
            "annee"=>SemestreResource::make($this->annee),
            // "objectifcc"=>ObjectifccResource::make($this->objectifcc)
        ];
    }
}
