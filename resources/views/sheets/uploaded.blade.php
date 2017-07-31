@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Upload Sheets</div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        @foreach($error as $error)
                        <div class="alert alert-danger" role="alert">{!! $error !!}</div>
                        @endforeach
                        @foreach($success as $msg)
                        <div class="alert alert-success" role="alert">{!! $msg !!}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection