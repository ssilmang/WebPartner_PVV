<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Models\Pallier;
use  Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
class PallierController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            return DB::transaction(function() use($request){
                $pallier = Pallier::create([
                    "libelle"=>$request->libelle,
                    "condition"=>$request->condition,
                    "regle_pallier"=>$request->regle_pallier,
                    "regle_pallier_sablix"=>$request->regle_pallier_sablix,
                    "commission_CC"=>str_replace(' ', '', $request->commission_CC),
                    "commission_RA"=>str_replace(' ', '', $request->commission_RA),
                    // "commission_RAVT"=>$request->commission_RAVT,
                    // "commission_SADI"=>$request->commission_SADI,
                ]);
                return response()->json([
                    "statut"=>Response::HTTP_OK,
                    "message"=>"le pallier est ajouter avec succès",
                    "data"=>$pallier
                ]);
            });
        }catch(Exception $e)
        {
            return response()->json([
                "error"=>$e->getMessage(),
            ]);
        }
    }
    public function update(Request $request, Pallier $pallier)
    {
        if($pallier){
            $pallier->update([
                "libelle"=>$request->libelle,
                "regle_pallier"=>$request->regle_pallier,
                "condition"=>$request->condition,
                "commission_CC"=>str_replace(' ', '', $request->commission_CC),
                "commission_RA"=>str_replace(' ', '',$request->commission_RA),
                // "commission_RAVT"=>$request->commission_RAVT,
                // "commissioon_SADI"=>$request->commissioon_SADI,
                // "regle_pallier_sablix"=>$request->regle_pallier_sablix,
            ]);
            return response()->json([
                "statut"=>200,
                "message"=>"mise en jour avec succès",
                "data"=>$pallier
            ]);
        }
        else{
            return response()->json([
                "statut"=>404,
                "message"=>"L'indicateur n'existe pas",
                "data"=>$pallier
            ]);
        }
    }
}
