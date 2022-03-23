<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Eloquent
{
    use softDeletes;
    protected $table = 'students';

    protected $fillable = [
        'id',
        'uuid',
        'curp',
        'people_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function people(){
        return $this->belongsTo(People::class);
    }
    
    public function assignment(){
        return $this->hasOne(Assignment::class);
    }
}
