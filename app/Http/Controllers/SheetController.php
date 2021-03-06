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
use App\Batch;
use Exception;

class SheetController extends Controller
{
    /**
     * Require authentication to access
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check Sorting
        $order_by = ' ORDER BY id';
        $order = ($request->order == 'desc') ? 'DESC' : 'ASC';
        $data['order'] = strtolower($order);
        $data['sort'] = $request->sort;
        if ($request->sort) {
            switch ($request->sort) {
            case 'id' :
                $order_by = " ORDER BY id $order";
                break;
            case 'type' :
                $order_by = " ORDER BY self_signed $order, id ASC";
                break;
            case 'signers' :
                $order_by = " ORDER BY signature_count $order, id ASC";
                break;
            case 'no_match' :
                $order_by = " ORDER BY no_match $order, id ASC";
                break;
            case 'cir' :
                $order_by = " ORDER BY circulator_completed_by $order, flagged_by $order, id ASC";
                break;
            case 'sig' :
                $order_by = " ORDER BY signatures_completed_by $order, flagged_by $order, id ASC";
                break;
            case 'rev' :
                $order_by = " ORDER BY reviewed_by $order, id ASC";
                break;
            }
        }
        $data['sheets'] = DB::select('SELECT s.id, s.circulator_id, s.filename, s.original_filename, flagged_by, signatures_completed_by, circulator_completed_by, reviewed_by, date_signed, signature_count, self_signed, s.user_id, s.comments, s.created_at, s.updated_at, nm.no_match  FROM sheets s LEFT JOIN (SELECT count(id) no_match, sheet_id FROM signers WHERE voter_id = 0 GROUP BY sheet_id) nm ON (s.id = nm.sheet_id)' . $order_by);
        //dd($data);
        return view('sheets.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = ['batches' => ['0' => 'New Batch']];
        $batches = Batch::all();
        foreach ($batches as $batch) {
            $data['batches'][$batch->id] = '[ ' . $batch->id . ' ] - ' . $batch->created_at; 
        }
        return view('sheets.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        if(!$request->sheets)
            return back()->with('error','Please select one or more sheets to upload.');
        $result_data = ['error' => [], 'success' => []];
        // Find or create specified Batch
        if($request->batch) {
            $batch = Batch::find($request->batch);
        } else {
            $batch = Batch::create();
        }

        // Get all of the assets
        $sheet_array = [];
        $sheets = $request->sheets;
        //$sheets = Input::file('sheets');
        foreach ($sheets as $k => $sheet) {
            $md5_hash = md5_file($sheet); // Get hash to check for duplicate files
            $original_name = $sheet->getClientOriginalName(); // Get the uploaded filename
            if($match = Sheet::where('md5_hash',$md5_hash)->first()) {
                $result_data['error'][] = "Skipping $original_name: Sheet appears to be a duplicate of <a href='/sheets/" . $match->id . "'>Sheet # " . $match->id . "</a>";
                continue;
            }
            $filename = Storage::disk('uploads')->put('sheets',$sheet);
            $sheet_data = ['md5_hash' => $md5_hash, 'original_filename' => $original_name, 'filename' => $filename, 'user_id' => $request->user()->id, 'batch_id' => $batch->id, 'user_id' => Auth::user()->id];
            $new_sheet = Sheet::create($sheet_data);
            $result_data['success'][] = "Added $original_name as <a href='/sheets/" . $new_sheet->id . "'>Sheet # " . $new_sheet->id . "</a>";
        }
        return view('sheets.uploaded', $result_data);
    }

 public function queue() {
    	$data['sheet'] = Sheet::whereNull('flagged_by')->whereNull('signatures_completed_by')->whereNotNull('circulator_completed_by')
            ->where(function ($query) {
                $query->where('checked_out', '<', date("Y-m-d H:i:s",time() - 30 * 60))
                ->orWhereNull('checked_out');
            })->with('signers')->first();
    	if(!$data['sheet'])
            return redirect('/')->withErrors(['empty' => 'Hmmmm ... it appears that there are no sheets in the Signer Queue for review.']);

        $data['voters'] = array();
        // Get associated voters:
        foreach ($data['sheet']->signers as $signer) {
            if($signer->voter_id){
                $data['voters'][$signer->row] = Voter::select(['first_name','middle_name','last_name','voter_id','res_address_1','city','zip_code'])->where('voter_id',$signer->voter_id)->first();
            } else {
                $data['voters'][$signer->row] = is_null($signer->voter_id) ? 1 : 0;
            }
        }

        // Parse comments
        $data['comments'] = explode('|',$data['sheet']->comments);
        foreach($data['comments'] as $k => $v){
            // Remove empty comments
            if(!$v)
                unset($data['comments'][$k]);
        }

        // Check out sheet
        $data['sheet']->checked_out = date("Y-m-d H:i:s");
        $data['sheet']->save();
        
    	return view('sheets.queue',$data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['sheet'] = Sheet::find($id);
        if(!$data['sheet'])
        return redirect('/')->withErrors(['empty' => 'Unable to locate sheet ' . $id . '.']);

        $data['voters'] = array();
        // Get associated voters:
        foreach ($data['sheet']->signers as $signer) {
            if ($signer->voter_id) {
                $data['voters'][$signer->row] = Voter::select(['first_name','middle_name','last_name','voter_id','res_address_1','city','zip_code'])->where('voter_id',$signer->voter_id)->first();
            } else {
                $data['voters'][$signer->row] = is_null($signer->voter_id) ? 1 : 0;
            }
        }

        // Parse comments
        $data['comments'] = explode('|',$data['sheet']->comments);
        foreach($data['comments'] as $k => $v){
            // Remove empty comments
            if(!$v)
                unset($data['comments'][$k]);
        }

        // Check out sheet
        $data['sheet']->checked_out = date("Y-m-d H:i:s");
        $data['sheet']->save();
        
        return view('sheets.edit',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // @todo: handle AJAX update requests
        $sheet = Sheet::find($id);
        $values = $request->all();
        if(isset($values['_token']))
            unset($values['_token']);
        foreach ($values as $key => $val) {
            if ($key == 'self_signed') {
                if ($val == '1' && !$sheet->self_signed) {
                    // Remove all Signers and add a new Signer as the Circulator, if they are set
                    if ($sheet->signers()->count()) {
                        $sheet->signers()->delete();
                    }
                    if ($sheet->circulator_id) {
                        $circulator = $sheet->circulator;
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
                } else if ($val == '0' && $sheet->self_signed && $sheet->signatures_completed_by) {
                    // Remove all Signers that are the same as the Circulator
                    $sheet->signers()->delete();
                    $sheet->signatures_completed_by = null;
                }
            } 
            $sheet->{$key} = ($key == 'comments') ? $sheet->{$key} . '|[' . Auth::user()->name . ' on ' . date('m/d/Y h:i:s') . '] ' . $val : $val;
        }
        if($sheet->save()){
            return json_encode(['success' => 'true', 'message' => 'Successfully updated ' . implode(', ', array_keys($values)) . ' for Sheet #' . $id]);
        } else {
            return json_encode(['success' => false, 'error' => 'Unknown error saving changes']);
        }

        //return $sheet->filename;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkCompletion($id)
    {
        $sheet = Sheet::find($id);
        if(!$sheet)
            return json_encode(['success' => 0, 'error' => 'Unable to find the requested sheet']);

        $complete = ($sheet->signers()->count() == $sheet->signature_count) ? true : false;
        return json_encode(['success' => true, 'complete' => $complete]);
    }

}
