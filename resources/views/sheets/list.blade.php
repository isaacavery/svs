@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">List Sheets</div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <table class="table table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th><a href="/sheets?sort=id&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">ID</a></th>
                                    <th><a href="/sheets?sort=type&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">TYPE</th>
                                    <th><a href="/sheets?sort=signers&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">SIGNATURES</th>
                                    <th><a href="/sheets?sort=no_match&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">NO MATCH</th>
                                    <th><a href="/sheets?sort=cir&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">CIR</th>
                                    <th><a href="/sheets?sort=sig&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">SIG</th>
                                    <th><a href="/sheets?sort=rev&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">REV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sheets as $sheet)
                                <tr>
                                    <td><a href="/sheets/{{ $sheet->id }}">{{ $sheet->id }}</a></td>
                                    <td>{{ ($sheet->self_signed) ? 'single' : 'multi' }}</td>
                                    <td>{{ $sheet->signature_count }}</td>
                                    <td>{{ ($sheet->no_match) ? $sheet->no_match : 0 }}</td>
                                    <td>{!! ($sheet->circulator_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}{!! (!$sheet->circulator_completed_by && $sheet->flagged_by) ? '<span class="glyphicon glyphicon-flag text-danger"></span>' : '' !!}</td>
                                    <td>{!! ($sheet->signatures_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}{!! ($sheet->circulator_completed_by && !$sheet->signatures_completed_by && $sheet->flagged_by) ? '<span class="glyphicon glyphicon-flag text-danger"></span>' : '' !!}</td>
                                    <td>{!! ($sheet->reviewed_by) ? '<span class="glyphicon glyphicon-thumbs-up text-success"></span>' : '' !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection