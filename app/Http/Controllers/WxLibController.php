<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxLibController extends Controller
{

    /**
     * @var \Illuminate\Config\Repository|mixed
     * 定义变量
     */
    private $wxappid;
    private $wxsecret;
    private $wxcodeurl;
    private $sessionKey;


    /**
     * @var int
     * 定义错误代码
     */
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;


    /**
     * 构造函数
     * @param $wxsecret string 用户在小程序登录后获取的会话密钥
     * @param $wxappid string 小程序的appid
     */
    public function __construct()
    {
        $this->wxappid = config('app.wx_appid');
        $this->wxsecret = config('app.wx_secret');
        $this->wxcodeurl = config('app.wx_code_url');
    }


    /**
     * @param $code
     * @return array|bool|mixed
     * 获取session_key、openid
     */
    public function getLoginInfo($code)
    {
        $code_url = sprintf($this->wxcodeurl,$this->wxappid,$this->wxsecret,$code);
        $userInfo = $this->wxcurl($code_url);
        if(!isset($userInfo['session_key'])){
            return [
                'code' => 10000,
                'msg' => '获取 session_key 失败',
            ];
        }
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }


    /**
     * @param $encryptedData
     * @param $iv
     * @param null $sessionKey
     * @return array|string
     * 用户详细信息的解密
     */
    public function getUserInfo($encryptedData, $iv, $sessionKey = null)
    {
        if (empty($sessionKey)) {
            $sessionKey = $this->sessionKey;
        }
        $decodeData = "";
        $errorCode = $this->decryptData($encryptedData, $iv, $decodeData,$sessionKey);
        if ($errorCode !=0 ) {
            return [
                'code' => 10001,
                'msg' => 'encryptedData 解密失败'
            ];
        }
        return $decodeData;
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData( $encryptedData, $iv, &$data,$sessionKey )
    {
        if (strlen($sessionKey) != 24) {
            return WxLibController::$IllegalAesKey;
        }
        $aesKey=base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return WxLibController::$IllegalIv;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return WxLibController::$IllegalBuffer;
        }
        if( $dataObj->watermark->appid != $this->wxappid )
        {
            return WxLibController::$IllegalBuffer;
        }
        $data = $result;
        return WxLibController::$OK;
    }


}
