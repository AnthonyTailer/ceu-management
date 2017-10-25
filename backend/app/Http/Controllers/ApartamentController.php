<?php

namespace App\Http\Controllers;

use App\Apartament;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Notifications\ChangeApto;

use JWTAuth;

class ApartamentController extends Controller
{
    public function postApto(Request $request){
        $this->validate($request, [
            'number' => 'required|unique:apartaments',
            'capacity' => 'required',
            'vacancy' => 'required',
            'block' => 'required',
            'building' => 'required'
        ], [
            'number.required' => 'O Apartamento não foi informado.',
            'number.unique' => 'Este Apartamento já existe.',
            'capacity.required' => 'A capacidade do apartamento não foi informada.',
            'vacancy.required' => 'A quantidade de vagas não foi informada.',
            'block.required' => 'O Bloco não foi informado',
            'building.required' => 'O Prédio não foi informado',
        ]);

        $apto = New Apartament([
            'number' => $request->input('number'),
            'capacity' => (int)$request->input('capacity'),
            'vacancy' => (int)$request->input('vacancy'),
            'block' => $request->input('block'),
            'building' => $request->input('building')
        ]);

        if( $apto->save() ) {
            return response()->json([
                'message' => 'Apartamento '.$apto['number'].' criado com sucesso', 'apto' => $apto
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
        $aptos = Apartament::all();

        $response = [
            'aptos' => $aptos
        ];

        return response()->json($response, 200);
    }

    public function getVacancyAptos(){
        $aptos = DB::table('apartaments')
            ->whereRaw('vacancy > 0', [200])
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

        $apto = Apartament::where('number', $request->input('number'))->first();

        if(!$apto){
            return response()->json(['message' => "Apartamento não encontrado"], 404);
        }

        $oldCapacity = $apto->capacity;
        $newCapacity = $request->input('capacity');

        $apto->number = $request->input('number');
        $apto->block = substr($request->input('number'),0,2);

        if($oldCapacity < $newCapacity){
            $apto->capacity = $newCapacity;
            $apto->vacancy += ($newCapacity - $oldCapacity);
        }else if ($oldCapacity > $newCapacity) {

            if(!empty($request->input('id'))){
                $users = DB::table('users')
                    ->where('id_apto', $request->input('id'))
                    ->count();
            }else {
                $id_apto = DB::table('apartaments')
                ->select('id')
                ->where('number', $request->input('number'))->first();

                $id = $id_apto->id;

                $users = DB::table('users')
                    ->where('id_apto', $id)
                    ->count();
            }

            if($apto->vacancy >= $newCapacity && $users <= $newCapacity){
                $apto->capacity = $newCapacity;
                $apto->vacancy = $users - $newCapacity;
            }else {
                return response()->json('Impossível diminuir a capacidade deste Apto, pois o mesmo possui mais ou a mesma quantidade de moradores', 403);
            }
        }

        if($apto->save()){
            return response()->json([
                'message' => 'Apartamento '.$apto['number'].' alterado com sucesso', 'aptos' => Apartament::all()
            ], 200);
        }

    }

    public function deleteApto($id){
        $apto = Apartament::find($id);


        if(!$apto){
            return response()->json(['message' => "Apartamento não encontrado"], 404);
        }

        $apto->delete();
        return response()->json(['message' => "Apartamento deletado com sucesso"], 200);
    }

    public function changeApto(Request $request){

        $user = JWTAuth::toUser($request->token);
        $user2 = User::find($request->input('id_user'));

        $user->notify(new ChangeApto($user, $user2));
    }
}
