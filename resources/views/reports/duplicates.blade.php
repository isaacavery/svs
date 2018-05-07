@extends('layouts.app')

@section('content')

<div class="container">
    <p class="text-danger">{{ $duplicate_info['summary'] }}</p>
    <p><strong>Duplicates:</strong> {{ $duplicate_info['voters'] }} Voters, {{ $duplicate_info['count'] }} Total Signatures ({{ $duplicate_info['remaining'] }} need removal)<br><strong>NOTE:</strong> the accurate corrected signature count is currently <strong>{{ $signers_added - $duplicate_info['offset'] }}</strong> after removing duplicates!</p>
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="{{ (!$current_letter) ? 'active' : '' }}"><a href="/reports/duplicates">A-Z</a></li>
<?php $letters = range('A', 'Z'); ?>
                @foreach($letters as $l)
                <li class="{{ ($current_letter == $l) ? 'active' : '' }}"><a href="/reports/duplicates?letter={{ $l }}">{{ $l }}</a></li>
                @endforeach
                <li><a href="/reports/duplicates/download" target="_blank"><span class="glyphicon glyphicon-save"></span> A-Z</a></li>
            </ul>
            </nav>
            <div class="panel panel-default">
                <div class="panel-heading">List Sheets</div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <table class="table table table-hover table-condensed">
                            <thead>
                                @if($current_letter)
                                <tr>
                                    <th><a href="/reports/duplicates{{ ($current_letter) ? '?letter=' . $current_letter : '' }}">Circulator Name</a></th>
                                    <th style="width: 120px">Date</th>
                                    <th>Sheet</th>
                                    <th>Line</th>
                                    <th><a href="/reports/duplicates?sort=signer{{ ($current_letter) ? '&letter=' . $current_letter : '' }}">Signer Name</a></th>
                                    <th>Signer Address</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duplicates as $k => $row)
                                    @if($row->do_not_remove == false && !$row->deleted_at)
                                <tr>
                                    <td>{{ $row->circulator_name }}</a></td>
                                    <td>{{ $row->date_signed }}</td>
                                    <td><a href="/sheets/{{ $row->sheet_id }}" target="_blank">View</a></td>
                                    <td>{{ ($row->self_signed) ? 'SS' : $row->row }}</td>
                                    <td><strong>{{ $row->signer_name }}</strong></td>
                                    <td>{{ $row->signer_address }}</td>
                                    @if(!$row->deleted_at)
                                    <td><span class="toggle-signature enabled" data-action="delete" data-id="{{ $row->signer_id }}"><span class="glyphicon glyphicon-remove"></span>Delete</span></td>
                                    @else
                                    <td><span class="toggle-signature enabled" data-action="restore" data-id="{{ $row->signer_id }}"><span class="glyphicon glyphicon-open"></span>Restore</span></td>
                                    @endif
                                </tr>
                                    @endif
                                @endforeach
                                @else
                                <tr>
                                    <th></th>
                                    <th><a href="/reports/duplicates{{ ($current_letter) ? '?letter=' . $current_letter : '' }}">Circulator Name</a></th>
                                    <th>Circulator Address</th>
                                    <th>Date</th>
                                    <th>Sheet</th>
                                    <th>Line</th>
                                    <th><a href="/reports/duplicates?sort=signer{{ ($current_letter) ? '&letter=' . $current_letter : '' }}">Signer Name</a></th>
                                    <th>Signer Address</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duplicates as $k => $row)
                                <tr class="{{ ($row->do_not_remove) ? '' : (($row->deleted_at) ? 'deleted' : 'danger')}}">
                                    <td>{{ $k }}</td>
                                    <td>{{ $row->circulator_name }}</a></td>
                                    <td>{{ $row->circulator_address }}</td>
                                    <td>{{ $row->date_signed }}</td>
                                    <td><a href="/sheets/{{ $row->sheet_id }}" target="_blank">View</a></td>
                                    <td>{{ ($row->self_signed) ? 'SS' : $row->row }}</td>
                                    <td>{{ $row->signer_name }}</td>
                                    <td>{{ $row->signer_address }}</td>
                                    @if($row->do_not_remove)
                                    <td>Master</td>
                                    @else
                                        @if(!$row->deleted_at)
                                        <td><span class="toggle-signature enabled" data-action="delete" data-id="{{ $row->signer_id }}"><span class="glyphicon glyphicon-remove"></span>Delete</span></td>
                                        @else
                                        <td><span class="toggle-signature enabled" data-action="restore" data-id="{{ $row->signer_id }}"><span class="glyphicon glyphicon-open"></span>Restore</span></td>
                                        @endif
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('document').ready(function(){
        $('body').on('click', '.toggle-signature.enabled', function(e){
            
            var activeTarget = $(e.currentTarget);

            // Disable link while processing ...
            activeTarget.addClass('disabled').removeClass('enabled');

            if(activeTarget.data('action') == 'delete') {

                // Delete signature record
                console.log('Deleting ' + activeTarget.data('id'));
                $.ajax({
                    url: '/signers/delete/' + activeTarget.data('id'),
                    activeLink: activeTarget, // Pass the event target into closure scope
                    success: function(data, status, jqXHR) {
                        this.activeLink.removeClass('disabled').addClass('enabled');
                        data = JSON.parse(data);
                        if(data.success == false) {
                            console.log('ERROR');
                            this.activeLink.addClass('has-error').html('<span class="glyphicon glyphicon-exclamation-sign"></span>Delete</a>');
                            if(data.error) {
                                alert('Error deleting signature: ' + data.error);
                            } else {
                                alert('Unknown error deleting signature');
                            }
                            // Restore link
                        } else {
                            // Signature has been deleted. Replace link
                            var html = '<span class="toggle-signature enabled" data-action="restore" data-id="' + this.activeLink.data('id') + '"><span class="glyphicon glyphicon-open"></span>Restore</a>';
                            this.activeLink.closest('tr').addClass('deleted');
                            this.activeLink.replaceWith(html);
                        }
                    },
                    error: function(jqXHR, status, error) {
                        alert (status + " Error deleting Signature: " + error);
                    }
                });
            } else {
                // Restore
                console.log('Restoring ' + activeTarget.data('id'));
                $.ajax({
                    url: '/signers/restore/' + activeTarget.data('id'),
                    activeLink: activeTarget, // Pass the event target into closure scope
                    success: function(data, status, jqXHR) {
                        this.activeLink.removeClass('disabled').addClass('enabled');
                        data = JSON.parse(data);
                        if(data.success == false) {
                            console.log('ERROR');
                            this.activeLink.addClass('has-error').html('<span class="glyphicon glyphicon-exclamation-sign"></span>Restore</a>');
                            if(data.error) {
                                alert('Error restoring signature: ' + data.error);
                            } else {
                                alert('Unknown error restoring signature. Please check your network developer tools for full details.');
                            }
                            // Restore link
                        } else {
                            // Signature has been deleted. Replace link
                            var html = '<span class="toggle-signature enabled" data-action="delete" data-id="' + this.activeLink.data('id') + '"><span class="glyphicon glyphicon-remove"></span>Delete</a>';
                            @if($current_letter)                            
                            this.activeLink.closest('tr').removeClass('deleted');
                            @else
                            this.activeLink.closest('tr').removeClass('deleted').addClass('danger');
                            @endif
                            this.activeLink.replaceWith(html);
                        }
                    },
                    error: function(jqXHR, status, error) {
                        alert (status + " Error deleting Signature: " + error);
                    }
                });
            }
        });
    });
</script>

@endsection