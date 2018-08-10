<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Invite;
use App\Like;
use App\Partner;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token.refresh', ['except' => ['login']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 制作邀请函Api
     */
    public function SetInvite(Request $request)
    {
        $input = $request->all();

        //参数不完整
        if (!isset($input['openId_id']) || empty($input['openId_id']) || !isset($input['content']) || empty($input['content']) || !isset($input['pic']) || empty($input['pic']) ){

            return response()
                ->json([
                    'status' => 403,
                    'msg' => '数据缺失,请完整输入个人信息！',
                ]);

        }else{

            //如果存在则更新，如果不存在则创建
            Invite::updateOrCreate( ['openId_id' => $input['openId_id'] ], ['content' => $input['content'], 'pic' => $input['pic'] ] );

            return response()
                ->json([
                    'status' => 200,
                    'msg' => '邀请函创建成功！',
                ]);

        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 添加留言Api
     */
    public function SetComment(Request $request)
    {
        $input = $request->all();

        //参数不完整
        if (!isset($input['invite_id']) || empty($input['invite_id']) || !isset($input['openId_id']) || empty($input['openId_id']) || !isset($input['content']) || empty($input['content']) ){

            return response()
                ->json([
                    'status' => 403,
                    'msg' => '数据缺失,请完整输入个人信息！',
                ]);

        }else{

            Comment::create($input);

            return response()
                ->json([
                    'status' => 200,
                    'msg' => '留言成功！',
                ]);

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 点赞Api
     */
    public function SetLike(Request $request)
    {
        $input = $request->all();

        //参数不完整
        if (!isset($input['comment_id']) || empty($input['comment_id']) || !isset($input['openId_id']) || empty($input['openId_id']) ){

            return response()
                ->json([
                    'status' => 403,
                    'msg' => '数据缺失,请完整输入个人信息！',
                ]);

        }else{

            //如果存在则更新，如果不存在则创建
            Like::updateOrCreate( ['comment_id' => $input['comment_id'], 'openId_id' => $input['openId_id'] ], [ ] );

            return response()
                ->json([
                    'status' => 200,
                    'msg' => '点赞成功！',
                ]);

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 取消点赞Api
     */
    public function CancelLike(Request $request)
    {
        $input = $request->all();

        //参数不完整
        if (!isset($input['comment_id']) || empty($input['comment_id']) || !isset($input['openId_id']) || empty($input['openId_id']) ){

            return response()
                ->json([
                    'status' => 403,
                    'msg' => '数据缺失,请完整输入个人信息！',
                ]);

        }else{

            $condition['comment_id'] = $input['comment_id'];
            $condition['openId_id'] = $input['openId_id'];

            //删除数据
            Like::where($condition)->delete();

            return response()
                ->json([
                    'status' => 200,
                    'msg' => '取消成功！',
                ]);

        }
    }
    
    

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 参与者Api
     */
    public function SetPartner(Request $request)
    {

        $input = $request->all();

        //参数不完整
        if (!isset($input['invite_id']) || empty($input['invite_id']) || !isset($input['openId_id']) || empty($input['openId_id']) ){

            return response()
                ->json([
                    'status' => 403,
                    'msg' => '数据缺失,请完整输入个人信息！',
                ]);

        }else{

//            Partner::create($input);
            //如果存在则更新，如果不存在则创建
            Partner::updateOrCreate( ['invite_id' => $input['invite_id'], 'openId_id' => $input['openId_id'] ], [ ] );

            return response()
                ->json([
                    'status' => 200,
                    'msg' => '报名成功！',
                ]);

        }

    }


}
