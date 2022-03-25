<?php
namespace App\Repositories;
use App\Models\Assignment;

class AssignmentRepository{

    public function create(
        $uuid,
        $assignmentDate,
        $assistance,
        $student_id,
        $teacher_id,
        $activity_id,
        $practice_id,
        $grade_id,
        $period_id)
    {
        $assignment['uuid'] = $uuid;
        $assignment['assignmentDate'] = $assignmentDate;
        $assignment['assistance'] = $assistance;
        $assignment['student_id'] = $student_id;
        $assignment['teacher_id'] = $teacher_id;
        $assignment['activity_id'] = $activity_id;
        $assignment['practice_id'] = $practice_id;
        $assignment['grade_id'] = $grade_id;
        $assignment['period_id'] = $period_id;

        return Assignment::create($assignment);
    }

    public function update($uuid, $assignmentDate, $assistance){
        $assignment = $this->find($uuid);
        $assignment->assignmentDate = $assignmentDate;
        $assignment->assistance = $assistance;
        $assignment->save();
        return $assignment;
    }

    public function find($uuid){
        return Assignment::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $assignment = $this->find($uuid);
        return $assignment->delete();
    }

    public function list(){
        return Assignment::all();
    }
}
