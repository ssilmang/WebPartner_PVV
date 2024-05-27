<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annee;
use App\Models\Objectif;
use App\Models\Semestre;
use App\Models\AnneeSemestre;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ObjectifResource;
use Illuminate\Database\QueryException;

class ObjectifController extends Controller
{
    public function index(Request $request )
    {
        try{
            $objectifs=Objectif::all();
            return response()->json([
                "statut"=>200,
                "message"=>"succès",
                "data"=>ObjectifResource::collection($objectifs)
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"erreur",
                "data"=>$e->getMesssage(),
            ]);
        }
    }
    public function store(Request $request)
    {
        try{
            return DB::transaction(function() use($request){
                $all= Annee::query()->update(["etat"=>0]);
                $annee = Annee::firstOrNew(["libelle"=>$request->annee]);
                $annee->etat=1;
                $annee->save();
                if(!$annee->exists){
                    $annee->save();
                }
                     
                foreach ($request->semestre as $key => $value) {
                    $idsAnneeSemestre = AnneeSemestre::where('annee_id', $annee->id)
                    ->where('semestre_id', $value)
                    ->first();
                    if(!$idsAnneeSemestre){
                        $annee->semestres()->attach($value);
                    }
                }
                Semestre::query()->update(["etat"=>0]);
                Semestre::whereIn('id', $request->semestre)->update(['etat' => 1]);
                foreach ($request->objectifs as $key => $value) {
                    $obj =  Objectif::firstOrNew(["outil_id"=>$value['outil_id']]);
                    if (!$obj->exists) {
                        $obj->annee_id = $annee->id;
                        $obj->value = $value["value"];
                        $obj->outil_id =$value["outil_id"];
                        $obj->save();
                    }
                    $obj->value = $value["value"];
                    $obj->outil_id =$value["outil_id"];
                    $obj->save();

                }
                    
                return response()->json([
                    "statut"=>200,
                    "message"=>"succès",
                    "data"=>$annee,
                ]);
            });
                
        
        }catch(QueryException $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"erreur lors de l'ajout",
                "data"=>$e->getMessage(),
            ]);
        }
    }
    public function delete(Request $request, $id,$annee)
    {
        $annee_id = Annee::where('libelle',$annee)->first();
        if($annee_id){
            $objectif = Objectif::where('outil_id',$id)->where('annee_id',$annee_id->id)->first();
            if($objectif){
                $objectif->delete();
            }
            return response()->json([
                "statut"=>200,
                "message"=>"cette outil de l'objectif est supprimmé avec succès ",
                "data"=>$objectif
            ]);
        }
    }
}
