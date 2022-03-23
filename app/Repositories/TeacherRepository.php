<?php
namespace App\Repositories;
use App\Models\Teacher;

class TeacherRepository{

    public function create($uuid, $rfc, $people_id){
        $teacher['uuid'] = $uuid;
        $teacher['rfc'] = $rfc;
        $teacher['people_id'] = $people_id;
        return Teacher::create($teacher);
    }

    public function update($uuid, $rfc){
        $teacher = $this->find($uuid);
        $teacher->rfc = $rfc;
        $teacher->save();
        return $teacher;
    }

    public function find($uuid){
        return Teacher::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $teacher = $this->find($uuid);
        return $teacher->delete();
    }
}
