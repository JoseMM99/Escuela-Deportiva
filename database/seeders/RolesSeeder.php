<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use Uuid;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'administrador';
        $roles->Description = 'Tiene acceso agregar maestros y alumnos';
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'maestro';
        $roles->Description = "Tiene acceso a asignar calificaciones, dar retrolimentación y asignar actividades";
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'alumno';
        $roles->Description = "Tiene acceso a ver su calificación y ver retroalimentación";
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'SuperAdmin';
        $roles->Description = 'Puede agregar, eliminar administradores y crear un backup del sistema';
        $roles->save();
    }
}
