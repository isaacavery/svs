<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCirculator;
use App\Sheet;
use App\Circulator;
use App\Voter;
use App\User;
use App\Signer;
use Auth;
use Exception;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['circulator_ready'] = Sheet::whereNull('circulator_id')->whereNull('flagged_by')->count();
        $data['circulator_count'] = Sheet::whereNotNull('circulator_id')->count();
        $data['circulator_unique'] = Circulator::count();
        $data['signature_count'] = DB::table('sheets')->select(DB::raw('sum(signature_count) as count'))->first();
        $data['signers_added'] = Signer::count();
        $data['signers_ready'] = DB::table('sheets')->select(DB::raw('sum(signature_count) as count'))->whereNull('signatures_completed_by')->whereNull('flagged_by')->where('self_signed',false)->first();
        $data['user_data'] = array();
        foreach (User::all() as $user) {
            $data['user_data'][] = array(
                'name' => $user->name,
                'signers' => DB::table('signers')
                    ->join('sheets','sheets.id','=','signers.sheet_id')
                    ->whereNotNull('signers.voter_id')->where('signers.voter_id','>',0)
                    ->where('sheets.self_signed',0)
                    ->select('signers.id')
                    ->where('signers.user_id', $user->id)
                    ->count(),
                'circulators' => $user->circulators()->count()
            );
        }
        usort($data['user_data'], function($a, $b) {
            return $b['circulators'] - $a['circulators'];
        });
        return view('home', $data);
    }
    public function searchSigned(Request $request)
    {
        $response_code = 200;
        $response = [];
        if (!$request->auth) {
            $response_code = 401;
            $response = ['success' => false, 'error' => 'No authentication provided. Please include your API key.'];
        } else {
            // Validate API key
            $keys = json_decode($_ENV['API_CLIENT_KEYS']);
            if (!in_array($request->auth, $keys)) {
                $response_code = 401;
                $response = ['success' => false, 'error' => 'Invalid API key'];
            } else {
                if (!$request->firstName || !$request->lastName) {
                    $response = ['success' => false, 'error' => 'firstName and lastName parameters are required.'];
                } else {
                    // Validate input
                    $results = DB::table('voters')
                        ->where('first_name', 'like', strtoupper($request->firstName) . '%')
                        ->where('last_name', strtoupper($request->lastName));
                    if ($request->zipCode) {
                        $results->where('zip_code', $request->zipCode);
                    }
                    $results = $results->limit(10)
                        ->get();
                    $response['success'] = true;
                    $res_arr = [];
                    foreach ($results as $res) {
                        $signed = Signer::where('voter_id', $res->voter_id)->count();
                        $res_arr[] = [
                            'name' => $res->first_name . ' ' 
                            . (($res->middle_name) ? $res->middle_name . ' ' : '')
                                . $res->last_name,
                            'address' => $res->eff_address_1 . ' ' 
                            . (($res->eff_address_2) ? $res->eff_address_2 . ', ' : '')
                                . $res->eff_city . ', '
                                . $res->eff_state . ' '
                                . $res->eff_zip_code,
                            'signed' => ($signed > 0) ? 1 : 0
                        ];
                    }
                    $response['results'] = $res_arr;
                }
            }
        }
        return response(json_encode($response), $response_code)
            ->header('Content-Type', 'text/json');

    }
}
