@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Upload Sheets</div>

                <div class="panel-body">
                    {{ Form::open(['route' => 'sheets.store', 'enctype' => 'multipart/form-data']) }}
                    <div class="col-xs-12 col-md-6 col-md-offset-3">
                        <div class="form-group">
                            {{ Form::label('batch','Select Batch (leave blank to create new batch)') }}
                            {{ Form::select('batch',$batches,[],['class'=>'form-control']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('sheets[]', 'Select Sheets to Upload')}}
                            {{ Form::file('sheets[]',['multiple' => 'multiple']) }}
                        </div>
                        {{ Form::submit('Upload', ['class' => 'btn btn-primary btn-block']) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection