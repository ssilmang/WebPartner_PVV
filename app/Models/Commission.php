<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Annee;
use App\Models\Mois;
class Commission extends Model
{
    use HasFactory,SoftDeletes;
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
