<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\ActivityRepository;
use App\Models\Activity;
use Uuid;

class ActivityController extends Controller
{
    //
    protected $activity_repository;

    public function __construct(ActivityRepository $activity){
        $this->activity_repository = $activity;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:30',
            'description' => 'required|string|min:10|max:150'
        ]);
        if($validator->fails()){
            Log::warning('ActivityController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $activity = $this->activity_repository->create(
                Uuid::generate()->string,
                $request->get('name'),
                $request->get('description'),
            );
            
            Log::info('ActivityController - register - Se creo una nueva actividad');
            return response()->json(compact('activity'),201);

        }catch(\Exception $ex){
            Log::emergency('ActivityController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:30',
            'description' => 'required|string|min:10|max:150'
        ]);
        if($validator->fails()){
            Log::warning('ActivityController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = Activity::Where('uuid', '=', $uuid)->first();

            $activity = $this->activity_repository->update(
                $global->uuid,
                $request->get('name'),
                $request->get('description'),
            );

            Log::info('ActivityController - update - Se actualizó una actividad');
            return response()->json(compact('activity'),201);

        }catch(\Exception $ex){
            Log::emergency('ActivityController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function list(){
        return $this->activity_repository->list();
    }

    public function edit($uuid){
        $activity = Activity::Where('uuid', '=', $uuid)->first();

        $masvar = [
            'id' => $activity['id'],
            'uuid' => $activity['uuid'],
            'name' => $activity['name'],
            'description' => $activity['description'],
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $activity = Activity::Where('uuid', '=', $uuid)->first();
            $activity->delete();
            Log::info('ActivityController - delete - Eliminaste una actividad');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('ActivityController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
