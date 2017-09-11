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

    public function update(Request $request)
    {
        dd($request);
        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'admin' => $request->admin,
            'active' => $request->active
        ];
        if($request->password)
            $data['password'] = $request->password;

        dd($data);
    }
}
