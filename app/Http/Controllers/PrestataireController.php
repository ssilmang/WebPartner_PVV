<?php

namespace App\Http\Controllers;

use App\Http\Resources\PrestataireResource;
use App\Models\Prestataire;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PrestataireController extends Controller
{
    public function index(Request $request, $libelle)
    {
        try{
            $prestataire = "";
            if(strtolower($libelle)!=="permanents"){
                $prestataire = Prestataire::where(['libelle'=>$libelle])->get();
            }
           else{
            $prestataire = Prestataire::all();
           }
            return response()->json([
                'statut'=>Response::HTTP_OK,
                'message'=>'all',
                'data'=>[
                    "prestataires"=>PrestataireResource::collection($prestataire)
                ]
            ]);

        }catch(QueryException $th)
        {
            return response()->json([
                'statut'=>Response::HTTP_OK,
                'message'=>'erreur',
                'data'=>$th->getMessage()
            ]);
        }
    }
   
   
    public function store(Request $request)
    {
        try {
            return DB::transaction(function() use($request){
                $request->validate([
                    'libelle'=>'required|unique:prestataires,libelle',
                ]);
                $prestat = Prestataire::create($request->all());
                return response()->json([
                    "statut"=>Response::HTTP_OK,
                    "message"=>"succès",
                    "data"=>$prestat
                ]);
            });
    
        } catch (QueryException $e) {
            return response()->json([
                "statut"=>Response::HTTP_OK,
                "message"=>"erreur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
}
