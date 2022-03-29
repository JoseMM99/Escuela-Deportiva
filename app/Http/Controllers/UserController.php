<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\TeacherRepository;
use App\Repositories\PeopleRepository;
use App\Repositories\UserRepository;
use App\Repositories\StudentRepository;
use App\Models\Teacher;
use App\Models\People;
use App\Models\User;
use App\Models\Student;
use JWTAuth;
use Mail;
use Uuid;

class UserController extends Controller
{
    //
    protected $teacher_repository;
    protected $people_repository;
    protected $user_repository;
    protected $student_repository;

    public function __construct(TeacherRepository $teacher, PeopleRepository $people, UserRepository $user, StudentRepository $student){
        $this->teacher_repository = $teacher;
        $this->people_repository = $people;
        $this->user_repository = $user;
        $this->student_repository = $student;
    }

    public function authenticate(Request $request){
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $users = JWTAuth::user();

        if ($users->validation != '') {
            Log::warning('UserController - authenticate - el usuario no fue validado' . $users);
            return response()->json(['error' => 'User_not_validated'], 403);
        }

        if($users->rol_id == User::Teacher){
            $person = $users->people;
            $roles = $users->rol;
            $teacher = $users->people->teacher;
        }

        if($users->rol_id == User::Student){
            $person = $users->people;
            $roles = $users->rol;
            $student = $users->people->student;
        }

        if($users->rol_id == User::Admin || $users->rol_id == User::SuperAdmin){
            $person = $users->people;
            $roles = $users->rol;
        }

        Log::info('UserController - authenticate - Inicio sesión el usuario' . $users);
        return response()->json(compact('token', 'users'));
    }

    public function getAuthenticatedUser(){
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return response()->json(['token_expired'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                    return response()->json(['token_invalid'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return response()->json(['token_absent'], $e->getStatusCode());
            }
            $person = $user->people;
            $roles = $user->rol;
            return response()->json(compact('user'));
    }

    public function sendEmail($user){
        $datas['subject'] = 'Piso Trece';
        $datas['for'] = $user['email'];
        Mail::send('mail.mail', ['user'=>$user],function ($msj) use ($datas){
            $msj->from("20183l301014@utcv.edu.mx", "Piso Trece");
            $msj->subject($datas['subject']);
            $msj->to($datas['for']);
        });
    }

    public function validation(Request $request){
        $user = User::Where([['email', '=', $request->get('email')],['validation', '=', $request->get('validar')]])->get();

        if(count($user) > 0){
            $user[0]->validation='';
            $user[0]->save();
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:35',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|max:16',
            'lastNameP' => 'required|string|max:30',
            'lastNameM' => 'required|string|max:30',
            'gender' => 'required|string|max:9',
            'bloodGroup' => 'required|string|max:2',
            'rhFactor' => 'required|string|max:8',
            'birthDate' => 'required|date',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:25',
            'avenue' => 'required|string|max:25',
            'postalCode' => 'required|string|max:5',
        ]);
        if($validator->fails()){
            Log::warning('UserController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);

        }
        try{
            if($request->input("roles")=="teacher"){
                $person = $this->people_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                $teacher = $this->teacher_repository->create(
                    Uuid::generate()->string,
                    $request->get('rfc'),
                    $person->id,
                );
                $user = $this->user_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    Hash::make($request->get('password')),
                    $request->get('validation').substr($request->get('name'),0,3).substr($request->get('email'),0,3).'2022',
                    $person->id,
                    User::Teacher
                );
            }
            else if($request->input("roles")=="student"){
                $person = $this->people_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                $student = $this->student_repository->create(
                    Uuid::generate()->string,
                    $request->get('curp'),
                    $request->get('period_id'),
                    $person->id,
                );
                $user = $this->user_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    Hash::make($request->get('password')),
                    $request->get('validation').substr($request->get('name'),0,3).substr($request->get('email'),0,3).'2022',
                    $person->id,
                    User::Student
                );
            }
            else if($request->input("roles")=="admin"){
                $person = $this->people_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                    $user = $this->user_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    Hash::make($request->get('password')),
                    $request->get('validation').substr($request->get('name'),0,3).substr($request->get('email'),0,3).'2022',
                    $person->id,
                    User::Admin
                );
            }
            else if($request->input("roles")=="superadmin"){
                $person = $this->people_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                    $user = $this->user_repository->create(
                    Uuid::generate()->string,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    Hash::make($request->get('password')),
                    $request->get('validation').substr($request->get('name'),0,3).substr($request->get('email'),0,3).'2022',
                    $person->id,
                    User::SuperAdmin
                );
            } 

            $token = JWTAuth::fromUser($user);
            $this->sendEmail($user);
            
            Log::info('UserController - register - Se creo un nuevo usuario');
            return response()->json(compact('user', 'token', 'person'),201);

        }catch(\Exception $ex){
            Log::emergency('UserController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:35',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|max:16',
            'lastNameP' => 'required|string|max:30',
            'lastNameM' => 'required|string|max:30',
            'gender' => 'required|string|max:9',
            'bloodGroup' => 'required|string|max:2',
            'rhFactor' => 'required|string|max:8',
            'birthDate' => 'required|date',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:25',
            'avenue' => 'required|string|max:25',
            'postalCode' => 'required|string|max:5',
        ]);
        if($validator->fails()){
            Log::warning('UserController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }
        try{
            $global = People::Where('uuid', '=', $uuid)->first();

            if($request->input("roles")=="student"){

                $person = $this->people_repository->update(
                    $global->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                $teacher = $this->teacher_repository->update(
                    $global->teacher->uuid,
                    $request->get('rfc'),
                );
                $user = $this->user_repository->update(
                    $global->user->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    $request->get('password'),
                );
            }
            else if($request->input("roles")=="student"){
                $person = $this->people_repository->update(
                    $global->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                $student = $this->student_repository->update(
                    $global->student->uuid,
                    $request->get('curp'),
                );
                $user = $this->user_repository->update(
                    $global->user->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    $request->get('password'),
                );
            }
            else if($request->input("roles")=="admin"){
                $person = $this->people_repository->update(
                    $global->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                    $user = $this->user_repository->update(
                    $global->user->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    $request->get('password'),
                );
            }
            else if($request->input("roles")=="superadmin"){
                $person = $this->people_repository->update(
                    $global->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('gender'),
                    $request->get('bloodGroup'),
                    $request->get('rhFactor'),
                    $request->get('birthDate'),
                    $request->get('phone'),
                    $request->get('street'),
                    $request->get('avenue'),
                    $request->get('postalCode'),
                    'default.jpg'
                );
                    $user = $this->user_repository->update(
                    $global->user->uuid,
                    $request->get('name'),
                    $request->get('lastNameP'),
                    $request->get('lastNameM'),
                    $request->get('email'),
                    $request->get('password'),
                );
            }
            
            $token = JWTAuth::fromUser($user);
            $this->sendEmail($user);
            
            Log::info('UserController - update - Se actualizó un usuario');
            return response()->json(compact('user', 'token', 'person'),201);

        }catch(\Exception $ex){
            Log::emergency('UserController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    public function listSA(){
        $person = User::Where('rol_id', '=', User::SuperAdmin)->get();
        $usersL = [];
        foreach($person as $key=> $value){
            $usersL[$key] = [
                'id'=> $value['id'],
                'uuid'=> $value['uuid'],
                'name'=> $value['name'],
                'email'=> $value['email'],
                'validation'=> $value['validation'],
                'people_id'=> $value['people_id'],
                'rol_id'=> $value['rol_id'],
                'uuid_persona' => $value->people->uuid,
                'name_persona' => $value->people->name,
                'lastNameP' => $value->people->lastNameP,
                'lastNameM' => $value->people->lastNameM,
                'gender' => $value->people->gender,
                'bloodGroup' => $value->people->bloodGroup,
                'rhFactor' => $value->people->rhFactor,
                'birthDate' => $value->people->birthDate,
                'phone' => $value->people->phone,
                'street' => $value->people->street,
                'avenue' => $value->people->avenue,
                'postalCode' => $value->people->postalCode,
                'photo' => $value->people->photo,
            ];
            return response()->json($usersL);
        }
    }

    public function listA(){
        $person = User::Where('rol_id', '=', User::Admin)->get();
        $usersL = [];
        foreach($person as $key=> $value){
            $usersL[$key] = [
                'id'=> $value['id'],
                'uuid'=> $value['uuid'],
                'name'=> $value['name'],
                'email'=> $value['email'],
                'validation'=> $value['validation'],
                'people_id'=> $value['people_id'],
                'rol_id'=> $value['rol_id'],
                'uuid_persona' => $value->people->uuid,
                'name_persona' => $value->people->name,
                'lastNameP' => $value->people->lastNameP,
                'lastNameM' => $value->people->lastNameM,
                'gender' => $value->people->gender,
                'bloodGroup' => $value->people->bloodGroup,
                'rhFactor' => $value->people->rhFactor,
                'birthDate' => $value->people->birthDate,
                'phone' => $value->people->phone,
                'street' => $value->people->street,
                'avenue' => $value->people->avenue,
                'postalCode' => $value->people->postalCode,
                'photo' => $value->people->photo,
            ];
            return response()->json($usersL);
        }
    }

    public function edit($uuid){
        $person = People::Where('uuid', '=', $uuid)->first();
        $user = User::Where('uuid', '=', $person->user->uuid)->first();

        $masvar = [
            'id' => $person['id'],
            'uuid' => $person['uuid'],
            'name' => $person['name'],
            'lastNameP' => $person['lastNameP'],
            'lastNameM' => $person['lastNameM'],
            'gender' => $person['gender'],
            'bloodGroup' => $person['bloodGroup'],
            'rhFactor' => $person['rhFactor'],
            'birthDate' => $person['birthDate'],
            'phone' => $person['phone'],
            'street' => $person['street'],
            'avenue' => $person['avenue'],
            'postalCode' => $person['postalCode'],
            'photo' => $person['photo'],
            'uuid_user' => $user['uuid'],
            'name_user' => $user['name'],
            'email' => $user['email'],
            'validation' => $user['validation'],
            'rol_id' => $user['rol_id'],
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $person = People::Where('uuid', '=', $uuid)->first();
            $person->user->delete();
            $person->delete();
            Log::info('UserController - delete - Eliminaste un Entrenador');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('UserController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
}
