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
        <div class="panel-heading">Add User</div>

        <div class="panel-body">
        	{{ Form::open(['route' => 'users.store'])}}
            <div class="form-group col-xs-12">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name','',['class'=>'form-control', 'tabindex' => '1']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('email', 'Email') }}
                {{ Form::text('email','',['class'=>'form-control', 'tabindex' => '2']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('password', 'Password') }}
                {{ Form::password('password',['class'=>'form-control', 'tabindex' => '3']) }}
            </div>
            <div class="form-group col-xs-12">
                {{ Form::label('password_confirmation', 'Confirm Password') }}
                {{ Form::password('password_confirmation',['class'=>'form-control', 'tabindex' => '3']) }}
            </div>

            <div class="form-group col-xs-12">
                {{ Form::checkbox('admin','yes',0,['tabindex' => '4']) }} 
                {{ Form::label('admin', 'Admin') }}
            </div>
            {{ Form::submit('Create', ['class' => 'btn btn-primary']) }}

            {{ Form::close() }}
        </div>
    </div>
</div>
</div>
@endsection