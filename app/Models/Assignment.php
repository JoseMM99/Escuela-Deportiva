<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Eloquent
{
    use softDeletes;
    protected $table = 'assignments';
    protected $fillable = [
        'id',
        'uuid',
        'assignmentDate',
        'assistance',
        'student_id',
        'teacher_id',
        'activity_id',
        'practice_id',
        'grade_id',
        'period_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function activity(){
        return $this->belongsTo(Activity::class);
    }

    public function practice(){
        return $this->belongsTo(Practice::class);
    }

    public function grade(){
        return $this->belongsTo(Grade::class);
    }

}
