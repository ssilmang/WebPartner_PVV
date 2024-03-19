<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Cc;
use App\Models\Objectifcc;
use App\Models\Objectifra;

class Ra extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded =['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ccs(){
        return $this->hasMany(Cc::class);
    }
    public function objectifcc()
    {
        return $this->belongsTo(Objectifcc::class);
    }
    public function objectifs()
    {
        return $this->hasMany(Objectifra::class);
    }
}
