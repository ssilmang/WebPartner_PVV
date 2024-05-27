<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicateurQualiResouce;
use App\Http\Resources\StockraResouce;
class ObjectifraQualiResource extends JsonResource
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
            "ra_id"=>$this->ra_id,
            "statut"=>$this->statut,
            "commission"=>$this->commission,
            "indicateur_quali"=>IndicateurQualiResource::make($this->indicateurQuali),
            "stockra_qualis"=>StockraResource::collection($this->StockraQualis),
        ];
    }
}
