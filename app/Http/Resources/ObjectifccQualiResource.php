<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicateurQualiResource;
use App\Http\Resources\ObjectifraQualiResource;
use App\Http\Resources\StockccResource;
class ObjectifccQualiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "id"=>$this->id,
            "realisation"=>$this->realisation,
            "taux"=>$this->taux,
            "cc_id"=>$this->cc_id,
            "statut"=>$this->statut,
            "commission"=>$this->commission,
            "objectifra_quali_id"=>ObjectifraQualiResource::make($this->objectifraQuali),
            "stockcc_qualis"=>StockccResource::collection($this->StockccQualis),
        ];
    }
}
