<?php
namespace App\Repositories;
use App\Models\Rol;

class RolRepository{

    public function list(){
        return Rol::all();
    }

    
} 