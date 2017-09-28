<?php

namespace App\Http\Controllers;

use App\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function postCourse(Request $request){
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
