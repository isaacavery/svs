<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Batch;
use App\Sheet;
use Input;

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
    public function index()
    {
        $data['sheets'] = Sheet::all();
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
        foreach($batches as $batch) {
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
            $sheet_data = ['md5_hash' => $md5_hash, 'original_filename' => $original_name, 'filename' => $filename, 'user_id' => $request->user()->id, 'batch_id' => $batch->id];
            $new_sheet = Sheet::create($sheet_data);
            $result_data['success'][] = "Added $original_name as <a href='/sheets/" . $new_sheet->id . "'>Sheet # " . $new_sheet->id . "</a>";
        }
        return view('sheets.uploaded', $result_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
}