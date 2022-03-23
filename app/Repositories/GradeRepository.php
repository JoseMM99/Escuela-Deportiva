<?php
namespace App\Repositories;
use App\Models\Grade;

class GradeRepository{

    public function create($uuid, $grade, $feedback){
        $grades['uuid'] = $uuid;
        $grades['grade'] = $grade;
        $grades['feedback'] = $feedback;
        return Grade::create($grades);
    }

    public function update($uuid, $grade, $feedback){
        $grades = $this->find($uuid);
        $grades->grade = $grade;
        $grades->feedback = $feedback;
        $grades->save();
        return $grades;
    }

    public function find($uuid){
        return Grade::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $grade = $this->find($uuid);
        return $grade->delete();
    }

    public function list(){
        return Grade::all();
    }
}