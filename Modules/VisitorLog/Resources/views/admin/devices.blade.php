@extends('adminlte::page')
@section('title', 'Visitor Log Devices')
@section('content_header')
<h1>Visitor Log Devices</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2-container{
        width: 100% !important;
    }

</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Devices">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Name</th>
            <th>Activation Code</th>
            <th>Last Active Time</th>
            <th>Template</th>
            <!-- <th>Camera Mode</th>
            <th>Scaner Camera Mode</th>
            <th>Created At</th> -->
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Office</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'devices-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <!-- <div class="col-md-12"> -->
                    <div id="customer_id" class="form-group">
                        <label for="customer_id" class="col-sm-3 control-label">Customer</label>
                        <div class="col-sm-9">
                            {{ Form::select('customer_id',[''=>'Select Customer']+$customers, old('customer_id'),array('class'=> 'form-control', 'id'=>'customerId')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="name">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Name')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="description">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                            {{ Form::textarea('description',null,['class' => 'form-control','id'=>'note','rows'=>'3']) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="camera_mode">
                        <label for="camera_mode" class="col-sm-3 control-label">Camera Mode </label>
                        <div class="col-sm-9" style="margin-top: 8px;">
                            <input type="radio" id="" name="camera_mode" value="1"> <label for="">Front Camere</label>
                            <input type="radio" id="" name="camera_mode" value="0"> <label for="">Rear Camera</label>
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="scaner_camera_mode">
                        <label for="scaner_camera_mode" class="col-sm-3 control-label">Scaner Camera Mode </label>
                        <div class="col-sm-9" style="margin-top: 8px;">
                            <input type="radio" id="" name="scaner_camera_mode" value="1"> <label for="">Front Camere</label>
                            <input type="radio" id="" name="scaner_camera_mode" value="0"> <label for="">Rear Camera</label>
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div id="template_id" class="form-group">
                        <label for="template_id" class="col-sm-3 control-label">Templates</label>
                        <div class="col-sm-9">
                        <select name="template_id"  id="templates" class="form-control" >
                            <option>Please Select</option>
                        </select>
                            <small class="help-block"></small>
                        </div>
                    </div>

                <!-- </div> -->
            </div>
            <hr>
            <div class="modal-footer" style="text-align: right !important;">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop
@section('js')

<script>
    $(function () {
        $('#customerId').select2();

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#data-table').DataTable({

                ajax: {
                    "url": "{{ route('visitor-log.device.lists') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'customer.client_name_and_number',
                        name: 'customer.client_name_and_number'
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'activation_code',
                        name: 'activation_code'
                    },
                    {
                        data: 'last_active_time',
                        name: 'last_active_time'
                    },
                    {
                        data: 'visitor_log_device_settings.visitor_log_templates.template_name',
                        name: 'visitor_log_device_settings.visitor_log_templates.template_name'
                    },
                    // {
                    //     data: null,
                    //     orderable: false,
                    //     render: function (o) {
                    //        actions = "";
                    //        if(o.visitor_log_device_settings.camera_mode == 1){
                    //          actions = 'Front Camere';
                    //        }else{
                    //         actions = ' Rear Camera';
                    //        }
                    //        return actions;
                    //     },
                    // },
                    // {
                    //     data: null,
                    //     orderable: false,
                    //     render: function (o) {
                    //        actions = "";
                    //        if(o.visitor_log_device_settings.scaner_camera_mode == 1){
                    //          actions = 'Front Camere';
                    //        }else{
                    //          actions = ' Rear Camera';
                    //        }
                    //        return actions;
                    //     },
                    // },
                    // {
                    //     data: 'created_at',
                    //     name: 'created_at'
                    // },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var actions = "";
                           return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $("#devices-form").on("change", "#customerId", function (e) {

            let id = $(this).val();
            var url = '{{ route("visitor-log.template-allocated",":customerId") }}';
            var url = url.replace(':customerId', id);
            $('#devices-form #templates').empty().append($("<option></option>").attr("value", '').text('Please Select'));
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $.each(data, function(index, slot) {
                        $('#templates').append($("<option></option>")
                            .attr("value", slot.id)
                            .text(slot.template_name));
                    });
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });

        });
        /* Office Store - Start*/
        $('#devices-form').submit(function (e) {
            e.preventDefault();
            if($('#devices-form input[name="id"]').val()){
                var message = 'Data has been updated successfully';
            }else{
                var message = 'Data has been created successfully';
            }
            formSubmit($('#devices-form'), "{{ route('visitor-log.device.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Office Edit - Start*/
        $("#data-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("payment-methods.single",":id") }}';
            var url = url.replace(':id', id);
            $('#devices-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#devices-form')[0].reset();

                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="full_name"]').val(data.full_name)
                        $('#myModal input[name="short_name"]').val(data.short_name)

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Payment Method: " + data.full_name)
                    } else {
                        // console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Office Edit - End*/

        /* Office Delete  - Start */
        $('#data-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('payment-methods.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Data has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Office Delete  - End */


    });
</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

@stop
