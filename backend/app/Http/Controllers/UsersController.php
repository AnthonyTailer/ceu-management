<?php
/**
 * Created by PhpStorm.
 * User: tailer
 * Date: 24/09/17
 * Time: 18:54
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;

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
        ]);

        $randomPass = str_random(8);

        #dd($request);

        $user = new User([
            'fullName' => $request->input('fullName'),
            'email' => $request->input('email'),
            'registration' => $request->input('registration'),
            'password' => bcrypt($randomPass),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'genre' => $request->input('genre'),
            'is_bse_active' => $request->input('is_bse_active'),
            'is_admin' => $request->input('is_admin'),
            'id_course' => $request->input('id_course'),
            'id_apto' => $request->input('id_apto')
        ]);

        $user->save();


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
}