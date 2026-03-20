<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Http\Request;

class StageController extends Controller
{
    //

      public function show(string $id){
        $stage = Stage::find($id);
        return response()->json($stage, 200);
    }

       public function getAll(){
        $stage = Stage::all();
        return response()->json($stage , 200);
    }

    
}
