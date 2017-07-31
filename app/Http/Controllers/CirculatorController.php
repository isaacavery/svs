<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCirculator;
use App\Sheet;
use App\Circulator;

class CirculatorController extends Controller
{
    public function queue() {
    	$data['sheet'] = Sheet::first();
    	if(!$data['sheet'])
    		dd("Sorry, there are no sheets to review.");
    	return view('circulator.queue',$data);
    }
    public function search(Request $request) {
    	dd($request);
    }
    public function add(StoreCirculator $request) {
    	// Store Circulator
    	$circulator = Circulator::create(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'city' => $request->city, 'zip' => $request->zip]);
    	if($circulator){
    		return json_encode(['success' => true, 'id' => $circulator->id]);
    	} else {
    		return json_encode(['success' => false, 'error' => 'Unknown error']);
    	}
    }
}
