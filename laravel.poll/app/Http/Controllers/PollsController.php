<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;

class PollsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $polls = Poll::all();
        return response($polls,200);
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
            'title'=>'required',
            'description'=>'required',
            'ends_at'=>'required',
        ]);
        $d=DateTime::createFromFormat("Y-m-d H:i:s",$data['ends_at']);          //проверка формата даты на соответствие "Y-m-d H:i:s"
        if($d->format("Y-m-d H:i:s") == $data['ends_at']){
            $poll=Poll::create($data);
            return response($poll,200);
        }else{
            return response('Неверный формат даты окончания опроса', 401);
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
            'title'=>'required',
            'description'=>'required',
            'ends_at'=>'required',
        ]);
        $d=DateTime::createFromFormat("Y-m-d H:i:s",$data['ends_at']);          //проверка форматат даты
        if($d&&$d->format("Y-m-d H:i:s") == $data['ends_at']){
            $poll=Poll::where('id',$id)->update($data,$id);
            return response($poll,200);
        }else{
            return response('Неверный формат даты окончания опроса', 401);
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
        $poll=Poll::find($id);
        $poll->delete();
        return response('Опрос удалён', 200);
    }
    
    public function get_active_polls()
    {
        $active_polls=[];
        $polls = Poll::all();
        date_default_timezone_set('Europe/Moscow');
        $now=date("Y-m-d H:i:s");
        foreach($polls as $poll){
            if(($poll->ends_at>$now)){
                $active_polls[]=$poll;
            }
        }
        return response($active_polls,200);
    }

    public function take_poll($id){
        $questions=Question::where('poll_id',$id)->get();
        $poll=[];
        foreach($questions as $question){
            $answers=Answer::where('question_id',$question->id)->get();
            $poll[]=compact('question','answers');
        }
        return response($poll,200);
    }

    public function complete_poll(Request $request){
        $data=$request->validate([
            'user_id'=>'required',
            'poll_id'=>'required',
        ]);
        $count1=DB::select('select count(distinct question_id) as count1 from user_answer where user_id=? and poll_id=?',[$data['user_id'],$data['poll_id']]);
        $count2=DB::select('select count(*) as count1 from questions where poll_id=?',[$data['poll_id']]);
        if($count1==$count2)            //сравнение количества вопросов с ответом с общим количеством вопросов в опросе
        {
            DB::insert('insert into user_poll (user_id, poll_id) values (?, ?)', [$data['user_id'],$data['poll_id']]);
            return response('Опрос пройден',200);
        }
        else{
            return response('Для завершения опроса ответьте на все вопросы',401);
        }
    }

    public function get_completed_polls($user_id){
        $polls_id=DB::select('select poll_id from user_poll where user_id=?',[$user_id]);
        foreach($polls_id as $poll_id){
            $completed_polls=Poll::where('id',$poll_id->poll_id)->get();
        }
        return response($completed_polls,200);

    }

    public function get_completed_poll($user_id,$poll_id){
        $questions=Question::where('poll_id',$poll_id)->get();
        foreach($questions as $question){           //повопросам получаем ответы к ним
            $answers=DB::select('select text from user_answer where user_id=? and poll_id=? and question_id=?',[$user_id,$poll_id,$question->id]);
            $poll[]=compact('question','answers');      //компануем вопросы и ответы
        }
        return response($poll,200);
    }
}
