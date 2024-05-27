<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockraQuali;
use App\Models\Mois;
use App\Models\Annee;

class StockccQuali extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function stockra_qualis(){
        return $this->belongsTo(StockraQuali::class);
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
