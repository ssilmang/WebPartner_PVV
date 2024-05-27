<?php

namespace App\Http\Resources;

use App\Models\Drv;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    
    public function toArray(Request $request): array
    {
        $count = $this->ras->count();
        return [
            'id'=>$this->id,
            'libelle'=>$this->libelle,
            'count'=>$count,
            'prestataire_id'=>$this->prestataire_id,
            'user'=>UserResource::make($this->user),
            'ras'=>RaResource::collection($this->ras)
        ];
    }
}
