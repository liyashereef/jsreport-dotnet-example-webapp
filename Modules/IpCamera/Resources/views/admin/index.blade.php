@extends('adminlte::page')
@section('title', 'IP Camera')
@section('content_header')

    <h1>IP Cameras</h1>
@stop
@section('content')
@if(!isset($roomId) && empty($roomId))
        <div class="add-new" data-title="Add New IP Camera">Add
            <span class="add-new-label">New</span>
        </div>
    @endif
    <table class="table table-bordered" id="type-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Customer</th>
            <th>Room</th>
            <th>IP:Host</th>
            <th>Unique Id</th>
            <th>Online</th>
            <th>Low Battery</th>
            <th>Enabled</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>
    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'ipcamera_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                {{ Form::hidden('room_id', isset($roomId) ? old('room_id',$roomId) : null,array('id'=>'room_id')) }}
                <div class="modal-body">
                    <!-- Active Toggle button - Start -->
{{--                    <div class="form-group col-sm-12" id="enabled">--}}
{{--                        <label class="switch" style="float:right;">--}}
{{--                            {{ Form::checkbox('enabled',1,null, array('class'=>'form-control')) }}--}}
{{--                            <span class="slider round"></span>--}}
{{--                        </label>--}}
{{--                        <label style="float:right;padding-right: 5px;">Active</label>--}}
{{--                    </div>--}}
                    <!-- Active Toggle button - End -->
                    <div class="form-group" id="name">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('name',null,array('class' => 'form-control', 'Placeholder'=>'IP Camera Name', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="credential_username">
                        <label for="credential_username" class="col-sm-3 control-label">Credential Username</label>
                        <div class="col-sm-9">
                            {{ Form::text('credential_username',null,array('class' => 'form-control', 'Placeholder'=>'Credential Username', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                     <div class="form-group" id="credential_password">
                        <label for="credential_password" class="col-sm-3 control-label">Credential Password</label>
                        <div class="col-sm-9">
                            {{ Form::text('credential_password',null,array('class' => 'form-control', 'Placeholder'=>'Credential Password', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="ip">
                        <label for="ip" class="col-sm-3 control-label">Host IP</label>
                        <div class="col-sm-9">
                            {{ Form::text('ip',null,array('class' => 'form-control', 'Placeholder'=>'Host IP', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                     <div class="form-group" id="rtsp_port">
                        <label for="rtsp_port" class="col-sm-3 control-label">RTSP Port</label>
                        <div class="col-sm-9">
                            {{ Form::text('rtsp_port',null,array('class' => 'form-control', 'Placeholder'=>'RTSP Port', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="controller_port">
                        <label for="controller_port" class="col-sm-3 control-label">Controller Port</label>
                        <div class="col-sm-9">
                            {{ Form::text('controller_port',null,array('class' => 'form-control', 'Placeholder'=>'Port')) }}
                            <small class="help-block"></small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                        {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script>

        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                let roomIdVal = $('#room_id').val();
                let id = (roomIdVal !== "" && typeof (roomIdVal) !== undefined) ? roomIdVal : null;
                let url = '';
                if (id !== null) {
                    url = '{{ route('ip_camera.list',[':id']) }}';
                    url = url.replace(':id', id);
                } else {
                    url = '{{ route('ip_camera.list') }}';
                }
                var table = $('#type-table').DataTable({
                    dom: 'lfrtBip',
                    bprocessing: false,
                    buttons: [
                        {
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
                            action: function (e, dt, node, conf) {
                                emailContent(table, 'Positions');
                            }
                        }
                    ],
                    processing: false,
                    serverSide: true,
                    responsive: false,
                    ajax: url,
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
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false,
                    },
                        {
                            data: 'name',
                            name: 'name',
                            defaultContent: "--"
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',
                            defaultContent: "--"
                        },
                        {
                            data: 'room_name',
                            name: 'room_name',
                            defaultContent: "--"
                        },
                        {
                            data: 'ip_port',
                            name: 'ip_port',
                            defaultContent: "--"
                        },
                        {
                            data: 'unique_id',
                            name: 'unique_id',
                            defaultContent: "--"
                        },
                        {
                            data: 'online',
                            name: 'online',
                            defaultContent: "--"
                        },
                        {
                            data: 'low_battery',
                            name: 'low_battery',
                            defaultContent: "--"
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var authorised = '';
                                if (o.enabled == 0) {
                                    authorised = 'Disabled';
                                } else {
                                    authorised = 'Enabled';
                                }

                                return authorised;
                            },
                            name: 'authorised'
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var actions = '';
                                @can('edit_masters')
                                    actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                                @endcan
                                        @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                                @endcan
                                    return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            $('.add-new').click(function () {
                $('#myModal input[name="enabled"]').prop('checked', true);
                $('#myModal input[name="nod_mac"]').prop('disabled', false);
                $('#myModal input:checkbox').prop('disabled', false);
                $('#myModal #room-warnings').hide();
                $('#myModal #enable-warning').hide();

            });

            $('#ipcamera_form').submit(function (e) {
                e.preventDefault();
                if ($('#ipcamera_form input[name="id"]').val()) {
                    var message = 'IP Camera has been updated successfully';
                } else {
                    var message = 'IP Camera has been created successfully';
                }
                formSubmit($('#ipcamera_form'), "{{ route('ip_camera.store') }}", table, e, message);
            });

            $("#type-table").on("click", ".edit", function (e) {
                var id = $(this).data('id');
                var url = '{{ route("ip_camera.single",":id") }}';
                var url = url.replace(':id', id);
                $('#ipcamera_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                    '');
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="id"]').val(data.id);
                            $('#myModal input[name="name"]').val(data.name);
                            $('#myModal input[name="credential_username"]').val(data.credential_username);
                            $('#myModal input[name="credential_password"]').val(data.credential_password);
                            $('#myModal input[name="ip"]').val(data.ip);
                            $('#myModal input[name="rtsp_port"]').val(data.rtsp_port);
                            $('#myModal input[name="controller_port"]').val(data.controller_port);
                            $('#statusMessage').html('');
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Category: " + data.name)
                            if(typeof(data.room_id) != "undefined" && data.room_id !== null) {
                                $('#myModal input[name="credentials"]').prop('readonly',true);
                            }else{
                                $('#myModal input[name="credentials"]').prop('readonly',false);
                            }
                            if(typeof(data.room) != "undefined" && data.room !== null){
                                // if(data.room.customer.motion_sensor_enabled > 0) {
                                //     $('#myModal input:checkbox').prop('checked', data.enabled);
                                //     $('#myModal #room-warnings').hide();
                                //     $('#myModal #enable-warning').hide();
                                //  }else{
                                //     $('#myModal input:checkbox').prop('checked', false);
                                //     $('#myModal input:checkbox').prop('disabled', true);
                                //     $('#myModal #room-warnings').show();
                                //     $('#myModal #enable-warning').show();
                                // }
                            }else{
                                $('#myModal input:checkbox').prop('checked', data.enabled);
                            }
                        } else {
                            alert(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });

            $('#type-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('ip_camera.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'IP Camera has been deleted successfully';
                deleteRecord(url, table, message);
            });


        });
</script>
<style>
        a.disabled {
            pointer-events: none;
            cursor: default;
        }
</style>
@stop
