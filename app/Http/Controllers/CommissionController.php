<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function store(Request $request)
    {
        try{

        }catch(Exception $e)
        {
            return response()->json([
            'statut'=>221,
            "message"=>"erreuur",
            "datta"=>$e->getMessage(),
            ]);
        }
    }
}
