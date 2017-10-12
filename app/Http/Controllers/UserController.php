<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\User;
use Auth;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        if(!Auth::user()->admin)
            return redirect('/')->withErrors(['Uh, oh. Sorry, you do not have permission to manage users.']);
        $data['users'] = User::all();
        return view('users.list', $data);
    }

    public function create()
    {
        if(!Auth::user()->admin)
            return redirect('/')->withErrors(['Uh, oh. Sorry, you do not have permission to manage users.']);
        return view('users.create');
    }

    public function store(StoreUser $request)
    {
        $data = $request->all();
        unset($data['password_confirmation']);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        return redirect('/users');
    }

    public function show($id)
    {
        if(!Auth::user()->admin && $id != Auth::user()->id)
            return redirect('/')->withErrors(['Uh, oh. Sorry, you do not have permission to manage users.']);

    	$data['user'] = User::findOrFail($id);
    	return view('users.edit',$data);
    }

    public function update(UpdateUser $request, $id)
    {
        if(!Auth::user()->admin && $id != Auth::user()->id)
            return redirect('/')->withErrors(['Uh, oh. Sorry, you do not have permission to manage users.']);

        $user = User::find($id);

        if(!$user)
            return redirect('/')->withErrors(['Uh, oh. We were unable to find the specified user.']);

        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $request->name;
        $user->admin = (Auth::user()->admin) ? $request->admin : false;
        $user->active = $request->active;

        if($request->password){
            $user->password = bcrypt($request->password);
        }

        if($user->save()) {
            if(Auth::user()->admin){
                return redirect('/users');
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/')->withErrors(['Sorry, there was an error updating this user account.']);
        }
    }
}
