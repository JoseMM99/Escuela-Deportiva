<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CourseRepository;
use App\Repositories\PeriodRepository;
use App\Models\Course;
use App\Models\Period;
use Uuid;

class CourseController extends Controller
{
    //
    protected $course_repository;
    protected $period_repository;

    public function __construct(CourseRepository $course, PeriodRepository $period){
        $this->course_repository = $course;
        $this->period_repository = $period;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:60',
            'description' => 'required|string|min:10|max:255',
            'period_id' => 'required|numeric'
        ]);
        if($validator->fails()){
            Log::warning('CourseController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();

        try{
            $course = $this->course_repository->create(
                Uuid::generate()->string,
                $request->get('name'),
                $request->get('description'),
                $request->get('period_id'),
            );
        DB::commit();
            
            Log::info('CourseController - register - Se creo un curso');
            return response()->json(compact('course'),201);

        }catch(\Exception $ex){
            Log::emergency('CourseController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
            DB::rollback();
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:60',
            'description' => 'required|string|min:10|max:255',
        ]);
        if($validator->fails()){
            Log::warning('CourseController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = Course::Where('uuid', '=', $uuid)->first();

            $course = $this->course_repository->update(
                $global->uuid,
                $request->get('name'),
                $request->get('description'),
            );

            Log::info('CourseController - update - Se actualizó un curso');
            return response()->json(compact('course'),201);

        }catch(\Exception $ex){
            Log::emergency('CourseController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    //public function list(){
      //  return $this->course_repository->list();
    //}
    public function list(){
        $courses = Course::Where('uuid', '!=', null)->get();
        $datos = [];
        foreach($courses as $key=> $value){
            $datos[$key] = [
                'id'=> $value['id'],
                'uuid'=> $value['uuid'],
                'name'=> $value['name'],
                'description'=> $value['description'],
                'period_id'=> $value['period_id'],
                'uuid_period' => $value->period->uuid,
                'dateStarPeriod' => $value->period->dateStarPeriod,
                'dateClosingPeriod' => $value->period->dateClosingPeriod,
            ];
        }
        return response()->json($datos);
    }

    public function edit($uuid){
        $course = Course::Where('uuid', '=', $uuid)->first();
        $period = Period::Where('uuid', '=', $course->period->uuid)->first();

        $masvar = [
            'id' => $course['id'],
            'uuid' => $course['uuid'],
            'name' => $course['name'],
            'description' => $course['description'],
            'period_id' => $course['period_id'],
            'uuid_period' => $period['uuid'],
            'dateStarPeriod' => $period['dateStarPeriod'],
            'dateClosingPeriod' => $period['dateClosingPeriod']
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $course = Course::Where('uuid', '=', $uuid)->first();
            $course->delete();
            Log::info('CourseController - delete - Eliminaste un curso');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('CourseController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
