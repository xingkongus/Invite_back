<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{


    protected $guarded = [];

    // 关联所有的评论
    public function comment()
    {
        return $this->hasMany('App\Models\Comment', 'invite_id', 'id');
    }

    //关联所有的参与者
    public function partner()
    {
        return $this->hasMany('App\Models\Partner', 'invite_id', 'id');
    }

}
