<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{


public function register(Request $request){
    $request->validate([
       'name' => 'required|string|max:255',
       'role'=>  'required|string|max:255',
       'stage_id'=>  'required|integer|exists:stages,id',
        'email'=>'required|string|email|max:255|unique:users,email',
        'password'=>'required|string|min:8|confirmed',
        'description'=> 'nullable'|'string'|'max:255',
    ]);

    $user = User::create([
        'name'=>$request->name,
        'role'=>$request->role,
        'stage_id'=>$request->stage_id,
        'description'=>$request->description,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)

    ]);

    $user->stages()->attach($request->stage_id);

    return new UserResource($user);
}

public function update(Request $request)
{
    $user = Auth::auth()->user();

    $validation = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'stage_id' => 'sometimes|required|integer|exists:stages,id',
        'description' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:png,jpg,giv,jpeg|max:2048'
    ]);


   if($request->hasFile('image')){
    $path = $request->file('image')->store('my photo','public');
    $validation['image'] = $path;
   }

    $validation = User::update($validation);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $validation
    ]);
}


function login(Request $request){

 $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string',
    ]);

    if(!Auth::attempt($request->only('email','password')))
         return response()->json(

    [
        'message'=>'Invalid email or password'

    ],401);


        $user = User::where('email',$request->email)->FirstOrFail();
        $token= $user->createToken('auth_token')->plainTextToken;
        return response()->json(

    [
        'message'=>'Login successfully',
        'user'=>$user,
        'token'=>$token
    ],200);

}


function logout(Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'message'=>'Logout Successfully'
    ]);

}

public function show($id)
{
    $user = User::with('tasks')->findOrFail($id);
    return new UserResource($user);
}


           public function getAll(){
        $user = User::all();
        return response()->json($user , 200);
    }
}
