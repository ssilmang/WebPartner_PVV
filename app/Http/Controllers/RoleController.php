<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Exception;
class RoleController extends Controller
{
    public function store(Request $request){
        try{
            return DB::transaction(function() use($request){
                $role = Role::create([
                    "libelle"=>$request->libelle,
                    "code"=>$request->code,
                ]);
                return response()->json([
                    "statut"=>200,
                    "message"=>"Role ajouter avec succès",
                    "data"=>$role
                ]);
            });
        }catch(Exception $e){
            return response()->json([
                "statut"=>221,
                "message"=>"erreur lors de l'ajout d'un role",
                "data"=>$e->getMessage(),
            ]);
        };
    }
}
