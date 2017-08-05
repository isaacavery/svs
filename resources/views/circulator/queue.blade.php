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
    <div id="messages">
    </div>
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
                        <a href="#" class="btn btn-default pull-right" id="comment_update_btn">Add Note</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>Sheet Type</h3>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" id="type" value="0" checked>
                        Multi-line (5 or 10 lines)
                    </label>
                </div>
                <div class="radio selected">
                    <label>
                        <input type="radio" name="type" id="type" value="1">
                        Single signer
                    </label>
                </div>
                <h3>Number of Signatures</h3>
                <div class="btn-group selected" id="signature-count-group" role="group">
                @for($i=1; $i<11;$i++)
                    <button type="button" class="btn {{ ($sheet->signature_count == $i) ? 'btn-primary' : 'btn-default' }}">{{ $i }}</button>
                @endfor
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

        // Listen for update to comment
        $('#comment_update_btn').click(function(e){
            console.log('Updating comment ...');
            var comment = $('#comment').val();
            // Submit comment to the AJAX function
            ajaxUpdate('comment',comment);
        });

        // Listen for update to Self Signer status
        $('input[name="type"]').change(function(e){
            console.log('Updated Type:');
            var self_signed = $(e.currentTarget).val();
            // Submit self_signed (bool) to AJAX function
            ajaxUpdate('self_signed',self_signed);
        });

        // Listen for update on Signature Count
        $('#signature-count-group button').click(function(e){
            var tgt = $(e.currentTarget);
            if(tgt.hasClass('btn-primary')) {
                // Take no action because count is not changing
            } else {
                $('#signature-count-group button').removeClass('btn-primary').addClass('btn-default');
                tgt.addClass('btn-primary');
                var val = tgt.html();
                // Submit val to the AJAX function
                ajaxUpdate('signature_count',val);
            }
        })

        // Submit AJAX update request
        function ajaxUpdate(type,val){
            var data = {'_token': $('input[name="_token"').val()};
            data[type] = val;
            $.ajax('/sheets/{{ $sheet->id }}', {
                'data': data,
                'dataType': 'json',
                'success': function(res, status, jqXHR){
                    // Deal with response
                    console.log(res);
                    if(res.success){
                        var msg = "Success: " + res.message;
                        $('#messages').append('<div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>');
                    } else {
                        var err = "Error: " + res.error;
                        $('#messages').append('<div class="alert alert-danger alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + err + '</div>');
                    }
                },
                'error': function(xhr){
                    console.log("ERROR");
                    console.log(errors);
                },
                'method': 'PUT'
            });
        }
    });
</script>
</div>
@endsection