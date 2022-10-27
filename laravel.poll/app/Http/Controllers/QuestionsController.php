<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Models\Poll;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::all();
        return response($questions,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'poll_id'=>'required',
            'question'=>'required',
            'type'=>'required'
        ]);
        if(Poll::find($data['poll_id'])){
            if($data['type']=='text'||$data['type']=='single'||$data['type']=='multiple'){
                $question=Question::create($data);
                return response($question,200);
            }
            else{
                return response('Неверный тип вопроса',401);
            }
        }
        else{
            return response('Нет опроса с таким ID',401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data=$request->validate([
            'question'=>'required',
            'type'=>'required',
        ]);
        if($data['type']=='text'||$data['type']=='single'||$data['type']=='multiple'){
            $question=Question::where('id',$id)->update($data,$id);
            return response($question,200);
        }
        else{
            return response('Неверный тип вопроса',401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question=Question::find($id);
        DB::delete('delete from answers where question_id=?',[$id]);
        $question->delete();
        return response('Вопрос удалён', 200);
    }

    public function get_questions_by_poll_id($id){
        $questions=Question::where('poll_id',$id)->get();
        return response($questions,200);
    }
}
