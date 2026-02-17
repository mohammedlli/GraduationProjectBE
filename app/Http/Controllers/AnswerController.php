<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    //

     public function store(Request $request){
        $answer = Answer::create($request->all());

        return response()->json($answer , 200);
    }


     public function update(Request $request , $id){

     $answer = Answer::findOrFail($id);
        $answer ->update([$request->all()]);
        return response()->json($answer , 200);
    }


         public function destroy(string $id){

     $answer = Answer::findOrFail($id);
        $answer ->delete();
        return response()->json($answer , 200);
    }


         public function show(string $id){
        $answer = Answer::find($id);
        return response()->json($answer , 200);
    }

           public function getAll(){
        $answer = Answer::all();
        return response()->json($answer , 200);
    }
}
