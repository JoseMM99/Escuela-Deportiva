<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Eloquent
{
    use softDeletes;
    protected $table = 'roles';
    protected $fillable = [
        'id',
        'uuid',
        'rol',
        'Description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'rol_id');
    }
}
