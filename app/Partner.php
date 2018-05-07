<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];


    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\User', 'openId_id', 'openId');
    }


}
