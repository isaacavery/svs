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
                                    <th>ID</th>
                                    <th>Date Added</th>
                                    <th>Date Signed</th>
                                    <th># Sig</th>
                                    <th>FLAG</th>
                                    <th>CIR</th>
                                    <th>SIG</th>
                                    <th>REV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sheets as $sheet)
                                <tr>
                                    <td>{{ $sheet->id }}</td>
                                    <td>{!! date('m/d/y', strtotime($sheet->created_at)) . ' <small><span class="text-muted">(' . $sheet->batch_id . ')</span></small>' !!}</td>
                                    <td>{{ date('m/d/y', strtotime($sheet->date_signed)) }}</td>
                                    <td>{{ $sheet->signature_count }}</td>
                                    <td>{!! ($sheet->flagged_by) ? '<a href="/sheets/' . $sheet->id . '"><span class="glyphicon glyphicon-flag text-danger"></span></a>' : '' !!}</td>
                                    <td>{!! ($sheet->circulator_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}</td>
                                    <td>{!! ($sheet->signatures_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}</td>
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