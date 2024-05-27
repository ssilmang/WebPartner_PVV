<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\FileBag;

class ExportController extends Controller
{
  public function  export()
  {
    $users = User::all();
    // Export all users
   return (new FastExcel($users))->download('file.xlsx');
  }
  public function import(Request $request){
    try{
        return DB::transaction(function() use($request){
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
          return response()->json([
            "statut"=>Response::HTTP_OK,
            "message"=>"file access",
            "data"=>$data,
          ]);
        });

    }catch(QueryException $e){

      return response()->json([
        "statut"=>Response::HTTP_NO_CONTENT,
        "message"=>"erreur",
        "data"=>$e->getMessage(),
      ]);
    }
  }
}
 