@extends('layouts.app')

@section('content')

<script>
    function toggleFlagged(sheetId) {
        $.ajax({
            url: '/sheets/toggleFlagged/' + sheetId,
            success: function(data, status, xhr) {
                data = JSON.parse(data);
                if(!data.success) {
                    alert('ERROR: ' + data.error);
                } else {
                    var row = $('#sheet-' + data.id);
                    var html = '';
                    if (data.flagged)
                        html = '<span class="glyphicon glyphicon-flag text-danger"></span>';
                    row.find('.flagged').html(html);
                }
            },
            error: function(xhr, status, error) {
                alert('ERROR: ' + error);
            }
        });
    }
    function toggleReview(sheetId) {
        $.ajax({
            url: '/sheets/toggleReviewed/' + sheetId,
            success: function(data, status, xhr) {
                data = JSON.parse(data);
                if(!data.success) {
                    alert('ERROR: ' + data.error);
                } else {
                    var row = $('#sheet-' + data.id);
                    var html = '';
                    if (data.reviewed)
                        html = '<span class="glyphicon glyphicon-thumbs-up text-success"></span>';
                    row.find('.reviewed').html(html);
                }
            },
            error: function(xhr, status, error) {
                alert('ERROR: ' + error);
            }
        });
    }
</script>
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
                                    <th><a href="/sheets?sort=type&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">TYPE</a></th>
                                    <th><a href="/sheets?sort=signers&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">SIGNATURES</a></th>
                                    <th><a href="/sheets?sort=no_match&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">NO MATCH</a></th>
                                    <th><a href="/sheets?sort=ratio&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">%</a> | <a href="/sheets?sort=ratio&order={{ ($order == 'desc') ? 'asc' : 'desc' }}&type=ss">SS</a> | <a href="/sheets?sort=ratio&order={{ ($order == 'desc') ? 'asc' : 'desc' }}&type=reg">REG</a></th>
                                    <th><a href="/sheets?sort=cir&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">CIR</a></th>
                                    <th><a href="/sheets?sort=sig&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">SIG</a></th>
                                    <th><a href="/sheets?sort=flag&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">FLAG</a></th>
                                    <th><a href="/sheets?sort=rev&order={{ ($order == 'desc') ? 'asc' : 'desc' }}">REV</a></th>
                                    <th>Controls</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sheets as $sheet)
                                <tr id="sheet-{{ $sheet->id }}">
                                    <td class="sheet-id"><a href="/sheets/{{ $sheet->id }}" target="_blank">{{ $sheet->id }}</a></td>
                                    <td class="self-signed">{{ ($sheet->self_signed) ? 'single' : 'multi' }}</td>
                                    <td class="count">{{ $sheet->signature_count }}</td>
                                    <td class="no-match">{{ ($sheet->no_match) ? $sheet->no_match : 0 }}</td>
                                    <td class="ratio">{{ $sheet->ratio }}</td>
                                    <td class="circulator">{!! ($sheet->circulator_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}</td>
                                    <td class="signatures">{!! ($sheet->signatures_completed_by) ? '<span class="glyphicon glyphicon-ok text-primary"></span>' : '' !!}</td>
                                    <td class="flagged">{!! ($sheet->flagged_by) ? '<span class="glyphicon glyphicon-flag text-danger"></span>' : '' !!}</td>
                                    <td class="reviewed">{!! ($sheet->reviewed_by) ? '<span class="glyphicon glyphicon-thumbs-up text-success"></span>' : '' !!}</td>
                                    <td class="controls"><a href="javascript:toggleFlagged({{ $sheet->id }})">Flag</a> | <a href="javascript:toggleReview({{ $sheet->id }})">Review</a></td>
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