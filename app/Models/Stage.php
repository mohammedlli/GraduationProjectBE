<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    //

    protected $guarded = ['id'];

    public function user(){
      return $this->belongsToMany(User::class,'stage_user');
       }

       public function task(){
        return $this->hasMany(Task::class);
       }
}
