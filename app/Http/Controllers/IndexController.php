<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Invite;
use App\Partner;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token.refresh', ['except' => ['login','BackInfofriend']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 微信授权登录获取用户详细信息
     */
    public function login(Request $request)
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
        $res = json_decode( $wx->getUserInfo($encryptedData, $iv,$userInfo['session_key']) );

        //若存在则更新，若不存在则插入
        $user = User::updateOrCreate(
            ['openId' => $res->openId],['nickName' => $res->nickName, 'avatarUrl' => $res->avatarUrl]
        );
        $token = Auth::guard('api')->fromUser($user);

        //返回前端
        return response()->json([
            'token' => "Bearer ".$token,
            'userinfo' => array(
                'openId' => $res->openId,
                'nickName' => $res->nickName,
                'avatarUrl' => $res->avatarUrl
            )
        ]);

    }


    /**
     * 返回分享朋友圈图片
     */
    public function GetUrl(Request $request)
    {
        $input = $request->all();
        $wx = new WxLibController();

        $info = $wx->GetAccessToken();

        $scene = $input['scene'];            //场景值
        $page = "pages/other/other";             //页面

        $res = $wx->GetpicUrl($scene,$page,$info);

        return response()
            ->json([
                'status' => 200,
                'msg' => '小程序码创建成功！',
            ]);

    }

    /**
     * 返回每个邀请函的具体信息
     *
     *   ----邀请函创建人
     *       ---用户名
     *       ---头像
     *       ---背景图片(第几幅图)
     *       ---邀请函文字
     *   ----参与者
     *       ---参与者头像
     *       ---参与者总数
     *   ----评论
     *       ---留言点赞数
     *       ---留言者头像
     *       ---留言者用户名
     *       ---留言者内容
     *       ---留言者openID
     *
     */
    public function BackInfo(Request $request)
    {
        $openid = $request->all();

        //查询Invite表(可获得 邀请函文字、背景图片)
        $invite = Invite::where('openId_id',$openid['openid'])->first();

        //添加该人位添加邀请函情况
        if (!isset($invite) || empty($invite)){

            //返回前端
            return response()->json([
                'status' => 401,
                'msg' => '未存在该用户！'
            ]);

        }else{

            $inviteID = $invite['id'];
            $UserInfo = $invite->user;                              //邀请函主人的详细信息(用户名、头像)

            //查询Partner表(可获得 所有在该邀请函中参与者openID)
            $Partner = array();                                     //所有参与者存数组
            $partners = $invite->partner;
            $partnernum = $partners->count();                        //参与者总数
            foreach ($partners as $partner){

                $PartnerInfo = Partner::find($partner['id'])->user;                //每条参与者用户信息
                $Partner[] = array(
                    'avatar' => $PartnerInfo['avatarUrl']
                );

            }

            //查询Comment表(可获得 所有在该邀请函中留言者openID、留言内容)
            $Comment = array();                                     //所有留言者存数组
            $comments = $invite->comment;
            foreach ($comments as $comment){

                $CommentInfo = Comment::find($comment['id'])->user;                //每条留言用户信息
                $CommentLikenum = Comment::find($comment['id'])->like->count();        //每条留言的点赞数

                //判断该用户是否已经点赞 点赞true 未点赞false
                $IsLikeflag = false;
                $IsLikes = Comment::find($comment['id'])->like;
                foreach ($IsLikes as $key => $IsLike) {
                    if ($IsLike['openId_id'] == $openid['openid']) {
                        $IsLikeflag = true;
                    }
                }

                $Comment[] = array(
                    'IsLikeflag' => $IsLikeflag,                                    //该用户是否点赞
                    'avatar' => $CommentInfo['avatarUrl'],                          //留言者头像
                    'nickname' => $CommentInfo['nickName'],                            //留言者昵称
                    'content' => $comment['content'],                                  //每条留言的内容
                    'openID' => $comment['openId_id'],                                 //每条留言者openID
                    'id' => $comment['id'],                                         //每条留言ID
                    'goodnum' => $CommentLikenum                                      //每条留言的点赞数
                );

            }


            //返回前端
            return response()->json([
                'status' => 200,
                'inviteID' => $inviteID,                    //邀请函ID
                'nickName' => $UserInfo['nickName'],        //邀请函主人昵称
                'avatarUrl' => $UserInfo['avatarUrl'],      //邀请函主人头像
                'invitewords' => $invite['content'],            //邀请函文字
                'siteImg' => $invite['pic'],                    //背景图片(第几幅图)
                'acceptedAvators' => $Partner,                      //所有参与者
                'acceptedAvatorsnum' => $partnernum,                //参与者总数
                'message' => $Comment,                      //所有留言者
            ]);

        }


    }

    /**
     * 返回每个邀请函的具体信息(朋友圈入口)
     *
     *   ----邀请函创建人
     *       ---用户名
     *       ---头像
     *       ---背景图片(第几幅图)
     *       ---邀请函文字
     *   ----参与者
     *       ---参与者头像
     *       ---参与者总数
     *   ----评论
     *       ---留言点赞数
     *       ---留言者头像
     *       ---留言者用户名
     *       ---留言者内容
     *       ---留言者openID
     *
     */
    public function BackInfofriend(Request $request)
    {
        $openid = $request->all();

        //查询Invite表(可获得 邀请函文字、背景图片)
        $invite = Invite::where('openId_id',$openid['openid'])->first();

        //添加该人位添加邀请函情况
        if (!isset($invite) || empty($invite)){

            //返回前端
            return response()->json([
                'status' => 401,
                'msg' => '未存在该用户！'
            ]);

        }else{

            $inviteID = $invite['id'];
            $UserInfo = $invite->user;                              //邀请函主人的详细信息(用户名、头像)

            //查询Partner表(可获得 所有在该邀请函中参与者openID)
            $Partner = array();                                     //所有参与者存数组
            $partners = $invite->partner;
            $partnernum = $partners->count();                        //参与者总数
            foreach ($partners as $partner){

                $PartnerInfo = Partner::find($partner['id'])->user;                //每条参与者用户信息
                $Partner[] = array(
                    'avatar' => $PartnerInfo['avatarUrl']
                );

            }

            //查询Comment表(可获得 所有在该邀请函中留言者openID、留言内容)
            $Comment = array();                                     //所有留言者存数组
            $comments = $invite->comment;
            foreach ($comments as $comment){

                $CommentInfo = Comment::find($comment['id'])->user;                //每条留言用户信息
                $CommentLikenum = Comment::find($comment['id'])->like->count();        //每条留言的点赞数

                //判断该用户是否已经点赞 点赞true 未点赞false
                $IsLikeflag = false;
                $IsLikes = Comment::find($comment['id'])->like;
                foreach ($IsLikes as $key => $IsLike) {
                    if ($IsLike['openId_id'] == $openid['openid']) {
                        $IsLikeflag = true;
                    }
                }

                $Comment[] = array(
                    'IsLikeflag' => $IsLikeflag,                                    //该用户是否点赞
                    'avatar' => $CommentInfo['avatarUrl'],                          //留言者头像
                    'nickname' => $CommentInfo['nickName'],                            //留言者昵称
                    'content' => $comment['content'],                                  //每条留言的内容
                    'openID' => $comment['openId_id'],                                 //每条留言者openID
                    'id' => $comment['id'],                                         //每条留言ID
                    'goodnum' => $CommentLikenum                                      //每条留言的点赞数
                );

            }


            //返回前端
            return response()->json([
                'status' => 200,
                'inviteID' => $inviteID,                    //邀请函ID
                'nickName' => $UserInfo['nickName'],        //邀请函主人昵称
                'avatarUrl' => $UserInfo['avatarUrl'],      //邀请函主人头像
                'invitewords' => $invite['content'],            //邀请函文字
                'siteImg' => $invite['pic'],                    //背景图片(第几幅图)
                'acceptedAvators' => $Partner,                      //所有参与者
                'acceptedAvatorsnum' => $partnernum,                //参与者总数
                'message' => $Comment,                      //所有留言者
            ]);

        }


    }

}
