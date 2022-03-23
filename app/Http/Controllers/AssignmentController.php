<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\AssignmentRepository;
use App\Repositories\PracticeRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\StudentRepository;
use App\Repositories\PeriodRepository;
use App\Repositories\GradeRepository;
use App\Models\Assignment;
use App\Models\Practice;
use App\Models\Activity;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Period;
use App\Models\Grade;
use Uuid;

class AssignmentController extends Controller
{
    //
    protected $assignment_repository;
    protected $practice_repository;
    protected $activity_repository;
    protected $teacher_repository;
    protected $student_repository;
    protected $period_repository;
    protected $grade_repository;

    public function __construct(AssignmentRepository $assignment, PracticeRepository $practice, ActivityRepository $activity,
                                TeacherRepository $teacher, StudentRepository $student, PeriodRepository $period, GradeRepository $grade){
        $this->assignment_repository = $assignment;
        $this->practice_repository = $practice;
        $this->activity_repository = $activity;
        $this->teacher_repository = $teacher;
        $this->student_repository = $student;
        $this->period_repository = $period;
        $this->grade_repository = $grade;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'assignmentDate' => 'required|date',
            'assistance' => 'required|boolean',

            'period_id' => 'required|numeric',
            
            'fechaEntrenamiento' => 'required|date',
            
            'activity_id' => 'required|numeric',
            
            'grade' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'feedback' => 'required|string|min:10|max:255',
            
            'student_id' => 'required|numeric',
            'teacher_id' => 'required|numeric'

            
        ]);
        if($validator->fails()){
            Log::warning('AssignmentController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $practice = $this->practice_repository->create(
                Uuid::generate()->string,
                $request->get('fechaEntrenamiento'),
            );
            $calif = $this->grade_repository->create(
                Uuid::generate()->string,
                $request->get('grade'),
                $request->get('feedback'),
            );
            $assignment = $this->assignment_repository->create(
                Uuid::generate()->string,
                $request->get('assignmentDate'),
                $request->get('assistance'),
                $request->get('student_id'),
                $request->get('teacher_id'),
                $request->get('period_id'),
                $request->get('activity_id'),
                $request->get('student_id'),
                $practice->id,
                $calif->id
            );
            
            Log::info('AssignmentController - register - Se creo un nuevo usuario');
            return response()->json(compact('practice', 'calif', 'assignment'),201);

        }catch(\Exception $ex){
            Log::emergency('AssignmentController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
