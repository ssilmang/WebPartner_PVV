<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ra;
use App\Models\Cc;
use App\Http\Resources\RaResource;
use App\Models\Demande;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;
use File; 

class RAController extends Controller
{
    public function index(){
        $ras = Ra::paginate(20);
        // $ccs = Cc::whereIn('ra_id',$ras->id)->get();
        return response()->json([
            "statut"=>200,
            "message"=>"all",
          
            "data"=>[
                "pagination"=>[
                    "page"=>$ras->currentPage(),
                    "total"=>$ras->total(),
                    "taille"=>$ras->perPage(),
                ],
                "ras"=>RaResource::collection($ras)
            ],
        ]);
    }
    public function declinaison(Request $request)
    {
        $ras = Ra::paginate(20);
        return response()->json([
            "statut"=>200,
            "message"=>"all",
          
            "data"=>[
                "pagination"=>[
                    "page"=>$ras->currentPage(),
                    "total"=>$ras->total(),
                    "taille"=>$ras->perPage(),
                ],
                "ras"=>RaResource::collection($ras)
            ],
        ]);
    }
    public function ra(){
        $ras = Ra::all();
        return response()->json([
            "statut"=>200,
            "message"=>"all",
          
            "data"=>[
                "ras"=>RaResource::collection($ras)
            ],
        ]);
    }
    public function demandeTraitement(Request $request,$id)
    {
        try{
           return  DB::transaction(function() use($request,$id){
               $validate = $request->validate([
                'file_zip'=>'required|file|mimes:zip|max:20480',
                'ra_id'=>'required|exists:ras,id'
               ]);
               $zipPath = $request->file('file_zip')->store('zips','public');
              $demande =  Demande::create($validate);
              return response()->json([
                "data"=>$demande,
              ]);
               $zip = new ZipArchive();
               $fille_name = 'zip_name.zip';
               if($zip->open(public_path($$fille_name), ZipArchive::CREATE)==TRUE){
                // $files = File::files(public_path('files'));
                // if(count($files) >0){
                //     foreach ($files as $key => $value) {
                //        $relativeName  =basename($value);
                //        $zip->addFile($value,$relativeName);
                //     }
                // }
                $zip->close();
                return response()->download(public_path($fille_name));
               }else{
                return response()->json([

                ]);
               }
              

            });

        }catch(QueryException $e)
        {
            return response()->json([
                "statut"=>Response::HTTP_NO_CONTENT,
                "message"=>"Erreur lors du traitement",
                "data"=>$e->getMessage(),

            ]);
        }
    }
}
