<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stockcc;
use App\Models\Objectifra;

class Objectifcc extends Model
{
    use HasFactory;
    protected $guarded=["id"];
   
    public function objectifra(){
        return $this->belongsTo(Objectifra::class);
    }
    public function stockccs(){
        return $this->hasMany(Stockcc::class);
    }
}
