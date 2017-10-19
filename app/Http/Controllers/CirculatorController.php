<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCirculator;
use App\Sheet;
use App\Circulator;
use App\Voter;
use App\Signer;
use Auth;
use Exception;
/**
 * A resource controller with additional AJAX functionality to handle
 * the Circulator portion of the App
 */
class CirculatorController extends Controller
{
    /**
     * Loads the next available sheet into the Circulators queue for processing.
     *
     * @return void
     */
    public function queue()
    {
        $data['recent_circulators'] = Circulator::with('voter')->limit(5)->orderBy('updated_at', 'desc')->get();
        $data['last_date'] = Sheet::select('date_signed')->where('circulator_completed_by', Auth::user()->id)->orderBy('updated_at','desc')->first();
        $data['sheet'] = Sheet::whereNull('flagged_by')->whereNull('circulator_completed_by')
            ->where(
                function ($query) {
                    $query->where('checked_out', '<', date("Y-m-d H:i:s", time() - 30 * 60))
                        ->orWhereNull('checked_out');
                }
            )->with('circulator')->first();
        if (!$data['sheet']) {
            return redirect('/')->withErrors(['empty' => 'Hmmmm ... it appears that there are no sheets in the Circulator Queue for review.']);
        }
        // Allow an optional parameter to be passed to automatically select the Single Signer option.
        $data['single_signer'] = (isset($_GET['single_signer']) && $_GET['single_signer'] == 'true') ? true : false;

        // Check out sheet
        $data['sheet']->checked_out = date("Y-m-d H:i:s");
        $data['sheet']->save();

        // Parse comments
        $data['comments'] = explode('|', $data['sheet']->comments);
        foreach ($data['comments'] as $k => $v) {
            // Remove empty comments
            if(!$v)
                unset($data['comments'][$k]);
        }
        return view('circulator.queue', $data);
    }
    /**
     * Handles AJAX search requests for Circulators
     *
     * @param Request $request The AJAX Request object
     * 
     * @return void
     */
    public function search(Request $request)
    {
        $form = $request->all();
        $exact_match = $form['exact_match'];
        // Step 1: search for existing Circulators
        $circulators = Circulator::with('voter')->limit(5);
        $v1 = [];
        $no_data = true;
        $q1 = "SELECT '' as circulator_id, voter_id, first_name, middle_name, last_name, res_address_1, COALESCE(eff_address_1,eff_address_2) eff_address_1, city, county, zip_code FROM voters WHERE voter_id NOT IN (SELECT voter_id FROM circulators WHERE voter_id IS NOT NULL) ";
        if ($form['vid']) {
            $q1 .= 'AND voter_id = ' . $form['vid'] . ' ';
            $circulators->where('voter_id', $form['vid']);
        } else {
            if ($form['first']) {
                $no_data = false;
                $q1 .= "AND first_name LIKE ? ";
                if ($exact_match) {
                    $v1[] = strtoupper($form['first']);
                    $circulators->where('first_name',strtoupper($form['first']));
                } else {
                    $v1[] = '%' . strtoupper($form['first']) . '%';
                    $circulators->where('first_name','%' . strtoupper($form['first']) . '%');

                }
            }
            if ($form['last']) {
                $no_data = false;
                $q1 .= "AND last_name LIKE ? ";
                if ($exact_match) {
                    $v1[] = strtoupper($form['last']);
                    $circulators->where('last_name',strtoupper($form['last']));
                } else {
                    $v1[] = '%' . strtoupper($form['last']) . '%';
                    $circulators->where('last_name','%' . strtoupper($form['last']) . '%');

                }
            }
            if ($form['city']) {
                $no_data = false;
                $circulators->whereRaw('UPPER(city) LIKE "' . strtoupper($form['city']) . '"');
                $q1 .= "AND city LIKE ? ";
                $v1[] = ($exact_match) ? strtoupper($form['city']) : '%' . strtoupper($form['city']) . '%';
            }
            if ($form['zip']) {
                $no_data = false;
                $circulators->where('zip_code',$form['zip']);
                $q1 .= "AND (zip_code = ? OR eff_zip_code = ?) ";
                $val = $form['zip'];
                $v1[] = $val;
                $v1[] = $val;
            }
            if (isset($form['po_box']) && $form['po_box'] == 'true') {
                // Perform PO Box Search
                if(!$form['number'])
                    die('Please enter a PO Box number in the "Street Number" field.');
                $num = $form['number'];
                $pobox_list = "('PO BOX $num', 'P O BOX $num', 'POBOX $num', 'POB $num')"; 
                $q1 .= " AND (eff_address_1 IN $pobox_list OR eff_address_2 IN $pobox_list) ";
                $no_data = false;
            } else {
                if ($form['number']) {
                    $no_data = false;
                    $circulators->where('street_number',$form['number']);
                    $q1 .= "AND house_num = ? ";
                    $v1[] = $form['number'];
                }
                if ($form['street_name']) {
                    $no_data = false;
                    $circulators->whereRaw('UPPER(street_name) LIKE "' . strtoupper($form['street_name']) . '"');
                    $q1 .= "AND street_name like ? ";
                    $v1[] = ($exact_match) ? strtoupper($form['street_name']) : '%' . strtoupper($form['street_name']) . '%';
                }
            }
            if ($no_data) {
                return json_encode(['success' => false, 'error' => 'No search parameters provided']);
            }
        }
        $q1 .= 'LIMIT 10';
        $res1 = DB::select($q1, $v1);
        foreach ($circulators->get() as $res) {
            $res2[] = [
                'circulator_id' => $res->id,
                'voter_id' => $res->voter_id,
                'first_name' => $res->first_name,
                'middle_name' => $res->middle_name,
                'last_name' => $res->last_name,
                'res_address_1' => $res->street_number . ' ' . $res->street_name,
                'eff_address_1' => ($res->voter) ? $res->voter->eff_address_1 : '',
                'city' => $res->city,
                'county' => '',
                'zip_code' => $res->zip_code
            ];
        }
        // Search existing Circulators:
        if($circulators->count())
            $res1 = array_merge($res2,$res1);

        return json_encode(['success' => true, 'count' => count($res1), 'matches' => $res1]);
    }

    /**
     * Stores a Circulator in the database
     *
     * @param StoreCirculator $request Request object built from the current request
     * 
     * @return void
     */
    public function add(StoreCirculator $request)
    {
        // Store Circulator
        try{
            $circulator = Circulator::create(['first_name' => trim(strtoupper($request->first_name)), 'last_name' => trim(strtoupper($request->last_name)), 'street_name' => trim(strtoupper($request->street_name)), 'street_number' => trim(strtoupper($request->street_number)), 'address' => trim(strtoupper($request->street_number)) . ' ' . trim(strtoupper($request->street_name)),'city' => trim(strtoupper($request->city)), 'zip_code' => trim($request->zip), 'user_id' => Auth::user()->id,'state' => $request->state]);
            if ($circulator) {
                // Circulator was created. Return the id.
                return json_encode(['success' => 'true', 'message' => 'Circulator added as Circulator #' . $circulator->id, 'id' => $circulator->id]);
            } else {
                // Unknown error
                throw new Exception('There was an unknown error when adding the following data to the database: ' . json_encode($request->all()),1);
            }
        } catch(\Exception $e) {
            // Exception handler
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Selects a Circulator based on the proved AJAX request
     *
     * @param Request $request The AJAX Request object
     * 
     * @return void
     */
    public function ajaxSelect(Request $request)
    {
        try{
            if ($request->cid) {
                // Circulator ID is provided, so look up and return Circulator record
                $circulator = Circulator::find($request->cid);
                if(!$circulator)
                    throw new Exception('Unable to find Circulator with ID ' . $request->cid);
                $circulator->updated_at = date('Y-m-d H:i:s');
                $circulator->save();
            } else if ($request->vid) {
                // Circulator ID is NOT provided, so create a new Circulator and return the data
                $voter = Voter::where('voter_id',$request->vid)->first();
                if(!$voter)
                    throw new Exception('Unable to find Voter with ID ' . $request->vid);
                $circulator = $this->createCirculatorFromVoter($voter);
            }

            if ($request->sid) {
                $sheet = Sheet::find($request->sid);
                $sheet->circulator_id = $circulator->id;
                if ($sheet->self_signed) {
                    // Delete any existing Signers
                    if ($sheet->signers()->count()) {
                        $sheet->signers()->delete();
                    }
                    // Add Circulator as Signer
                    $data = [
                        'sheet_id' => $sheet->id,
                        'voter_id' => ($circulator->voter_id) ? $circulator->voter_id : 0,
                        'row' => 1,
                        'user_id' => Auth::user()->id
                    ];
                    $signer = Signer::create($data);
                    // And mark the sheet as Signer Complete
                    $sheet->signatures_completed_by = Auth::user()->id;
                }
                $sheet->save();
            } else {
                throw new Exception('No Sheet ID specified');
            }


            return json_encode(['success' => true, 'message' => 'Assigned ' . $circulator->first_name . ' ' . $circulator->last_name . ' as the circulator for this sheet', 'circulator' => $circulator->toArray()]);
        } catch(\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    /**
     * Handles an AJAX request to unlink the Circulator for a given sheet.
     *
     * @param Request $request The AJAX Request object. Requires an 'id' value corresponding to the sheet's id attribute.
     * 
     * @return void
     */
    public function ajaxRemoveCirculator(Request $request)
    {
        try{
            if(!$request->sid)
                throw new Exception('No Sheet ID specified');

            $sheet = Sheet::find($request->sid);
            $sheet->circulator_id = null;
            // Remove the Signer if it is a self-signed
            if ($sheet->self_signed) {
                $sheet->signers()->delete();
                $sheet->signatures_completed_by = null;
            }
            $sheet->save();

            return json_encode(['success' => true, 'message' => 'Removed circulator from sheet ' . $request->sid]);
        } catch(\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Checks to see whether all of the sheet's required data has been added.
     *
     * @param int $id The id attribute for the sheet to check
     * 
     * @return void
     */
    public function checkCompletion($id)
    {
        $sheet = Sheet::find($id);
        if(!$sheet)
            return json_encode(['success' => 0, 'error' => 'Unable to find the requested sheet']);

        $complete = ($sheet->circulator_id && $sheet->date_signed && $sheet->signature_count) ? true : false;
        return json_encode(['success' => true, 'complete' => $complete]);
    }

    /**
     * Creates a new Circulator from the provided Voter
     *
     * @param Voter $voter The Voter object to use for a data source
     * 
     * @return void
     */
    private function createCirculatorFromVoter($voter)
    {
        if (Circulator::where('voter_id', $voter->voter_id)->count()) {
            $circulator = Circulator::where('voter_id')->first();
        } else {
            $circulator = Circulator::create(
                [
                    'first_name' => $voter->first_name,
                    'middle_name' => $voter->middle_name,
                    'last_name' => $voter->last_name,
                    'street_number' => $voter->house_num,
                    'street_name' => $voter->street_name,
                    'city' => $voter->city,
                    'zip_code' => $voter->eff_zip_code,
                    'address' => $voter->res_address_1,
                    'voter_id' => $voter->voter_id,
                    'zip_code' => $voter->zip_code,
                    'user_id' => Auth::user()->id
                ]
            );
        }
        return $circulator;
    }
}
