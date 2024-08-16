<?php

namespace App\Http\Controllers;

use App\baseLogic\UserLogic;
use App\Models\LogEventoError;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function getList(Request $request){

        try{
            //validar los campos

            $users = User::all();
            return response()->json([$users],200);

        }catch(\Exception $th){
            
            LogEventoError::create(array(
                "id_log" => "getList",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($request),
                "created_by" => isset($request->usuario) ? $request->usuario : "Admin"
            ));
            return response()->json(['error' => 'Error en el proceso'], 500);
        }  
    }

    public function create(Request $request){
        try{
            //validar los campos
            //mensajes de validacion que se muestran en la vista debajo de cada campo con error 

            $messages = [ 
                'name.required' => 'Este campo es obligatorio.',
                'name.max' => 'Este campo debe tener maximo 255 caracteres',
                'email.required' => 'Este campo es obligatorio.',
                'email.max' => 'Este campo debe tener maximo 255 caracteres',
                'email.email' => 'Este campo debe ser un correo electronico',
                'email.unique'=> 'El correo ya esta en uso',
                'password.required' => 'Este campo es obligatorio.',
                
                
            ];
            
            $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'email' => 'required|unique:users|email:rfc,dns|max:255',
                'password' => 'required',
            ],$messages);

            if($validator->fails()){
                return response()->json(['error' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 1
    
            ]);
            return response()->json(["msg"=>"usuario creado correctamente" ,"data" => $user],200);   
        }catch(\Exception $th){
            
            LogEventoError::create(array(
                "id_log" => "create",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($request),
                "created_by" => $request->usuario? $request->usuario : "Admin"
            ));
            return response()->json(['error' => 'Error en el proceso'], 500);
        }     
    }

    public function editUser($id,Request $request){

        try{
            //validar los campos
            //mensajes de validacion que se muestran en la vista debajo de cada campo con error 

            $messages = [ 
                'name.required' => 'Este campo es obligatorio.',
                'name.max' => 'Este campo debe tener maximo 255 caracteres',
                'email.required' => 'Este campo es obligatorio.',
                'email.max' => 'Este campo debe tener maximo 255 caracteres',
                'email.email' => 'Este campo debe ser un correo electronico',
                'password.required' => 'Este campo es obligatorio.'

            ];
            
            $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'email' => 'required|email:rfc,dns|max:255',
                //'password' => 'required',
            ],$messages);

            if($validator->fails()){
                return response()->json(['error' => $validator->errors()], 422);
            }

            //query
            $user = user::where('id', $id)->first();
            
            if(!isset($user)){
                return response()->json(['msg' => "no existe el usuario",'status' => 404], 200);
            }

            $existsEmail = User::where('email', $user->email)->where('id','<>', $user->id)->first();

            if ( $existsEmail ) {
                return response()->json(['message' => 'El correo ya esta en uso', 'status' => 404], 200);
            }

            //Inicio de la transaccion
            DB::beginTransaction();
    
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
           
            //Transaccion exitosa
            DB::commit();

            return response()->json(['msg' => 'Usuario editado con exito'], 200);

        }catch(\Exception $th){
            
            //devolver el commit en la db
            DB::rollback();

            LogEventoError::create(array(
                "id_log" => "editUser",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($request),
                "created_by" => isset($request->usuario) ? $request->usuario : "Admin"
            ));
            return response()->json(['error' => 'Error en el proceso'], 500);
        }  
    }

    
    public function statedUser($id,Request $request){

        try{

           //validaciones 
            $user = user::where('id', $id)->first();

            if(!isset($user)){
                return response()->json(['msg' => "no existe el usuario",'status' => 404], 200);
            }

            //Inicio de la transaccion
            DB::beginTransaction();
            $user->update([
                'status' => $request->accion == 'activar' ? 1 : 0

            ]);

            
            //Transaccion exitosa
            DB::commit();

            if($request->accion == 'activar'){
                return response()->json(['msg' => 'Usuario activado con exito'], 200);
            }
            return response()->json(['msg' => 'Usuario eliminado con exito'], 200);

        }catch(\Exception $th){
            
            //devolver el commit en la db
            DB::rollback();

            LogEventoError::create(array(
                "id_log" => "statedUser",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($request),
                "created_by" => $request->usuario ? $request->usuario : "Admin"
            ));

            return response()->json(['error' => 'Error en el proceso'], 500);
        }  
    }

    public function getuserById ($id){

        try {
            $user = user::where('id',$id)->first();
            
            if(!isset($user)){
                return response()->json(['msg' => "no existe el usuario" ,'status' => 404], 200);
            }
            return response()->json([$user], 200);
        } catch (\Throwable $th) {

            LogEventoError::create(array(
                "id_log" => "statedUser",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($id),
                "created_by" => /*$request->usuario ? $request->usuario :*/ "Admin"
            ));
             return response()->json(['error' => 'Error en el proceso'], 500);
        }
    }

    public function userRequest(Request $request){
        return UserLogic::processCommand($request);
    }
}
