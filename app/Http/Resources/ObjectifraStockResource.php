<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StockccResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\SemestreResource;
class ObjectifraStockResource extends JsonResource
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
            "commission"=>$this->commission,
            "mois"=>SemestreResource::make($this->mois),
            "annee"=>SemestreResource::make($this->annee)
        ];
    }
}
