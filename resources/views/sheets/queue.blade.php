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
    .modal-backdrop.in { opacity: 0.1 !important; }
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
                <h2 class="noMargin" id = 'numOfSigners'>{{ count($voters) }} of {{$sheet->signature_count}} signers added</h2>
                <table class="table table-condensed" id="signer-match" data-selected="0">
                    <tbody>
                    @for($i=0; $i<$sheet->signature_count; $i++)
                        
                            @if(isset($voters[$i+1]))
                            <tr class="signer done">
                                @if (is_int($voters[$i+1]))
                                    @if($voters[$i+1] == 0)
                                        <td><strong class="text-danger signer">NO MATCH</strong></td><td><a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>
                                     @elseif ($voters[$i+1] == 1)
                                        <td><strong class="text-default signer">SKIPPED</strong></td><td><a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>
                                    @endif
                                @else
                                   <td><strong class="text-primary signer">{{ $voters[$i+1]->first_name }} {{$voters[$i+1]->middle_name }} {{$voters[$i+1]->last_name }}</strong></td><td>{{ $voters[$i+1]->res_address_1 }}, {{ $voters[$i+1]->city }}, OR {{ $voters[$i+1]->zip_code}}<a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>
                                @endif
                            @else
                            <tr class="signer">
                            <td></td><td><a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>
                            @endif
                        </tr>
                    @endfor
                    </tbody>
                </table>
                <div class="row">
                    <div class="form-group col-xs-5">
                        {{ Form::label('first_name', 'First Name', ['class' => 'control-label']) }}
                        {{ Form::text('first_name','',['class'=>'form-control', 'id' => 'first', 'tabindex' => 1]) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-5">
                        {{ Form::label('last_name', 'Last Name', ['class' => 'control-label']) }}
                        {{ Form::text('last_name','',['class'=>'form-control', 'id' => 'last', 'tabindex' => 2]) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-2">
                        {{ Form::label('voter_id', 'Voter ID', ['class' => 'control-label']) }}
                        {{ Form::text('voter_id','',['class'=>'form-control', 'id' => 'voter_id', 'tabindex' => 8]) }}
                        <span class="help-block hidden"></span>
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('number', 'Street Number') }}
                        {{ Form::text('number','',['class'=>'form-control', 'id' => 'street_number', 'tabindex' => 3]) }}
                        {{ Form::checkbox('po_box','yes',0,['tabindex' => '4', 'id' => 'po_box']) }} 
                        {{ Form::label('po_box', 'This is a PO Box') }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('street_name', 'Street Name') }}
                        {{ Form::text('street_name','',['class'=>'form-control', 'id' => 'street_name', 'tabindex' => 5]) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('city', 'City') }}
                        {{ Form::text('city','',['class'=>'form-control', 'id' => 'city', 'tabindex' => 6]) }}
                    </div>
                    <div class="form-group col-xs-3">
                        {{ Form::label('zip', 'Zip') }}
                        {{ Form::text('zip','',['class'=>'form-control', 'id' => 'zip', 'tabindex' => 7]) }}
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
                                    <td>ID</td>
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
        <a href="/" class="btn btn-primary">Exit</a>
        <a href="#" id="finish-sheet" class="btn btn-default pull-right" disabled="disabled">Finish &amp; Get Next Sheet ></a>
        <a href="#modalComment" class="btn btn-default pull-right" data-toggle="modal">Flag Sheet &amp; Skip</a>
    </div>
</div>
    
<script type="text/javascript">
    var searchResults;

    $('document').ready(function(){
        $('#finish-sheet').click(function(e){
            if($(e).attr('disabled')){
                console.log('Not ready');
            } else if (!$('tr.signer').not('.done').length) {
                console.log('Submitting');
                updateSheet('signatures_completed_by', {{ Auth::user()->id }});
                // Reload the page to retrieve the next sheet in the queue
                setTimeout(function(){
                    location.reload(true);
                }, 500);
            } else {
                console.log('Cannot submit yet ... incomplete.');
            }
        });
  //      $(document)
  //     .ajaxStart(function(){
  //        $('#blockui, #ajaxSpinnerContainer').fadeIn();
  //      })
  //      .ajaxStop(function(){
  //          $('#blockui, #ajaxSpinnerContainer').fadeOut();
  //      });
       var signerCnt  = {{ count($voters) }};
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

        if(!$('tr.signer').not('.done').length){
            $('#finish-sheet').attr('disabled',false);
        }

        // Check for comment before flagging sheet if comment exists move to next sheet else require reason for flagging
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
            // Submit Voter search
            $('#search-results').html('<tr><td colspan="3" class="text-primary">Searching, please wait ...</td></tr>');
            var data = {
                exact_match: 1,
                vid: $('#voter_id').val(),
                first: $('#first').val(),
                last: $('#last').val(),
                street_name: $('#street_name').val(),
                number: $('#street_number').val(),
                po_box: $('#po_box').first().is(':checked'),
                city: $('#city').val(),
                zip: $('#zip').val(),
                _token: $('input[name="_token"').val()
            };
            if ($('input#exact_match[value="0"]').is(':checked')) {
                data['exact_match'] = 0;
            }

            // Reset the PO Box and Loose Search options
            $('input[name="exact_match"]').prop('checked',false).filter('[value="1"]').prop('checked',true);
            $('#po_box').prop('checked',false);

            $.post('/signers/search',data, function(res, status, jqXHR){
                // Deal with response
                if(res.success){
                    if(!res.count) {
                        // Clear the search results
                        searchResults = {};
                        $('#search-results').html('<tr><td colspan="3" class="text-danger">No matches found!</td></tr>');
                        $('#results-container').removeClass('hidden');
                    } else {
                        // Update the global search results
                        searchResults = {};
                        for (var i = res.matches.length - 1; i >= 0; i--) {
                            searchResults[res.matches[i].voter_id] = res.matches[i];
                        }
                        var html = '';
                        $.each(res.matches, function(i,v){
                            html += '<tr class="match" data-voter-id="' + v.voter_id + '"><td>';
                            
                            html += v.first_name + ' ';
                            if(v.middle_name)
                                html += v.middle_name + ' ';
                            html += v.last_name + '</td><td>'
                                + v.res_address_1 + ' ' + v.city + ' ' + v.zip_code + '</td><td>';
                            html += (v.res_address_1 == v.eff_address_1) ? '</td>' : v.eff_address_1 + '</td>';

                            if(v.voter_id){
                                html += '<td><span class="text-muted">' + v.voter_id + '</span></td>';
                            } else {
                                '<td></td>';
                            }
                        });
                        $('#search-results').html(html);
                        $('#searchFinished').show();
						$('#results-container').removeClass('hidden');
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

        // Listen for the ENTER keypress in the search form
        $('#first,#last,#street_name,#street_number,#city,#zip,#voter_id').keypress(function (e) {
          if (e.which == 13) {
            $('#search_submit_btn').click();
          }
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
            $('.signer').removeClass('signer-info activeSigner');
            $('.skip').addClass('hidden');
            $(this).addClass('signer-info activeSigner');
            $(this).find('.skip').removeClass('hidden');
            // Focus on and clear Search form
            $('input[type="text"]').val('');
            $('input#first').focus().select();
        });

        $('.skip').on('click', function(e){
            setRow(null);
        })

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
                setRow(0);
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
            if(res.success){
                console.log(voterId);
                $('ul#comments').append('<li class="text-success">' + res.message + '</li>');

                if(voterId){
                    var voter = searchResults[voterId]; // Set 
                    var html = '<td><strong class="text-primary signer">'
                    + voter.first_name + ' ' + voter.middle_name + ' ' + voter.last_name + '</strong></td><td>'
                    + voter.res_address_1 + ', ' + voter.city + ', OR ' + voter.zip_code + '<a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>';
                } else if(voterId == 0){
                    var voter = {first_name: 'No', middle_name: 'Match', last_name: 'Found', res_address_1: '--', city: '--', zip_code: '--'};
                    var html = '<td><strong class="text-danger signer">NO MATCH FOUND</strong></td><td><a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>';
                } else {
                    var voter = {first_name: 'No', middle_name: 'Match', last_name: 'Found', res_address_1: '--', city: '--', zip_code: '--'};
                    var html = '<td><strong class="text-default signer">SKIPPED</strong></td><td><a href="#" type="button" class = "skip btn-primary btn-xs pull-right hidden">SKIP</a></td>';
                    // Blur the enput to enable the TAB listener
                    $('input').blur();
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