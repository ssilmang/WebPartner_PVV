<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Annee;
use Exception;
class AnneeController extends Controller
{
    public function store(Request $request){
        try{
            return DB::transaction(function() use($request){
                $annee = Annee::create([
                    "libelle"=>$request->libelle,
                ]);
                $annee->semestres()->attach($request->semestre);

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
