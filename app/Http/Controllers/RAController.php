<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ra;
use App\Models\Cc;
use App\Http\Resources\RaResource;
class RAController extends Controller
{
    public function index(){
        $ras = Ra::all();
        // $ccs = Cc::whereIn('ra_id',$ras->id)->get();
        return response()->json([
            "statut"=>200,
            "message"=>"all",
            "data"=>[
                "ras"=>RaResource::collection($ras)
            ],
        ]);
    }
}
