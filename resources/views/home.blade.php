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
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Home</div>

                <div class="panel-body">
                    <div class="col-xs-12 col-md-6">
                        <h2>Add Circulators</h2>
                        <p><strong>1.872 Circulators</strong> ready to be added</p>
                        <a href="/circulators/queue" class="btn btn-primary">Start ></a>
                        <h3>Circulator stats</h3>
                        <p><strong>{{ $circulator_count }} Circulators added.</strong></p>
                        <p>890 unique Circulators have gathered a total of <strong>40,783 signatures</strong></p>
                        <table>
                            <tfoot><strong>Circulators added per user:</strong></tfoot>
                            <tbody>
                                <tr>
                                    <td>1,930</td>
                                    <td>Jane User</td>
                                </tr>
                                <tr>
                                    <td>1,675</td>
                                    <td>John User</td>
                                </tr>
                                <tr>
                                    <td>451</td>
                                    <td>Jill User</td>
                                </tr>
                                <tr>
                                    <td>98</td>
                                    <td>Jack User</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-12 col-md-6">
                    <h2>Add Signers</h2>
                        <p><strong>14.219 Signers</strong> ready to be added</p>
                        <a href="/sheets/queue" class="btn btn-primary">Start ></a>
                        <h3>Signer stats</h3>
                        <p><strong>26,564 Signers added.</strong></p></p>
                        <table>
                            <tfoot><strong>Signers added per user:</strong></tfoot>
                            <tbody>
                                <tr>
                                    <td>10,930</td>
                                    <td>Jane User</td>
                                </tr>
                                <tr>
                                    <td>10,675</td>
                                    <td>John User</td>
                                </tr>
                                <tr>
                                    <td>4,051</td>
                                    <td>Jill User</td>
                                </tr>
                                <tr>
                                    <td>908</td>
                                    <td>Jack User</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <h3>Admin Tools & Reports</h3>
    <div class="col-md-3">
        <h4>Sheets</h4>
        <ul>

        @if(Auth::user()->admin)
            <li><a href="/sheets/create">Upload sheets</a></li>
        @endif
            <li><a href="#">View flagged sheets</a></li>
        </ul>
    </div>
    <div class="col-md-3">
        <h4>Circulators</h4>
        <ul>
            <li><a href="#">Download all circulators (CSV)</a></li>
        </ul>
    </div>
    <div class="col-md-3">
        <h4>Signers</h4>
        <ul>
            <li><a href="#">View list of duplicates</a></li>
            <li><a href="#">View list of no matches</a></li>
            <li><a href="#">Download all Signers (CSV)</a></li>
        </ul>
    </div>
    <div class="col-md-3">
        <h4>Users</h4>
        <ul>
            <li><a href="#">View user activity report</a></li>
            <li><a href="#">Manage users</a></li>
        </ul>
    </div>
</div>
@endsection