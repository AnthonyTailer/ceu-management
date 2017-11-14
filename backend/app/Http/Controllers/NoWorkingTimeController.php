<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NoWorkingTime;

class NoWorkingTimeController extends Controller
{
    public function postNoWorkingTime(Request $request){
        $this->validate($request,[
            'period' => 'required',
            'day' => 'required',
        ],[
            'period.required' => "O período é necessário",
            'day.required' => "O dia da semana é necessário",
        ]);


        #dd(date_create_from_format('Y/m/d', $request->input('day')));

        $book = new NoWorkingTime([
            'period' => $request->input('period'),
            'day' => $request->input('day')
        ]);

        if($book->save()){
            return response()->json("Horário de não funcionamento cadastrado");
        }else{
            return response()->json("Problema ao cadastrar o horário de não funcionamento");
        }
    }
}
