<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ra;
use App\Models\Mois;
use App\Models\Annee;
class CommissionccQuali extends Model
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
