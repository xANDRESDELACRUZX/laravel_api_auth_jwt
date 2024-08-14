<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(){
        //validar los campos
        try{

            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin')
    
            ]);
            return array("msg"=>"usuario creado correctamente" ,"data" => $user);   
        }catch(\Exception $th){
            
/*             LogEventosError::create(array(
                "id_log" => "preparar_prefactura",
                "id_entidad" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($item),
                "created_by" => $data->usuario
            )); */
            return response()->json(['error' => 'Error al crear el usuario', "msg" => $th->getMessage()], 500);
        }     
    }

    public function getList(){
        $users = User::all();
        return $users;

       // array("msg"=>"usuario creado correctamente" ,"data" => $users);        
    }
}
