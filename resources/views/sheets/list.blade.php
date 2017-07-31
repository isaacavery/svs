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
                                    <th>Flagged</th>
                                    <th>Completed</th>
                                    <th>Approved</th>
                                    <th>Links</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sheets as $sheet)
                                <tr>
                                    <td>{{ $sheet->id }}</td>
                                    <td>{{ $sheet->created_at }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{!! (!$sheet->flagged_by) ? '<span class="glyphicon glyphicon-flag text-danger"></span>' : '' !!}</td>
                                    <td>{!! (!$sheet->completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}</td>
                                    <td>{!! (!$sheet->approved_by) ? '<span class="glyphicon glyphicon-thumbs-up text-success"></span>' : '' !!}</td>
                                    <td><a href="/sheets/{{ $sheet->id }}/edit"><span class="glyphicon glyphicon-lg glyphicon-pencil"></span> Edit</a></td>
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