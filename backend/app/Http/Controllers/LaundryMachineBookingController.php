<?php

namespace App\Http\Controllers;

use App\LaundryMachineBooking;
use Illuminate\Http\Request;

class LaundryMachineBookingController extends Controller
{
    public function postLMBooking(Request $request){
        $this->validate($request,[
            'start' => 'required',
            'end' => 'required',
            'day' => 'required',
            'id_user' => 'required',
            'id_machine' => 'required'
        ],[
            'start.required' => "O horário de início é necessário",
            'end.required' => "O horário de término é necessário",
            'day.required' => "O dia da reserva é necessário",
            'id_user.required' => "Informe o usuário que está efetuando a reserva",
            'id_machine.required' => "Informe a máquina para a qual a reserva será feita"
        ]);

        $startTime = strtotime($request->input('start'));
        $endTime = strtotime($request->input('end'));

        $book = new LaundryMachineBooking([
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'day' => $request->input('day'),
            'id_user' => $request->input('id_user'),
            'id_machine' => $request->input('id_machine'),
        ]);

        if($book->save()){
            return response()->json("Reserva efetuada com sucesso");
        }else{
            return response()->json("Não foi possível efetuar a reserva");
        }
    }
}
