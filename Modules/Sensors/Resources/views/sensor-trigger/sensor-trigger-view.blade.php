@extends('layouts.app')
@section('css')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
    table.dataTable tbody th,
    table.dataTable tbody td {
    padding: 8px 18px;
    }
    .add-new{
    margin-top: 0px;
    margin-bottom: 10px;
    }


</style>
@stop
@section('content')
<div class="table_title">
    <h4>Motion Sensor</h4>
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
<br>
<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
             <th class="sorting">#</th>
             <th class="sorting">Date</th>
             <th class="sorting">Customer</th>
             <th class="sorting">Address</th>
             <th class="sorting">Room</th>
             <th class="sorting">Entry</th>
             <th class="sorting">Exit</th>
         </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Add Key Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'customerkey-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        {{ Form::hidden('customer_id', isset($roomId) ? old('customer_id',$roomId) : null,array('id'=>'customer_id')) }}
        {{ Form::close() }}
        </div>
    </div>
</div>
@include('keymanagement::key-setting.partials.modal')
@stop
@section('scripts')
<script>
        function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
            }
        }

    $(function () {
        $('.select2').select2();
        var _URL = window.URL || window.webkitURL;
        var id = $('#customer_id').val();
        var url = '{{ route('sensors.triggers.list',[":id"]) }}';
        url = url.replace(':id', id);


        /* Datatable- Start */

        var table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":url,
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2'
                },
                {
                    extend: 'excelHtml5'
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {
                    data: 'date',
                    name: 'date',
                    defaultContent: "--"
                },
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    defaultContent: "--",
                },
                {
                    data: 'customer_address',
                    name: 'customer_address',
                    defaultContent: "--"

                },
                {
                    data: 'room_name',
                    name: 'room_name',
                    defaultContent: "--"

                },
                {
                    data: 'entry',
                    name: 'entry',
                    defaultContent: "--"

                },
                {
                    data: 'exit',
                    name: 'exit',
                    defaultContent: "--"

                },
            ]

        });
         /* Datatable- End */
         $(".client-filter").change(function(){
            table.ajax.reload();
        });

        /*Add new - modal popup - Start */

        $('.add-new').on('click', function () {
            var title = $(this).data('title');
            $("#myModal").modal();
            $('#myModal form').trigger('reset');
            $('#myModal .modal-title').text(title);
            $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal form').find('#key_image_name').html('');
            $('#myModal form').find("input[name='id']").val('');
        });

        /*Add new - modal popup - End */

        $('#customerkey-form').submit(function (e) {
            e.preventDefault();

            var $form = $(this);
            url = "{{ route('keysetting.store') }}";
            var formData = new FormData($('#customerkey-form')[0]);
            // $('#myModal form').find('#key_image_name').html('');
            $('#severity-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                $("#myModal").modal('hide');
                if (data.success) {
                    if(data.result){
                        swal({
                        title: "Saved",
                        text: "Key details updated successfully",
                        type: "success"
                    },function(){
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        $('#customerkey-form')[0].reset();
                        table.ajax.reload();
                    });
                    } else {
                        swal({
                        title: "Saved",
                        text: "New key details added",
                        type: "success"
                    },function(){
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        $('#customerkey-form')[0].reset();
                        table.ajax.reload();
                    });

                    }

                } else {
                    console.log(data);
                    swal("Oops", "The record has not been saved", "warning");
                }
                },
                fail: function (response) {
                console.log(response);
                swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
                if(typeof(xhr.responseJSON.errors.key_image) != "undefined" && xhr.responseJSON.errors.key_image !== null) {
                    if(xhr.responseJSON.errors.key_image.length > 0){
                    $('#key_image_id').val('');
                }
                }
                },
                contentType: false,
                processData: false,
            });
            });


            $('#table-id').on('click', '.edit', function(e){
                var id = $(this).data('id');
                var base_url = "{{route('keysetting.single', ':id')}}";
                var url = base_url.replace(':id', id);
                console.log(id,url);
                $("#key_image_id").val(null);
                $('#customerkey-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                        '');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:url,
                    type: 'GET',
                    success: function (data) {
                    if(data){
                        $('input[name="id"]').val(data.id);
                        $('input[name="key_id"]').val(data.key_id);
                        $('input[name="room_name"]').val(data.room_name);
                        if(data.attachment){
                            $('#key_image').val(data.attachment.original_name);
                            $('#key_image_name').html(data.attachment.original_name);
                        }else{
                            $('#key_image_name').html('');
                        }

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Key : ")
                    }
                    },
                    fail: function (response) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                    contentType: false,
                    processData: false,
                });
                });

        /***** Key SUmmary  Delete - Start */
        $('#table-id').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('keysetting.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action.Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "Record has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete ", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });

    });

    $(document).keyup(function(e) {jQuery
         if (e.key === "Escape") {
          $("#myModal").modal('hide');
       }
     });


    function showFileSize(file_name) {
        var input, file;
        var file_size = 0;
        if (!window.FileReader) {
            console.log("The file API isn't supported on this browser yet.");
            return;
        }
        input = $(file_name);
        if(input[0].files.length > 0){
            file_size = (input[0].files[0].size)/(1000000);
        }
        return file_size;
    }








</script>


@stop


