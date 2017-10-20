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
                'signers' => $user->signers()->count(),
                'circulators' => $user->circulators()->count()
            );
        }
        usort($data['user_data'], function($a, $b) {
            return $b['circulators'] - $a['circulators'];
        });
        return view('home', $data);
    }
}
