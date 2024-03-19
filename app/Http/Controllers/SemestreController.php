<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;
use App\Models\Semestre;
use Exception;
class SemestreController extends Controller
{
    public function store(Request $request){
        try{
            return DB::transaction(function() use($request){
                $annee = Semestre::create([
                    "libelle"=>$request->libelle,
                ]);
                return response()->json([
                    "statut"=>200,
                    "message"=>"l'année est ajouté avec succès",
                    "data"=>$annee
                ]);
            });
        }catch(Exception $e){
            return response()->json([
                "statut"=>221,
                "message"=>"Erreur lors de l'ajout de l'année",
                "data"=>$e->getMessage(),
            ]);
        };
    }
}
