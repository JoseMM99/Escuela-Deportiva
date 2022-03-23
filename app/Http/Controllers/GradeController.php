<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\GradeRepository;
use App\Models\Grade;
use Uuid;

class GradeController extends Controller
{
    protected $grade_repository;

    public function __construct(GradeRepository $grade){
        $this->grade_repository = $grade;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'grade' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'feedback' => 'required|string|min:10|max:255',
        ]);
        if($validator->fails()){
            Log::warning('GradeController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $grade = $this->grade_repository->create(
                Uuid::generate()->string,
                $request->get('grade'),
                $request->get('feedback'),
            );
            
            Log::info('GradeController - register - Se creo una nueva actividad');
            return response()->json(compact('grade'),201);

        }catch(\Exception $ex){
            Log::emergency('GradeController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'grade' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'feedback' => 'required|string|min:10|max:255'
        ]);
        if($validator->fails()){
            Log::warning('GradeController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = Grade::Where('uuid', '=', $uuid)->first();

            $grade = $this->grade_repository->update(
                $global->uuid,
                $request->get('grade'),
                $request->get('feedback'),
            );

            Log::info('GradeController - update - Se actualizó una calificación');
            return response()->json(compact('grade'),201);

        }catch(\Exception $ex){
            Log::emergency('GradeController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function list(){
        return response()->json($this->grade_repository->list());
    }

    public function edit($uuid){
        $grade = Grade::Where('uuid', '=', $uuid)->first();

        $masvar = [
            'id' => $grade['id'],
            'uuid' => $grade['uuid'],
            'grade' => $grade['grade'],
            'feedback' => $grade['feedback'],
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $grade = Grade::Where('uuid', '=', $uuid)->first();
            $grade->delete();
            Log::info('GradeController - delete - Eliminaste una actividad');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('GradeController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
