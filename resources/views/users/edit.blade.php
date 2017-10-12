@extends('layouts.app')

@section('content')
<div class="col-md-12" style="padding-bottom: 40px;">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="panel panel-default">
        <div class="panel-heading">Edit User</div>

        <div class="panel-body">
            {{ Form::model($user, ['url' => ['users/' . $user->id]]) }}
            {{ method_field('PUT') }}
            {{ Form::hidden('user_id', $user->id )}}
            <div class="form-group col-xs-12">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name',$user->name,['class'=>'form-control', 'tabindex' => '1']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email',$user->email,['class'=>'form-control', 'tabindex' => '2']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('password', 'Password') }}
                {{ Form::password('password',['class'=>'form-control', 'tabindex' => '3','placeholder' => 'Leave blank unless you want to change the password']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('password_confirmation', 'Confirm Password') }}
                {{ Form::password('password_confirmation',['class'=>'form-control', 'tabindex' => '3','placeholder' => 'Leave blank unless you want to change the password']) }}
            </div>
@if(Auth::user()->admin)
            <div class="form-group col-xs-12">
                {{ Form::checkbox('admin','1',$user->admin,['tabindex' => '4']) }} 
                {{ Form::label('admin', 'ADMIN USER') }}
            </div>
@endif
            <div class="form-group col-xs-12">
                {{ Form::checkbox('active','1',$user->active,['tabindex' => '4']) }} 
                {{ Form::label('active', 'User is currently Active') }}
            </div>
            {{ Form::submit('Create', ['class' => 'btn btn-primary']) }}

            {{ Form::close() }}
            {{ Form::close() }}
        </div>
    </div>
</div>
</div>
@endsection