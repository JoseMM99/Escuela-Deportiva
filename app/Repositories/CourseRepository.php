<?php
namespace App\Repositories;
use App\Models\Course;

class CourseRepository{

    public function create($uuid, $name, $description, $period_id){
        $course['uuid'] = $uuid;
        $course['name'] = $name;
        $course['description'] = $description;
        $course['period_id'] = $period_id;
        return Course::create($course);
    }

    public function update($uuid, $name, $description){
        $course = $this->find($uuid);
        $course->name = $name;
        $course->description = $description;
        $course->save();
        return $course;
    }
    
    public function find($uuid){
        return Course::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $course = $this->find($uuid);
        return $course->delete();
    }

    public function list(){
        return Course::all();
    }
}