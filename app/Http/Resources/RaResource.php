<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CcResource;
use App\Http\Resources\ObjectifraResource;
class RaResource extends JsonResource
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
            "nom_agence"=>$this->nom_agence,
            "adresse_agence"=>$this->adresse_agence,
            "user"=>userResource::make($this->user),
            "statut"=>$this->statut,
            "objectifs"=>ObjectifraResource::collection($this->objectifs),
            "ccs"=>CcResource::collection($this->ccs)
        ];
    }
}
