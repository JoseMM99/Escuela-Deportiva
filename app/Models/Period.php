<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Eloquent
{
    use softDeletes;
    protected $table = 'periods';

    protected $fillable = [
        'id',
        'uuid',
        'dateStarPeriod',
        'dateClosingPeriod'
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
