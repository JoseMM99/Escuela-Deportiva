<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Eloquent
{
    use softDeletes;
    protected $table = 'teachers';

    protected $fillable = [
        'id',
        'uuid',
        'rfc',
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
