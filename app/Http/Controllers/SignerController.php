<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sheet;
use App\Voter;
use App\Signer;
use Auth;

class SignerController extends Controller
{
    /**
     * Require user authentication
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $error = false;
        $data = [
            'sheet_id' => $request->sheet_id,
            'voter_id' => $request->voter_id,
            'row' => $request->row,
            'user_id' => Auth::user()->id
        ];

        // Handle the validatation
        if(!isset($request->row) || !$request->row || $request->row > 10 || $request->row < 1){
            $error = 'Invalid Row ID';
        } else if(!Sheet::find($request->sheet_id)){
            $error = 'Invalid Sheet ID "' . $request->sheet_id . '"';
        } else if($request->voter_id != null && $request->voter_id != 0 && !Voter::where('voter_id',$request->voter_id)->count()) {
            $error = 'Invalid voter ID: ' . $request->voter_id;
        }
        if($error)
            return json_encode(['success' => false, 'error' => $error]);

        // Check for existing row value
        $row = Signer::where('sheet_id', $request->sheet_id)->where('row',$request->row)->first();
        $message = '';
        if(!$row) {
            // Create new signer
            $row = Signer::create($data);
            $message = 'Created new Signer for ROW ' . $request->row;
        } else {
            // Update the existing signer
            $old_id = $row->voter_id;
            $row->voter_id = $request->voter_id;
            $row->save();
            $message = 'Replaced ' . $old_id . ' with ' . $row->voter_id . ' for row ' . $request->row;
        }

        // Write the new voter to the database

        return json_encode(['success' => true, 'message' => $message ]);
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
