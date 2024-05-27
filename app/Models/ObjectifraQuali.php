<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\indicateurQuali;
use App\Models\ObjectifccQuali;
use App\Models\StockraQuali;
class ObjectifraQuali extends Model
{
    use HasFactory;
    protected $guarded =["id"];
    public function indicateurQuali()
    {
        return $this->belongsTo(IndicateurQuali::class);
    }
    public function objectifccQualis()
    {
        return $this->hasMany(ObjectifccQuali::class);
    }
    public function StockraQualis()
    {
        return $this->hasMany(StockraQuali::class,'objectira_quali_id');
    }
   
}
