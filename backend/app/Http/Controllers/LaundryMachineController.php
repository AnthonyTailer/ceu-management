<?php

namespace App\Http\Controllers;

use App\LaundryMachine;
use Illuminate\Http\Request;

class LaundryMachineController extends Controller
{

    public function postLaundryMachine(Request $request){
        $this->validate($request, [
            'capacity' =>'required'
        ],[
            'capacity.required' => "Deve ser fornecida a capacidade de lavagem da máquina"
        ]);


        $machine = new LaundryMachine([
            'capacity' => $request->input('capacity')
        ]);

        if($machine->save()){
            return response()->json([
                'message' => 'Máquina registrada com sucesso'
            ],201);
        }else{
            return response()->json([
                'message' => 'Erro ao criar máquina'
            ],400);
        }
    }

    public function getLaundryMachines(){
        $machines = LaundryMachine::all();

        $response = [
            'data' => $machines
        ];

        return response()->json($response, 200);
    }

    public function getLaundryMachine($id){
        $machine = LaundryMachine::where('id', $id)->first();

        $response = [
            'data' =>$machine
        ];

        return response()->json($response, 200);
    }


    public function putLaundryMachine(Request $request){
        $this->validate($request, [
            'capacity' =>'required'
        ],[
            'capacity.required' => "Deve ser fornecida a capacidade de lavagem da máquina"
        ]);

        $machine = LaundryMachine::where('id', $request->input('id'))->first;

        if(!$machine){
            return responss()->json("Não foi possível localizar esta máquina");
        }

        $machine->capacity = $request->input('capacity');

        if($machine->save()){
            return response()->json([
                'message' => 'Máquina '.$machine['id'].' alterada com sucesso'
            ], 200);
        }

    }
}
