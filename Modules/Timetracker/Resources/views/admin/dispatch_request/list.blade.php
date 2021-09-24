@extends('layouts.app')
@section('content')
<style>
    .add-new{

        margin-top:-5px;
        margin-bottom:15px;
    }
</style>
<div class="table_title">
    <h4>Dispatch Request List</h4> <br>
</div>

<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>

@if(Session::has('flash_message'))
<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span>
    <em> {!! session('flash_message') !!}</em>
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@endif

<a href="{{ route('dispatchrequest.create') }}" type="button" class="add-new">Add New</a>

<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Request Type</th>
            <th>Subject</th>
            <th>Customer Name</th>
            <th>Address</th>
            <th>Postal Code</th>
            <th>Rate</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

@stop
@section('scripts')
<script>

    function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
            }
    }
    $(function() {
        $(".select2").select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Dispatch Request');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: {
                "url":'{{ route("dispatch_request.list") }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());

                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [1, "asc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'dispatch_request_type.name',
                        name: 'dispatch_request_type.name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: null,
                        render: function(o) {
                            if (o.customer_id != null) {
                                return o.customer_trashed.client_name
                            } else {
                                return o.name
                            }
                        },
                        sortable: false,
                        name: 'customer_name'
                    },
                    {
                        data: 'site_address',
                        name: 'site_address'
                    },
                    {
                        data: 'site_postalcode',
                        name: 'site_postalcode'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                    {
                        data: 'dispatch_request_status.name',
                        name: 'dispatch_request_status.name'
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {

                            var actions = '';
                            if (o.dispatch_request_status_id == 1) {

                                var edit_url = '{{ route("dispatchrequest.edit", ":id") }}';
                                edit_url = edit_url.replace(':id', o.id);
                                actions += '<a title="Edit" href="' + edit_url + '" class="fa fa-edit fa-lg link-ico" data-id=' + o.id + '></a>';

                                actions += '<button title="Push"  class="btn btn-primary Push" style="height: 30px;" data-id=' + o.id + '>Push</button>';
                            }

                            var show_url = '{{ route("dispatchrequest.show", ":id") }}';
                            show_url = show_url.replace(':id', o.id);
                            actions += '<a title="Show" href="' + show_url + '" class="view btn fa fa-eye" data-id=' + o.id + '></a>';

                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            table.ajax.reload();
        });

        /* Course Category Delete - Start */
        $('#table').on('click', '.Push', function(e) {
            var id = $(this).data('id');
            var base_url = "{{ route('dispatch_request.triggerPushNotification',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, send",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function() {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                swal("Push Notification", "successfully send", "success");
                            } else {
                                swal("Alert", "Try again", "warning");
                            }
                        },
                        error: function(xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });



    });
</script>
@stop
