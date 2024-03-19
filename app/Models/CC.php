<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ra;
use App\Models\User;
use App\Models\Objectifcc;
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
}
