<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Objectifcc;
use App\Models\Objectifra;

class Objectifcc extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function objectifccs(){
        return $this->hasMany(Objectifcc::class);
    }
    public function objectifra(){
        return $this->belongsTo(Objectifra::class);
    }
}
