<?php

namespace App\Http\Controllers;

use App\Models\LogEventoError;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    
    public function login(Request $request){

        try {
            //validaciones
            $messages = [ 
                'email.required' => 'Este campo es obligatorio.',
                'email.max:255' => 'Este campo debe tener maximo 255 caracteres',
                'email.email' => 'Este campo debe ser un correo electronico',
                'password.required' => 'Este campo es obligatorio.',
                'password.max:255' => 'Este campo debe tener maximo 255 caracteres',

            ];

            $validator = Validator::make($request->all(),[
                'email' => 'required|email:rfc,dns|max:255',
                'password' => 'required',
            ],$messages);

            if($validator->fails()){
                return response()->json(['error' => $validator->errors()], 422);
            }

            //credenciales

            $credentials = request(['email', 'password']);

            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $response =[
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ];
            return $response;

            /*
            //login ok
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
            
                return response()->json(['status' => 200, 'message' => 'Usuario Inicio sesion correctamente', 'tokenAccess' => ''], 200);
            }

            return response()->json(['status' => 404, 'message' => 'Credenciales incorrectas', 'tokenAccess' => null], 200);*/
        } catch (\Throwable $th) {

            LogEventoError::create(array(
                "id_log" => "create",
                "id_evento" => "EXCEPTION",
                "notes" => $th->getMessage() . " " . json_encode($request),
                "created_by" => $request->email ? $request->email : "Admin"
            ));
            return response()->json(['error' => 'Error en el proceso'], 500);
        }

    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['status'=> 200,'message' => 'Sesion cerrada correctamente'],200);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
