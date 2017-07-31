@extends('layouts.app')

@section('content')
<style type="text/css">
    #bottom-bar {
        display: block;
        background: #ccc;
        position: fixed;
        bottom: 0;
    }
</style>
<div class="col-md-12" style="padding-bottom: 40px;">
    <div class="panel panel-default">
        <div class="panel-heading">Circulator Queue</div>

        <div class="panel-body">
            {{ Form::open(['route' => 'sheets.store', 'enctype' => 'multipart/form-data']) }}
            <div class="col-xs-12 col-md-6">
                <img src="/uploads/{{ $sheet->filename }}" width="100%">
                <div class="col-xs-6">
                    <h4>Sheet Info</h4>
                    <p><strong>Sheet ID:</strong> <span id="sheet_id">{{ $sheet->id }}</span></p>
                    <p><strong>File name:</strong> <span id="filename">{{ $sheet->original_filename }}</span></p>
                </div>
                <div class="col-xs-6">
                    <h4>Problem with this sheet?</h4>
                    <div class="form-group">
                        {{ Form::textarea('comment',$sheet->comments,['placeholder'=>'Describe the problem...', 'style' => 'width: 100%;','rows'=>3, 'id' => 'comment']) }}
                        <a href="#" class="btn btn-default pull-right updateBtn" data-field="comment">Add Note</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>Sheet Type</h3>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="type" value="multiline" checked>
                        Multi-line (5 or 10 lines)
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="type" value="single">
                        Single signer
                    </label>
                </div>
                <h3>Number of Signatures</h3>
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default">1</button>
                    <button type="button" class="btn btn-default">2</button>
                    <button type="button" class="btn btn-default">3</button>
                    <button type="button" class="btn btn-default">4</button>
                    <button type="button" class="btn btn-default">5</button>
                    <button type="button" class="btn btn-default">6</button>
                    <button type="button" class="btn btn-default">7</button>
                    <button type="button" class="btn btn-default">8</button>
                    <button type="button" class="btn btn-default">9</button>
                    <button type="button" class="btn btn-default">10</button>
                </div>
                <h3>Circulator Date</h3>
                {{ Form::date('name', \Carbon\Carbon::now()) }}
                <h3>Circulator Name</h3>
                <div class="row">
                    <div class="form-group col-xs-6">
                        {{ Form::label('first', 'First Name') }}
                        {{ Form::text('first','',['class'=>'form-control']) }}
                    </div>
                    <div class="form-group col-xs-6">
                        {{ Form::label('last', 'Last Name') }}
                        {{ Form::text('last','',['class'=>'form-control']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('street_name', 'Street Name') }}
                        {{ Form::text('street_name','',['class'=>'form-control']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('number', 'Street Number') }}
                        {{ Form::text('number','',['class'=>'form-control']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('city', 'City') }}
                        {{ Form::text('city','',['class'=>'form-control']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('zip', 'Zip') }}
                        {{ Form::text('zip','',['class'=>'form-control']) }}
                    </div>
                </div>
                <a href="#" class="col-xs-4 pull-right btn btn-primary">Search</a>
                <div class="clearfix"></div>
                <hr />
                <div class="row clearfix">
                    <div class="col-xs-6">
                        <h4>Search Results</h4>
                        <p>@todo: get search results</p>
                    </div>
                    <div class="col-xs-6">
                        <h4>Recent Circulators</h4>
                        <p>@todo: place recent circulators here</p>
                    </div>
                </div>
                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addCirculator">No Match - Create New Record</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div id="bottom-bar" style="background: #eee; position: fixed; bottom: 0; width: 100%; padding: 12px 0;">
    <div class="col-xs-12">
        <a href="#" class="btn btn-primary">Exit</a>
        <a href="#" class="btn btn-default pull-right" disabled="disabled">Finish &amp; Get Next Sheet ></a>
        <a href="#" class="btn btn-primary pull-right">Flag Sheet &amp; Skip</a>
    </div>
</div>
<div class="modal fade" id="addCirculator" tabindex="-1" role="dialog" aria-labelledby="addCirculatorLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Circulator</h4>
            </div>
            {{ Form::open(['url' => 'circulator/add', 'method' => 'post', 'id' => 'addCirculatorForm']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-6">
                        {{ Form::label('first_name', 'First Name', ['class' => 'control-label']) }}
                        {{ Form::text('first_name','',['class'=>'form-control', 'id' => 'first']) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-6">
                        {{ Form::label('last_name', 'Last Name', ['class' => 'control-label']) }}
                        {{ Form::text('last_name','',['class'=>'form-control', 'id' => 'last']) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('street_number', 'Street Number', ['class' => 'control-label']) }}
                        {{ Form::text('street_number','',['class'=>'form-control', 'id' => 'number']) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('street_name', 'Street Name', ['class' => 'control-label']) }}
                        {{ Form::text('street_name','',['class'=>'form-control', 'id' => 'street_name']) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('city', 'City', ['class' => 'control-label']) }}
                        {{ Form::text('city','',['class'=>'form-control', 'id' => 'city']) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('zip', 'Zip', ['class' => 'control-label']) }}
                        {{ Form::text('zip','',['class'=>'form-control', 'id' => 'zip']) }}
                        <span class="help-block hidden"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
<script type="text/javascript">
    $('document').ready(function(){
        $('#addCirculatorForm').on('submit',function(e){
            e.preventDefault();
            var form = $(e.currentTarget);
            var data = form.serialize();
            form.find('input').closest('.form-group').removeClass('has-error').find('.help-block').html('').addClass('hidden');
            $.post('/circulators/add',form.serialize(), function(res, status, jqXHR){
                // Deal with response
                console.log(res);
            }).fail(function(xhr){
                if(xhr.status == 422){
                    // Add validation handling
                    var errors = xhr.responseJSON;
                    $.each(errors,function(k,v){
                        $('input#' + k).closest('.form-group').addClass('has-error').find('.help-block').html(v).removeClass('hidden');
                    });
                } else {
                    // Unknown error
                    alert('Unknown ' + xhr.status + ' error: ' + xhr.responseText);
                }
            });
        });
        $('.updateBtn').click(function(e){
            var field = $(e.currentTarget).data('field');
            var data = {'_token': $('input[name="_token"').val()};
            switch(field){
                case 'comment' :
                    data.comments = $('#comment').val()};
                    break;
                case 'signature_count' :
                    data.signature_count = 1;
                    break;
                case 'self_signed' :
                    data.self_signed = 1;
                    break;
                default:
                    alert('no match');

            }

            $.ajax('/sheets/{{ $sheet->id }}', {
                'data': data,
                'success': function(res, status, jqXHR){
                 // Deal with response
                    alert(res);
                },
                'error': function(xhr){
                    alert(errors);
                },
                'method': 'PUT'
            });
        });
    });
</script>
</div>
@endsection