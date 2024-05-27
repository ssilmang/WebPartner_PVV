<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Annee;
use App\Models\Mois;
use App\Models\Objectifcc;

class Stockcc extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function mois()
    {
        return $this->belongsTo(Mois::class);
    }
    public function annee()
    {
        return $this->belongsTo(Annee::class);
    }
    public function objectifcc()
    {
        return $this->belongsTo(Objectifcc::class);
    }
}
