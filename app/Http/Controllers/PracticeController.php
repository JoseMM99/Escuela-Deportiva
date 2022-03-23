<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\PracticeRepository;
use App\Models\Practice;
use Uuid;

class PracticeController extends Controller
{
    //
    protected $practice_repository;

    public function __construct(PracticeRepository $practice){
        $this->practice_repository = $practice;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'fechaEntrenamiento' => 'required|date'
        ]);
        if($validator->fails()){
            Log::warning('PracticeController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $practice = $this->practice_repository->create(
                Uuid::generate()->string,
                $request->get('fechaEntrenamiento'),
            );
            
            Log::info('PracticeController - register - Se creo una nueva fecha de entrenamiento');
            return response()->json(compact('practice'),201);

        }catch(\Exception $ex){
            Log::emergency('PracticeController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'fechaEntrenamiento' => 'required|date'
        ]);
        if($validator->fails()){
            Log::warning('PracticeController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = Practice::Where('uuid', '=', $uuid)->first();

            $practice = $this->practice_repository->update(
                $global->uuid,
                $request->get('fechaEntrenamiento'),
            );

            Log::info('PracticeController - update - Se actualizó un entrenamiento');
            return response()->json(compact('practice'),201);

        }catch(\Exception $ex){
            Log::emergency('PracticeController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function list(){
        return response()->json($this->practice_repository->list());
    }

    public function edit($uuid){
        $practice = Practice::Where('uuid', '=', $uuid)->first();

        $masvar = [
            'id' => $practice['id'],
            'uuid' => $practice['uuid'],
            'fechaEntrenamiento' => $practice['fechaEntrenamiento'],
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $practice = Practice::Where('uuid', '=', $uuid)->first();
            $practice->delete();
            Log::info('PracticeController - delete - Eliminaste una actividad');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('PracticeController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
