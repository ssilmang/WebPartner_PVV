<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Objectifra;
use App\Models\Stockcc;
use App\Models\Mois;
use App\Models\Annee;

class Stockra extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function objectifra()
    {
        return $this->belongsTo(Objectifra::class);
    }
    public function stockccs(){
        return $this->hasMany(Stockcc::class);
    }
    public function mois()
    {
        return $this->belongsTo(Mois::class);
    }
    public function annee()
    {
        return $this->belongsTo(Annee::class);
    }
}
