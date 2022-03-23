<?php
namespace App\Repositories;
use App\Models\User;

class UserRepository{

    public function create($uuid, $name, $lastNameP, $lastNameM, $email, $password, $validation, $people_id, $rol_id){
        $user['uuid'] = $uuid;
        $user['name'] = $name.' '.$lastNameP.' '.$lastNameM;
        $user['email'] = $email;
        $user['password'] = $password;
        $user['validation'] = $validation;
        $user['people_id'] = $people_id;
        $user['rol_id'] = $rol_id;
        return User::create($user);
    }

    public function update($uuid, $name, $lastNameP, $lastNameM){
        $user = $this->find($uuid);
        $user->name = $name .' '.$lastNameP.' '. $lastNameM;
        $user->save();
        return $user;
    }

    public function find($uuid){
        return User::Where('uuid', '=', $uuid)->first();
    }

    public function delete($uuid){
        $user = $this->find($uuid);
        return $user->delete();
    }
}
