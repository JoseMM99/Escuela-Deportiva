<?php
namespace App\Repositories;
use App\Models\Student;

class StudentRepository{

    public function create($uuid, $curp, $people_id, $course_id){
        $student['uuid'] = $uuid;
        $student['curp'] = $curp;
        $student['people_id'] = $people_id;
        $student['course_id'] = $course_id;
        return Student::create($student);
    }

    public function update($uuid, $curp){
        $student = $this->find($uuid);
        $student->curp = $curp;
        $student->save();
        return $student;
    }

    public function find($uuid){
        return Student::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $student = $this->find($uuid);
        return $student->delete();
    }
}
