<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\People;
use Uuid;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        /*$person = new People();
        $person->uuid = Uuid::generate()->string;
        $person->name = 'José Alberto';
        $person->lastNameP = 'Mendoza';
        $person->lastNameM = 'Montañez';
        $person->gender = 'Masculino';
        $person->bloodGroup = 'O';
        $person->rhFactor = 'Positivo';
        $person->birthDate = '1999-09-09';
        $person->phone = '271-172-0699';
        $person->street = 'Reforma';
        $person->avenue = 's/n';
        $person->postalCode = '94940';
        $person->photo = 'default.jpg';
        $person->save();

        $users = new User();
        $users->uuid = Uuid::generate()->string;
        $users->name = 'José Alberto Mendoza Montañez';
        $users->email = 'jm14josemendozajamm@gmail.com';
        $users->password = 'Jose1999$';
        $users->validation = 'Josjos2022';
        $users->people_id = 1;
        $users->rol_id = 4;
        $users->save();*/
    }
}
