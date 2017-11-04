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
use App\Apartament;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Gate;


class UsersController extends Controller {

    /*
     * TODO diminuir vags conforme cadastro se for designado apartamento
     * TODO verifcar se ainda ha vagas para cadastrar usuarios naquele apartamneto
     * TODO Adicionar telefone
    */
    public function postUser(Request $request) {

        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'required|email|unique:users',
            'registration' => 'required|Min:9|unique:users',
            'cpf' => 'required|Min:11|Max:11|unique:users',
            'rg' => 'required|unique:users',
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

            $apto = Apartament::find($request->input('id_apto'));

            if($apto){
                if( $apto-> vacancy == 0 ) {
                    return response()->json([
                        'message' => 'Usuário não pode ser alocado a um apartamento sem vaga'
                    ],403);
                }
                $apto->vacancy = $apto->vacancy - 1;
                $apto->save();
            }


            $job = (new SendEmailJob($user, $randomPass))->delay(Carbon::now()->addSeconds(3));

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
                        ->delay(Carbon::now()->addSeconds(3));

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
        $users = User::with(['course', 'apto'])->get();
        $response = [
            'data' => $users
        ];

        return response()->json($response, 200);
    }

    public function getUsersWithoutApto(){
        $users = User::where('id_apto', null)->with(['course', 'apto'])->get();
        $response = [
            'data' => $users
        ];

        return response()->json($response, 200);
    }

    public function getUsersFromApto($apto_id){
        $users = User::where('id_apto', $apto_id)->with(['course', 'apto'])->get();
        $response = [
            'data' => $users
        ];

        return response()->json($response, 200);
    }

    public function putUser(Request $request){

        $user = User::find($request['id']);
        $userApto = Apartament::where('id', $user->id_apto)->first();


        $newApto = Apartament::find($request->input('id_apto'));

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }else {
            $this->validate($request, [
                'fullName' => 'required',
                'email' => 'required|email',
                'registration' => 'required|Min:9 |Max:9',
                'cpf' => 'required|Min:11|Max:11',
                'rg' => 'required|Min:10|Max:10',
                'age' => 'required|Min:2',
                'genre' => 'required',
                'id_course' => 'required',
                'is_admin' => 'required',
                'is_bse_active' => 'required'
            ],[
                'fullName.required' => 'Um nome completo é necessário',
                'email.required'  => 'Preencha o campo de email',
                'registration.required' => "Você deve fornecer o número de matrícula",
                'cpf.required' => "Você deve fornecer o número do CPF",
                'rg.required' => "Você deve fornecer o número do RG",
                'age.required' => "Você deve especificar a idade do usuário",
                'genre.required' => "Você deve fornecer o gênero do usuário",
                'id_course.required' => "Você deve especificar o curso",
                'is_admin.required' => "Você deve especificar se o usuário é administrador",
                'is_bse_active.required' => "Você deve especificar se o usuário possui BSE ativo",
            ]);

            if($user->update([

                'fullName' => $request->input('fullName'),
                'email' => $request->input('email'),
                'registration' => $request->input('registration'),
                'cpf' => $request->input('cpf'),
                'rg' => $request->input('rg'),
                'age' => $request->input('age'),
                'genre' => $request->input('genre'),
                'is_bse_active' => $request->input('is_bse_active'),
                'is_admin' => $request->input('is_admin'),
                'id_course' => $request->input('id_course'),
                'id_apto' => $request->input('id_apto')
            ])

            )

            if (!($userApto) AND ($newApto)){
                $newApto->vacancy = $newApto->vacancy - 1;
                $newApto->save();
            }elseif(!($userApto->number == $newApto->number)) {

                if ($userApto) {
                    $userApto->vacancy = $userApto->vacancy + 1;
                    $userApto->save();
                }

                if ($newApto) {
                    $newApto->vacancy = $newApto->vacancy - 1;
                    $newApto->save();
                }
            }

            return response()->json(['message' => 'Usuário '.$user->fullName.' alterado com sucesso'], 200);
        }
    }

    public function deleteUser($id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }

        $user->delete();
        return response()->json(['message' => "Usuário deletado com sucesso"], 200);
    }

    public function addUserToApto($id, $apto){
        $user = User::find($id);
        $apartament = Apartament::where('number',$apto)->first();

        if(empty($apartament) || empty($user)){
            return response()->json(['message' => "Usuário ou Apartamento não encontrado"], 404);
        }

        if ($apartament->vacancy > 0) {
            $user->id_apto = $apartament->id;

            if($user->update()){
                DB::table('apartaments')->where('id', $apartament->id)->decrement('vacancy', 1); //diminui uma vaga
                return response()->json(['message' => "Usuário adicionado com sucesso ao apartamento ". $apartament->number], 200);
            }
        }else {
            return response()->json(['message' => "O apartamento não possui vagas"], 403);
        }

    }

    public function removeUserFromApto($id){
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }

        $id_apto = $user->id_apto;

        $user->id_apto = null;

        if($user->update()){
            DB::table('apartaments')->where('id', $id_apto)->increment('vacancy', 1); //aumenta uma vaga
            return response()->json(['message' => "Usuário removido com sucesso do apartamento"], 200);
        }
    }

    public function changeUserApto(Request $request){

        $this->validate($request, [
            'id' => 'required',
            'id_apto' => 'required',
            'option' => 'required'
        ],[
            'id.required' => "Você deve especificar o aluno",
            'id_apto.required' => "Você deve especificar o apartamento de origem",
            'option.required' => "Você deve escolher uma opção",
        ]);

        $user = User::find($request['id']);
        $userApto = Apartament::find($request['id_apto']);
        $newApto = Apartament::find($request['new_id_apto']);

        if($request->input('option') === 'option-1'){
            $this->validate($request,[
                'new_id_apto' => 'required'
            ],[
                'new_id_apto.required' => 'Você deve selecionar um apartamento de destino'
            ]);

            if($request['id_apto'] === $request['new_id_apto']){
                return response()->json(['message' => 'Selecione um apartamento diferente'], 500);
            }else{

                $user->id_apto = $request['new_id_apto'];

                if($user->update()){
                    DB::table('apartaments')->where('id', $userApto->id)->increment('vacancy', 1); //apartamento que saiu
                    DB::table('apartaments')->where('id', $user->id_apto)->decrement('vacancy', 1); //apartamento que entrou
                    return response()->json(['message' => "Aluno ". $user->fullName . " trocado para o apartamento ". $newApto->number], 200);
                }else {
                    return response()->json(['message' => "Erro ao alterar o apartamento do aluno"], 500);
                }

            }

        }else if ($request->input('option') === 'option-2'){
            $this->validate($request,[
                'new_id_apto' => 'required',
                'new_student' => 'required'
            ],[
                'new_id_apto.required' => 'Você deve selecionar um apartamento de destino',
                'new_student.required' => 'Você não selecionou um aluno para a troca'
            ]);

            if($request['id_apto'] === $request['new_id_apto']){
                return response()->json(['message' => 'Selecione um apartamento diferente'], 500);
            }else {
                $newUser = User::find($request['new_student']);

                $user->id_apto = $newUser->id_apto;
                $newUser->id_apto = $userApto->id;

                if($user->update() && $newUser->update()){
                    return response()->json(['message' => "Aluno ". $user->fullName . " trocado para o apartamento ". $newApto->number], 200);
                }else{
                    return response()->json(['message' => "Erro ao alterar o apartamento do aluno"], 500);
                }
            }

        }else{
            return response()->json(['message' => 'Opção inválida'], 404);
        }
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


    /*Verificar se esta é melhor forma para essa operação, quando e onde chamar esse método*/
    /*Método que realiza a busca de todas as notificações referentes ao usuário logado*/
    /*Formato do JSON de entrada
        {
            "id_user" ---> id do usuário logado
        }
    */
    /*Formato do JSON de saida
        {
            "Notifications": [
                {
                    Nesta seção estarão os dados referentes a notificação, podendo variar de acordo com o tipo da notificação
                    EX:
                    "type": "Apto Change",
                    "from": 27,
                    "to": 29
                }
            ],
            "IDs": [
                Nesta seção constarão os ids das notificações, estes devem ser retornado para a funcão makeAsRead, para marcar os notificações como lidas
                "d3b1e7e4-7aa9-483e-81e2-3a1ea3a72ff7"
            ]
        }

    */

    public function getNotifications(Request $request){
        $user = JWTAuth::toUser($request->token);

        $response = Null;

        foreach ($user->notifications as $notification) {
            if(!$notification->read_at){
                $response = array($notification->data);
                $ids = array($notification->id);
            }
        }

        if($response){
            return response()->json([
                'Notifications' => $response,
                'IDs' => $ids
            ]);
        }else{
            return response()->json([
                'response' => 'Sem Notificações',
            ]);
        }
    }

    /*Método que marca a notificão como lida*/
    /*Formato do JSON de entrada
        {
            "id_user" ---> id do usuário logado
            "id_notification --> id da notificação que deve ser marcada como lida
        }
    */
    /*Formato do JSON de saida

    */

    public function markAsRead(Request $request){
        $user = JWTAuth::toUser($request->token);
        $id_not = $request->input('id_notification');

        $notification = $user->notifications()->where('id',$id_not)->first();

        $notification->markAsRead();

    }
}