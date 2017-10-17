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
use App\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller {

    /*
     * TODO diminuir vags conforme cadastro se for designado apartamento
     * TODO verifcar se ainda ha vagas para cadastrar usuarios naquele apartamneto
    */
    public function postUser(Request $request, Mailer $mailer) {

        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'required|email|unique:users',
            'registration' => 'required|Min:9 |Max:9|unique:users',
            'cpf' => 'required|Min:11|Max:11|unique:users',
            'rg' => 'required|Min:10|Max:10|unique:users',
            'age' => 'required|Min:2',
            'genre' => 'required',
            'id_course' => 'required',
            'is_admin' => 'required',
            'is_bse_active' => 'required'
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
            'age.required' => "Você deve especificar a idade do usuário",
            'genre.required' => "Você deve fornecer o gênero do usuário",
            'id_course.required' => "Você deve especificar o curso",
            'is_admin.required' => "Você deve especificar se o usuário é administrador",
            'is_bse_active.required' => "Você deve especificar se o usuário possui BSE ativo",
        ]);

        $randomPass = str_random(8);

        $user = new User([
            'fullName' => $request->input('fullName'),
            'email' => $request->input('email'),
            'registration' => $request->input('registration'),
            'password' => bcrypt($randomPass),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'age' => $request->input('age'),
            'genre' => $request->input('genre'),
            'is_bse_active' => $request->input('is_bse_active'),
            'is_admin' => $request->input('is_admin'),
            'id_course' => $request->input('id_course'),
            'id_apto' => $request->input('id_apto')
        ]);

        if($user->save()) {
            $job = (new SendEmailJob($user, $randomPass))
                ->delay(Carbon::now()->addSeconds(2));
            $this->dispatch($job);
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


            if(!empty($db_email) || !empty($db_registration)){
                continue;
            }
            if(empty($value['email'])){
                $errors[$key]["email"] = "O campo email é obrigatório e deve ser preenchido.";
            }
            if(empty($value['registration'])){
                $errors[$key]["registration"] = "O campo Matrícula é obrigatório e deve ser preenchido.";
            }
            if(empty($value['cpf'])){
                $errors[$key]["cpf"] = "O campo CPF é obrigatório e deve ser preenchido.";
            }
            if(empty($value['rg'])){
                $errors[$key]["rg"] = "O RG é obrigatório e deve ser preenchido.";
            }
            if(empty($errors[$key])){
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
                        ->delay(Carbon::now()->addSeconds(2));

                    $this->dispatch($job);

                    $success[$key]["message"] = "Usuário criado com sucesso, um e-mail foi mandado para ".$value['email'];
                }
            }
        }


        return response()->json([
            "erros" => $errors,
            "total_erros" => count($errors),
            "success" => $success,
            "total_success" => count($success),
        ], 200);

    }

    public function getUsers(){
        $users = User::all();
        $response = [
            'data' => $users
        ];

        return response()->json($response, 200);
    }

    public function putUser(Request $request){
        $user = User::find($request['registration']);

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

        $this->validate($request, [
           'registration' => 'required',
           'password' => 'required'
        ]);
        $credentials = $request->only('registration', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Matrícula ou Senha estão incorretos',
                ], 403);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Não pode criar o token',
            ], 500);
        }
        return response()->json([
            'response' => 'success',
            'token' => $token
        ], 200);
    }

    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['user' => $user]);
    }

    public function getGenre(){
        $male = User::where('genre', "M")->get();
        $female = User::where('genre', "F")->get();

        $total = count($male) + count($female);

        return response()->json([
            'body' =>['Male' => count($male),
                      'Female' => count($female)],
                      'Total' => $total
        ], 200);
    }

    public function getCoursesType(){

        $graduation = DB::select( DB::raw("select c.type from courses as c inner join (users) on users.id_course = c.id and c.type = \"Graduação\";") );
        $mestrado = DB::select( DB::raw("select c.type from courses as c inner join (users) on users.id_course = c.id and c.type = \"Mestrado\";") );

        $total = count($graduation) + count($mestrado);

        return response()->json([
            'body' =>['Graduation' => count($graduation),
                      'Master' => count($mestrado)],
                      'Total' => $total
        ], 200);
    }
}