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
use App\Notifications\NotificationAlert;


class UsersController extends Controller {

    /*
     * TODO Adicionar telefone ao usuário
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

        if($user->save()) {

            $job = (new SendEmailJob($user, $randomPass))->delay(Carbon::now()->addSeconds(3));

            $this->dispatch($job);

            return response()->json([
                'message' => 'Usuário criado com sucesso, um e-mail foi mandado para '. $request->input('email')
            ],201);
        }

    }

    public function postUsers(Request $request){

        $errors = Array();
        $success = Array();
        foreach ( $request->body as $key=>$value) {
            try {
                $db_email = DB::table('users')->where('email', $value['email'])->value('email');
                $db_registration = DB::table('users')->where('registration', $value['registration'])->value('registration');
            } catch (\Exception $exception) {
                $errors[$key]["email"] = "O campo email não foi informado.";
                $errors[$key]["registration"] = "O campo matrícula não foi informado.";
            }

            if( $value['email'] == null){
                $errors[$key]["email"] = "O campo email não foi informado.";
            }
            if( $value['registration'] == null ){
                $errors[$key]["registration"] = "O campo matrícula não foi informado.";
            }
            if(!empty($db_email) || !empty($db_registration)){
                continue;
            }
            if(empty($value['age'])){
                $errors[$key]["age"] = "O campo Idade é obrigatório e deve ser preenchido.";
            }
            if(empty($value['cpf'])){
                $errors[$key]["cpf"] = "O campo CPF é obrigatório e deve ser preenchido.";
            }
            if(empty($value['rg'])){
                $errors[$key]["rg"] = "O RG é obrigatório e deve ser preenchido.";
            }
            if(empty($value['genre'])){
                $errors[$key]["genre"] = "O sexo é obrigatório e deve ser preenchido.";
            }
            if(empty($value['is_bse_active'])){
                $errors[$key]["is_bse_active"] = "O BSE é obrigatório e deve ser preenchido.";
            }
            if(empty($value['is_admin'])){
                $errors[$key]["is_admin"] = "Você deve informar se o aluno é da Diretoria ou não.";
            }
            if(empty($value['id_course'])){
                $errors[$key]['id_course'] = "Você deve informar o Curso do Aluno.";
            }
            if(!empty($value['id_course'])){
                DB::enableQueryLog();
                $course = Course::where('courseName','LIKE', "%".$value['id_course']."%")->get();
                $query = DB::getQueryLog();

                if($course && count($course) > 1){

                    $errors[$key]['id_course'] = "O curso informado não existe.\n";
                    $errors[$key]['id_course'] .= "tente as seguintes entradas: \n";

                    foreach ( $course as $i=>$val){
                        $errors[$key]['id_course'] .= $val->courseName."\n";
                    }

                }else if ($course && count($course) == 1) {
                    foreach ( $course as $i=>$val){
                        $value['id_course'] = $val->id;
                    }
                }else {
                    $errors[$key]['id_course'] = "O curso informado não existe.";
                }
            }
            if(!empty($value['id_apto'])) {
                $apto = Apartament::where('number', $value['id_apto'])->first();

                if( $apto && $apto->vacancy > 0 ){
                    $value['id_apto'] = $apto->id;
                } else {
                    $errors[$key]['id_apto'] = "O Apartamento informado não existe ou não possui vagas.";
                }
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
                    'genre' => $value['genre'],
                    'is_bse_active' => strtoupper($value['is_bse_active']) === "SIM" ? 1 : 0,
                    'is_admin' => strtoupper($value['is_admin']) === "SIM" ? 1 : 0,
                    'id_course' => $value['id_course'],
                    'id_apto' => $value['id_apto']
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


        $user = User::find($request->input('id'));

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }else {

            if($user->id_apto !== null){ //ja tem apartamento

                $userApto = Apartament::where('id', $user->id_apto)->first();

                if($request->input('id_apto') !== null) { //ja possui apartamento ou troca de apartamento

                    $newApto = Apartament::find($request->input('id_apto'));

                    if ($userApto->number != $newApto->number) { //apartamento diferente

                        if( $newApto->vacancy == 0 ) { //verifica se possui vaga
                            return response()->json([
                                'message' => 'Usuário não pode ser alocado a um apartamento sem vaga'
                            ],403);
                        }else { //troca o usuário de apartamento -> TODO notificar o usuário da troca de apartamento

                            DB::table('apartaments')->where('id', $userApto->id)->increment('vacancy', 1); //apartamento que saiu
                            DB::table('apartaments')->where('id', $newApto->id)->decrement('vacancy', 1); //apartamento que entrou
                        }
                    }
                } else { //novo apartamento vazio, apenas sair do apartamento atual
                    DB::table('apartaments')->where('id', $userApto->id)->increment('vacancy', 1); //apartamento que saiu
                }
            } else if ($user->id_apto == null && $request->input('id_apto') !== null) { // não tem apartamento e quer entrar em apto
                $newApto = Apartament::find($request->input('id_apto'));

                if( $newApto->vacancy == 0 ) { //verifica se possui vaga
                    return response()->json([
                        'message' => 'Usuário não pode ser alocado a um apartamento sem vaga'
                    ],403);
                }else { //troca o usuário de apartamento -> TODO notificar o usuário da troca de apartamento
                    DB::table('apartaments')->where('id', $newApto->id)->decrement('vacancy', 1); //apartamento que entrou
                }

            }


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
            ]))
                return response()->json(['message' => 'Usuário '.$user->fullName.' alterado com sucesso'], 200);
        }
    }

    public function deleteUser($id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }

        if($user->delete()) {
            if ($user->id_apto !== null)
                DB::table('apartaments')->where('id', $user->id_apto)->increment('vacancy', 1); //aumenta uma vaga
            return response()->json(['message' => "Usuário deletado com sucesso"], 200);
        }
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
            'token' => $token,
        ], 200);
    }

    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['user' => $user]);
    }

    public function isAdmin(Request $request){
        $user = JWTAuth::toUser($request->token);

        return response()->json(['data' => $user->is_admin]);
    }

    public function stats(){

        /*Estatísticas sobre sexo*/
        $male = User::where('genre', "M")->get();
        $female = User::where('genre', "F")->get();

        $totalSexo = count($male) + count($female);

        /*Estatísticas sobre tipo de graduação*/
        $graduation = DB::select( DB::raw("select c.type from courses as c inner join (users) on users.id_course = c.id and c.type = \"Graduação\";"));
        $mestrado = DB::select( DB::raw("select c.type from courses as c inner join (users) on users.id_course = c.id and c.type = \"Mestrado\";"));

        $totalGraduacao = count($graduation) + count($mestrado);


        /*Estatísticas alunos sem aptos*/
        $noApto = User::where('id_apto', Null)->get();

        $vacancyApto = Apartament::sum('vacancy');
        $totalAptos = Apartament::all()->count();

        /*Estatísticas sobre bse*/
        $noBSE = User::where('is_bse_active', 0)->get();
        $yesBSE = User::where('is_bse_active', 1)->get();

        /*Respostas*/
        return response()->json([
            'students' =>[
                'male' => ['text' => 'Masculino', 'value' => count($male)],
                'female' => ['text' => 'Feminino', 'value' =>count($female)],
                'total' => ['text' => 'Total', 'value' => $totalSexo]
            ],
            'courses' => [
                'graduation' => ['text' => 'Graduação', 'value' => count($graduation)],
                'master' => ['text' => 'Mestrado', 'value' => count($mestrado)],
                'total' => ['text' => 'Total', 'value' => $totalGraduacao]
            ],
            'aptos' => [
                'no_apto' => ['text' => 'Alunos sem apartamento', 'value' => count($noApto) ],
                'vacancy' => ['text' => 'Qtde. de vagas', 'value' => $vacancyApto ],
                'total' => ['text' => 'Total de apartamentos', 'value' => $totalAptos]
            ],
            'bse' =>[
                'bse_active' => ['text' => 'Ativo', 'value' =>count($yesBSE)],
                'bse_inactive' => ['text' => 'Inativo', 'value' => count($noBSE)]
            ]
        ], 200);
    }

    public function createAlert(Request $request){
        $this->validate($request, [
            'text' => 'required',
            'to.type' => 'required',
            'priority' => 'required',
        ],[
            'text.required' => 'A mensagem não pode estar vazia',
            'to.type.required'  => 'Você deve escolher para quem deseja enviar a mensagem',
            'priority' => "Você deve selecionar uma prioridade para a mensagem",
        ]);

        if($request->input('text')){
            $when = Carbon::now()->addSeconds(5);

            if($request->input('to.type') == "user"){
                $user = User::find($request->input('to.id'));
                $user->notify(new NotificationAlert($request->input('text'), $request->input('priority')));

                return response()->json(['message' => "Mensagem enviada com sucesso à ".$user->fullName], 200);
            }elseif($request->input('to.type') == "apto"){
                $users = User::where('id_apto' , $request->input('to.id'))->get();

                foreach ($users as $user){
                    $user->notify( (new NotificationAlert($request->input('text'), $request->input('priority')) )->delay($when));
                }

                return response()->json(['message' => "Mensagem enviada com sucesso ao apartamento"], 200);

            }elseif($request->input('to.type') == "all"){
                $users = User::all();
                foreach ($users as $user){
                    $user->notify( (new NotificationAlert($request->input('text'), $request->input('priority')) )->delay($when));
                }

                return response()->json(['message' => "Mensagem enviada com sucesso à todos moradores da CEU"], 200);
            }
        }

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
        $notify =array();
        $notifyIDs = array();
        foreach ($user->unreadNotifications as $notification) {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->format('d-m-Y');
            array_push($notify, array_merge($notification->data,["date" => $date], ["priority" => $notification->priority] ,["id" => $notification->id]));
            array_push($notifyIDs,$notification->id);
        }

        return response()->json([
            'notifications' => $notify,
            'count' => count($notify),
            'ids' => $notifyIDs
        ]);

    }


    public function getReadNotifications(Request $request){
        $user = JWTAuth::toUser($request->token);

        $notify = array();
        foreach ($user->notifications as $notification) {
            if($notification->read_at){
                array_push($notify, array_merge($notification->data, ["id" => $notification->id]));
            }
        }

        return response()->json([
            'notifications' => $notify,
            'count' => count($notify)
        ]);
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


    public function markMultipleAsRead(Request $request){
        $user = JWTAuth::toUser($request->token);

        foreach($request->input("ids") as $notificationID){
            $notification = $user->notifications()->where('id',$notificationID)->first();
            $notification->markAsRead();
        }
    }
}