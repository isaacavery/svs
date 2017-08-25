<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signer extends Model
{
    protected $fillable = ['voter_id', 'sheet_id', 'row', 'user_id'];
}
