<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //




     public function store(Request $request){
        $task = Task::create($request->all());

        return response()->json($task , 200);
    }


     public function update(Request $request , $id){

     $task = Task::findOrFail($id);
        $task ->update([$request->all()]);
        return response()->json($task , 200);
    }


         public function destroy(string $id){

     $task = Task::findOrFail($id);
        $task ->delete();
        return response()->json($task , 200);
    }


         public function show(string $id){
        $task = Task::find($id);
        return response()->json($task , 200);
    }

           public function getAll(){
        $task = Task::all();
        return response()->json($task , 200);
    }
}
