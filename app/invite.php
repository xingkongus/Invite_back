<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invite extends Model
{


    protected $guarded = [];


    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'openId_id', 'openId');
    }

}
