<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ra;
use App\Models\User;
use App\Models\Objectifcc;
use App\Models\Stockcc;
use App\Models\StockccQuali;
use App\Models\CommissionccQuali;
use App\Models\CommissionccQuanti;
use App\Models\Commissionccfinale;
class Cc extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function ra()
    {
        return $this->belongsTo(Ra::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function objectifccs()
    {
        return $this->hasMany(Objectifcc::class);
    }
    public function objectif_qualis()
    {
        return $this->hasMany(ObjectifccQuali::class);
    }
    
    public function commissionccquali(){
        return  $this->hasMany(CommissionccQuali::class);
    }
    public function commissionccquanti(){
        return  $this->hasMany(CommissionccQuanti::class);
    }
    public function commissionccfinale(){
        return  $this->hasMany(Commissionccfinale::class);
    }
   
}
