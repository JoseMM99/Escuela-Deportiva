<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\TeacherRepository;
use App\Repositories\PeopleRepository;
use App\Repositories\UserRepository;
use App\Models\Teacher;
use App\Models\People;
use App\Models\User;
use File;
use JWTAuth;
use Mail;
use Uuid;

class TeacherController extends Controller
{
    //
    protected $teacher_repository;
    protected $people_repository;
    protected $user_repository;

    public function __construct(TeacherRepository $teacher, PeopleRepository $people, UserRepository $user){
        $this->teacher_repository = $teacher;
        $this->people_repository = $people;
        $this->user_repository = $user;
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
            'rfc' => 'required|string|max:13',
        ]);
        if($validator->fails()){
            Log::warning('TeacherController - register - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();

        try{
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

            $token = JWTAuth::fromUser($user);
            $this->sendEmail($user);

            DB::commit();
            
            Log::info('TeacherController - register - Se creo un nuevo usuario');
            return response()->json(compact('user', 'token', 'teacher', 'person'),201);

        }catch(\Exception $ex){
            Log::emergency('TeacherController - register - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
            DB::rollback();
        }
    }

    public function update(Request $request, $uuid){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:35',
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
            'rfc' => 'required|string|max:13'
        ]);
        if($validator->fails()){
            Log::warning('TeacherController - update - Falta un campo por llenar');
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();

        try{
            $global = People::Where('uuid', '=', $uuid)->first();

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
                $request->get('photo')
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
            );

            DB::commit();

            Log::info('TeacherController - update - Se actualizó un profesor');
            return response()->json(compact('user', 'teacher', 'person'),201);

        }catch(\Exception $ex){
            Log::emergency('TeacherController - update - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
            DB::rollback();
        }
    }

    public function upload(Request $request){
        $image = $request->file('file0');
        $validator = Validator::make($request->all(),[
            'file0'=> 'mimes:jpg,png,jpeg|required|max:3000'//si no es max es size
        ]);

        if($validator->fails()){
            Log::warning('TeacherController - upload - Error, Imagen no aceptada o imagen no enviada');
            return response()->json($validator->errors()->toJson(), 400);
        }
        $image_name = time(). $image->getClientOriginalName();
        \Storage::disk('imagenes')->put($image_name, \File::get($image));
        $data = array(
            'code' => 200,
            'imagen' => $image_name,
            'status' => 'success'
        );
        return response()->json($data, $data['code']);
    }

    public function return_image($name){
        $image = \Storage::disk('imagenes')->get($name);
        if($imagen){
            $file = \Storage::disk('imagenes')->get($name);
            return new Response($file,201);
        }else{
            return response()->Json('No existe la imagen');
        }
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

    public function list(){
        $person = User::Where('rol_id', '=', User::Teacher)->get();
        $teachers = [];
        foreach($person as $key=> $value){
            $teachers[$key] = [
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
                'uuid_teacher' => $value->people->teacher->uuid,
                'rfc' => $value->people->teacher->rfc,
            ];
            return response()->json($teachers);
        }
    }

    public function edit($uuid){
        $person = People::Where('uuid', '=', $uuid)->first();
        $user = User::Where('uuid', '=', $person->user->uuid)->first();
        $teacher = Teacher::Where('uuid', '=', $person->teacher->uuid)->first();

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
            'uuid_teacher' => $teacher['uuid'],
            'rfc' => $teacher['rfc'],
        ];
        return response()->json($masvar);
    }

    public function delete($uuid){
        try{
            $person = People::Where('uuid', '=', $uuid)->first();
            $person->teacher->delete();
            $person->user->delete();
            $person->delete();
            Log::info('TeacherController - delete - Eliminaste un Entrenador');
            return response()->json('Datos eliminados');

        }catch(\Exception $ex){
            Log::emergency('TeacherController - delete - Ocurrio un error');
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }
        
}
