<?php

namespace App\Http\Controllers;

use App\Http\Requests\store;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    //

       public function store(store $request){
        $car = Car::create($request->validate());

        return response()->json($car , 200);
    }


     public function update(Request $request , $id){

     $car = Car::findOrFail($id);
        $car ->update([$request->all()]);
        return response()->json($car , 200);
    }


         public function delete( $id){

     $car = Car::findOrFail($id);
        $car ->delete();
        return response()->json($car , 200);
    }


         public function getById($id){
        $car = Car::find($id);
        return response()->json($car , 200);
    }

           public function getAll(){
        $car = Car::all();
        return response()->json($car , 200);
    }
}