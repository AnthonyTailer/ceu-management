<?php

namespace App\Http\Controllers;

use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function postCourse(Request $request){
       dd($request);

        $this->validate($request, [
            'courseName' => 'required',
            'duration' => 'required',
            'type' => 'required',
        ]);

        $course = New Course([
            'courseName' => $request->input('courseName'),
            'duration' => $request->input('duration'),
            'type' => $request->input('type'),
        ]);

        $course->save();

        return response()->json([
            'message'=> "Curso cadastrado com sucesso", 'curso' => $course,
        ],201);
    }


    public function postCourses(Request $request){


        $errors = Array();
        $success = Array();

        #dd($request[0]);

        foreach ( $request->all() as $key=>$value) {
            #dd($value['courseName']);
            $db_courseName = DB::table('courses')->where('courseName', $value['courseName'])->value('courseName');
            #*$db_duration = DB::table('course')->where('duration', $value['duration'])->value('duration');
            #$db_type = DB::table('course')->where('type', $value['type'])->value('type');

            if(empty($value['courseName'])){
                $errors[$key]["courseName"] = "O campo Nome do Curso é obrigatório e deve ser preenchido.";
            }
            if(!empty($db_courseName)){
                $errors[$key]["courseName"] = "O curso ".$db_courseName." já existe no sistema.";
            }
            if(empty($value['duration'])){
                $errors[$key]["duration"] = "O campo Duração é obrigatório e deve ser preenchido.";
            }
            if(empty($value['type'])){
                $errors[$key]["type"] = "O campo Tipo é obrigatório e deve ser preenchido.";
            }

            #dd($errors);

            if(empty($errors)){
                $course = New Course([
                    'courseName' => $value['courseName'],
                    'duration' => $value['duration'],
                    'type' => $value['type'],
                ]);

                if($course->save()){
                    $success[$key]["message"] = "O Curso" .$value['courseName']. "foi salvo com sucesso";
                };
            }
        }

        return response()->json([
            "erros" => $errors,
            "success" => $success
        ], 200);
    }

    public function getCourses(){
        $courses = Course::all();
        $response = [
            'courses' => $courses
        ];

        return response()->json($response, 200);
    }

    public function putCourse(Request $request, $id){
        $course = Course::find($id);

        if(!$course){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }


        $this->validate($request, [
            'courseName' => 'required',
            'duration' => 'required',
            'type' => 'required',
        ]);

        $course->courseName = $request->input('courseName');
        $course->duration = $request->input('duration');
        $course->type = $request->input('type');
        $course->save;

        return response()->json(['user' => $course], 200);
    }

    public function deleteCourse($id){
        $course = Course::find($id);


        if(!$course){
            return response()->json(['message' => "Usuário não encontrado"], 404);
        }

        $course->delete();
        return response()->json(['message' => "Usuário deletado com sucesso"], 200);
    }


}
