<?php

namespace App\Http\Controllers;

use App\Apartament;
use Illuminate\Http\Request;

class ApartamentController extends Controller
{
    public function postApto(Request $request){
        $this->validate($request, [
            'number' => 'required'|'unique:Apartament',
            'capacity' => 'required'
        ]);

        $apto = New Apartament([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'vacancy' => $request->input('capacity'),
            'block' => substr($request->input('number'),0,2)
        ]);

        $apto->save();

        return response()->json([
            'message' => 'Apartamento'.$apto.'criado com sucesso', 'apto' => $apto
        ],201);
    }

    public function getAptos(){
        $aptos = Apartament::where('vacancy', '>', 0)->all();

        $response = [
            'data' => $aptos
        ];

        return response()->json($response, 200);
    }

    public function putApto(Request $request){

        $this->validate($request, [
            'number' => 'required'|'unique:Apartament',
            'capacity' => 'required'
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
            $users = DB::table('users')
                ->select(DB::raw('count(*) as users_in_apto'))
                ->where('id_apto', '=', $request->input('id'))
                ->get();
            if($apto->vacancy > $newCapacity && $users < $newCapacity){
                $apto->capacity = $newCapacity;
                $apto->vacancy = $newCapacity - $apto->vacancy;
            }else {
                return response()->json('Impossível diminuir a capacidade deste Apto, pois o mesmo possui mais moradores do que a capacidade informada', 405);
            }
        }

        if($apto->save()){
            return response()->json(['apto' => $apto], 200);
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
}
