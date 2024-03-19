<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use App\Models\Role;
use App\Models\Ra;
use App\Models\Cc;
use App\Models\Objectifra;
use App\Models\Objectifcc;
use App\Http\Resources\ObjectifccResource;


use Exception;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        try{
            return DB::transaction(function() use($request){
                $username = $request->name.'_'. $request->matricule;



                $user = new User();
                    $user->name=$request->name;
                    $user->prenom=$request->prenom;
                    $user->email=$request->email;
                    $user->password=$request->password;
                    $user->options = isset($request->options)? $request->options : "neant";
                    $user->role_id=$request->role_id;
                    $user->matricule=$request->matricule;
                    $user->username=$username;
                $user->save();
                $role = Role::find($user->role_id);
                if($role->code === "RA")
                {
                    $ra =Ra::create([
                        "nom_agence"=>$request->nom_agence,
                        "adresse_agence"=>$request->adresse_agence,
                        "user_id"=>$user->id,
                    ]);
                    foreach ($request->parteners as $key => $value) {
                        $objectifRa = Objectifra::create([
                            "objectif_id"=>$value["objectif_id"],
                            "ra_id"=>$ra->id,
                            "value"=>$value["value"],
                        ]);  
                    }
                }else if($role->code ==="CC")
                {
                    $cc = Cc::create([
                        "ra_id"=>$request->ra_id,
                        "user_id"=>$user->id,
                    ]);
                    $ras = Objectifra::where('ra_id',$request->ra_id)->get();
                    $couter = Cc::where('ra_id',$request->ra_id)->count();
                    foreach ($ras as $key => $value) {
                        $valueAffecter = $value["value"]/$couter;
                        if($valueAffecter - floor($value["value"]/$couter) >= 0.5){
                            $valueAffecter = ceil( $value["value"]/$couter);
                        }else{
                            $valueAffecter = floor($value["value"]/$couter);
                        }
                        $objectifCc = new Objectifcc();
                        $objectifCc->cc_id = $cc->id;
                        $objectifCc->value = $valueAffecter;
                        $objectifCc->objectifra_id = $value['id'];
                        $objectifCc->save();
                        $objectifCcAncien = Objectifcc::where('objectifra_id',$value["id"])->update(["value"=>$valueAffecter]);
                    }
                }
                return response()->json([
                    "statut"=>200,
                    "message"=>"Ajouter avec succès",
                    "data"=>$user
                ]);
            });
        }catch(Exception $e){
            return response()->json([
                "statut"=>221,
                "message"=>"erreur l'hort de l'ajout d'un utilisateur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
    public function indexCC(Request $request,$idcc)
    {
        try{
            $user = User::where('id',$idcc)->first();
            $cc = Cc::where('user_id',$user->id)->first();
          
            $objectifs = Objectifcc::where('cc_id',$cc->id)->get();
            return response()->json([
                "statut"=>200,
                "message"=>"les objectifs  de cette user",
                "data"=>[
                    "cc"=>ObjectifccResource::collection($objectifs)
                ],
            ]);
        }catch(Exception $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"erreur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
    public function updateObjectif(Request $request,$id){
        try{
            $objectifcc = Objectifcc::find($id);
            if($objectifcc){
                $objectifcc->increment("realisation",$request->realisation);
                $taux = ($objectifcc->realisation/ $objectifcc->value) *100 ;
                if($taux - floor($taux)>= 0.5){
                    $taux = ceil($taux);
                }else{
                    $taux = floor($taux);
                }
                 $objectifcc->update(["taux"=>$taux]);
                 $objectifcc->save();
                $ObjectifAllra= Objectifcc::where('objectifra_id',$request->objectifra_id)->get()->pluck('realisation')->toArray();
                $sum=array_sum($ObjectifAllra);
                $ra= Objectifra::where('id',$request->objectifra_id)->first();
                $ra->update(['realisation'=>$sum]);
                $valuer = ($ra->realisation/ $ra->value) * 100 ;
                
                if($valuer - floor($valuer)>= 0.5){
                    $valuer = ceil($valuer);
                }else{
                    $valuer = floor($valuer);
                }
                 $ra->update(["taux"=>$valuer]);
                 $ra->save();
                return response()->json([
                    "statut"=>200,
                    "message">"Realisation effectuer avec succès",
                    "data"=>[
                        "cc"=>ObjectifccResource::make($objectifcc)
                    ],
                ]);
            }else{
                return response()->json([
                    "message"=>"l'objectif n'existe pas"
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                "statut"=>221,
                "message"=>"erreur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
}
