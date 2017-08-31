@extends('layouts.app')

@section('content')
<div class="col-md-12" style="padding-bottom: 40px;">
    <div id="messages">
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Manage Users <span class="pull-right"><a href="/users/create" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-plus"></span> Add Another</a></span></div>

        <div class="panel-body">
        <table class="table table-striped table-condensed">
        	<thead>
        		<tr>
        			<th>Name</th>
        			<th>Email</th>
        			<th>Added</th>
        			<th>Circulators</th>
        			<th>Signers</th>
        		</tr>
        	</thead>
        	<tbody>
        	@foreach($users as $user)
        		<tr>
        			<td><a href="/users/{{ $user->id }}">{{ $user->name }}</a></td>
        			<td>{{ $user->email }}</td>
        			<td>{{ Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</td>
        			<td>{{ $user->circulators()->count() }}</td>
        			<td>{{ $user->signers()->count() }}</td>
        		</tr>
        	@endforeach
        	</tbody>
        </table>
        </div>
    </div>
</div>
</div>
@endsection