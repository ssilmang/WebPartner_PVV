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
use App\Models\IndicateurQuali;
use App\Models\IndicateurQuanti;
use App\Models\ObjectifraQuali;
use App\Models\ObjectifccQuali;
use App\Models\Objectif;
use App\Models\Outil;
use App\Models\Annee;
use App\Models\Mois;
use App\Models\Stockra;
use App\Models\Stockcc;
use App\Models\CommissionQuali;
use App\Models\CommissionFinale;
use App\Models\CommissionQuanti;
use App\Models\CommissionccQuali;
use App\Models\Commissionccfinale;
use App\Models\CommissionccQuanti;
use App\Models\StockccQuali;
use App\Models\StockRAQuali;
use App\Http\Resources\ObjectifccResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Http\Resources\RaResource;
use App\Http\Resources\CcResource;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Drv;
use Exception;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        try{
            return DB::transaction(function() use($request){
                $username = $request->name.'_'. $request->matricule;
                $date = Carbon::now();
                $journee = explode('-',$date->toDateTimeString());
                $month =$date->monthName;
                $jour =$journee[2];
                $date = $journee[0];
                $mois=0 ;
                $annee = Annee::where('libelle',$date)->first();
                if($annee)
                {
                    $mois= Mois::firstOrNew([
                        "libelle"=>$month,
                        "code"=>$journee[1],
                        "annee_id"=>$annee->id,
                    ]);
                    if(!$mois->exists)
                    {
                        $mois->save();
                    }  
                }
                $role = Role::find($request->role_id);     
                $user = new User();
                    $user->name=$request->name;
                    $user->prenom=$request->prenom;
                    $user->email=$request->email;
                    $user->options = isset($request->options)? $request->options : "neant";
                    $user->role_id=$role->id;
                    $user->matricule=$request->matricule;
                    $user->username=$username;   
                $tomail = "silmangsarr1998@gmail.com";
                $object = "testing mail";
                $user->password="0000";
                    $user->save();
                    // Mail::to($tomail)->send(new SendMail($request));
                if($role->code === "DRV"){
                   $validate =  $request->validate([
                    'libelle'=>'required|unique:drvs,libelle',
                    'prestataire_id'=>'required|exists:prestataires,id',
                   ]);
                   $drv = Drv::create([
                        'libelle'=>$request->libelle,
                        'prestataire_id'=>$request->prestataire_id,
                        'user_id'=>$user->id
                   ]);
                   return response()->json([
                    "statut"=>Response::HTTP_OK,
                    "message"=>"succès",
                    "data"=>$drv
                   ]);
                }
                if($role->code === "RA")
                {
                    $ra =Ra::create([
                        "nom_agence"=>$request->nom_agence,
                        "adresse_agence"=>$request->adresse_agence,
                        "user_id"=>$user->id,
                        "drv_id"=>$request->drv_id,
                    ]);  
                    $ids[]=[];             
                    $indicateurQuanti = IndicateurQuanti::get();           
                    if($request->parteners){
                        foreach ($request->parteners as $key => $value) {
                            $ids[] = $value['objectif_id'];
                            $objectifRa = Objectifra::create([
                                "objectif_id"=>$value["objectif_id"],
                                "ra_id"=>$ra->id,
                                "value"=>$value["value"],
                            ]); 
                            Stockra::create([
                                'mois_id'=>$mois->id,
                                'annee_id'=>$annee->id,
                                'objectifra_id'=>$objectifRa->id,
                                'value'=>$value["value"],
                            ]) ;
                        }
                    }
                    foreach ($indicateurQuanti as $key => $valeur) {
                       $outil = Outil::where('indicateur_quanti_id',$valeur['id'])->first();
                       $objectif = Objectif::where('outil_id',$outil->id)->first();                 
                       if(!in_array($objectif->id,$ids)){
                        $objectifRa = Objectifra::create([
                            "objectif_id"=>$objectif->id,
                            "ra_id"=>$ra->id,
                            "value"=>0,
                        ]); 
                        Stockra::create([
                            "mois_id"=>$mois->id,
                            "annee_id"=>$annee->id,
                            "objectifra_id"=>$objectifRa->id,
                        ]);
                       }
                    }
                    $indicateurQuali = IndicateurQuali::all();
                    foreach ($indicateurQuali as $key => $value) {
                        $objectra=ObjectifraQuali::create([
                            "indicateur_quali_id"=>$value['id'],
                            "ra_id"=>$ra->id,
                            "realisation"=>0,
                        ]);
                        StockraQuali::UpdateOrCreate([
                            "objectira_quali_id"=>$objectra->id,
                            "annee_id"=>$annee->id,
                            "mois_id"=>$mois->id,
                        ],[
                            "realisation"=>0
                        ]);
                    }
                    CommissionQuanti::UpdateOrCreate([
                        "ra_id"=>$ra->id,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0,
                    ]);
                    CommissionQuali::UpdateOrCreate([
                        "ra_id"=>$ra->id,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0,
                    ]);
                    CommissionFinale::UpdateOrCreate([
                        "ra_id"=>$ra->id,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0,
                    ]);
                }else if($role->code ==="CC")
                {
                    $cc = Cc::create([
                        "ra_id"=>$request->ra_id,
                        "user_id"=>$user->id,
                    ]);
                    $ras = Objectifra::where('ra_id',$request->ra_id)->get();
                    $couter = Cc::where('ra_id',$request->ra_id)->count();
                    foreach ($ras as $key => $value) {
                        $valueAffecter = ceil( $value["value"]/$couter);
                        $objectifCc = new Objectifcc();
                        $objectifCc->cc_id = $cc->id;
                        $objectifCc->value = $valueAffecter;
                        $objectifCc->objectifra_id = $value['id'];
                        $objectifCc->save();
                        $objectifCcAncien = Objectifcc::where('objectifra_id',$value["id"])->update(["value"=>$valueAffecter]);
                        $stockra = Stockra::where('objectifra_id',$value['id'])->first();
                        $val= new Stockcc();
                            $val->mois_id=$mois->id;
                            $val->annee_id=$annee->id;
                            $val->stockra_id=$stockra->id;
                            $val->objectifcc_id=$objectifCc->id;
                            $val->save();
                        $val->where("stockra_id",$stockra->id)->update(["value"=>$valueAffecter]);
                    }
                    $_quali = ObjectifraQuali::where('ra_id',$request->ra_id)->get();
                    foreach ($_quali as $key => $number) {
                        $indic = IndicateurQuali::where('id',$number['indicateur_quali_id'])->first();
                        $objectifccquali = new ObjectifccQuali();
                        $objectifccquali->cc_id = $cc->id;
                        $objectifccquali->realisation = $indic->objectif;
                        $objectifccquali->objectifra_quali_id = $number['id'] ;
                        $objectifccquali->save();
                        StockccQuali::UpdateOrCreate([
                            "mois_id"=>$mois->id,
                            "annee_id"=>$annee->id,
                            "objectifcc_quali_id"=>$objectifccquali->id,
                        ],[
                            "realisation"=> 0,
                        ]);
                    }
                    CommissionccQuanti::UpdateOrCreate([
                        "cc_id"=>$cc->id ,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0
                    ]); 
                    CommissionccQuali::UpdateOrCreate([
                        "cc_id"=>$cc->id ,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0
                    ]); 
                    Commissionccfinale::UpdateOrCreate([
                        "cc_id"=>$cc->id ,
                        "mois_id"=>$mois->id,
                        "annee_id"=>$annee->id,
                    ],[
                        "commission"=>0
                    ]); 
                }
                return response()->json([
                    "statut"=>200,
                    "message"=>"Ajouter avec succès",
                    "data"=>$user
                ]);
            });
        }catch(QueryException $e){
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
    public function updateObjectif(Request $request,$id)
    {
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
                $ra = Objectifra::where('id',$request->objectifra_id)->first();
                $ra->update(['realisation'=>$sum]);
                $valuer = ($ra->realisation / $ra->value) * 100 ;
                
                if($valuer - floor($valuer)>= 0.5){
                    $valuer = ceil($valuer);
                }else{
                    $valuer = floor($valuer);
                }
                 $ra->update(["taux"=>$valuer]);
                 $ra->save();
                return response()->json([
                    "statut"=>200,
                    "message">"Ajout objectif effectuer avec succès",
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
    public function  update(Request $request){
        return DB::transaction(function() use($request){
            foreach ($request->objectifras as $key => $value) { 
                $ras = Objectifra::where(['ra_id'=>$value['ra_id'],'objectif_id'=>$value['objectifra_id']])->first();
                $ras->update(['value'=>$value['value']]);
                $stockra = Stockra::where(['objectifra_id'=>$ras->id,'annee_id'=>$request->annee['id']])->first();
                $stockra->update(['value'=>$value['value']]);
                $couter = Cc::where('ra_id',$value['ra_id'])->count();
                $cc = Cc::where('ra_id',$value['ra_id'])->get();
                $valueAffecter = 0;        
                if($ras->value !== 0){
                    $valueAffecter = $ras->value/$couter;
                }
                $valueAffecter = ceil( $valueAffecter); 
                foreach ($cc as $key => $val) {
                    $objectifCc = Objectifcc::where(['objectifra_id'=>$value['id'],'cc_id'=>$val['id']])->first();;
                    $objectifCc->update(["value"=> $valueAffecter]);
                    $objectifCc->save();
                    $stockcc = Stockcc::where(['objectifcc_id'=>$objectifCc->id,'annee_id'=>$request->annee['id'],'stockra_id'=>$stockra->id])->first();
                    $stockcc->update(['value'=>$valueAffecter]);  
                }
            }
            return response()->json([
                "statut"=>200,
                "message">"Ajout objectif effectuer avec succès",
                "data"=>[
                    
                ],
            ]);
        });
    }
    public function login(Request $request)
    {
        try{
            
            $identifiants = $request->validate([
                'username'=>'required',
                'password'=>'required'
            ]);
            if(strpos($identifiants['username'],'@gmail.com'))
            {
                $user = Auth::attempt(["password"=>$identifiants['password'],"email"=>$identifiants['username']]);
            }
            else
            {
                $user =Auth::attempt($identifiants);
            }
            if(!$user)
            {
                return response()->json([
                    'statut'=>Response::HTTP_NO_CONTENT,
                    'message'=>"Mot de passe ou nom d'utilisateur incorrecte",
                ],422);
            }
            $user = Auth::user();         
            $token = $user->createToken('MON_TOKEN')->plainTextToken;
            return response()->json([
                'statut'=>Response::HTTP_OK,
                "message"=>"connecter avec succès",
                "data"=>[
                    "token"=>$token,
                    "user"=>UserResource::make($user)
                ]
            ],200);
        }catch(Exception $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"erreur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
    public function logout()
    {
        $user=Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            "statut"=>Response::HTTP_NO_CONTENT,
            "message"=>"vous êtes déconnecter avec succès"
        ]);
    }
    public function registrer(Request $request)
    {
        try{

            return DB::transaction(function() use($request){
                $username = $request->name.'_'. $request->matricule;
                if(is_int($request->role_id)){
                    $role_id= $request->role_id;
                }else{
                    $role = Role::firstOrNew([
                        "libelle"=>$request->role_id,
                    ]);
                    if(!$role->exists)
                    {
                        $role->code = ucfirst(substr($request->role_id,0,5));
                        $role->save();
                    }
                    $role_id = $role->id;
                }
                $role = Role::find($role_id);
                
                $user = new User();
                    $user->name=$request->name;
                    $user->prenom=$request->prenom;
                    $user->email=$request->email;
                    $user->password=$request->password;
                    $user->options = isset($request->options)? $request->options : "neant";
                    $user->role_id=$role->id;
                    $user->matricule=$request->matricule;
                    $user->username=$username;
                $user->save();
                return response()->json([
                    "statut"=>200,
                    "message"=>"Vous êtes enregistrer avec succès",
                    "data"=>$user
                ]);
            });
        }catch(Exception $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"erreur",
                "data"=>$e->getMessage(),
            ]);
        }
    }
    function getRa(Request $request,$id)
    {
        $user = User::where('id',$id)->first();
        if($user)
        {
            $ra = RA::where('user_id',$user->id)->first();
            $cc = Cc::where('user_id',$user->id)->first();
           if($ra){
            return response()->json([
                "statut"=>200,
                "message"=>"ra existe",
                "data"=>RaResource::make($ra)
            ]);
           }elseif ($cc) {
            return response()->json([
                "statut"=>200,
                "message"=>"cette utilisateur n'est pas un ra",
                "data"=>CcResource::make($cc)
            ]);
           }else{
            return response()->json([
                "statut"=>200,
                "message"=>"cette utilisateur n'est pas un ra",
                "data"=>UserResource::make($user)
            ]);
           }
        }
        return response()->json([
            "statut"=>200,
            "message"=>"l'utilisateur n'existe pas",
        ]);
    }
    public function updatePassword(Request $request)
    {
        $user = User::where(['email'=>$request->email])->first();
        if($user){
            $user->update(['password'=>$request->password]);
            return response()->json([
                "statut"=>200,
                "message"=>"Vous avez changer votre mot de passe",
                "data"=>$user
            ]);
        }else{
            return response()->json([
                "statut"=>221,
                "message"=>"l'utilisateur n'existe pas",
                "data"=>$user
            ]);
        }
    }
}
