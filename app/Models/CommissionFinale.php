<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Annee;
use App\Models\Mois;
class CommissionFinale extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function annee()
    {
        return $this->belongsTo(Annee::class);
    }
    public function mois()
    {
        return $this->belongsTo(Mois::class);
    }
}
