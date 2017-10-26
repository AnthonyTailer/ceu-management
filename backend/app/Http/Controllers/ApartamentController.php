<?php

namespace App\Http\Controllers;

use App\Apartament;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartamentController extends Controller
{
    public function postApto(Request $request){
        $this->validate($request, [
            'number' => 'required|unique:apartaments',
            'capacity' => 'required',
            'block' => 'required',
            'building' => 'required'
        ], [
            'number.required' => 'O Apartamento não foi informado.',
            'number.unique' => 'Este Apartamento já existe.',
            'capacity.required' => 'A capacidade do apartamento não foi informada.',
            'block.required' => 'O Bloco não foi informado',
            'building.required' => 'O Prédio não foi informado',
        ]);

        $apto = New Apartament([
            'number' => $request->input('number'),
            'capacity' => (int)$request->input('capacity'),
            'vacancy' => (int)$request->input('capacity'),
            'block' => $request->input('block'),
            'building' => $request->input('building')
        ]);

        if( $apto->save() ) {
            return response()->json([
                'message' => 'Apartamento '.$apto['number'].' criado com sucesso', 'aptos' => Apartament::all()
            ],201);
        }

    }

    public function postAptos(Request $request){ // TODO post bulk apartaments
        $this->validate($request, [
            'number' => 'required'|'unique:Apartament',
            'capacity' => 'required',
            'vacancy' => 'required',
            'block' => 'required',
            'building' => 'required'
        ],
            [
                'number.required' => 'O Apartamento não foi informado.',
                'capacity.required' => 'A capacidade do apartamento não foi informada.',
                'vacancy.required' => 'A quantidade de vagas não foi informada.',
                'block.required' => 'O Bloco não foi informado',
                'building.required' => 'O Prédio não foi informado',
            ]);

        $apto = New Apartament([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'vacancy' => $request->input('vacancy'),
            'block' => $request->input('block'),
            'building' => $request->input('building')
        ]);

        if( $apto->save() ) {
            return response()->json([
                'message' => 'Apartamento'.$apto.'criado com sucesso', 'apto' => $apto
            ],201);
        }

    }

    public function getAptos(){
        $aptos = Apartament::get();

        $response = [
            'aptos' => $aptos
        ];

        return response()->json($response, 200);
    }

    public function getApto($number){
        $apto = Apartament::where('number', $number)->first();

        $users = User::where('id_apto', $apto->id)->get();

        $response = [
            'apto' => $apto,
            'users' => $users
        ];

        return response()->json($response, 200);
    }

    public function getVacancyAptos(){
        $aptos = DB::table('apartaments')
            ->where('vacancy', '>', 0)
            ->get();

        $response = [
            'aptos' => $aptos
        ];

        return response()->json($response, 200);
    }

    public function putApto(Request $request){

        $this->validate($request, [
            'number' => 'required',
            'capacity' => 'required',
            'vacancy' => 'required',
            'block' => 'required',
            'building' => 'required'
        ], [
            'number.required' => 'O Apartamento não foi informado.',
            'capacity.required' => 'A capacidade do apartamento não foi informada.',
            'vacancy.required' => 'A quantidade de vagas não foi informada.',
            'block.required' => 'O Bloco não foi informado',
            'building.required' => 'O Prédio não foi informado',
        ]);

        $apto = Apartament::where('id', $request->input('id'))->first();

        if(!$apto){
            return response()->json(['message' => "Apartamento não encontrado"], 404);
        }

//        $allAptos = Apartament::all();
//
//        foreach($allAptos as $apartament){
//            if($apartament->number == $request->input('number'))
//                return response()->json(['message' => "Você não pode alterar o n° do apto pois já existe outro apartamento com este número."], 403);
//        }
//        $oldApto = $apto->number;
        $oldCapacity = $apto->capacity;
        $oldVacancy = $apto->vacancy;

        $newCapacity = $request->input('capacity');
//        $newVacancy = $request->input('vacancy');
//        $newApto = $request->input('number');

        $apto->number = $request->input('number');
        $apto->block = $request->input('block');
        $apto->building = $request->input('building');

        if($oldCapacity < $newCapacity){
            $apto->capacity = $newCapacity;
            $apto->vacancy += ($newCapacity - $oldCapacity);
        }else if ($oldCapacity > $newCapacity) {

            if ($apto->vacancy == 0) {
                return response()->json('Impossível diminuir a capacidade deste Apto, o mesmo não possui mais vagas', 403);
            }

            $id_apto = $request->input('id');

            $users = DB::table('users')
                ->where('id_apto', $id_apto)
                ->count();

            if($users <= $newCapacity){
                $apto->capacity = $newCapacity;
                $apto->vacancy =  $newCapacity - $users;
            }else {
                return response()->json('Impossível diminuir a capacidade deste Apto, pois o mesmo possui mais ou a mesma quantidade de moradores', 403);
            }
        }else {

            $apto->capacity = $newCapacity;
        }

        if($apto->save()){
            return response()->json([
                'message' => 'Apartamento '.$apto['number'].' alterado com sucesso', 'aptos' => Apartament::all()
            ], 200);
        }

    }

    public function deleteApto($number){

        $apto = Apartament::where('number',$number)->first();

        if(!$apto){
            return response()->json(['message' => "Apartamento não encontrado"], 404);
        }

        if($apto->delete()){
            return response()->json(['message' => "Apartamento deletado com sucesso"], 200);
        }

    }
}
