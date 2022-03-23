<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Practice extends Eloquent
{
    use softDeletes;
    protected $table = 'practices';

    protected $fillable = [
        'id',
        'uuid',
        'fechaEntrenamiento',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //public function period(){
    //    return $this->belongsTo(Period::class);
    //}

    public function assignment(){
        return $this->hasOne(Assignment::class);
    }
}
