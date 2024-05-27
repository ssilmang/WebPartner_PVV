<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ObjectifraResource;
use App\Http\Resources\StockccResource;
use App\Http\Resources\SemestreResource;
class StockraResource extends JsonResource
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
            "taux"=>$this->taux,
            "value"=>$this->value,
            "commission"=>$this->commission,
            "mois"=>SemestreResource::make($this->mois),
            "annee"=>SemestreResource::make($this->annee),
            "objectifra"=>$this->objectifra_id,
            // "stockccs"=>StockccResource::collection($this->stockccs)
            
        ];
    }
}
