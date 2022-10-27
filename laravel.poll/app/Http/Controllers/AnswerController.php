<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $answers = Answer::all();
        return response($answers,200);
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
            'question_id'=>'required',
            'text'=>'required',
        ]);
        if(Question::find($data['question_id'])){
            if(Question::find($data['question_id'])->type=='text'){
                return response('Для этого вопроса нельзя создать варианты ответов',401);
            }
            $answer=Answer::create($data);
            return response($answer,200);
        }
        else{
            return response('Нет вопроса с таким ID',401);
        }
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
            'text'=>'required',
        ]);
        $answer=Answer::where('id',$id)->update($data,$id);
        return response($answer,200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $answer=Answer::find($id);
        $answer->delete();
        return response('Ответ удалён', 200);
    }

    public function get_answers_by_question_id($id){
        $answers=Answer::where('question_id',$id)->get();
        return response($answers,200);
    }

    public function answer_question(Request $request,){
        $data=$request->validate([
            'user_id'=>'required',
            'poll_id'=>'required',
            'question_id'=>'required',
            'answer_id'=>'nullable|integer|present',
            'text'=>'nullable',
        ]);
        if($data['answer_id']){
            $text=Answer::where('id',$data['answer_id'])->firstOrFail();
            $text=$text->text;
            // проверка на наличие отправляемого ответа в БД, защищает от отправки одного и того же ответа несколько раз
            if(!empty(DB::select('select * from user_answer where user_id=? and poll_id=? and question_id=? and answer_id=? and text=?',[$data['user_id'], $data['poll_id'], $data['question_id'], $data['answer_id'],$text]))){
                return response('Этот ответ уже отправлен',401);
            }
            DB::insert('insert into user_answer (user_id,poll_id,question_id,answer_id,text) values(?,?,?,?,?)',[$data['user_id'],$data['poll_id'],$data['question_id'],$data['answer_id'],$text]);
        }
        else {
            // проверка на наличие отправляемого ответа в БД, защищает от отправки одного и того же ответа несколько раз
            if(!empty(DB::select('select * from user_answer where user_id=? and poll_id=? and question_id=? and answer_id=? and text=?',[$data['user_id'], $data['poll_id'], $data['question_id'], $data['answer_id'],$data['text']]))){
                return response('Этот ответ уже отправлен',401);
            }
            DB::insert('insert into user_answer (user_id,poll_id,question_id,answer_id,text) values(?,?,?,?,?)',[$data['user_id'],$data['poll_id'],$data['question_id'],NULL,$data['text']]);
        }
        return response('Ответ на вопрос получен',200);
    }

    public function answers_of_users(){
        $answers=DB::select('select * from user_answer');
        return response($answers,200);
    }
}
