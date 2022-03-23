<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class People extends Eloquent
{
    use softDeletes;
    protected $table = 'people';

    protected $fillable = [
        'id',
        'uuid',
        'name',
        'lastNameP',
        'lastNameM',
        'gender',
        'bloodGroup',
        'rhFactor',
        'birthDate',
        'phone',
        'street',
        'avenue',
        'postalCode',
        'photo'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user(){
        return $this->hasOne(User::class);
    }
    
    public function student(){
        return $this->hasOne(Student::class);
    }

    public function teacher(){
        return $this->hasOne(Teacher::class);
    }
}
