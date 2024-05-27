<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RaResource;
use App\Http\Resources\StockccResource;
use App\Http\Resources\ObjectifraResource;
class ObjectifccResource extends JsonResource
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
            "objectifra_id"=>ObjectifraResource::make($this->objectifra),
            "stockcc"=>StockccResource::collection($this->stockccs),
            "cc_id"=>$this->cc_id,
        ];
    }
}
