<?php
namespace App\Repositories;
use App\Models\Period;

class PeriodRepository{

    public function create($uuid, $dateStarPeriod, $dateClosingPeriod){
        $period['uuid'] = $uuid;
        $period['dateStarPeriod'] = $dateStarPeriod;
        $period['dateClosingPeriod'] = $dateClosingPeriod;
        return Period::create($period);
    }

    public function update($uuid, $dateStarPeriod, $dateClosingPeriod){
        $period = $this->find($uuid);
        $period->dateStarPeriod = $dateStarPeriod;
        $period->dateClosingPeriod = $dateClosingPeriod;
        $period->save();
        return $period;
    }

    public function find($uuid){
        return Period::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $period = $this->find($uuid);
        return $period->delete();
    }
    
    public function list(){
        return Period::all();
    }
}