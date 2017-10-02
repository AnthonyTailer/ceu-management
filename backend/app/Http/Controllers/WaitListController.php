<?php

namespace App\Http\Controllers;

use App\WaitList;
use Illuminate\Http\Request;

class WaitListController extends Controller
{
    public function postWaitList(Request $request){
        $this->validate($request,[
            'id_user' => 'required|unique:wait_lists',
            'id_apto' => 'required',
        ]);

        $waitList = new WaitList([
            'id_user' => $request->input('id_user'),
            'id_apto' => $request->input('ad_apto'),
        ]);

        $waitList->save();

        return response()->json([
            'message' => 'Requisição de alocação criada com sucesso', 'waitList' => $waitList
        ],201);
    }

    public function getWaitLists(){

    }

    public function putWaitLists(Request $request, $id){

    }

    public function delete(Request $request, $id){

    }
}
