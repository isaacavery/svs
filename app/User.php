<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function circulators()
    {
        return $this->hasMany('App\Sheet','circulator_completed_by','id');
    }

    public function signers()
    {
        return $this->hasMany('App\Signer','user_id','id')->whereNotNull('voter_id')->where('voter_id','>',0);
    }

}
