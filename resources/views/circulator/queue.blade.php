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
         {{--  <div class="panel-heading">Circulator Queue</div>   --}}

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
                    <h4>Comments or problem with this sheet:</h4>
                    <ul id="comments">
                    @foreach($comments as $comment)
                        <li class="text-primary">{{ $comment }}</li>
                    @endforeach
                    </ul>
                    <div class="form-group">
                        {{ Form::textarea('comment','',['placeholder'=>'Describe the problem...', 'style' => 'width: 100%;','rows'=>3, 'id' => 'comment']) }}
                        <a href="#" class="btn btn-default pull-right" id="comment_update_btn">Add Note</a>
                    </div>
                </div>
                <div class="col-xs-12">
                    <h4>Recent Circulators</h4>
                    <p>@todo: place recent circulators here</p>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>Sheet Type</h3>
                <div class="radio-inline">
                    <label>
                        <input type="radio" name="type" id="type" value="0"{{ ($sheet->self_signed) ? '' : ' checked="checked"' }}>
                        Multi-line (5 or 10 lines)
                    </label>
                </div>
                <div class="radio-inline">
                    <label>
                        <input type="radio" name="type" id="type" value="1"{{ ($sheet->self_signed) ? 'checked="checked"' : '' }}>
                        Single signer
                    </label>
                </div>
                <div class="numOfSignatures{{ ($sheet->self_signed) ? ' hidden' : '' }}">                  
                    <h3>Number of Signatures</h3>
                    <div class="btn-group selected" id="signature-count-group" role="group">
                    @for($i=1; $i<11;$i++)
                        <button type="button" class="btn {{ ($sheet->signature_count == $i) ? 'btn-primary' : 'btn-default' }}">{{ $i }}</button>
                    @endfor
                    </div>
                </div>
                <h3>Circulator Date</h3>
                {{ Form::date('name', \Carbon\Carbon::now()) }}
                <h3>Circulator</h3>
                <div id="voter-match" data-selected="0"></div><a id="remove-circulator-btn" href="#" class="btn btn-danger">Remove Circulator</a>
                <div id="voter-search">
                <div class="col-xs-12">
                    <div class="radio-inline" style="padding:10px">
                        <label>
                            <input type="radio" name="exact_match" id="exact_match" value="1" checked="checked">
                            Exact Match
                        </label>
                    </div>
                    <div class="radio-inline" style="padding:10px">
                        <label>
                            <input type="radio" name="exact_match" id="exact_match" value="0">
                            Loose Search 
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-6">
                        {{ Form::label('first', 'First Name') }}
                        {{ Form::text('first','',['class'=>'form-control', 'tabindex' => '1', 'autofocus' => 'true']) }}
                    </div>
                    <div class="form-group col-xs-6">
                        {{ Form::label('last', 'Last Name') }}
                        {{ Form::text('last','',['class'=>'form-control', 'tabindex' => '2']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('street_name', 'Street Name') }}
                        {{ Form::text('street_name','',['class'=>'form-control', 'tabindex' => '3']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('number', 'Street Number') }}
                        {{ Form::text('number','',['class'=>'form-control', 'tabindex' => '4']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('city', 'City') }}
                        {{ Form::text('city','',['class'=>'form-control', 'tabindex' => '5']) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('zip', 'Zip') }}
                        {{ Form::text('zip','',['class'=>'form-control', 'tabindex' => '6']) }}
                    </div>
                </div>
                <a href="#" class="col-xs-4 pull-right btn btn-primary" id="search_submit_btn" tabindex="7">Search</a>
                <div class="clearfix"></div>
                <hr />
                <div class="row clearfix">
                    <div class="col-xs-12">
                        <h4>Search Results</h4>
                        <table class="table table-striped table-condensed table-hover">
                            <thead>
                                <tr>
                                    <td>NAME</td>
                                    <td>ADDRESS</td>
                                    <td>ALT ADDRESS</td>
                                </tr>
                            </thead>
                            <tbody id="search-results">
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addCirculator">No Match - Create New Record</button>
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div id="bottom-bar" style="background: #eee; position: fixed; bottom: 0; width: 100%; padding: 12px 0;">
    <div class="col-xs-12">
        <a href="#" class="btn btn-primary">Exit</a>
        <a href="#" class="btn btn-default pull-right" id="finish-sheet" disabled ='true'>Finish &amp; Get Next Sheet ></a>
        <a href="#" class="btn btn-primary pull-right" id="flagBtn">Flag Sheet &amp; Skip</a>
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
    var searchResults;
    $('document').ready(function(){
        $('#addCirculatorForm').on('submit',function(e){
            e.preventDefault();
            var form = $(e.currentTarget);
            var data = form.serialize();
            form.find('input').closest('.form-group').removeClass('has-error').find('.help-block').html('').addClass('hidden');
            $.post('/circulators/add',form.serialize(), function(res, status, jqXHR){
                // Deal with response
                console.log(res);
                if(res.success){
                    $('ul#comments').append('<li class="text-success">' + res.message + '</li>')
                } else {
                    $('ul#comments').append('<li class="text-danger">' + res.message + '</li>')
                }
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
            ajaxUpdate('sheets','comments',comment);
        });

        // Listen for update to Self Signer status
        $('input[name="type"]').change(function(e){
            console.log('Updated Type:');
            var self_signed = $(e.currentTarget).val();
            // If only one signer disallow hide multiple signature option
            if (self_signed ==1) {
                $('.numOfSignatures').addClass('hidden');
                ajaxUpdate('sheets','signature_count',1);
                $('#signature-count-group button').removeClass('btn-primary').addClass('btn-default').first().removeClass('btn-default').addClass('btn-primary');
            } else {
                $('.numOfSignatures').removeClass('hidden').show();
            }
            // Submit self_signed (bool) to AJAX function
            ajaxUpdate('sheets','self_signed',self_signed);
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
                ajaxUpdate('sheets','signature_count',val);
            }
        });



        // Search for signer
        $('#search_submit_btn').click(function(e){
            e.preventDefault();
            // Submit Circulator search
            $('#search-results').html('<tr><td colspan="3" class="text-primary">Searching, please wait ...</td></tr>');
            var data = {
                exact_match: 1,
                first: $('#first').val(),
                last: $('#last').val(),
                street_name: $('#street_name').val(),
                number: $('#number').val(),
                city: $('#city').val(),
                zip: $('#zip').val(),
                _token: $('input[name="_token"').val()
            };
            if ($('input#exact_match[value="0"]').is(':checked')) {
                data['exact_match'] = 0;
            }

            $.post('/circulators/search',data, function(res, status, jqXHR){
                // Deal with response
                if(res.success){
                    if(!res.count) {
                        // Clear the search results
                        searchResults = {};
                        $('#search-results').html('<tr><td colspan="3" class="text-danger">No matches found!</td></tr>');
                    } else {
                        // Update the global search results
                        searchResults = {};
                        for (var i = res.matches.length - 1; i >= 0; i--) {
                            searchResults[res.matches[i].voter_id] = res.matches[i];
                        }
                        var html = '';
                        $.each(res.matches, function(i,v){
                            html += '<tr class="match" data-voter-id="' + v.voter_id + '"><td>'
                                + v.first_name + ' ';
                            if(v.middle_name)
                                html += v.middle_name + ' ';
                            html += v.last_name + '</td><td>'
                                + v.res_address_1 + ' ' + v.city + ' ' + v.zip_code + '</td><td>';
                            html += (v.res_address_1 == v.eff_address_1) ? '</td>' : v.eff_address_1 + '<td>';
                        });
                        $('#search-results').html(html);
                    }
                } else {
                    $('#search-results').html('<tr><td colspan="3" class="text-danger">Error: ' + res.error + '</td></tr>');
                }
            }, 'json').fail(function(xhr){
                if(xhr.status == 422){
                    // Add validation handling
                    var errors = xhr.responseJSON;
                    $.each(errors,function(k,v){
                        $('#search-results').html('<tr><td colspan="3" class="text-danger">Error: ' + res.error + '</td></tr>');
                    });
                } else {
                    // Unknown error
                    $('#search-results').html('<tr><td colspan="3" class="text-danger">' + xhr.status + ' ERROR: ' + xhr.responseText + '</td></tr>');
                }
            });
        });
        $('#first,#last,#street_name,#number,#city,#zip').keypress(function (e) {
          if (e.which == 13) {
            $('#search_submit_btn').click();
          }
        });

        // Check for comment before flagging sheet if comment exists move to next sheet else require reason for flagging
        $('#flagBtn').click(function(e){
            if(!$('#comment').val()) {
                alert("Please put a reason for flagging in the comments.");
            }
            else {
                // Add a comment for flagging
                ajaxUpdate('sheets','comments',$('#comment').val());
                // Flag the sheet
                ajaxUpdate('sheets','flagged_by',{{ Auth::user()->id }});
                // Reload the page to retreive the next sheet in the queue
                location.reload(true);
            }
        });

        // Listen for finish sheet
        $('#bottom-bar').on('click', '#finish-sheet', function(e){
          location.reload();
          ajaxUpdate('sheets','circulator',)
        });

        // Submit AJAX update request
        function ajaxUpdate(resource,type,val){
            var data = {'_token': $('input[name="_token"').val()};
            data[type] = val;
            var callbackData = {type: type, val: val};
            $.ajax('/' + resource + '/{{ $sheet->id }}', {
                'data': data,
                'context': callbackData,
                'dataType': 'json',
                'success': function(res, status, jqXHR,){
                    // Deal with response
                    if(res.success){
                        console.log(callbackData);
                        var msg = "Success: " + res.message;
                        $('#messages').append('<div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>');
                        if(callbackData.type == 'comments'){
                            // Add new comment to the display
                            $('ul#comments').append('<li class="text-success">' + callbackData.val + '</li>');
                            $('textarea#comment').val('');
                        }
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

        // Assign selected voter
        $("#search-results").on('click','tr.match',function(e){
            var voterId = $(e.currentTarget).data('voter-id');
            var voter = searchResults[voterId]; // Set 
            var html = '<p class="text-muted"><strong class="text-primary">'
                + voter.first_name + ' ' + voter.middle_name + ' ' + voter.last_name + '</strong><br />'
                + voter.res_address_1 + ', ' + voter.city + ', OR ' + voter.zip_code;
            $('#voter-match').attr('data-selected',voterId).html(html).show();
            $('#remove-circulator-btn').show();
            $('#voter-search').hide();
            $('#finish-sheet').attr('disabled',false);
        });
        $('#remove-circulator-btn').click(function(e){
            $('#voter-match').attr('data-selected','0').html('').hide();
            $('#remove-circulator-btn').hide();
            $('#voter-search').show();
            $('#finish-sheet').attr('disabled',true);
        });

        $('input[name="first"]').focus();


    });
    // Remove AJAX feedback notices
    setInterval(function(){
        if($('#messages .alert').length){
            console.log('found some');
            $('#messages .alert').delay(1000).fadeOut(400,function(){
                $(this).remove();
            });
        }
    },3000);
</script>
</div>
@endsection