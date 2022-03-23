<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\RolRepository;

class RolController extends Controller
{
    //
    protected $rol_repository;
    
    public function __construct(RolRepository $rol){
        $this->rol_repository = $rol;
    }

    public function list(){
        return response()->json($this->rol_repository->list());
    }
}
