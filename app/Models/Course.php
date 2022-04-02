<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Eloquent
{
    use softDeletes;
    protected $table = 'courses';

    protected $fillable = [
        'id',
        'uuid',
        'name',
        'description',
        'period_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    public function student(){
        return $this->hasOne(Student::class);
    }
    public function period(){
        return $this->belongsTo(Period::class);
    }
}
