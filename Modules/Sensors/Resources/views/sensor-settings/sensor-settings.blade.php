@extends('adminlte::page')
@section('title', 'Motion Sensor Settings')
 @section('content_header')
<h1>Motion Sensor Settings</h1>
@stop
@section('content')

<div class="row">
    <div class="col-md-12">
            {{ Form::open(array('url'=>'#','id'=>'motion-sensor-settings','class'=>'form-horizontal', 'method'=> 'POST')) }}
                 {{ csrf_field() }}
                 {{ Form::hidden('id', isset($motion_sensor_settings->id) ? old('room_id',$motion_sensor_settings->id) : null,array('id'=>'id')) }}
                <div class="box-body">
                 <div class="form-group col-md-5" id="motion-sensor-sleep-after-trigger">
                     <div class="col-md-6">
                         <label for="motion_sensor_sleep_after_trigger">Minutes after sensor has to remain inactive after initial trigger</label>
                     </div>
                     <div class="col-md-6">
                         {{ Form::number('motion_sensor_sleep_after_trigger',isset($motion_sensor_settings->sleep_after_trigger) ? old('motion_sensor_sleep_after_trigger',$motion_sensor_settings->sleep_after_trigger) : null,array('class'=>'form-control','max'=>'60','min'=>0))}}
                         <small class="help-block"></small>
                     </div>
                 </div>
                 <div class="form-group col-md-5" id="motion_sensor_tigger_end_after">
                     <div class="col-md-6">
                         <label for="motion_sensor_tigger_end_after">Minutes after sensor has to trigger end event after final trigger</label>
                     </div>
                     <div class="col-md-6">
                         {{ Form::number('motion_sensor_tigger_end_after', isset($motion_sensor_settings->end_trigger_after) ?  old('motion_sensor_tigger_end_after',$motion_sensor_settings->end_trigger_after) : null,array('class'=>'form-control','max'=>'60','min'=>0))}}
                         <small class="help-block"></small>
                     </div>
                 </div>
                 <div class="form-group col-md-2">
                     <div class="col-md-2">
                         {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>''))}}
                         <small class="help-block"></small>
                     </div>
                 </div>
                </div>
            {{ Form::close() }}
    </div>
 </div>

<div class="add-new" data-title="Add Day Time Setting">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="reason-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer Name</th>
            <th>Room Name</th>
            <th>Weekday Start Time</th>
            <th>Weekday End Time</th>
            <th>Weekend Start time</th>
            <th>Weekend End time</th>
            <th>Actions</th>

        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Customers</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'sensor-active-setting-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('cus_id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="customer_id">
                    <label for="customer_id" class="col-sm-3 control-label" style="text-align: left;">Customer Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('customer_id',[''=>'Select a customer']+$customerlist,null,array('class'=>'form-control select2 customer_select','id'=>'customers','onchange'=>'getRoomList()','style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div id="room_id" class="form-group">
                    <label for="room_id" class="col-sm-3 control-label" style="text-align: left;">Room Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('room_id',[''=>'Select a room'],null,array('class'=>'form-control select2 customer_select','id'=>'rooms','style'=>"width: 100%;")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="is_weekday_active">
                    <label for="is_weekday_active" class="col-sm-3 control-label" style="text-align: left;">Weekday Active</label>
                    <div class="col-sm-9">
                        <label class="switch" style="">
                        {{ Form::checkbox('is_weekday_active',1,null, array('class'=>'form-control')) }}
                        <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="week_day_start_time">
                    <label for="start_time" class="col-sm-3 control-label" style="text-align: left;">Weekday Start Time</label>
                    <div class="col-sm-9">
                        {{ Form::time('week_day_start_time',old('week_day_start_time',null),array('class'=>'form-control','required'=>TRUE,'maxlength'=>'50'))}}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="week_day_end_time">
                    <label for="end_time" class="col-sm-3 control-label" style="text-align: left;">Weekday End Time</label>
                    <div class="col-sm-9">
                        {{ Form::time('week_day_end_time',old('week_day_end_time',null),array('class'=>'form-control','required'=>TRUE,'maxlength'=>'50'))}}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="is_weekend_active">
                    <label for="is_weekend_active" class="col-sm-3 control-label" style="text-align: left;">Weekend Active</label>
                    <div class="col-sm-9">
                        <label class="switch" style="">
                        {{ Form::checkbox('is_weekend_active',1,null, array('class'=>'form-control')) }}
                        <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="week_end_start_time">
                    <label for="end_time" class="col-sm-3 control-label" style="text-align: left;">Weekend Start Time</label>
                    <div class="col-sm-9">
                        {{ Form::time('week_end_start_time',old('week_end_start_time',null),array('class'=>'form-control','required'=>TRUE,'maxlength'=>'50'))}}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="week_end_end_time">
                    <label for="end_time" class="col-sm-3 control-label" style="text-align: left;">Weekend End Time</label>
                    <div class="col-sm-9">
                        {{ Form::time('week_end_end_time',old('week_end_end_time',null),array('class'=>'form-control','required'=>TRUE,'maxlength'=>'50'))}}
                     <small class="help-block"></small>
                    </div>
                </div>

            </div>
          <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
          </div>
        {{ Form::close() }}
    </div>
</div>
</div>
@stop @section('js')
<script>
    $(function () {
        $('.select2').select2();
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#reason-table').DataTable({
             bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
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
                        emailContent(table, 'Training');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('motionSensor.active-setting.list') }}",
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
                    sortable:false
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {data: 'room_name', name: 'room_name'},
                {data: 'weekday_start_time', name: 'weekday_start_time'},
                {data: 'weekday_end_time', name: 'weekday_end_time'},
                {data: 'weekend_start_time', name: 'weekend_start_time'},
                {data: 'weekend_end_time', name: 'weekend_end_time'},
                   {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        return actions;
                    },
                }

            ]
        });
          } catch(e){
            console.log(e.stack);
        }

        /* Posting data to Training Controller - Start*/
        $('#sensor-active-setting-form').submit(function (e) {
            e.preventDefault();
            if($('#sensor-active-setting-form input[name="id"]').val()){
                var message = 'Sensor Setting has been updated successfully';
            }else{
                var message = 'Sensor Setting has been created successfully';
            }
            formSubmit($('#sensor-active-setting-form'), "{{ route('motionSensor.active-setting.store') }}", table, e, message);
        });
        /* Posting data to Training Controller - End*/

         /* Course Category Edit - Start */
         $("#reason-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("motionSensor.active-setting.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        $('#myModal select[name="room_id"]').find('option').remove();
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="cus_id"]').val(data.customer_id);
                        $('#myModal select[name="customer_id"] option[value="'+data.customer_id+'"]').prop('selected',true);
                        $('#myModal select[name="room_id"]').append("<option value="+data.id+">"+data.name+"</option>");
                        $('#myModal select[name="room_id"] option[value="'+data.id+'"]').prop('selected',true);
                        $('#myModal input[name="is_weekday_active"]').prop('checked', data.active_sensors[0].is_active);
                        $('#myModal input[name="week_day_start_time"]').val(data.active_sensors[0].start_time);
                        $('#myModal input[name="week_day_end_time"]').val(data.active_sensors[0].end_time);
                        $('#myModal input[name="is_weekend_active"]').prop('checked', data.active_sensors[1].is_active);
                        $('#myModal input[name="week_end_start_time"]').val(data.active_sensors[1].start_time);
                        $('#myModal input[name="week_end_end_time"]').val(data.active_sensors[1].end_time);
                        $('#myModal select[name="customer_id"]').prop('disabled',true);
                        $('#myModal select[name="room_id"]').prop('disabled',true);
                        $(".select2").select2()
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Day Time Setting: "+ data.name)
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
        /* Course Category Edit - End */

        /* Delete Training type- Start*/
        $('#reason-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('leavereasons.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Leave reason has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Delete Training type- End*/

    $('.add-new').click(function(){
        $("#sensor-active-setting-form")[0].reset();
        $("#customers").val('').trigger('change') ;
        $("#rooms").val('').trigger('change') ;
        $('#myModal select[name="room_id"]').find('option').remove();
        $('#myModal select[name="customer_id"]').val('');
        $('#myModal select[name="customer_id"]').prop('disabled',false);
        $('#myModal select[name="room_id"]').prop('disabled',false);
        $('#myModal input[name="is_weekday_active"]').prop('checked', true);
        $('#myModal input[name="is_weekend_active"]').prop('checked', true);
     });

    });
</script>
<script>
    $('#motion-sensor-settings').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData($('#motion-sensor-settings')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('motionSensor.settings.store')}}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    swal("Success", "Settings has been successfully updated", "success");
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    //table.ajax.reload();
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

            function getRoomList(customerId){
            let customer_id = $("#customers").val();
            if(customer_id > 0) {
                let url = '{{ route("motionSensor.active-setting.getroom.list",":id") }}';
            url = url.replace(':id', customer_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        var options = '';
                        $.each(data, function (key, value) {
                            $('#rooms').append("<option value="+value.id+">"+value.name+"</option>");
                            $('#myModal select[name="room_id"] option[value="'+customerId+'"]').prop('selected',true);
                        });
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false
            });
            } else {
                return false;
            }
        }

</script>
@stop
