<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Circulator extends Model
{
    protected $fillable = ['first_name', 'last_name', 'street_number', 'street_name', 'city', 'zip'];
}
