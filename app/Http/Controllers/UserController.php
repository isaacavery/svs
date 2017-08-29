<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Auth;
use Exception;

class UserController extends Controller
{
    public function show($id)
    {
    	if(Auth::user()->id == $id && Auth::user()->admin)
    		//return back()->withError('Sorry, you do not have access to manage this user.');
    	$data['user'] = User::findOrFail($id);
    	return view('users.edit',$data);
    }

    public function update(Request $request)
    {
    	dd($request);
    }
}
