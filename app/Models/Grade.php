<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Eloquent
{
    use softDeletes;
    protected $table = 'grades';

    protected $fillable = [
        'id',
        'uuid',
        'grade',
        'feedback'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function assignment(){
        return $this->hasOne(Assignment::class);
    }

}
