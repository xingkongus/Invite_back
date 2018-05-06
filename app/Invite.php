<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{


    protected $guarded = [];

    // 关联所有的评论
    public function comment()
    {
        return $this->hasMany('App\Comment', 'invite_id', 'id');
    }

    //关联所有的参与者
    public function partner()
    {
        return $this->hasMany('App\Partner', 'invite_id', 'id');
    }

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\User', 'openId_id', 'openId');
    }



}
