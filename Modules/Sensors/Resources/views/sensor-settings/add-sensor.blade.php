{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
    <h1> Sensors </h1>
@stop
@section('content')
    @if(!isset($roomId) && empty($roomId))
        <div class="add-new" data-title="Add New Sensor">Add
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
            <th>Node Mac Address</th>
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
                {{ Form::open(array('url'=>'#','id'=>'sensor_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                {{ Form::hidden('room_id', isset($roomId) ? old('room_id',$roomId) : null,array('id'=>'room_id')) }}
                <div class="modal-body">
                    <!-- Active Toggle button - Start -->
                    <div class="form-group col-sm-12" id="enabled">
                        <label class="switch" style="float:right;">
                            {{ Form::checkbox('enabled',1,null, array('class'=>'form-control')) }}
                            <span class="slider round"></span>
                        </label>
                        <label style="float:right;padding-right: 5px;">Active</label>
                    </div>
                    <!-- Active Toggle button - End -->
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label"></label>
                        <div class="col-sm-9">
                            <small class="help-block"></small>
                             <div id="room-warnings" style="background-color: lightgoldenrodyellow; padding: 5px; display: none"> Following issues might cause the sensor triggers to be ignored by system.
                            <ul>
                                <li id="enable-warning" style="display: none">
                                    <small class="warning-block" style="color: red">Please enable motion sensor in customer settings and assign incident subject</small>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <div class="form-group" id="name">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('name',null,array('class' => 'form-control', 'Placeholder'=>'Sensor Name', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="nod_mac">
                        <label for="nod_mac" class="col-sm-3 control-label">Node Mac</label>
                        <div class="col-sm-9">
                            {{ Form::text('nod_mac',null,array('class' => 'form-control', 'Placeholder'=>'Node Mac', 'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="pan_mac">
                        <label for="pan_mac" class="col-sm-3 control-label">Pan Mac</label>
                        <div class="col-sm-9">
                            {{ Form::text('pan_mac',null,array('class' => 'form-control', 'Placeholder'=>'Node Mac')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="gateway_mac">
                        <label for="gateway_mac" class="col-sm-3 control-label">Gateway Mac</label>
                        <div class="col-sm-9">
                            {{ Form::text('gateway_mac',null,array('class' => 'form-control', 'Placeholder'=>'Gateway Mac')) }}
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
@stop @section('js')
    <script>

        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                let roomIdVal = $('#room_id').val();
                let id = (roomIdVal !== "" && typeof (roomIdVal) !== undefined) ? roomIdVal : null;
                let url = '';
                if (id !== null) {
                    url = '{{ route('sensors.list',[':id']) }}';
                    url = url.replace(':id', id);
                } else {
                    url = '{{ route('sensors.list') }}';
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
                    responsive: true,
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
                            data: 'nod_mac',
                            name: 'nod_mac',
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
                                    actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                                @endcan
                                        @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                                @endcan
                                    return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            /* Posting data to PositionLookupController - Start*/
            $('#sensor_form').submit(function (e) {
                e.preventDefault();
                if ($('#sensor_form input[name="id"]').val()) {
                    var message = 'Sensor Name has been updated successfully';
                } else {
                    var message = 'Sensor Name has been created successfully';
                }
                formSubmit($('#sensor_form'), "{{ route('sensors.store') }}", table, e, message);
            });


            $("#type-table").on("click", ".edit", function (e) {
                var id = $(this).data('id');
                var url = '{{ route("sensors.single",":id") }}';
                var url = url.replace(':id', id);
                $('#sensor_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                    '');
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="id"]').val(data.id);
                            $('#myModal input[name="name"]').val(data.name);
                            $('#myModal input[name="nod_mac"]').val(data.nod_mac);
                            $('#myModal input[name="nod_mac"]').prop('readonly',true);
                            $('#myModal input[name="pan_mac"]').val(data.pan_mac);
                            $('#myModal input[name="gateway_mac"]').val(data.gateway_mac);
                            $('#myModal input:checkbox').prop('checked', data.enabled);
                            $('#myModal input:checkbox').prop('disabled', false);
                            $('#statusMessage').html('');
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Category: " + data.name)
                            if(typeof(data.room_id) != "undefined" && data.room_id !== null) {
                                $('#myModal input[name="nod_mac"]').prop('readonly',true);
                            }else{
                                $('#myModal input[name="nod_mac"]').prop('readonly',false);
                            }
                            if(typeof(data.room) != "undefined" && data.room !== null){
                                if(data.room.customer.motion_sensor_enabled > 0) {
                                    $('#myModal input:checkbox').prop('checked', data.enabled);
                                    $('#myModal #room-warnings').hide();
                                    $('#myModal #enable-warning').hide();
                                 }else{
                                    $('#myModal input:checkbox').prop('checked', false);
                                    $('#myModal input:checkbox').prop('disabled', true);
                                    $('#myModal #room-warnings').show();
                                    $('#myModal #enable-warning').show();
                                }
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
                var base_url = "{{ route('sensors.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Sensor has been deleted successfully';
                deleteRecord(url, table, message);
            });
            $('.add-new').click(function () {
                $('#myModal input[name="enabled"]').prop('checked', true);
                $('#myModal input[name="nod_mac"]').prop('disabled', false);
                $('#myModal input:checkbox').prop('disabled', false);
                $('#myModal #room-warnings').hide();
                $('#myModal #enable-warning').hide();

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
