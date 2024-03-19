<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndicateurQuanti;
use App\Models\Outil;
use App\Models\Semestre;
use App\Models\Objectif;
use App\Models\Role;
use App\Models\Ra;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OutilResource;
use App\Http\Resources\SemestreResource;
use App\Http\Resources\ObjectifResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RaResource;

use Exception;



class OutilController extends Controller
{
    public function store(Request $request){
        try{
            return DB::transaction(function() use($request){
                $indicateurs = IndicateurQuanti::all();
                foreach ($indicateurs as $key => $value) {
                    $outil = new Outil();
                    $outil->indicateur_quanti_id=$value->id;
                    $outil->save();
                };
                return response()->json([
                    "statut"=>200,
                    "message"=>"Outil ajouter avec succès",
                    "data"=>$outil
                ]);
            });
        }catch(Exception $e){
            return response()->json([
                "statut"=>221,
                "message"=>"Erreur lors de l'ajout outil",
                "data"=>$e->getMessage()
            ]);
        };
    }
    public function index(Request $request)
    {
        $outils = Outil::all();
        $semestre = Semestre::all();
        $objectifs=Objectif::all();
        $roles =Role::all();
        $ras =Ra::all();
        return response()->json([
            "statut"=>200,
            "message"=>"All",
            "data"=>[
                "outil"=>OutilResource::collection($outils),
                "semestre"=>SemestreResource::collection($semestre),
                "objectifs"=>ObjectifResource::collection($objectifs),
                "roles"=>RoleResource::collection($roles),
                'ras'=>RaResource::collection($ras),
            ]
            
        ]);
    }
}
