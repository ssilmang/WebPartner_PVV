<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ObjectifccResource;
use App\Http\Resources\CommissionResource;
class CcResource extends JsonResource
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
            "user"=>UserResource::make($this->user),
            "objectif"=>ObjectifccResource::collection($this->objectifccs),
            "objectif_quali"=>ObjectifccQualiResource::collection($this->objectif_qualis),
            "commissionquali"=>CommissionResource::collection($this->commissionccquali),
            "commissionquanti"=>CommissionResource::collection($this->commissionccquanti),
            "commissionfinale"=>CommissionResource::collection($this->commissionccfinale),
            "ra_id"=>$this->ra_id,
        ];
    }
}
