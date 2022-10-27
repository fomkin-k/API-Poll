<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class UsersController extends Controller
{

    public function register(Request $request){
        $data=$request->validate([
            'login'=>'required|string',
            'password'=>'required|string',
            'personal_id'=>'required',
            'is_admin'=>'required',
        ]);

        $user=User::create([
            'login'=>$data['login'],
            'password'=>bcrypt($data['password']),
            'personal_id'=>$data['personal_id'],
            'is_admin'=>$data['is_admin'],
        ]);
        //Auth::login($user,true);
        if($user->is_admin==1){
            $token=$user->createToken('myapptoken')->plainTextToken;
            $response=[
                'user'=> $user,
                'token'=>$token,
            ];
        }else{
            $response=[
                'user'=> $user,
            ];
        }
        return response($response,201);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        //Auth::logout();
        return response('Вы вышли из режима администратора',200);
    }

    public function login(Request $request){
        $data=$request->validate([
            'login'=>'required|string',
            'password'=>'required|string',
        ]);

        $user=User::where('login',$data['login'])->get();

        if(!$user||!Hash::check($data['password'],$user[0]->password)){
            return response('Неверный логин или пароль',401);
        }
        if($user[0]->is_admin!=1){
            return response('Отсутствуют права администратора',401);
        }
        //Auth::login($user[0],true);
        $token=$user[0]->createToken('myapptoken')->plainTextToken;

        $response=[
            'user'=> $user,
            'token'=>$token
        ];
        return response($response,201);
    }

}
