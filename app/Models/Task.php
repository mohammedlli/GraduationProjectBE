<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $guarded = ['id'];

    public function stage() {
return $this->belongsTo(Stage::class);
    }

     public function users(){

    return $this->hasOne(user::class);

    }


    public function answers(){
        return $this->hasMany(Answer::class);
    }
}
