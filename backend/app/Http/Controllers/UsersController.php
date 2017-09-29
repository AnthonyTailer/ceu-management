<?php
/**
 * Created by PhpStorm.
 * User: tailer
 * Date: 24/09/17
 * Time: 18:54
 */

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;

class UsersController extends Controller {

    public function __construct(){
        $this->user = new User;
    }

    public function register(Request $request, Mailer $mailer) {

        $validator = Validator::make($request->all(), [
            '*.fullName' => 'required',
            '*.email' => 'required|email|unique:users',
            '*.registration' => 'required|Min:9|Max:9|unique:users',
            '*.cpf' => 'required|Min:11|Max:11|unique:users',
            '*.rg' => 'required|Min:10|Max:10|unique:users',
            '*.is_admin' => 'required'
        ]);

        dd($validator->errors());

//        $randomPass = str_random(8);
//
//        $user = new User([
//            'fullName' => $request->input('fullName'),
//            'email' => $request->input('email'),
//            'registration' => $request->input('registration'),
//            'password' => bcrypt($randomPass),
//            'cpf' => $request->input('cpf'),
//            'rg' => $request->input('rg'),
//            'genre' => $request->input('genre'),
//            'is_bse_active' => $request->input('is_bse_active'),
//            'is_admin' => $request->input('is_admin'),
//            'id_course' => $request->input('id_course'),
//            'id_apto' => $request->input('id_apto')
//        ]);
//
//        if($user->save()) {
//            $mailer->to($request->input('email'))
//                ->send(new \App\Mail\UserCreated(
//                    $request->input('fullName'), $request->input('registration'), $randomPass
//                ));
//        }

        return response()->json([
            'message' => 'Usuário criado com sucesso, um e-mail foi mandado para '. $request->input('email')
        ],201);
    }

    public function login(Request $request) {
        $credentials = $request->only('registration', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'E-mail ou senha estão incorretos',
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ]);
        }
        return response()->json([
            'response' => 'success',
            'result' => [
                'token' => $token,
            ],
        ]);
    }

    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }

}