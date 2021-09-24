{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<style>

    .view, .sensor-action-icon-margin {
        margin-left: 20px;
    }
    .option-adjust {
        display: inline !important;
        width: 350px !important;
    }
</style>
<h1> Customer Rooms</h1>
@stop
@section('content')
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customerlist,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<div class="add-new" data-title="Add New  Customer Rooms">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Room Name</th>
            <th>Customer</th>
            <th>Total Assigned Sensors</th>
            <th>Severity</th>
            <th>Total Assigned Ip Cameras</th>
            <th>Camera Actions</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Customers Room</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'room_name_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="customer_id">
                    <label for="customer_id" class="col-sm-3 control-label" style="text-align: left;">Customer Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('customer_id',[''=>'Select a customer']+$customerlist,null,array('class'=>'form-control select2 customer_select', 'style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div id="name" class="form-group">
                    <label for="name" class="col-sm-3 control-label" style="text-align: left;">Room Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Room Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="severity_id">
                    <label for="severity_id" class="col-sm-3 control-label" style="text-align: left;">Severity<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('severity_id',['0'=>'Please select severity']+$room_severity,null,array('class'=>'form-control select2', 'style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
            </div>
          <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal', 'onclick'=>"cancel()"))}}
          </div>
        {{ Form::close() }}
    </div>
</div>
</div>
@include('admin::customer.room-link-sensor')
@include('admin::customer.room-unlink-sensor')
@include('admin::customer.room-link-ipcamera')
@include('admin::customer.room-unlink-ipcamera')



@stop
@section('js')
<script>
     function cancel(){
        $('.customer_select').val('').trigger('change');
        $("#sensor-link-form")[0].reset();
        $('#sensor-unlink-form')[0].reset();
        $("#ipcamera-link-form")[0].reset();
        $('#ipcamera-unlink-form')[0].reset();

    }

    function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
            }
    }

    $(function () {
        $('.select2').select2();
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#type-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
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
            ajax: {
                "url":'{{route('customer-rooms.list') }}',
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
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'name',
                    name: 'name'
                    },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'total_assigned_sensors',
                    name: 'total_assigned_sensors',
                },
                {
                    data: 'severity_id',
                    name: 'severity_id'
                },
                {
                    data: 'total_assigned_ipcameras',
                    name: 'total_assigned_ipcameras',
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="ip_camera-link fa fa-link sensor-action-icon-margin" title="Link IP Camera" data-id=' + o.id + '></a>';
                        actions += '<a href="#" class="ip_camera-unlink fa fa-unlink sensor-action-icon-margin" title="Unlink IP Camera" data-id=' + o.id + '></a>';
                        actions += '<a href="{{route("ip_camera.view", ["id" => ""])}}/'+ o.id +'" class="view fa fa-eye sensor-action-icon-margin" title="View Camera"></a>';
                        return actions;
                    },
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        actions += '<a href="{{route("sensors.view", ["id" => ""])}}/'+ o.id +'" class="view fa fa-eye sensor-action-icon-margin" title="View Sensors"></a>';
                        if(o.motion_sensor_enabled !== 0 && o.motion_sensor_incident_subject !== null) {
                            actions += '<a href="#" class="sensor-link fa fa-link sensor-action-icon-margin" title="Link Sensor" data-id=' + o.id + '></a>';
                        } else {
                            actions += '<a href="#" class="sensor-info fa fa-info-circle sensor-action-icon-margin" title="Enable sensor for customer and assign incident subject"></a>';
                        }
                        actions += '<a href="#" class="sensor-unlink fa fa-unlink sensor-action-icon-margin" title="Unlink Sensor" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }

            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            table.ajax.reload();
        });

        /* Posting data to PositionLookupController - Start*/
        $('#room_name_form').submit(function (e) {
            e.preventDefault();
            if($('#room_name_form input[name="id"]').val()){
                var message = 'Room Name has been updated successfully';
            }else{
                var message = 'Room Name has been created successfully';
            }
            formSubmit($('#room_name_form'), "{{ route('customer-rooms.store') }}", table, e, message);
            cancel();
        });




        /*Edit Customer Shift - Start*/
        $("#type-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("customer-rooms.single",":id") }}';
            var url = url.replace(':id', id);
            $('#room_name_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal select[name="customer_id"] option[value="'+data.customer_id+'"]').prop('selected',true);
                        $('#myModal select[name="severity_id"] option[value="'+data.severity_id+'"]').prop('selected',true);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal select[name="customer_id"]').prop('disabled',true);
                        $(".select2").select2()
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Customer Room: " + data.customer. client_name + ' ( ' +data.name + ' ) ');

                    } else {
                        console.log(data);
                        swal("Oops", "Could not save data", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });

        $("#type-table").on("click", ".sensor-info", function (e) {
            swal("Link Disabled", "Please enable motion sensor for the customer and assign incident subject", "warning")
        });

        /* Edit Customer Shift - End*/

        $("#type-table").on("click", ".sensor-link", function (e) {

            var id = $(this).data('id');
            var url = '{{ route("customer-rooms.link-sensor",":id") }}';
            var url = url.replace(':id', id);
            $('#sensor-link-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            // $('#link-modal').find('select[name="sensor_id"]').select2().val('');
            $("#sensors").val('').trigger('change') ;
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('#link-modal select[name="sensor_id[]"]').find('option').remove();
                        $('#link-modal input[name="id"]').val(data.id);
                        $('#link-modal input[name="customer_name"]').val(data.customer.client_name_and_number);
                        $('#link-modal input[name="room_name"]').val(data.name);
                        $.each(data.unlinked_sensors, function( index, value ) {
                            $('#link-modal select[name="sensor_id[]"]').append("<option value="+value.id+">"+value.name+"</option>");
                          });
                        if(data.customer.motion_sensor_enabled == 0 || data.customer.motion_sensor_incident_subject == null) {
                            $('#link-modal #room-warnings').show();
                            $('#link-modal #enable-warning').show();
                        }else{
                            $('#link-modal #room-warnings').hide();
                            $('#link-modal #enable-warning').hide();
                        }
                        if(!data.room_active_configured) {
                            $('#link-modal #room-warnings').show();
                            $('#link-modal #config-warning').show();
                        }else{
                            $('#link-modal #room-warnings').hide();
                            $('#link-modal #config-warning').hide();
                        }
                        $(".select2").select2()
                        $("#link-modal").modal();
                        $('#link-modal .modal-title').text("Link Sensor: "+ data.name)
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

        $("#type-table").on("click", ".ip_camera-link", function (e) {

            var id = $(this).data('id');
            var url = '{{ route("customer-rooms.link-ipcamera",":id") }}';
            var url = url.replace(':id', id);
            $('#ipcamera-link-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data);
                    if (data) {
                        console.log(data);
                        $('#link-ipcamera-modal select[name="ipcamera_id[]"]').find('option').remove();
                        $('#link-ipcamera-modal input[name="id"]').val(data.id);
                        $('#link-ipcamera-modal input[name="customer_name"]').val(data.customer.client_name_and_number);
                        $('#link-ipcamera-modal input[name="room_name"]').val(data.name);
                        $.each(data.unlinked_ipcameras, function( index, value ) {
                            $('#link-ipcamera-modal select[name="ipcamera_id[]"]').append("<option value="+value.id+">"+value.name+"</option>");
                            $('#link-ipcamera-modal select[name="ipcamera_id[]"] option[value="'+value.id+'"]').prop('selected',true);
                        });
                        $(".select2").select2()
                        $("#link-ipcamera-modal").modal();
                        $('#link-ipcamera-modal .modal-title').text("Link IP Camera: "+ data.name)
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


        $('#sensor-link-form').submit(function (e) {
        e.preventDefault();
          var $form = $(this);
         var formData = new FormData($('#sensor-link-form')[0]);
         $.ajax({
                 headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                 url: "{{route('customer-rooms.link-sensor.store')}}",
                 type: 'POST',
                 data:  formData,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                     if (data.success) {
                        $("#link-modal").modal().hide();
                        swal({
                                title: 'Success',
                                text: "Sensor linked successfully",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });
                     } else {
                         //alert(data);
                         swal("Alert", "Something went wrong", "warning");
                     }
                 },
                 error: function (xhr, textStatus, thrownError) {
                     console.log(xhr.status);
                     console.log(thrownError);
                     associate_errors(xhr.responseJSON.errors, $form);
                     swal("Oops", "Something went wrong", "warning");
                 },
             });
     });

     $('#ipcamera-link-form').submit(function (e) {
        e.preventDefault();
         var $form = $(this);
         var formData = new FormData($('#ipcamera-link-form')[0]);
         $.ajax({
                 headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                 url: "{{route('customer-rooms.link-ipcamera.store')}}",
                 type: 'POST',
                 data:  formData,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                     if (data.success) {
                        $("#link-ipcamera-modal").modal().hide();
                        swal({
                                title: 'Success',
                                text: "IP Camera linked successfully",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });
                     } else {
                         swal("Alert", "Something went wrong", "warning");
                     }
                 },
                 error: function (xhr, textStatus, thrownError) {
                     console.log(xhr.status);
                     console.log(thrownError);
                     associate_errors(xhr.responseJSON.errors, $form);
                     swal("Oops", "Something went wrong", "warning");
                 },
             });
     });


        $("#type-table").on("click", ".sensor-unlink", function (e) {

        var id = $(this).data('id');
        var url = '{{ route("customer-rooms.unlink-sensor",":id") }}';
        var url = url.replace(':id', id);
        $('#sensor-unlink-form').find('.form-group').removeClass('has-error').find('.help-block').text(
            '');
        // $('#link-modal').find('select[name="sensor_id"]').select2().val('');
        $("#unlinksensors").val('').trigger('change') ;
        $.ajax({
            url: url,
            type: 'GET',
            data: "id=" + id,
            success: function (data) {
                if (data) {
                    $('#unlink-modal select[name="unlink_sensor_id[]"]').find('option').remove();
                    $('#unlink-modal input[name="room_id"]').val(data.id);
                    $.each(data.linked_sensors, function( index, value ) {
                        $('#unlink-modal select[name="unlink_sensor_id[]"]').append("<option value="+value.id+">"+value.name+"</option>");
                        $('#unlink-modal select[name="unlink_sensor_id[]"] option[value="'+value.id+'"]').prop('selected',true);
                      });
                    $(".select2").select2()
                    $("#unlink-modal").modal();
                    $('#unlink-modal .modal-title').text("Unlink Sensor: "+ data.name)
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

        $("#type-table").on("click", ".ip_camera-unlink", function (e) {

        var id = $(this).data('id');
        var url = '{{ route("customer-rooms.unlink-ipcamera",":id") }}';
        var url = url.replace(':id', id);
        $('#ip_camera-unlink-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        $.ajax({
            url: url,
            type: 'GET',
            data: "id=" + id,
            success: function (data) {
                console.log(data);
                if (data) {
                    $('#unlink-ipcamera-modal select[name="unlink_ipcamera_id[]"]').find('option').remove();
                    $('#unlink-ipcamera-modal input[name="room_id"]').val(data.id);
                    $.each(data.linked_ip_cameras, function( index, value ) {
                        $('#unlink-ipcamera-modal select[name="unlink_ipcamera_id[]"]').append("<option value="+value.id+">"+value.name+"</option>");
                        $('#unlink-ipcamera-modal select[name="unlink_ipcamera_id[]"] option[value="'+value.id+'"]').prop('selected',true);
                    });
                    $(".select2").select2()
                    $("#unlink-ipcamera-modal").modal();
                    $('#unlink-ipcamera-modal .modal-title').text("Unlink IP Camera: "+ data.name)
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

        $('#sensor-unlink-form').submit(function (e) {
        e.preventDefault();
          var $form = $(this);
         var formData = new FormData($('#sensor-unlink-form')[0]);
         $.ajax({
                 headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                 url: "{{route('customer-rooms.unlink-sensor.store')}}",
                 type: 'POST',
                 data:  formData,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                     if (data.success) {
                        $("#unlink-modal").modal().hide();
                        swal({
                                title: 'Success',
                                text: "Sensor unlinked successfully",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });
                     } else {
                         //alert(data);
                         swal("Alert", "Something went wrong", "warning");
                     }
                 },
                 error: function (xhr, textStatus, thrownError) {
                     console.log(xhr.status);
                     console.log(thrownError);
                     associate_errors(xhr.responseJSON.errors, $form);
                     swal("Oops", "Something went wrong", "warning");
                 },
             });
     });

     $('#ipcamera-unlink-form').submit(function (e) {
        e.preventDefault();
         var $form = $(this);
         var formData = new FormData($('#ipcamera-unlink-form')[0]);
         $.ajax({
                 headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                 url: "{{route('customer-rooms.unlink-ipcamera.store')}}",
                 type: 'POST',
                 data:  formData,
                 processData: false,
                 contentType: false,
                 success: function (data) {
                     if (data.success) {
                        $("#unlink-ipcamera-modal").modal().hide();
                        swal({
                                title: 'Success',
                                text: "IP Camera unlinked successfully",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });
                     } else {
                         swal("Alert", "Something went wrong", "warning");
                     }
                 },
                 error: function (xhr, textStatus, thrownError) {
                     console.log(xhr.status);
                     console.log(thrownError);
                     associate_errors(xhr.responseJSON.errors, $form);
                     swal("Oops", "Something went wrong", "warning");
                 },
             });
     });




        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('customer-rooms.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Room name has been deleted successfully';
            deleteRecord(url, table, message);
        });




    });
    $(document).ready(function(){
                // $('#sensors').select2();//Added Select2 to office-ids listing
                // $('#unlinksensors').select2();//Added Select2 to office-ids listing

            });
            $('.add-new').click(function(){
                $('#myModal select[name="customer_id"]').prop('selected',false);
                $('.customer_select').val('').trigger('change');
                $('#myModal select[name="customer_id"]').prop('disabled',false);
     });
</script>
<script>


 </script>
<style>
 a.disabled {
    pointer-events: none;
    cursor: default;
}
</style>
@stop
