<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\indicateurQuali;
use App\Models\ObjectifraQuali;
use App\Models\StockccQuali;
class ObjectifccQuali extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function objectifraQuali()
    {
        return $this->belongsTo(ObjectifraQuali::class);
    }
    public function StockccQualis()
    {
        return $this->hasMany(StockccQuali::class);
    }
}
