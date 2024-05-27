<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Models\IndicateurQuali;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
class IndicateurQualiController extends Controller
{
    public function store(Request $request)
    {

        try
        {
            return DB::transaction(function() use($request){

                $quali =new IndicateurQuali();
                $quali->indicateur = $request->indicateur;
                $quali->poids_RA = $request->poids_RA;
                $quali->poids_CC = $request->poids_CC;
                // $quali->poids_SADI = $request->poids_SADI;
                // $quali->poids_RAVT = $request->poids_RAVT;
                $quali->objectif = $request->objectif;
                $quali->save();
                return response()->json([
                    "statut"=>Response::HTTP_OK,
                    "message"=>"l'indicateur quali est ajouter avec succès",
                    "data"=>$quali
                ]);
            });
        }catch(Exception $e)
        {
            return response()->json([
                "error"=>$e->getMessage(),
            ]);
        }
    }
    public function update(Request $request, indicateurQuali $indicateurQuali)
    {
        if($indicateurQuali){

            $indicateurQuali->indicateur=$request->indicateur;
            $indicateurQuali->poids_CC=$request->poids_CC;
            $indicateurQuali->poids_RA=$request->poids_RA;
            // $indicateurQuali->poids_RAVT=$request->poids_RAVT;
            // $indicateurQuali->poids_SADI=$request->poids_SADI;
            $indicateurQuali->objectif=$request->objectif;
            $indicateurQuali->save();
            return response()->json([
                "statut"=>200,
                "message"=>"mise en jour avec succès",
                "data"=>$indicateurQuali
            ]);
        }
        else{
            return response()->json([
                "statut"=>404,
                "message"=>"L'indicateur n'existe pas",
                "data"=>$indicateurQuali
            ]);
        }


    }
}
