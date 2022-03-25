<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Repositories\AssignmentRepository;
use App\Repositories\PracticeRepository;
use App\Repositories\GradeRepository;

use App\Models\Assignment;
use App\Models\Practice;
use App\Models\Grade;



class AssignmentController extends Controller
{
    protected $assignment_repository;
    protected $practice_repository;
    protected $grade_repository;

    public function __construct(
        AssignmentRepository $assignment,
        PracticeRepository   $practice,
        GradeRepository      $grade
    )
    {
        $this->assignment_repository = $assignment;
        $this->practice_repository = $practice;
        $this->grade_repository = $grade;
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [

            'fechaEntrenamiento' => 'required|date',

            'grade' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'feedback' => 'required|string|min:10|max:255',

            'assignmentDate' => 'required|date',
            'assistance' => 'required|boolean',
            'student_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
            'activity_id' => 'required|numeric',
            'period_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Log::warning('AssignmentController - register - Falta un campo por llenar');
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {

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
                $request->get('activity_id'),
                $practice->id,
                $calif->id,
                $request->get('period_id'),
            );

            DB::commit();

            Log::info('AssignmentController - register - Se creo un nuevo usuario');
            return response()->json(compact('practice', 'calif', 'assignment'), 201);

        } catch (\Exception|\Throwable $e) {
            Log::emergency('AssignmentController - register - Ocurrio un error');
            DB::rollback();
        }

        return response()->json([
            'status' => 'error',
            'message' => 'something went wrong'
        ], 500);
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(), [
            'fechaEntrenamiento' => 'required|date',
            'grade' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'feedback' => 'required|string|min:10|max:255',
            'assignmentDate' => 'required|date',
            'assistance' => 'required|boolean',
            'student_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
            'activity_id' => 'required|numeric',
            'period_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            Log::warning('AssignmentController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();

        try {
            $global = Assignment::Where('uuid', '=', $uuid)->first();
            //var_dump($global);
            //die();

            $practice = $this->practice_repository->update(
                $global->practice->uuid,
                $request->get('fechaEntrenamiento'),
            );

            $calif = $this->grade_repository->update(
                $global->grade->uuid,
                $request->get('grade'),
                $request->get('feedback'),
            );

            $assignment = $this->assignment_repository->update(
                $global->uuid,
                $request->get('assignmentDate'),
                $request->get('assistance'),
                $request->get('student_id'),
                $request->get('teacher_id'),
                $request->get('activity_id'),
                $request->get('period_id'),
            );

            DB::commit();

           Log::info('AssignmentController - update - Se creo un nuevo usuario');
        return response()->json(compact('practice', 'calif', 'assignment'), 201);

        } catch (\Exception|\Throwable $e) {
            Log::emergency('AssignmentController - update - Ocurrio un error');
            DB::rollback();
            //return $e;
        }

        return response()->json([
            'status' => 'error',
            'message' => 'something went wrong'
        ], 500);
    }

    public function list(){
        return response()->json($this->assignment_repository->list());
    }

    public function edit($uuid){
        $assignment = Assignment::Where('uuid', '=', $uuid)->first();
        $practice = Practice::Where('uuid', '=', $assignment->practice->uuid)->first();
        $grade = Grade::Where('uuid', '=', $assignment->grade->uuid)->first();

        $masvar = [
            'id' => $assignment['id'],
            'uuid' => $assignment['uuid'],
            'assignmentDate' => $assignment['assignmentDate'],
            'assistance' => $assignment['assistance'],
            'student_id' => $assignment['student_id'],
            'activity_id' => $assignment['activity_id'],
            'period_id' => $assignment['period_id'],

            'fechaEntrenamiento' => $practice['fechaEntrenamiento'],

            'grade' => $grade['grade'],
            'feedback' => $grade['feedback']
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $assignment = Assignment::Where('uuid', '=', $uuid)->first();
            $assignment->practice->delete();
            $assignment->grade->delete();
            $assignment->delete();
            Log::info('AssignmentController - delete - Eliminaste un Entrenador');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('AssignmentController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
