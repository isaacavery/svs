<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = ['voter_id', 'first_name', 'middle_name', 'last_name', 'name_suffix', 'birth_date', 'confidential', 'eff_regn_date', 'status', 'party_code', 'phone_num', 'unlisted', 'county', 'res_address_1', 'res_address_2', 'house_num', 'house_suffix', 'pre_direction', 'street_name', 'street_type', 'post_direction', 'street_name', 'street_type', 'unit_type', 'unit_num', 'addr_non_std', 'city', 'eff_state', 'eff_zip_code', 'eff_zip_plus_four', 'absentee_type', 'precinct_name', 'precinct', 'split'];
}
