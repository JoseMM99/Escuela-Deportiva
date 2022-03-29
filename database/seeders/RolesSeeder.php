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
        $roles->Description = 'Tiene acceso a agregar, ver, editar o eliminar maestros y alumnos, así también crear periodos escolares en el sistema.';
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'maestro';
        $roles->Description = "Tiene acceso ver alumnos, también podrá crear, ver, editar o eliminar actividades, entrenamientos y evaluaciones de los alumnos.";
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'alumno';
        $roles->Description = "Tiene acceso a ver las calificaciones, retroalimentación de las actividades que realizó en el entrenamiento.";
        $roles->save();

        $roles = new Rol();
        $roles->uuid = Uuid::generate()->string;
        $roles->rol = 'SuperAdmin';
        $roles->Description = 'Tiene acceso a agregar, ver, editar o eliminar administradores y crear un backup del sistema.';
        $roles->save();
    }
}
