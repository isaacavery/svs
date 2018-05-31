<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Signer;

class ApiController extends Controller
{
    
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

                    // Custom exclusions:
                    $results->whereNotIn('voter_id',[17348977,18550976,100654958,100672316,100996473]);

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
