<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Sheet;
use App\Circulator;
use App\Voter;
use App\User;
use App\Signer;
use Auth;

class ReportsController extends Controller
{
    public function circulators()
    {
    	if(!Auth::user()->admin)
    		return 'Sorry, you do not have permission to generate the requested report';

    	$circulators = DB::select('select c.voter_id,
    		COALESCE(v.first_name, c.first_name) AS first_name,
    		COALESCE(v.middle_name, c.middle_name) AS middle_name,
    		COALESCE(v.last_name, c.last_name) AS last_name,
    		name_suffix,birth_date,confidential,eff_regn_date,status,party_code,county,
    		COALESCE(v.res_address_1, c.address) AS res_address_1,
    		res_address_2,
    		COALESCE(v.house_num, c.street_number) AS house_num,
    		house_suffix,pre_direction,
    		COALESCE(v.street_name, c.street_name) AS street_name,
    		street_type,post_direction,unit_type,unit_num,addr_non_std,
    		COALESCE(v.city, c.city) AS city,
    		COALESCE(v.state, c.state) AS state,
    		COALESCE(v.zip_code, c.zip_code) AS zip_code,
    		zip_plus_four,eff_address_1,eff_address_2,eff_address_3,eff_address_4,eff_city,eff_state,eff_zip_code,eff_zip_plus_four,absentee_type,precinct_name,precinct,split from circulators c LEFT JOIN voters v USING (voter_id) ORDER BY 1');
    	if(!$circulators)
    		return 'There are no circulators to generate a report on.';
    	$filename = "uploads/circulators.csv";
	    $handle = fopen($filename, 'w+');
		fputcsv($handle, array_keys((array) $circulators[0]));
	    foreach($circulators as $row) {
	        fputcsv($handle, (array) $row);
	    }

	    fclose($handle);

	    $headers = array(
	        'Content-Type' => 'text/csv',
	    );
    	return response()->download($filename, 'circulators_' . date('Y-m-d') . '.csv', $headers);
    }

    public function signers()
    {
    	if(!Auth::user()->admin)
    		return 'Sorry, you do not have permission to generate the requested report';


    	$signers = DB::select('select voter_id,first_name,middle_name,last_name,name_suffix,birth_date,confidential,eff_regn_date,status,party_code,county,res_address_1,res_address_2,house_num,house_suffix,pre_direction,street_name,street_type,post_direction,unit_type,unit_num,addr_non_std,city,state,zip_code,zip_plus_four,eff_address_1,eff_address_2,eff_address_3,eff_address_4,eff_city,eff_state,eff_zip_code,eff_zip_plus_four,absentee_type,precinct_name,precinct,split from voters WHERE voter_id IN (SELECT voter_id FROM signers) ORDER BY 1');
    	if(!$signers)
    		return 'There are not signers to generate a report on.';
		$filename = "uploads/signers.csv";
	    $handle = fopen($filename, 'w+');
		fputcsv($handle, array_keys((array) $signers[0]));
	    foreach($signers as $row) {
	        fputcsv($handle, (array) $row);
	    }

	    fclose($handle);

	    $headers = array(
	        'Content-Type' => 'text/csv',
	    );
    	return response()->download($filename, 'signers_' . date('Y-m-d') . '.csv', $headers);
    }
}
