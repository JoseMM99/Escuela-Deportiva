<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\PeriodRepository;
use App\Models\Period;
use Uuid;

class PeriodController extends Controller
{
    //
    protected $period_repository;

    public function __construct(PeriodRepository $period){
        $this->period_repository  = $period;
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'dateStarPeriod' => 'required|date',
            'dateClosingPeriod' => 'required|date'
        ]);
        if($validator->fails()){
            Log::warning('PeriodController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $period = $this->period_repository->create(
                Uuid::generate()->string,
                $request->get('dateStarPeriod'),
                $request->get('dateClosingPeriod')
            );
            
            Log::info('PeriodController - register - Se creo un periodo');
            return response()->json(compact('period'),201);

        }catch(\Exception $ex){
            Log::emergency('PeriodController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'dateStarPeriod' => 'required|date',
            'dateClosingPeriod' => 'required|date'
        ]);
        if($validator->fails()){
            Log::warning('PeriodController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = Period::Where('uuid', '=', $uuid)->first();

            $period = $this->period_repository->update(
                $global->uuid,
                $request->get('dateStarPeriod'),
                $request->get('dateClosingPeriod')
            );

            Log::info('PeriodController - update - Se actualizó un period');
            return response()->json(compact('period'),201);

        }catch(\Exception $ex){
            Log::emergency('PeriodController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function list(){
        return response()->json($this->period_repository->list());
    }

    public function edit($uuid){
        $period = Period::Where('uuid', '=', $uuid)->first();

        $masvar = [
            'id' => $period['id'],
            'uuid' => $period['uuid'],
            'dateStarPeriod' => $period['dateStarPeriod'],
            'dateClosingPeriod' => $period['dateClosingPeriod']
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $period = Period::Where('uuid', '=', $uuid)->first();
            $period->delete();
            Log::info('PeriodController - delete - Eliminaste una actividad');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('PeriodController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
