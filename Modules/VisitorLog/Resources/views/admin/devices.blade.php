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

    .select2-container {
        width: 100% !important;
    }

    .help-block {
        color: #dd4b39;
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
            <th>Pin</th>
            <th style="white-space: nowrap;">Activation Code</th>
            <th>Activated On</th>
            <!-- <th>Activated By</th> -->
            <th>Last Active</th>
            <th>Template</th>
            <th>Screening</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'devices-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <!-- <div class="col-md-12"> -->
                <div id="customer_id" class="form-group row">
                    <label for="customer_id" class="col-sm-3 control-label">Customer</label>
                    <div class="col-sm-9">
                        <!-- {{ Form::select('customer_id',[''=>'Select Customer']+$customers,old('customer_id'),array('class'=> 'form-control select2', 'id'=>'customerId','style'=>'width: 591px;')) }} -->
                        <select class="form-control option-adjust client-filter select2" name="customer_id" id="customerId">
                            <option value="">Select Customer</option>
                            @foreach($customers as $key=>$customer)
                                <option value="{{ $key}}">{{ $customer }} </option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="template_id" class="form-group row">
                    <label for="template_id" class="col-sm-3 control-label">Template</label>
                    <div class="col-sm-9">
                        <select name="template_id" id="templates" class="form-control">
                            <option>Please Select</option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-3 control-label">Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,['class' => 'form-control','id'=>'note','rows'=>'3']) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="pin">
                    <label for="pin" class="col-sm-3 control-label">Pin</label>
                    <div class="col-sm-9">
                        {{ Form::text('pin',null,array('class'=>'form-control','placeholder' => 'Device Pin')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="camera_mode">
                    <label for="camera_mode" class="col-sm-3 control-label">Camera Mode </label>
                    <div class="col-sm-9" style="margin-top: 8px;">
                        <input type="radio" id="front-camera" name="camera_mode" value="1"> <label for="">Front Camere</label>
                          <input type="radio" id="rear-camera" name="camera_mode" value="0"> <label for="">Rear Camera</label>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="scaner_camera_mode">
                    <label for="scaner_camera_mode" class="col-sm-3 control-label">Scaner Camera Mode </label>
                    <div class="col-sm-9" style="margin-top: 8px;">
                        <input type="radio" id="front-scaner" name="scaner_camera_mode" value="1"> <label for="">Front Camere</label>
                          <input type="radio" id="rear-scaner" name="scaner_camera_mode" value="0"> <label for="">Rear Camera</label>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="checkout_mode">
                    <label for="checkout_mode" class="col-sm-3 control-label">Checkout Mode </label>
                    <div class="col-sm-9" style="margin-top: 8px;">
                        <input type="radio" id="email" name="checkout_mode" value="1"> <label for="">Email</label>
                        <input type="radio" id="searchPick" name="checkout_mode" value="2"> <label for="">Search  Pick</label>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="screening_enabled">
                    <label for="screening_enabled" class="col-sm-3 control-label">Screening enabled</label>
                    <div class="col-sm-9">
                        <label class="switch" style="">
                            <input id="screeningEnabled" name="screening_enabled" type="checkbox" value="1">
                            <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>

            </div>

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
    $(function() {
        templateId = '';
        $('#customerId').select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#data-table').DataTable({
                ajax: {
                    "url": "{{ route('visitor-log.device.lists') }}",
                    "error": function(xhr, textStatus, thrownError) {
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
                        data: 'visitor_log_device_settings.pin',
                        name: 'visitor_log_device_settings.pin'
                    },
                    {
                        data: 'activation_code',
                        name: 'activation_code'
                    },
                    {
                        data: 'activated_at',
                        name: 'activated_at'
                    },

                    // {
                    //     data: null,
                    //     orderable: false,
                    //     render: function(o) {
                    //         var actions = "";
                    //         if(o.is_activated == 1){
                    //             actions = o.activated_by.name_with_emp_no;
                    //         }else{
                    //             actions = ""
                    //         }
                    //         return actions;
                    //     },
                    // },
                    {
                        data: 'last_active_time',
                        name: 'last_active_time'
                    },
                    {
                        data: 'visitor_log_device_settings.visitor_log_templates.template_name',
                        name: 'visitor_log_device_settings.visitor_log_templates.template_name'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(o) {
                            var actions = "";
                            if (o.screening_enabled == 1) {
                                actions = 'Yes';
                            } else {
                                actions = "No"
                            }
                            return actions;
                        },
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(o) {
                            var actions = "";
                            @can('edit_masters')
                            actions += '<a href="#" title="Edit" class="edit {{Config::get('
                            globals.editFontIcon ')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            if (o.is_activated == 0) {
                                actions += '<a href="#" title="Delete" class="delete {{Config::get('
                                globals.deleteFontIcon ')}}" data-id=' + o.id + '></a>';
                            } else {
                                if (o.is_blocked == 0) {
                                    actions += '<a href="#" title="Block" class="block {{Config::get('
                                    globals.blockFontIcon ')}}" data-id=' + o.id + ' data-block=' + o.is_blocked + '></a>';
                                } else {
                                    actions += '<a href="#" title="Activate" class="block {{Config::get('
                                    globals.activateFontIcon ')}}" data-id=' + o.id + ' data-block=' + o.is_blocked + '></a>';
                                }
                            }
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $("body").on("click", ".add-new", function(e) {
            templateId = '';
            $('#devices-form')[0].reset();
            $("#myModal #customerId").prop('disabled', false);
            $("#myModal #customerId").val('').trigger('change');
            $('#devices-form #templates').empty()
                .append($("<option></option>")
                    .attr("value", '')
                    .text('Please Select'));
            $("#myModal #templates").val('');
            $('#myModal input[name="pin"]').val(Math.floor(Math.random() * 100000))

        });

        $("#devices-form").on("change", "#customerId", function(e) {
            let id = $(this).val();
            if (id != '') {
                var url = '{{ route("visitor-log.template-allocated",":customerId") }}';
                var url = url.replace(':customerId', id);
                $('#devices-form #templates').empty().append($("<option></option>").attr("value", '').text('Please Select'));
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $.each(data, function(index, slot) {
                            if (templateId && slot.id == templateId) {
                                $('#templates').append($("<option></option>")
                                    .attr("value", slot.id)
                                    .attr("selected", true)
                                    .text(slot.template_name));
                            } else {
                                $('#templates').append($("<option></option>")
                                    .attr("value", slot.id)
                                    .text(slot.template_name));
                            }
                        });
                    },
                    error: function(xhr, textStatus, thrownError) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                    contentType: false,
                    processData: false,
                });

            }

            // alert(templateId+' template fetch');
            // $("#templates").val(templateId).change();
        });
        /* Office Store - Start*/
        $('#devices-form').submit(function(e) {
            e.preventDefault();
            if ($('#devices-form input[name="id"]').val()) {
                var message = 'Data has been updated successfully';
            } else {
                var message = 'Data has been created successfully';
            }
            formSubmit($('#devices-form'), "{{ route('visitor-log.device.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Office Edit - Start*/
        $("#data-table").on("click", ".edit", function(e) {
            id = $(this).data('id');
            var url = '{{ route("visitor-log.device.single",":id") }}';
            var url = url.replace(':id', id);
            $('#devices-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#devices-form')[0].reset();
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input[name="pin"]').val(data.visitor_log_device_settings.pin)
                        $('#myModal textarea[name="description"]').val('');
                        $('#myModal textarea[name="description"]').val(data.description);
                        $('#myModal input[name="screening_enabled"]').prop('checked', data.screening_enabled);
                        if (data.visitor_log_device_settings.camera_mode == 1) {
                            $("#myModal #front-camera").prop("checked", true);
                        } else {
                            $("#myModal #rear-camera").prop("checked", true);
                        }
                        if (data.visitor_log_device_settings.scaner_camera_mode == 1) {
                            $("#myModal #front-scaner").prop("checked", true);
                        } else {
                            $("#myModal #rear-scaner").prop("checked", true);
                        }
                        if (data.checkout_mode == 1) {
                            $("#myModal #email").prop("checked", true);
                        } else if (data.checkout_mode == 2) {
                            $("#myModal #searchPick").prop("checked", true);
                        }else {

                        }
                        templateId = data.visitor_log_device_settings.template_id;
                        $("#myModal #customerId").val(data.customer_id).trigger('change');
                        $("#myModal #customerId").prop('disabled', true);

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Device: " + data.name)
                    } else {
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Office Edit - End*/

        /* Office Delete  - Start */
        $('#data-table').on('click', '.delete', function(e) {
            var id = $(this).data('id');
            var base_url = "{{ route('visitor-log.device.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Data has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Office Delete  - End */

        /* Form submit - Start */
        $('#data-table').on('click', '.block', function(e) {
            var $form = $('#devices-form');
            var id = $(this).data('id');
            var block = $(this).data('block');
            var base_url = "{{ route('visitor-log.device.change-status',':id') }}";
            var url = base_url.replace(':id', id);
            var e = e;
            // var formData = new FormData($form[0]);
            if (block == 0) {
                message = 'Do you want to block the device. Proceed?'
            } else {
                message = 'Do you want to activate the device. Proceed?'
            }
            swal({
                    title: "Are you sure?",
                    text: message,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function() {
                    return new Promise(function(resolve, reject) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: url,
                            type: 'GET',
                            success: function(data) {
                                if (data.success) {
                                    swal("Saved", 'Successfully updated', "success");
                                    $("#myModal").modal('hide');
                                    if (table != null) {
                                        table.ajax.reload();
                                    }
                                } else if (data.success == false) {
                                    if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                                        swal("Warning", data.message, "warning");
                                    } else if (Object.prototype.hasOwnProperty.call(data, 'error') && data.error) {
                                        swal("Warning", "Something went wrong", "warning");
                                    } else {
                                        console.log(data);
                                    }
                                } else {
                                    console.log(data);
                                }
                                resolve(data);
                            },
                            fail: function(response) {
                                resolve();
                            },
                            error: function(xhr, textStatus, thrownError) {
                                // associate_errors(xhr.responseJSON.errors, $form);
                                resolve();
                            },
                            always: function() {
                                resolve();
                            },
                            contentType: false,
                            processData: false,
                        });
                    });
                });
        });
        /* Form submit - End */
    });
</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

@stop
