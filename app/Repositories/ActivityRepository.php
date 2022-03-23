<?php
namespace App\Repositories;
use App\Models\Activity;

class ActivityRepository{

    public function create($uuid, $name, $description){
        $activity['uuid'] = $uuid;
        $activity['name'] = $name;
        $activity['description'] = $description;
        return Activity::create($activity);
    }

    public function update($uuid, $name, $description){
        $activity = $this->find($uuid);
        $activity->name = $name;
        $activity->description = $description;
        $activity->save();
        return $activity;
    }
    
    public function find($uuid){
        return Activity::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $activity = $this->find($uuid);
        return $activity->delete();
    }

    public function list(){
        return Activity::all();
    }
}