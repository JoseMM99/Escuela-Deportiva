<?php
namespace App\Repositories;
use App\Models\Practice;

class PracticeRepository{

    public function create($uuid, $fechaEntrenamiento){
        $practice['uuid'] = $uuid;
        $practice['fechaEntrenamiento'] = $fechaEntrenamiento;
        return Practice::create($practice);
    }

    public function update($uuid, $fechaEntrenamiento){
        $practice = $this->find($uuid);
        $practice->fechaEntrenamiento = $fechaEntrenamiento;
        $practice->save();
        return $practice;
    }

    public function find($uuid){
        return Practice::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $practice = $this->find($uuid);
        return $practice->delete();
    }

    public function list(){
        return Practice::all();

    }
}