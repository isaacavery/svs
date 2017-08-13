@extends('layouts.app')

@section('content')
<div class="col-md-12" style="padding-bottom: 40px;">
    <div id="messages">
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Edit User</div>

        <div class="panel-body">
        {{ dd($user->toArray()) }}
            {{ Form::model($user, ['route' => ['users.update', $user]]) }}
            {{ Form::close() }}
        </div>
    </div>
</div>
</div>
@endsection