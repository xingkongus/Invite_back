<?php

namespace App\Http\Controllers;

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
        $res =  $wx->getUserInfo($encryptedData, $iv);
        return $res;
    }

}
