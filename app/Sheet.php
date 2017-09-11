<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $fillable = ['user_id','filename','batch_id','original_filename','md5_hash','user_id'];

    public function circulator()
    {
        return $this->belongsTo('App\Circulator');
    }

    public function circulatorComplete()
    {
    	if($this->circulator && $this->date_signed && $this->signature_count)
    		return true;
    	return false;
    }

    public function signers()
    {
    	return $this->hasMany('App\Signer')->orderBy('row');
    }
}
