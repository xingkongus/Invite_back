<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];


    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\User', 'openId_id', 'openId');
    }

    // 关联点赞表
    public function like()
    {
        return $this->hasMany('App\Like', 'comment_id', 'id');
    }


}
