<?php
/**
 * Created by PhpStorm.
 * User: tailer
 * Date: 24/09/17
 * Time: 18:54
 */

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller {

    public function postUser(Request $request, Mailer $mailer) {

        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'required|email|unique:users',
            'registration' => 'required|Min:9|Max:9|unique:users',
            'cpf' => 'required|Min:11|Max:11|unique:users',
            'rg' => 'required|Min:10|Max:10|unique:users',
            'is_admin' => 'required',
            'id_course' => 'required'
        ],[
            'fullName.required' => 'Um nome completo é necessário',
            'email.required'  => 'Preencha o campo de email',
            'email.unique' => "Este email já esta cadastrado no sistema",
            'registration.required' => "Você deve fornecer o número de matrícula",
            'registration.unique' => "Esta mátricula já esta cadastrada no sistema",
            'cpf.required' => "Você deve fornecer o número do CPF",
            'cpf.unique' => "Este CPF já esta cadastrado",
            'rg.required' => "Você deve fornecer o número do RG",
            'rg.unique' => "Este RG já esta cadastrado",
            'id_course.required' => "Você deve especificar o curso",
            'is_admin.required' => "Você deve especificar se o usuário é administrador",
        ]);

        $randomPass = str_random(8);

        $user = new User([
            'fullName' => $request->input('fullName'),
            'email' => $request->input('email'),
            'registration' => $request->input('registration'),
            'password' => bcrypt($randomPass),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'genre' => $request->input('genre'),
//            'is_bse_active' => $request->input('is_bse_active'),
            'is_admin' => $request->input('is_admin'),
            'id_course' => $request->input('id_course'),
//            'id_apto' => $request->input('id_apto')
        ]);

        if($user->save()) {
            $mailer->to($request->input('email'))
                ->send(new \App\Mail\UserCreated(
                    $request->input('fullName'), $request->input('registration'), $randomPass
                ));
        }

        return response()->json([
            'message' => 'Usuário criado com sucesso, um e-mail foi mandado para '. $request->input('email')
        ],201);
    }

    public function postUsers(Request $request){

        $errors = Array();
        $success = Array();
        foreach ( $request->body as $key=>$value) {

            $db_email = DB::table('users')->where('email', $value['email'])->value('email');
            $db_registration = DB::table('users')->where('registration', $value['registration'])->value('registration');
            $db_cpf = DB::table('users')->where('cpf', $value['cpf'])->value('cpf');
            $db_rg = DB::table('users')->where('rg', $value['rg'])->value('rg');

            if(empty($value['email'])){
                $errors[$key]["email"] = "O campo email é obrigatório e deve ser preenchido.";
            }
            else if(!empty($db_email)){
                $errors[$key]["email"] = "O email ".$db_email." deste usuário já existe no sistema.";
            }
            else if(empty($value['registration'])){
                $errors[$key]["registration"] = "O campo Matrícula é obrigatório e deve ser preenchido.";
            }
            else if(!empty($db_registration)){
                $errors[$key]["registration"] = "A Matrícula ".$db_registration." já existe no sistema.";
            }
            else if(empty($value['cpf'])){
                $errors[$key]["cpf"] = "O campo CPF é obrigatório e deve ser preenchido.";
            }
            else if(!empty($db_cpf)){
                $errors[$key]["cpf"] = "O CPF ".$db_cpf." já existe no sistema.";
            }
            else if(empty($value['rg'])){
                $errors[$key]["rg"] = "O RG é obrigatório e deve ser preenchido.";
            }
            else if(!empty($db_rg)){
                $errors[$key]["rg"] = "O RG ".$db_rg." já existe no sistema.";
            }else {
                $randomPass = str_random(8);
                $user = new User([
                    'fullName' => $value['fullName'],
                    'email' => $value['email'],
                    'registration' => $value['registration'],
                    'password' => bcrypt($randomPass),
                    'cpf' => $value['cpf'],
                    'rg' => $value['rg'],
//                'genre' => $i['genre'],
//                'is_bse_active' => $i['is_bse_active'],
                    'is_admin' => false,
//                'id_course' => $i['id_course'],
//                'id_apto' => $i['id_apto']
                ]);

                if($user->save()) {

                    $job = (new SendEmailJob($user, $randomPass))
                        ->delay(Carbon::now()->addSeconds(5));

                    $this->dispatch($job);
//                    Mail::later(5, 'emails.registeredUser', $data, function($message){
//                        $message->to($value['email'], $value['fullName'])->subject('Bem-vindo à CEU II');
//                    });

                    $success[$key]["message"] = "Usuário criado com sucesso, um e-mail foi mandado para ".$value['email'];
                }
            }
        }


        return response()->json([
            "erros" => $errors,
            "success" => $success
        ], 200);

    }


    public function getUsers(){
        $users = User::all();
        $response = [
            'users' => $users
        ];

        return response()->json($response, 200);
    }

    public function putUser(Request $request, $id){
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }


        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'required|email|unique:users',
            'registration' => 'required|Min:9|Max:9|unique:users',
            'cpf' => 'required|Min:11|Max:11|unique:users',
            'rg' => 'required|Min:10|Max:10|unique:users',
            'is_admin' => 'required',
            'id_course' => 'required'
        ]);

        $user->content = $request->input('content');
        $user->save;

        return response()->json(['user' => $user], 200);
    }

    public function deleteUser($id){
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }

        $user->delete();
        return response()->json(['message' => "Usuário deletado com sucesso"], 200);
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