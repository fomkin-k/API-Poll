<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function questions(){
        return $this->hasMany(Question::class);
    }
    public function users(){
        return $this->hasMany(Question::class);
    }
    
}
