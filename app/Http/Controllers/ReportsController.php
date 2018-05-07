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
    		zip_plus_four,eff_address_1,eff_address_2,eff_address_3,eff_address_4,eff_city,eff_state,eff_zip_code,eff_zip_plus_four,absentee_type,precinct_name,precinct,split, est as estimated_signatures, act as actual_signatures from circulators c 
            LEFT JOIN voters v USING (voter_id)
            LEFT JOIN (select sum(signature_count) est, circulator_id FROM sheets GROUP BY circulator_id) e ON (c.id = e.circulator_id)
            LEFT JOIN (SELECT COUNT(sheets.id) act, circulator_id FROM signers JOIN sheets ON (sheets.id = signers.sheet_id) WHERE signers.voter_id IS NOT NULL AND signers.voter_id != 0 GROUP BY circulator_id) a ON (c.id = a.circulator_id)
             ORDER BY 1 ');
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
	
    public function duplicates(Request $request)
    {

		$current_letter = $request->query('letter');
		// Create master list
		$sql =  "select signers.id as signer_id, voter_ids.voter_id, count, circulators.first_name cfname, circulators.last_name clname, circulators.middle_name cmname, CONCAT(circulators.last_name, ', ', circulators.first_name) as circulator_name, CONCAT(circulators.address, ', ', circulators.city, ', ', circulators.state, ' ', circulators.zip_code) as circulator_address, sheets.date_signed, sheet_id, filename, row, self_signed, voters.first_name vfname, voters.last_name vlname, voters.middle_name vmname, CONCAT(voters.last_name, ', ', voters.first_name) as signer_name, CONCAT(voters.res_address_1, ', ', voters.city, ', ', voters.state, ' ', voters.zip_code) as signer_address, signers.deleted_at from (SELECT voter_id, count(voter_id) as count from signers WHERE voter_id != 0 GROUP BY voter_id HAVING count(voter_id) > 1) as voter_ids JOIN signers ON (signers.voter_id = voter_ids.voter_id) JOIN sheets ON (sheets.id = signers.sheet_id) JOIN circulators ON (circulators.id = sheets.circulator_id) LEFT JOIN voters ON (voters.voter_id = signers.voter_id) ORDER BY 2, 9";
		$master_list = DB::select($sql, []);
		$active_id = 0;
		$position = 0;
		foreach($master_list as $k => $v) {
			if($active_id !== $master_list[$k]->voter_id) {
				$active_id = $master_list[$k]->voter_id;
				$position = 1;
			} else {
				$position++;
			}
			$master_list[$k]->position = $position;
		}

		if(!$master_list)
			return 'There are no circulators to generate a report on.';
			
		/* Used for downloading spreadsheet of full report
    	$filename = "uploads/duplicates.csv";
	    $handle = fopen($filename, 'w+');
		fputcsv($handle, array_keys((array) $master_list[0]));
	    foreach($master_list as $row) {
	        fputcsv($handle, (array) $row);
	    }
	    fclose($handle);
	    $headers = array(
	        'Content-Type' => 'text/csv',
	    );
    	return response()->download($filename, 'duplicates_' . date('Y-m-d') . '.csv', $headers);
		*/

		// Loop throught the results. Mark the first row as 'do-not-remove', unless there is a SS sheet.
		$ar = false;
		$rows = array();
		$keep = false;
		foreach($master_list as $k => $v) {
			if($ar != $v->voter_id) {
				// New match
				foreach($rows as $key) {
					$master_list[$key]->do_not_remove = ($key != $keep) ? false : true;
				}
				$ar = $v->voter_id;
				$rows = array();
				$keep = false;
			}
			if(!count($rows) || $v->self_signed == 1) {
				$keep = $k; // This is the first row, so we will keep it for now.
			} else if ($v->self_signed == 1) {
				$keep = $k;
			}
			$rows[] = $k;
		}
		// Don't forget the last duplicate!
		foreach($rows as $key) {
			$master_list[$key]->do_not_remove = ($key != $keep) ? false : true;
		}

		if($request->query('sort') == 'signer') {
			usort($master_list, function($a, $b){
				return strcmp($a->signer_name, $b->signer_name);
			});
		} else {
			usort($master_list, function($a, $b){
				return strcmp($a->circulator_name, $b->circulator_name);
			});
		}

		// Run filter (if any)
		if($current_letter) {
			$current_letter = strtoupper(substr($current_letter,0,1));
			// Loop through and drop non matches
			foreach($master_list as $k => $v) {
				if(substr($v->circulator_name, 0, 1) !== $current_letter)
					unset($master_list[$k]);
			}
			$sql .= " WHERE circulators.last_name LIKE '$current_letter%'";
		}

		$data = array('duplicates' => $master_list, 'current_letter' => $current_letter);
        return view('reports.duplicates', $data);
	}
	
	public function duplicatesDownload()
	{
		
		// Create master list
		$sql =  "select signers.id as signer_id, voter_ids.voter_id, count, circulators.first_name cfname, circulators.last_name clname, circulators.middle_name cmname, CONCAT(circulators.last_name, ', ', circulators.first_name) as circulator_name, CONCAT(circulators.address, ', ', circulators.city, ', ', circulators.state, ' ', circulators.zip_code) as circulator_address, sheets.date_signed, sheet_id, filename, row, self_signed, voters.first_name vfname, voters.last_name vlname, voters.middle_name vmname, CONCAT(voters.last_name, ', ', voters.first_name) as signer_name, CONCAT(voters.res_address_1, ', ', voters.city, ', ', voters.state, ' ', voters.zip_code) as signer_address, signers.deleted_at from (SELECT voter_id, count(voter_id) as count from signers WHERE voter_id != 0 GROUP BY voter_id HAVING count(voter_id) > 1) as voter_ids JOIN signers ON (signers.voter_id = voter_ids.voter_id) JOIN sheets ON (sheets.id = signers.sheet_id) JOIN circulators ON (circulators.id = sheets.circulator_id) LEFT JOIN voters ON (voters.voter_id = signers.voter_id) ORDER BY 2, 9";
		$master_list = DB::select($sql, []);
		$active_id = 0;
		$position = 0;
		foreach($master_list as $k => $v) {
			if($active_id !== $master_list[$k]->voter_id) {
				$active_id = $master_list[$k]->voter_id;
				$position = 1;
			} else {
				$position++;
			}
			$master_list[$k]->position = $position;
		}

		// Loop throught the results. Mark the first row as 'do-not-remove', unless there is a SS sheet.
		$ar = false;
		$rows = array();
		$keep = false;
		foreach($master_list as $k => $v) {
			if($ar != $v->voter_id) {
				// New match
				foreach($rows as $key) {
					$master_list[$key]->do_not_remove = ($key != $keep) ? false : true;
				}
				$ar = $v->voter_id;
				$rows = array();
				$keep = false;
			}
			if(!count($rows) || $v->self_signed == 1) {
				$keep = $k; // This is the first row, so we will keep it for now.
			} else if ($v->self_signed == 1) {
				$keep = $k;
			}
			$rows[] = $k;
		}
		// Don't forget the last duplicate!
		foreach($rows as $key) {
			$master_list[$key]->do_not_remove = ($key != $keep) ? false : true;
		}

		if(!$master_list)
			return 'There are no circulators to generate a report on.';
			
		/* Used for downloading spreadsheet of full report */
    	$filename = "uploads/duplicates.csv";
	    $handle = fopen($filename, 'w+');
		fputcsv($handle, array_keys((array) $master_list[0]));
	    foreach($master_list as $row) {
	        fputcsv($handle, (array) $row);
	    }
	    fclose($handle);
	    $headers = array(
	        'Content-Type' => 'text/csv',
	    );
    	return response()->download($filename, 'duplicates_' . date('Y-m-d') . '.csv', $headers);
	}
}
