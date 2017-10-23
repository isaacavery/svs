@extends('layouts.app')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <h2>API Test UI: <code>searchSigned</code></h2>
    <div class="col-xs-6">
        {{ Form::open(['class' => 'form']) }}
        <div class="form-group">
            {{ Form::label('auth (key provided below)') }}
            <pre>{{ json_decode($_ENV['API_CLIENT_KEYS'])[0] }}</pre>
        </div><div class="form-group">
            {{ Form::label('firstName') }}
            {{ Form::text('firstName', '', ['class' => 'form-control']) }}
        </div><div class="form-group">
            {{ Form::label('lastName') }}
            {{ Form::text('lastName', '', ['class' => 'form-control']) }}
        </div><div class="form-group">
            {{ Form::label('zipCode (optional)') }}
            {{ Form::text('zipCode', '', ['class' => 'form-control']) }}
        </div>
        {{ Form::button('Test', ['class' => 'form-control btn btn-primary test-trigger']) }}
        {{ Form::close() }}
        <div class="search-results">
        </div>
    </div>
    <div class="col-xs-6">
    <h3>Documentation</h3>
    <dl>
        <dt>URL</dt>
        <dd>{{ url('api/searchSigned') }}</dd>
        <dt>METHOD</dt>
        <dd>GET</dd>
        <dt>PARAMETERS</dt>
        <dd><pre style="white-space: pre-wrap; word-break: keep-all;">firstName (STRING) full or partial first name to search (matches to the beginning of the name)
lastName  (STRING) full last name to search (exact match)
zipCode   (MIXED)  [optional] A numeric 5 digit zip code (exact match)
auth      (STRING) A valid API key</pre></dd>
        <dt>RETURN VALUES</dt>
        <dd>Success: <pre  style="white-space: pre-wrap; word-break: keep-all;">{success: true, results: [{name: 'FULL NAME', address: 'FULL ADDRESS, CITY, ST ZIP', signed: 0 or 1},... (up to 10 matches)]}</pre>
        Error: <pre  style="white-space: pre-wrap; word-break: keep-all;">{success: false, error: 'ERROR DESCRIPTION'}</pre></dd>
    </div>
</div>
<script>
    $('document').ready(function(e){
        $('.test-trigger').click(function(e){
            var requestData = {
                firstName: $('input[name="firstName"]').val(),
                lastName: $('input[name="lastName"]').val(),
                zipCode: $('input[name="zipCode"]').val(),
                auth: '{{ json_decode($_ENV['API_CLIENT_KEYS'])[0] }}',
            };
            console.log(requestData);
            $.get('/api/searchSigned',requestData, function(res, status, jqXHR){
                // Deal with response
                if(res.success){
                    console.log(res);
                    if (!res.results.length) {
                        $('.search-results').html('No matches found');
                    } else {
                        $response = '<table class="table"><tr><th>Name</th><th>Address</th><th>Signed</th></tr>';
                        for (var i=0; i < res.results.length; i++) {
                            var row = res.results[i];
                            $response += '<tr><td>'
                                + row.name
                                + '</td><td>'
                                + row.address
                                + '</td><td>'
                                + row.signed
                                + '</td></tr>';
                        }
                        $response += '</table>';
                        $('.search-results').html($response);
                    }
                } else {
                    console.log('FAILED');
                    console.log(res);
                    $('.search-results').html('ERROR: ' + res.error);
                }
            }).fail(function(xhr){
                    // Unknown error
                    alert('Unknown ' + xhr.status + ' error: ' + xhr.responseText);
            });
        });
    });
</script>
@endsection