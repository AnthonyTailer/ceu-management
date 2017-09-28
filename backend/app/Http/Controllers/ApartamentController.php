<?php

namespace App\Http\Controllers;

use App\Apartament;
use Illuminate\Http\Request;

class ApartamentController extends Controller
{
    public function postApto(Request $request){
        $this->validate($request, [
            'number' => 'required'|'unique:Apartament',
            'capacity' => 'required',
            'id_block' => 'required'
        ]);

        $apto = New Apartament([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'id_block' => $request->input('id_block')
        ]);

        $apto->save();

        return response()->json([
            'message' => 'Apartamento criado com sucesso', 'apto' => $apto
        ],201);
    }

    public function getAptos(){
        $aptos = Apartament::all();

        $response = [
            'aptos' => $aptos
        ];

        return response()->json($response, 200);
    }

    public function putApto(Request $request, $id){
        $apto = Apartament::find($id);

        if(!$apto){
            return response()->json(['message' => "Apartamento não encontrado"], 404);
        }

        $this->validate($request, [
            'number' => 'required'|'unique:Apartament',
            'capacity' => 'required',
            'id_block' => 'required'
        ]);

        $apto->number = $request->input('number');
        $apto->capacity = $request->input('capacity');
        $apto->id_bloco = $request->input('id_block');

        $apto->save;

        return response()->json(['apto' => $apto], 200);

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
