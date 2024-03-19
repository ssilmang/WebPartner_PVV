<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Outil;
use App\Models\AnneeSemestre;
use App\Models\Objectra;
class Objectif extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded =["id"];
    public function outil()
    {
        return $this->belongsTo(Outil::class);
    }
    public function anneeSemestre()
    {
        return $this->belongsTo(AnneeSemestre::class);
    }
    public function objectifs()
    {
        return $this->belongsTo(Objectifra::class);
    }
}
