<?php
namespace App\Repositories;
use App\Models\People;

class PeopleRepository{

    public function create($uuid, $name, $lastNameP, $lastNameM, $gender, $bloodGroup, $rhFactor, $birthDate, $phone, $street, $avenue, $postalCode){
        $people['uuid'] = $uuid;
        $people['name'] = $name;
        $people['lastNameP'] = $lastNameP;
        $people['lastNameM'] = $lastNameM;
        $people['gender'] = $gender;
        $people['bloodGroup'] = $bloodGroup;
        $people['rhFactor'] = $rhFactor;
        $people['birthDate'] = $birthDate;
        $people['phone'] = $phone;
        $people['street'] = $street;
        $people['avenue'] = $avenue;
        $people['postalCode'] = $postalCode;
        return People::create($people);
    }

    public function update($uuid, $name, $lastNameP, $lastNameM, $gender, $bloodGroup, $rhFactor, $birthDate, $phone, $street, $avenue, $postalCode){
        $people = $this->find($uuid);
        $people->name = $name;
        $people->lastNameP = $lastNameP;
        $people->lastNameM = $lastNameM;
        $people->gender = $gender;
        $people->bloodGroup = $bloodGroup;
        $people->rhFactor = $rhFactor;
        $people->birthDate = $birthDate;
        $people->phone = $phone;
        $people->street = $street;
        $people->avenue = $avenue;
        $people->postalCode = $postalCode;
        $people->save();
        return $people;
    }

    public function find($uuid){
        return People::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $people = $this->find($uuid);
        return $people->delete();
    }
}