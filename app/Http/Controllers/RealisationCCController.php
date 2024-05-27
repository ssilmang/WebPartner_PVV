<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockraResource;
use App\Models\Cc;
use App\Models\Ra;
use App\Models\IndicateurQuanti;
use App\Models\IndicateurQuali;
use App\Models\ObjectifraQuali;
use App\Models\ObjectifccQuali;
use App\Models\Objectif;
use App\Models\Objectifcc;
use App\Models\Objectifra;
use App\Models\Outil;
use App\Models\User;
use App\Models\Stockra;
use App\Models\Stockcc;
use App\Models\StockccQuali;
use App\Models\StockraQuali;
use App\Models\CommissionQuali;
use App\Models\CommissionQuanti;
use App\Models\CommissionccQuali;
use App\Models\CommissionccQuanti;
use App\Models\CommissionFinale;
use App\Models\Commissionccfinale;
use App\Models\Annee;
use App\Models\Mois;
use App\Models\Pallier;
use App\Models\Seuil;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Rap2hpoutre\FastExcel\FastExcel;
class RealisationCCController extends Controller
{
    public function index(){
        $ras = Ra::all();
        return response()->json([
            "statut"=>Response::HTTP_OK,
            "message"=>"all",
            "data"=>StockraResource::collection($ras)
        ]);
        $stockras = Stockra::all();
        if($stockras)
        {
            return response()->json([
                "statut"=>Response::HTTP_OK,
                "message"=>"all",
                "data"=>StockraResource::collection($stockras)
            ]);
        }else
        {
            return response()->json([
                "statut"=>Response::HTTP_NO_CONTENT,
                "message"=>"erreur",
                "data"=>null
            ]);
        }
    }
    public function chargement(Request $request)
    {
        try
        {
            return DB::transaction(function() use($request)
            {
                request()->validate([
                    'files' => 'required|mimes:xlsx,xls|max:2048'
                ]);
                $file = $request->files;
                if($file->count()>0)
                {
                  $file = $file->get('files');
                }
                $fastExcel = new FastExcel();
                $realisation =[];
                $import =$fastExcel->import($file);
                $data = [];
                foreach ($import as $row)
                {
                  foreach ($row as $key => $value)
                  {
                    if (!empty($key) && ctype_upper($key[0]) )
                    {
                      $lowerKey = strtolower($key);
                      if(isset($row[$lowerKey]))
                      {
                        if (!isset($data[$lowerKey]))
                        {
                            $data[$lowerKey] = [];
                        }
                        if ( !empty($row[$key]) && ($lowerKey === "dma" || $lowerKey === "csat" || $lowerKey === "mystery shopping" ))
                        {
                          if($row[$key]!=="agence")
                          {
                            $data[$lowerKey][] = [
                                "agence" => $row[$key],
                                "value" => $row[$lowerKey],
                            ];
                          }
                      } elseif (!empty($row[$key]) && !empty($row[$lowerKey]))
                      {
                          if ($row[$key] !== "Login GAIA/ Nessico")
                          {
                              $data[$lowerKey][] = [
                                  "matricule" => $row[$key],
                                  "value" => $row[$lowerKey],
                              ];
                          }
                      }
                      } 
                    }
                  } 
                } 
               
                // return response()->json([
                //     "statut"=>Response::HTTP_OK,
                //     "message"=>"file access",
                //     "data"=>$file,
                // ]);
                Carbon::setLocale('fr');
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
                        "libelle"=>'Juin',
                        "code"=>5,
                        "annee_id"=>$annee->id,
                    ]);
                    // $mois= Mois::firstOrNew([
                    //         "libelle"=>$month,
                    //         "code"=>$journee[1],
                    //         "annee_id"=>$annee->id,
                    //     ]);
                    if(!$mois->exists)
                    {
                        $mois->save();
                    }               
                    foreach ($data as $key => $value)
                    {
                        $indicat= IndicateurQuanti::where('indicateur',$key)->first();
                        $indicat_quali = IndicateurQuali::where('indicateur',$key)->first(); 
                        $objec=[];                   
                        foreach ($value as $k => $val)
                        { 
                            if(isset($val['matricule']))
                            {
                                $user = User::where('matricule',$val['matricule'])->first();
                                $cc = Cc::where('user_id',$user->id)->first();   
                                                      
                                if($indicat && !$indicat_quali)
                                {                          
                                    $outil = Outil::where('indicateur_quanti_id',$indicat->id)->first();                   
                                    $objectif = Objectif::where('outil_id',$outil->id)->first();
                                    if($objectif)
                                    {
                                        $objectifra = Objectifra::where(['objectif_id'=>$objectif->id,'ra_id'=>$cc->ra_id])->first();                                   
                                        if($objectifra)
                                        {                                      
                                            $objectifcc = Objectifcc::where(["cc_id"=>$cc->id,"objectifra_id"=>$objectifra->id])->first();
                                            
                                            $taux = ceil( ($val["value"] / $objectifcc->value) *100);
                                            $objectifcc->update(["realisation"=>$val["value"],"taux"=>$taux]);                               
                                            $palliers = Pallier::all();
                                            $poids_fibre = 0;
                                            $poids_adsl = 0;
                                            $ad="";
                                            $comcc =0;
                                            $poids_f =0;
                                            $poids_autre=[];
                                            $palliers = Pallier::all();
                                            foreach ($palliers as $y => $pallier)
                                            {                                      
                                                if( $objectifcc->taux >= $pallier["regle_pallier"])
                                                {
                                                    $comcc= $pallier->commission_CC;
                                                }
                                            }
                                            $allobjectcc = Objectifcc::where('cc_id',$objectifcc->cc_id)->get();
                                            foreach ($allobjectcc as $e => $occ)
                                            {
                                                $raobj = Objectifra::where('id',$occ->objectifra_id)->first();
                                                $object = Objectif::where('id',$raobj->objectif_id)->first();
                                                $outile = Outil::where('id',$object->outil_id)->first();
                                                $indicQuanti = IndicateurQuanti::where('id',$outile->indicateur_quanti_id)->first();
                                                if($key)
                                                {
                                                    if(strtolower($indicQuanti->indicateur) === strtolower('Adsl'))
                                                    {
                                                        if($occ['value'] === 0)
                                                        {
                                                            $poids_adsl =$indicQuanti->poids_CC;
                                                            $poids_fibre +=$poids_adsl;
                                                        }else
                                                        {
                                                            $poids_adsl = $indicQuanti->poids_CC;
                                                        }
                                                    }
                                                    elseif(strtolower($indicQuanti->indicateur) === strtolower('Fibre'))
                                                    {
                                                        if($occ['value'] === 0)
                                                        {
                                                            $poids_fibre = $indicQuanti->poids_CC; 
                                                            $poids_adsl += $poids_fibre; 
                                                        }else
                                                        {
                                                            $poids_fibre = $indicQuanti->poids_CC;
                                                        }                              
                                                    }else
                                                    {
                                                        $poids_autre []= $indicQuanti;
                                                    }
                                                }                                 
                                            }
                                            if(strtolower($key)===strtolower("Fibre"))
                                            {
                                                $commisse = $comcc*($poids_fibre/100);                                     
                                                $objectifcc->update(["commission"=>$commisse]);
                                            }elseif(strtolower($key)=== strtolower("Adsl"))
                                            {
                                                $commisse = $comcc*($poids_adsl/100);                                       
                                                $objectifcc->update(["commission"=>$commisse]);
                                            }else
                                            {
                                                foreach ($poids_autre as  $ter)
                                                {
                                                    if($key===$ter->indicateur)
                                                    {
                                                        $commisse = $comcc*($ter->poids_CC/100);
                                                        $objectifcc->update(["commission"=>$commisse]);
                                                    }
                                                }
                                            }
                                            $dat[]=$objectifcc;  
                                            $real = Objectifcc::where('objectifra_id',$objectifra->id)->get()->pluck('realisation')->toArray();
                                            $sum = array_sum($real);
                                            $objectifra->update(["realisation"=>$sum]);
                                            $tauxRA = ceil(($objectifra->realisation/$objectifra->value)*100);
                                            $objectifra->update(["taux"=>$tauxRA]);
                                            $poids_fibre_ra = 0;
                                            $poids_adsl_ra = 0;
                                            $poids_autre_ra =[];
                                            $ind=[];
                                            $comRa =0;
                                            foreach ($palliers as  $pallier)
                                            {
                                                if( $objectifra->taux >= $pallier["regle_pallier"])
                                                {
                                                    $comRa = $pallier->commission_RA;
                                                    // $comCc = $nombre->commission_CC;
                                                }
                                                
                                            }
                                            $allobjectra = Objectifra::where('ra_id',$objectifra->ra_id)->get();
                                            foreach ($allobjectra as  $obra)
                                            {
                                                $objectif = Objectif::where('id',$obra->objectif_id)->first();
                                                $outile = Outil::where('id',$objectif->outil_id)->first();
                                                $indict = IndicateurQuanti::where('id',$outile->indicateur_quanti_id)->first(); 
                                                if($key)
                                                {
                                                    if(strtolower($indict->indicateur) === strtolower('Adsl'))
                                                    {                                           
                                                        if($obra['value'] === 0)
                                                        {
                                                            $poids_adsl_ra = $indict->poids_RA;
                                                            $poids_fibre_ra += $poids_adsl_ra;
                                                        }else
                                                        {
                                                            $poids_adsl_ra = $indict->poids_RA;
                                                        }
                                                    }
                                                    elseif(strtolower($indict->indicateur) === strtolower('Fibre'))
                                                    {
                                                        if($obra['value'] === 0)
                                                        {
                                                            $poids_fibre_ra = $indict->poids_RA; 
                                                            $poids_adsl_ra += $poids_fibre_ra; 
                                                        }else
                                                        {
                                                            $poids_fibre_ra = $indict->poids_RA;
                                                        }                              
                                                    }else
                                                    {
                                                        $poids_autre_ra []= $indict;
                                                    }  
                                                }
                                            }
                                            if(strtolower($key)===strtolower("Fibre"))
                                            {
                                                $commission = $comRa*($poids_fibre_ra/100);                                     
                                                $objectifra->update(["commission"=>$commission]);
                                            }elseif(strtolower($key)===strtolower("Adsl"))
                                            {
                                                $commission = $comRa*($poids_adsl_ra/100);                                       
                                                $objectifra->update(["commission"=>$commission]);
                                            }else
                                            {
                                                foreach ($poids_autre_ra as  $ter)
                                                {
                                                    if($key===$ter->indicateur)
                                                    {
                                                        $commission = $comRa*($ter->poids_RA/100);
                                                        $objectifra->update(["commission"=>$commission]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if($indicat_quali && !$indicat)
                                {
                                    $objectifra_quali = ObjectifraQuali::where(["ra_id"=>$cc->ra_id,"indicateur_quali_id"=>$indicat_quali->id])->first();
                                    $objectif_quali_cc = ObjectifccQuali::where(['objectifra_quali_id'=>$objectifra_quali->id, "cc_id"=>$cc->id])->first();
                                    $objcc = Objectifcc::where('cc_id',$cc->id)->get()->pluck('commission')->toArray();
                                    $objectif_quali_cc->update(["realisation"=>$val["value"]*100]);
                                    $totauxComm = array_sum($objcc);
                                    $point = explode("%",$indicat_quali->objectif);
                                    // $tax = explode("%",$val["value"]);    
                                    $tax = $val['value']; 
                                    $commi=0; 
                                    if(isset($point[0]) && is_numeric($point[0])) 
                                    {
                                        $point = floatval($point[0]);
                                    }                           
                                    if(strtolower($indicat_quali->indicateur) === strtolower("Mystery Shopping"))
                                    { 
                                        if( $tax >= $point)
                                        {
                                          $commi = $totauxComm * $indicat_quali->poids_CC; 
                                        }         
                                    }else
                                    {
                                       if($tax > $point/100)
                                       {
                                           $commi = -1*($totauxComm *( $indicat_quali->poids_CC/100));
                                        }
                                    } 
                                    $objectif_quali_cc->update(["commission"=>$commi]);                                   
                                }
                            }  
                           if(isset($val["agence"]))
                           {
                               $ra = Ra::where(["nom_agence"=>$val["agence"]])->first();
                               if($ra)
                               {
                                    $indicqual = IndicateurQuali::where('indicateur',$key)->first(); 
                                    $objectquali=ObjectifraQuali::where(["indicateur_quali_id"=>$indicqual->id,"ra_id"=>$ra->id])->first();
                                    $objectra = Objectifra::where('ra_id',$ra->id)->get()->pluck('commission')->toArray();
                                    $totauxCommra = array_sum($objectra);
                                    $taxe = explode("%",$val["value"]);
                                    $index =explode("%",$indicqual->objectif);
                                    $commira = 0;
                                    if(strtolower($key) === strtolower("DMA"))
                                    {
                                        $objectquali->update(["realisation"=>$val["value"]]);
                                        if($taxe > 0 && $taxe < $index)
                                        {
                                            $commira = $totauxCommra * ( $indicqual->poids_RA/100);                 
                                        }
                                    }else
                                    {
                                        if(strtolower($key) === strtolower("CSAT"))
                                        {
                                            $objectquali->update(["realisation"=>$val["value"]*100]);
                                        }
                                        if($taxe >= $index)
                                        {
                                            $commira = $totauxCommra *( $indicqual->poids_RA/100);
                                        }
                                    }                    
                                    $objectquali->update(["commission"=>$commira]);
                                    $objec[]=$objectquali;                                                 
                               }
                            }
                        }                               
                    }    
                    if($mois->exists)
                    {
                        $objectifras = Objectifra::all();
                        foreach ($objectifras as $key => $value) 
                        {
                            $stockra = Stockra::UpdateOrCreate([
                                "objectifra_id" => $value["id"],
                                "mois_id" => $mois->id,
                                "annee_id" => $annee->id,     
                            ],[
                                "value" => $value["value"],
                                "realisation" => $value["realisation"],
                                "taux" => $value["taux"],
                                "commission"=>$value["commission"],
                            ]);
                            $objectifccs = Objectifcc::where('objectifra_id',$value["id"])->get();
                            foreach ($objectifccs as $key => $val)
                            {
                                $stockcc = Stockcc::UpdateOrCreate([
                                    "stockra_id" => $stockra->id,
                                    "objectifcc_id" => $val["id"],
                                    "mois_id" => $mois->id,
                                    "annee_id" => $annee->id,
                                ],[
                                    "value" => $val["value"],
                                    "realisation" => $val["realisation"],
                                    "taux" => $val["taux"],
                                    "commission" => $val["commission"],
                                ]);
                            }
                        }
                        $objectifQuali = ObjectifraQuali::all();
                        foreach ($objectifQuali as $key => $quali) {
                            $realisation =0;
                            $objetraquali= IndicateurQuali::where('id',$quali['indicateur_quali_id'])->first();
                            
                            if($objetraquali->objectif==$quali["realisation"]){
                                $realisation = 0;
                            }else{$realisation = $quali["realisation"];}
                            $stockra = StockraQuali::UpdateOrCreate([
                                "objectira_quali_id" => $quali["id"],
                                "mois_id" => $mois->id,
                                "annee_id" => $annee->id,     
                            ],[
                                "realisation" => $realisation,
                                "taux" => $quali["taux"],
                                "commission"=>$quali["commission"],
                            ]);
                            $objectifccsQuali = ObjectifccQuali::where('objectifra_quali_id',$quali["id"])->get();
                            foreach ($objectifccsQuali as $key => $ccQuali)
                            {
                                $realisationcc = 0;
                                if($objetraquali->objectif==$ccQuali["realisation"]){
                                    $realisationcc = 0;
                                }else{$realisationcc = $ccQuali["realisation"];}
                                $stockccQuali = StockccQuali::UpdateOrCreate([
                                    "objectifcc_quali_id" => $ccQuali['id'],
                                    "mois_id" => $mois->id,
                                    "annee_id" => $annee->id,
                                ],[
                                    "realisation" => $realisationcc,
                                    "taux" => $ccQuali["taux"],
                                    "commission" => $ccQuali["commission"],
                                ]);
                            }
                        }
                        $ras  = Ra::get();
                        $seuil = Seuil::first();
                        foreach ($ras as $key => $ra) {
                            $commissionstoutqunti=[];
                            $commissionindicatraQuali = []; 
                            $commissionindicatraQuanti = []; 
                            
                            $idStockras = [];
                            $objRAs = Objectifra::where('ra_id',$ra['id'])->get();
                            foreach ($objRAs as $key => $objRA) {
                               $stock= Stockra::where(['objectifra_id'=>$objRA['id'],"annee_id"=>$annee->id,'mois_id'=>$mois->id])->get();
                               $idStockras = array_merge($idStockras,$stock->pluck('id')->toArray());
                               $commissions = $stock->pluck('commission')->toArray();
                               $commissionindicatraQuanti = array_merge($commissionindicatraQuanti,$commissions);
                            }
                            
                            CommissionQuanti::UpdateOrCreate([
                                "ra_id"=>$ra["id"],
                                "mois_id"=>$mois->id,
                                "annee_id"=>$annee->id,
                            ],[
                                "commission"=>array_sum($commissionindicatraQuanti),
                            ]);
                           
                            $valueAnomaliera =0;
                            $qualiRAs = ObjectifraQuali::where('ra_id',$ra['id'])->get();
                            foreach ($qualiRAs as $key => $qualiRA) {
                                $indicateu = IndicateurQuali::where('id',$qualiRA->indicateur_quali_id)->first();
                                if($indicateu->indicateur==="Anomalie"){
                                    $valueAnomaliera = $qualiRA->realisation;
                                }
                                $commissionsQuali= StockraQuali::where(['objectira_quali_id'=>$qualiRA['id'],"annee_id"=>$annee->id,'mois_id'=>$mois->id])->pluck('commission')->toArray();
                                $commissionindicatraQuali = array_merge($commissionindicatraQuali, $commissionsQuali);
                            }
                            CommissionQuali::UpdateOrCreate([
                                "ra_id"=>$ra["id"],
                                "mois_id"=>$mois->id,
                                "annee_id"=>$annee->id,
                            ],[
                                "commission"=>array_sum($commissionindicatraQuali),
                            ]);
                            $objCCs = Cc::where("ra_id",$ra['id'])->pluck('id')->toArray();
                            foreach ($objCCs as $key => $objCC) {
                                $commissionccQaunti = [];
                                $commissionccQuali = [];
                                $objectCCs= Objectifcc::where(['cc_id'=>$objCC])->get();
                                foreach ($objectCCs as $key => $objectCC) {
                                    $stockCC = Stockcc::where(['objectifcc_id'=>$objectCC['id'],"annee_id"=>$annee->id,'mois_id'=>$mois->id,])->first();
                                    $commissionccQaunti []= $stockCC->commission;
                                }
                                CommissionccQuanti::UpdateOrCreate([
                                    "cc_id"=>$objCC ,
                                    "mois_id"=>$mois->id,
                                    "annee_id"=>$annee->id,
                                ],[
                                    "commission"=>array_sum($commissionccQaunti)
                                ]);  
                                $commissionstoutqunti[]=array_sum($commissionccQaunti);
                                $valueAnomaliecc = 0;   
                                $objectCCs= ObjectifccQuali::where(['cc_id'=>$objCC])->get();
                                foreach ($objectCCs as $key => $objectCC) {
                                    $ob = ObjectifraQuali::where('id',$objectCC['objectifra_quali_id'])->first();
                                    $qual = IndicateurQuali::where('id',$ob->indicateur_quali_id)->first();
                                    if($qual->indicateur ==="Anomalie"){
                                        $valueAnomaliecc = $objectCC->realisation;
                                    }
                                    $stockCC = StockccQuali::where(['objectifcc_quali_id'=>$objectCC['id'],"annee_id"=>$annee->id,'mois_id'=>$mois->id,])->first();
                                    $commissionccQuali []= $stockCC->commission;
                                }
                                CommissionccQuali::UpdateOrCreate([
                                    "cc_id"=>$objCC ,
                                    "mois_id"=>$mois->id,
                                    "annee_id"=>$annee->id,
                                ],[
                                    "commission"=>array_sum($commissionccQuali)
                                ]);
                               
                                $valeur = 0;
                                $commissecc = 0;
                                if(explode('%',$valueAnomaliecc)>= explode('%',$seuil["value"])){
                                   $commissecc =0;
                                }else{
                                    $commissecc = array_sum($commissionccQaunti) + array_sum($commissionccQuali);
                                }
                                Commissionccfinale::UpdateOrCreate([
                                    "cc_id"=>$objCC,
                                    "annee_id"=>$annee->id,
                                    "mois_id"=>$mois->id,
                                ],[
                                    "commission"=>$commissecc <= 0 ? 0:ceil($commissecc/1000)*1000,
                                ]) ;    
                            } 
                            $commissera = 0;
                            if(explode('%',$valueAnomaliera)>= explode('%',$seuil["value"])){
                               $commissera =0;
                            }else{
                                $commissera = array_sum($commissionindicatraQuanti) + array_sum($commissionindicatraQuali);
                            }         
                            CommissionFinale::UpdateOrCreate([
                                "ra_id"=>$ra['id'],
                                "mois_id"=>$mois->id,
                                "annee_id"=>$annee->id,
                            ],[
                                "commission"=>$commissera <= 0 ? 0:ceil($commissera/1000)*1000,
                            ]);
                        }
                    }
                }
                return response()->json([
                    "statut"=>200,
                    "message"=>"chargement de requete",
                    "data"=>$key
                ]);
            });  
        }
        catch(QueryException $e)
        {
            return response()->json([
                "statut"=>221,
                "message"=>"ERREUR",
                "data"=>$e->getMessage()
            ]);
        }
    }
    public function getChargementRequête(Request $request)
    {
        try {
            
            
        } catch (QueryException $e) {
            return response()->json([
                'statut'=>Response::HTTP_OK,
                'message'=>"erreur lors du chargement",
                "data"=>$e->getMessage(),
            ]);
        }
    }
}
