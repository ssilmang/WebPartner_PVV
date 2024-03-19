<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Objectif;
class Objectifra extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function objectif()
    {
        return $this->belongsTo(Objectif::class);
    }
}
