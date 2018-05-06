<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Invite;
use App\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $wx = new WxLibController();

        //code 在小程序端使用 wx.login 获取
        $code = $request->code;

        //encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
        $encryptedData = $request->encryptedData;
        $iv = $request->iv;

        //根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
        $userInfo = $wx->getLoginInfo($code);

        //获取解密后的用户信息
        $res = json_decode( $wx->getUserInfo($encryptedData, $iv) );

        //若存在则更新，若不存在则插入
        User::updateOrCreate(
            ['openId' => $res->openId],['nickName' => $res->nickName, 'avatarUrl' => $res->avatarUrl]
        );

        //返回前端
        return response()->json([
            'openId' => $res->openId,
            'nickName' => $res->nickName,
            'avatarUrl' => $res->avatarUrl
        ]);

    }


    public function test()
    {
//        $res = Invite::where('openId_id','oH1Yn4yLnQ_AsJxVSslawb2Emsxg')->first();
//        $res = $res->comment;
////        dd($res['0']['id']);
//
//        $res = Comment::find($res['0']['id'])->user;
//        $res = Comment::find(1)->like->count();
//        dd($res);
//        $res = Invite::find(1)->user;
//        dd($res);


    }

}
