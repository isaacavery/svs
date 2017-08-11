<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCirculator;
use App\Sheet;
use App\Circulator;
use App\Voter;
use Auth;
use Exception;

class CirculatorController extends Controller
{
    public function queue() {

    	$data['sheet'] = Sheet::whereNull('flagged_by')->first();
    	if(!$data['sheet'])
            return back()->withErrors(['empty' => 'Hmmmm ... it appears that there are no sheets in the Circulator Queue for review.']);
        // Parse comments
        $data['comments'] = explode('|',$data['sheet']->comments);
        foreach($data['comments'] as $k => $v){
            // Remove empty comments
            if(!$v)
                unset($data['comments'][$k]);
        }
    	return view('circulator.queue',$data);
    }
    public function search(Request $request) {
        $form = $request->all();
        $exact_match = $form['exact_match'];
        // Step 1: search for existing Circulators
        $v1 = [];
        $no_data = true;
        $q1 = "SELECT id voter_id, first_name, middle_name, last_name, res_address_1, eff_address_1, city, county, zip_code FROM voters WHERE last_name IS NOT NULL ";
        if($form['first']) {
            $no_data = false;
            $q1 .= "AND first_name LIKE ? ";
            $v1[] = ($exact_match) ? strtoupper($form['first']) : '%' . strtoupper($form['first']) . '%';
        }
        if($form['last']) {
            $no_data = false;
            $q1 .= "AND last_name LIKE ? ";
            $v1[] = ($exact_match) ? strtoupper($form['last']) : '%' . strtoupper($form['last']) . '%';
        }
        if($form['city']) {
            $no_data = false;
            $q1 .= "AND city LIKE ? ";
            $v1[] = ($exact_match) ? strtoupper($form['city']) : '%' . strtoupper($form['city']) . '%';
        }
        if($form['zip']) {
            $no_data = false;
            $q1 .= "AND (zip_code LIKE ? OR eff_zip_code LIKE ?) ";
            $val = ($exact_match) ? $form['zip'] : '%' . $form['zip'] . '%';
            $v1[] = $val;
            $v1[] = $val;
        }
        if($form['number']) {
            $no_data = false;
            $q1 .= "AND house_num like ? ";
            $v1[] = ($exact_match) ? strtoupper($form['number']) : '%' . strtoupper($form['number']) . '%';
        }
        if($form['street_name']) {
            $no_data = false;
            $q1 .= "AND street_name like ? ";
            $v1[] = ($exact_match) ? strtoupper($form['street_name']) : '%' . strtoupper($form['street_name']) . '%';
        }
        if($no_data){
            return json_encode(['success' => false, 'error' => 'No search parameters provided']);
        }
        $q1 .= 'LIMIT 10';
        //$q1 .= 'LIMIT 10';
        $res1 = DB::select($q1,$v1);
        return json_encode(['success' => true, 'count' => count($res1), 'matches' => $res1]);
    }

    public function add(StoreCirculator $request) {
    	// Store Circulator
        try{
            $check = Circulator::where('first_name', trim(strtoupper($request->first_name)))->where('last_name',trim(strtoupper($request->last_name)))->first();
            if($check) {
                // User already exists. Return the user id
                return json_encode(['success' => 'true', 'message' => 'Circulator already exists in the database as Circulator #' . $check->id, 'id' => $check->id]);
            } else {
                $circulator = Circulator::create(['first_name' => trim(strtoupper($request->first_name)), 'last_name' => trim(strtoupper($request->last_name)), 'street_name' => trim(strtoupper($request->street_name)), 'street_number' => trim(strtoupper($request->street_number)), 'city' => trim(strtoupper($request->city)), 'zip' => trim($request->zip)]);
                if($circulator){
                    // Circulator was created. Return the id.
                    return json_encode(['success' => 'true', 'message' => 'Circulator added as Circulator #' . $circulator->id, 'id' => $circulator->id]);
                } else {
                    // Unknown error
                    throw new Exception('There was an unknown error when adding the following data to the database: ' . json_encode($request->all()),1);
                }
            }
        } catch(\Exception $e) {
            // Exception handler
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
