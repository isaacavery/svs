<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Circulator extends Model
{
    protected $fillable = ['first_name', 'middle_name', 'last_name', 'voter_id', 'street_number', 'street_name', 'address', 'city', 'zip_code','user_id'];
}
