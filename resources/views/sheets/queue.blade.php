@extends('layouts.app')

@section('content')
<style type="text/css">
    #bottom-bar {
        display: block;
        background: #ccc;
        position: fixed;
        bottom: 0;
    }
    .noMargin h2#numOfSigners {
    margin-top:0px;
    }
</style>
<div class="col-md-12" style="padding-bottom: 40px; padding-left:0px; padding-right:0px;">
    <div id="messages">
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {{ Form::open(['route' => 'sheets.store', 'enctype' => 'multipart/form-data']) }}
            <div id="formDiv" class="col-xs-12 col-md-6">
                <img src="/uploads/{{ $sheet->filename }}" width="100%">
                <p><strong>Sheet ID:</strong> <span id="sheet_id">{{ $sheet->id }}</span> <strong>File name:</strong> <span id="filename">{{ $sheet->original_filename }}</span></p>
            </div>
            <div class="col-xs-12 col-md-6">
                <h2 class="noMargin" id = 'numOfSigners'>0 of {{$sheet->signature_count}} signers added</h2>
                <table class="table table-condensed" id="signer-match" data-selected="0">
                    <tbody>
                    @for($i=0; $i<$sheet->signature_count; $i++)
                        <tr class="signer">
                            @if(isset($voters[$i+1]))
                            <td><strong class="text-primary signer">{{ $voters[$i+1]->first_name }} {{$voters[$i+1]->middle_name }} {{$voters[$i+1]->last_name }}</strong></td><td>{{ $voters[$i+1]->res_address_1 }}, {{ $voters[$i+1]->city }}, OR {{ $voters[$i+1]->zip_code}}</td>
                            @else
                            <td></td><td></td>
                            @endif
                        </tr>
                    @endfor
                    </tbody>
                </table>
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
                    <div class = "col-xs-12">
                        <div class="radio-inline">  
                            <label>
                                <input type="radio" name="exact_match" id="exact_match" value="1" checked="checked">
                                Exact Match
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="exact_match" id="exact_match" value="0">
                                Loose Search 
                            </label>
                        </div>
                        <div class="pull-right">
                            <a href="#" class="btn btn-primary" id="search_submit_btn" tabindex="7" sytle="margin:10px">Search</a>
                        </div>
                    </div>
                </div>
                <div id="results-container" class="hidden">
                    <h3 id="searchHeader">Search Results</h3>
                    <div id="voter-search">
                        <table class="table table-condensed">
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
                        <a href="#" class="btn btn-primary pull-right" id="not_readable" tabindex="7">No Match</a>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
<div id="bottom-bar" style="background: #eee; position: fixed; bottom: 0; width: 100%; padding: 12px 0;">
    <div class="col-xs-12 btn-toolbar">
        <a href="#" class="btn btn-primary">Exit</a>
        <a href="#" id="finish-sheet" class="btn btn-default pull-right" {{ (count($voters) != $sheet->signature_count) ? 'disabled="disabled"' : '' }}>Finish &amp; Get Next Sheet ></a>
        <a href="#modalComment" class="btn btn-default pull-right" data-toggle="modal">Flag Sheet &amp; Skip</a>
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
      var signerCnt  = 0;
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
        // Listen for Flag Sheet Button
        $('#flagBtn').click(function(e){
            if(!$('#comment').val()) {
                alert("Please put a reason for flagging in the comments.");
            }
            else {
                // Add a comment for flagging
                updateSheet('comments',$('#comment').val());
                // Flag the sheet
                updateSheet('flagged_by',{{ Auth::user()->id }});
                // Reload the page to retreive the next sheet in the queue
                setTimeout(function(){
                    location.reload(true);
                }, 1000);
            }
        });

        // Check for commen't before flagging sheet if comment exists move to next sheet else require reason for flagging
        $('#modalComment .modal-footer button').on('click', function(e){
            if(!$('#comment').val()) {
                alert("Please put a reason for flagging in the comments.");
            } else {
                // Add a comment for flagging
                updateSheet('comments',$('#comment').val());
                // Flag the sheet
                updateSheet('flagged_by',{{ Auth::user()->id }});
                // Reload the page to retrieve the next sheet in the queue
                setTimeout(function(){
                    location.reload(true);
                }, 1000);
            }
        });

        // Search for signer
        $('#search_submit_btn').click(function(e){
            e.preventDefault();
            $('input,textarea,select').blur();
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
        $('#results-container').removeClass('hidden');
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
        $(document).keydown(function (e) {
            if ($(e.target).is('input, textarea, select')) {
                return;
            } else {
                switch(e.which){
                    case 9 :
                        $('tr.signer td:empty').first().click();
                        return false;
                        break;
                    case 13 :
                        if($('tr.match.info').length){
                            var voter_id = $('tr.match.info').first().click();
                        }
                        break;
                    case 38 :
                        // Get selected row
                        if(!$('tr.match.info').length){
                            $('tr.match').last().addClass('info');
                        } else {
                            $('tr.match.info').first().not(':first-child').removeClass('info').prev().addClass('info');
                        }
                        return false;
                        break;
                    case 40 :
                        // Get selected row
                        if(!$('tr.match.info').length){
                            $('tr.match').first().addClass('info');
                        } else {
                            $('tr.match.info').first().not(':last-child').removeClass('info').next().addClass('info');
                        }
                        return false;
                        break;
                }
            }
        });

        //Watch for a signer to be selected and change classes to identify selected
        $(document.body).on('click', '.signer', function(e){
            $('.signer').removeClass('bg-info activeSigner');
            $(this).addClass('bg-info activeSigner');
            // Focus on and clear Search form
            $('input#first').focus().select();
        });

        // Assign selected voter
        $("#search-results").on('click','tr.match',function(e){
          if($('tr.signer').hasClass('activeSigner')){
            var voterId = $(e.currentTarget).data('voter-id');
            setRow(voterId); // Make the AXAX and UI updates
          } else {
              alert("Please select a signer to update");
            }
        });

        $('#not_readable').on('click', function(e){
            if($('tr.signer').hasClass('activeSigner')){
                $('.activeSigner').html('<td><strong class="text-primary" style="color:red;">No Match Found </strong></td><td>---- ---------, ----------, -- -----</td>');
                setRow();
            } else {
                alert("Please select a signer to update");
            }
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

    function setRow(voterId = 0) {
        var rowId = $('.activeSigner').index() + 1; // Get the row number
        console.log('Row is ' + rowId);
        console.log('Voter is ' + voterId);

        var data = {'_token': $('input[name="_token"').val()};
            data.sheet_id = {{ $sheet->id }};
            data.voter_id = voterId;
            data.row = rowId;

        $.post('/signers', data, function(res, status, jqXHR){
            // Deal with response
            console.log(res);
            if(res.success){
                console.log(voterId);
                $('ul#comments').append('<li class="text-success">' + res.message + '</li>');

                if(voterId != 0){
                    var voter = searchResults[voterId]; // Set 
                    var html = '<td><strong class="text-primary signer">'
                    + voter.first_name + ' ' + voter.middle_name + ' ' + voter.last_name + '</strong></td><td>'
                    + voter.res_address_1 + ', ' + voter.city + ', OR ' + voter.zip_code + '</td>';
                } else {
                    var voter = {first_name: 'No', middle_name: 'Match', last_name: 'Found', res_address_1: '--', city: '--', zip_code: '--'};
                    var html = '<td colspan="2"><span class="text-danger signer">NO MATCH FOUND</span></td>'
                }
                
                $('.activeSigner').attr('data-selected',voterId).html(html).show();
                
                
                $('.activeSigner').removeClass('bg-info activeSigner').addClass('done');
                 $('#numOfSigners').html('<h2 style="margin:0px; padding:0px;">' + ({{$sheet->signature_count}}-$('tr.signer').not('.done').length) + ' of ' + {{$sheet->signature_count}} +' signers added</h2>');
                 if(!$('tr.signer').not('.done').length){  
                      $('#finish-sheet').attr('disabled',false).removeClass('btn-default').addClass('btn-primary');
                }
            } else {
                $('ul#comments').append('<li class="text-danger">' + res.message + '</li>')
            }
        }, 'json').fail(function(xhr){
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
    }
    });
        
</script>
</div>
@endsection