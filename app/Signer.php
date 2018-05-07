<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signer extends Model
{
    use SoftDeletes;
    protected $fillable = ['voter_id', 'sheet_id', 'row', 'user_id'];

}
