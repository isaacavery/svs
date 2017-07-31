<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $fillable = ['user_id','filename','batch_id','original_filename','md5_hash'];
}
