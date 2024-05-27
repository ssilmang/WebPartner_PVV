<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Objectif;
use App\Models\Stockra;
class Objectifra extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function objectif()
    {
        return $this->belongsTo(Objectif::class);
    }
    public function stockras(){
        return $this->hasMany(Stockra::class);
    }
}
