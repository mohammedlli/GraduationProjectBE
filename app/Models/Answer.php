<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //


protected $fillable = [
        'answer',
        'task_id',
        'user_id',
        'file_path',
    ];


        public function task(){
        return $this->belongsTo(Task::class);
    }
}
