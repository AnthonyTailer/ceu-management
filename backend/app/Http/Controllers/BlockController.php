<?php

namespace App\Http\Controllers;

use App\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function postBlock(Request $request){
        $this->validate($request,[
            'number' => 'required|unique:blocks'
        ]);

        #dd($request);

        $block = new Block([
            'number' => $request->input('number'),
        ]);




        $block->save();

        return response()->json([
            'message' => 'Bloco criado com sucesso', 'block' => $block
        ],201);
    }

    public function getBlocks(){
        $blocks = Block::all();

        $response = [
            'blocks' => $blocks
        ];

        return response()->json($response, 200);
    }

    public function putBlock(Request $request, $id){
        $block = Block::find($id);

        if(!$block){
            return response()->json(['message'=> "Bloco não encontrado"], 404);
        }

        $this->validate($request,[
            'number' => 'required|unique:course',
        ]);

        $block->number = $request->input('number');

        $block->save;

        return response()->json(['block' => $block], 200);

    }

    public function deleteBlock($id){
        $block = Block::find($id);


        if(!$block){
            return response()->json(['message' => "Bloco não encontrado"], 404);
        }

        $block->delete();
        return response()->json(['message' => "Bloco deletado com sucesso"], 200);
    }
}
