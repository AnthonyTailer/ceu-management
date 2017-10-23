<?php

namespace App\Http\Controllers;

use App\Notifications\RepliedToAlocation;
use App\WaitList;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Notification;


class WaitListController extends Controller
{
    public function postWaitList(Request $request){



        $this->validate($request,[
            'id_user' => 'required|unique:wait_lists',
            'id_apto' => 'required',
        ]);

        $waitList = new WaitList([
            'id_user' => $request->input('id_user'),
            'id_apto' => $request->input('id_apto'),
        ]);


        Notification::route('mail', 'taylor@laravel.com')
            ->notify(new RepliedToAlocation());

        $aptoVacancy = DB::table('apartaments')->where('id', $request->input('id_apto'))->value('vacancy');

        if($aptoVacancy == 0){
            return response()->json([
                'message' => 'O apartamento solicitado não possui vagas disponíveis'
            ],401);
        }else if ($aptoVacancy > 0){
            $waitList->save();

            return response()->json([
                'message' => 'Requisição de alocação criada com sucesso', 'waitList' => $waitList
            ],201);
        }
    }

    public function getWaitLists(){

    }

    public function putWaitLists(Request $request, $id){

    }

    public function delete(Request $request, $id){

    }
}
