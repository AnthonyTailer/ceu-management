<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StandardWorkingTime;
use Illuminate\Support\Facades\DB;

class StandardWorkingTimeController extends Controller
{
    public function postStandardWorkingTime(Request $request){
        $this->validate($request,[
            'init' => 'required',
            'end' => 'required',
            'period' => 'required',
            'day' => 'required'
        ],[
            'start.required' => "O horário de início é necessário",
            'end.required' => "O horário de término é necessário",
            'day.required' => "O dia da semana é necessário",
            'period.required' => "Informe o período",
        ]);

        $book = new StandardWorkingTime([
            'init' => gmdate("H:i:s", strtotime($request->input('init'))),
            'end' => gmdate("H:i:s", strtotime($request->input('end'))),
            'day' => $request->input('day'),
            'period' => $request->input('period'),
        ]);

        if($book->save()){
            return response()->json("Horário de funcionamento cadastrado");
        }else{
            return response()->json("Problema ao cadastrar o horário de funcionamento");
        }
    }


    /*public function getWorkingTime(Request $request){

        $isNotWorking = DB::table('no_working_times')
            ->whereDate('day', $request->input('day'))
            ->get();

        if(!$isNotWorking){
            $workTime = DB::table('standard-working-times')
                ->whereDate('day', $request->input('day'))
                ->get();
        }else{
            return response()->json("A lavanderia não estará funcionando neste dia");
        }
    }*/
}
