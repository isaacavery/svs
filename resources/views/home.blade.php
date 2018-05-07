<style>
 table {
    text-align:center;
    margin-left:15%;
    margin-right:15%;
    width:70%;
}
.mainPanel{
    margin-top: 5%;
    margin-bottom: 5%;
}
hr{
    margin:5px;
}
td {
	text-align:center;
}
</style>
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
<div class="mainPanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default container" style="text-align:center;">

                <div class="panel-body">
                    <p class="text-danger">{{ $duplicates['summary'] }}</p>
                    <div class="col-xs-12 col-md-6">
                        <h2>Add Circulators</h2>
                        <p><strong>{{ $circulator_ready }} Circulators</strong> ready to be added</p>
                        <a href="/circulators/queue" class="btn btn-primary">Start ></a>
                        <h3>Circulator stats</h3>
                        <p><strong>{{ $circulator_count }} Circulators added.</strong></p>
                        <p>{{ $circulator_unique }} unique Circulators have gathered a total of <strong>{{ $signature_count->count }} signatures</strong></p>
                        <table class="table-striped">
                            <tfoot><strong>Circulators added per user:</strong></tfoot>
                            <hr class="noMargin">
                            <tbody>
                            @foreach($user_data as $user)
                                <tr>
                                    <td>{{ $user['circulators'] }}</td>
                                    <td>{{ $user['name'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
<?php
        usort($user_data, function($a, $b) {
            return $b['signers'] - $a['signers'];
        });
?>
                    <div class="col-xs-12 col-md-6" style="text-align:center">
                    <h2>Add Signers</h2>
                        <p><strong>{{ ($signers_ready->count) ? $signers_ready->count : 0 }} Signers</strong> ready to be added</p>
                        <a href="/sheets/queue" class="btn btn-primary">Start ></a>
                        <h3>Signer stats</h3>
                        <p><strong>{{ $signers_added }} Signers added.</strong></p>
                        <p><strong>Duplicates:</strong> {{ $duplicates['voters'] }} Voters, {{ $duplicates['count'] }} Total Signatures ({{ $duplicates['remaining'] }} need removal)<br><strong>NOTE:</strong> the accurate corrected signature count is currently <strong>{{ $signers_added - $duplicates['offset'] }}</strong> after removing duplicates!</p>
                        <table class="table-striped">
                        <p>&nbsp;</p>
                            <tfoot><strong>Signers added per user:</strong></tfoot>
                            <hr class="noMargin">
                            <tbody>
                            @foreach($user_data as $user)
                                <tr>
                                    <td>{{ $user['signers'] }}</td>
                                    <td>{{ $user['name'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->admin)
    <div class="container">
        <h3 style="text-align:center;">Admin Tools & Reports</h3>
        <div class="col-md-3">
            <h3 id="searchHeader">Sheets</h3>
            <ul>

            @if(Auth::user()->admin)
                <li><a href="/sheets/create">Upload sheets</a></li>
            @endif
                <li><a href="/sheets">View flagged sheets</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h3 id="searchHeader">Circulators</h3>
            <ul>
                <li><a href="/reports/circulators">Download all circulators (CSV)</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h3 id="searchHeader">Signers</h3>
            <ul>
                <li><a href="/reports/duplicates">View list of duplicates</a></li>
                <li><a href="/sheets?sort=no_match">View list of no matches</a></li>
                <li><a href="/reports/signers">Download all Signers (CSV)</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h3 id="searchHeader">Users</h4>
            <ul>
                <li><a href="#">View user activity report</a></li>
                <li><a href="/users">Manage users</a></li>
            </ul>
        </div>
    </div>
    @endif
</div>
@endsection