@extends('adminlte::page')
@section('title', 'Timeoff Settings')
@section('content_header')
<h1>Timeoff Settings</h1>
<style>
.no_of_days_label{
    color:black !important;
}
.has-error .help-block{
    color:red;
}
.has-error .control-label{
    color:black !important;
}
</style>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add Timeoff Settings">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="experience-wise-leave-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Experience Greater Than (In Months)</th>
            <th>No Of Leaves (Quarterly)</th>
            <th>Timeoff Request Type</th>
            <th>Status</th>
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
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'experience-wise-leave-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="min_experience">
                    <label for="min_experience" class="col-sm-3 control-label">Experience Greater than (In Months)</label>
                    <div class="col-sm-8">
                        <select class="form-control has-error" name="min_experience" id ='greater_than_value'></select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="no_of_leaves">
                    <label for="no_of_leaves" class="col-sm-3 control-label">No Of Leaves (Quarterly)</label>
                    <div class="col-sm-8">
                        {{ Form::text('no_of_leaves',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="time_off_request_type_id">
                    <label for="role_id" class="col-sm-3 control-label">Timeoff Request Type</label>
                    <div class="col-sm-8">
                    <select class="form-control" name="time_off_request_type_id" id="time_off_request_type_id">
                        <option value="">Select Type</option>
                        @foreach($timeOffRequestType as $each_type)
                        <option value="{{ $each_type->id}}">{{ $each_type->request_type }}
                        </option>
                        @endforeach
                    </select>
                     <small class="help-block"></small>
                 </div>
                </div>



                <!-- <div class="form-group">
                        <label for="accrual" class="col-sm-3 control-label">Accrual</label>
                        <div id="accrual_day" >
                            <label for="accrual_day" class="col-sm-2 control-label" id="yearly-on" style="margin-left: -2.3em;margin-right: 2.3em;">Yearly &nbsp; &nbsp; On</label>
                            <div class="col-sm-2">
                            {{ Form::select('accrual_day',[null=>'Please Select','1'=>'First Day','2'=>'Last Day'], old('accrual_day'),
                            array(
                                'id'=>"accrual-day",
                                'class'=> 'form-control',
                            )) }}
                            <small class="help-block"></small>
                            </div>
                        </div>
                        <div id="accrual_month" >
                            <label for="accrual_month"></label>
                            <div class="col-sm-2">
                            {{ Form::select('accrual_month',[null=>'Select']+$months, old('accrual_month'),array('class' => 'form-control colorChange','id'=>"accrual-month")) }}
                            <small class="help-block"></small>
                            </div>
                        </div> -->
                        <!-- <div id="no_of_leaves" style="margin-top:-1.3em">
                            <label for="no_of_leaves" class="col-sm-2 control-label " style="margin-left: -2em;" id="no_of_days_label">No of Days</label>
                            <div class="col-sm-1">
                            {{ Form::text('no_of_leaves',null,array('class'=>'form-control colorChange', 'id'=>"leave-id")) }}
                            <small class="help-block"></small>
                            </div>
                        </div> -->

                <!-- </div> -->

                <div class="form-group" id="role_exception">
                        <label for="role_exception" class="col-sm-3 control-label">Role Exception</label>
                        <div class="col-sm-8" style="margin-top: 0.5em;">
                            <label class="switch">
                                <input name="role_exception" type="checkbox" value="1" id="role_button">
                                <span class="slider round"></span>
                            </label>
                            <small class="help-block"></small>
                        </div>
                </div>

                <div class="form-group" id="roles">
                    <label for="roles" class="col-sm-3 control-label">Roles</label>
                    <div class="col-sm-8">
                        <select name="role_id[]" id="role_id" class="select2 form-control selected_role" multiple>
                            @foreach ($rolesList as $roles)
                                <option value="{{$roles->id}}">{{$roles->name}}</option>
                            @endforeach
                        </select>
                    <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" >
                        <label for="reset" class="col-sm-3 control-label">Reset</label>
                        <div id="reset_month" >
                            <label for="reset_month" class="col-sm-2 control-label" id="yearly-on-reset" style="margin-left: -2.3em;margin-right: 2.3em;">Yearly &nbsp; &nbsp; On</label>
                            <div class="col-sm-2">
                            {{ Form::select('reset_month',[null=>'Select']+$months, old('reset_month'),array('class' => 'form-control colorChange','id'=>"reset-month")) }}
                            <small class="help-block"></small>
                            </div>
                        </div>
                        <div id="reset_day" >
                            <label for="reset_day" ></label>
                            <div class="col-sm-2">
                            {{ Form::select('reset_day',[null=>'Please Select','1'=>'First Day','2'=>'Last Day'], old('reset_day'),
                            array(
                                'class'=> 'form-control colorChange',
                                'id'=>"reset-day"
                            )) }}

                            <small class="help-block"></small>
                            </div>
                        </div>
                            <label for="status" class="col-sm-3 control-label no-wrap" style="margin-left: -2em;"></label>
                            <small class="help-block"></small>
                </div>


                <div class="form-group" >
                    <div id="carry_forward_percentage" >
                        <label for="carry_forward_percentage" class="col-sm-3 control-label" id="carry_forward_label">Carry Forward Percentage</label>
                        <div class="col-sm-2" style="margin-top: 0.5em;">
                        {{ Form::text('carry_forward_percentage',null,array('class'=>'form-control colorChange','id'=>"carry-forward")) }}
                        <small class="help-block"></small>
                        </div>
                    </div>
                    <div id="carry_forward_expires_in_month">
                        <label for="carry_forward_expires_in_month" class="col-sm-2 " style="margin-top: 0.5em;">Expires In</label>
                        <div class="col-sm-2" style="margin-top: 0.5em;margin-left:0em">
                        {{ Form::text('carry_forward_expires_in_month',null,array('class'=>'form-control colorChange' ,'id'=>"expiring_month")) }}
                        <small class="help-block"></small>
                        </div>
                    </div>
                        <div class="col-sm-2">
                        <label for="months" class="control-label">Months</label>
                        </div>
                </div>

                <div class="form-group" id="encashment_percentage">
                        <label for="encashment_percentage" class="col-sm-3 control-label">Encashment</label>
                        <div class="col-sm-2" style="margin-top: 0.5em;">
                        {{ Form::text('encashment_percentage',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                        </div>
                        <div class="col-sm-2">
                        <label for="percentage" class="control-label">Percentage</label>
                        </div>
                </div>


                <div class="form-group" id="active">
                        <label for="active" class="col-sm-3 control-label">Status</label>
                        <div class="col-sm-8" style="margin-top: 0.5em;">
                            <label class="switch">
                                <input name="active" type="checkbox" id="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                            <small class="help-block"></small>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'save_experience_wise_leave'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop @section('js')
<script>
     $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#experience-wise-leave-table').DataTable({
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
                        emailContent(table, 'Feedback Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('experience-wise-leave-master.list') }}",
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
                    data: 'min_experience',
                    name: 'min_experience'
                },
                {
                    data: 'no_of_leaves',
                    name: 'no_of_leaves'
                },
                {
                    data: 'time_off_request_type_id',
                    name: 'time_off_request_type_id'
                },
                {data: 'active', name: 'active'},
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
        } catch(e){
            console.log(e.stack);
        }

        $("#role_id").select2({ width: '100%' });

        $('#experience-wise-leave-form').submit(function (e) {
            e.preventDefault();
            var id=$('#experience-wise-leave-form input[name="id"]').val()
            var $form = $(this);
            var formData = new FormData($('#experience-wise-leave-form')[0]);
            var url = "{{route('experience-wise-leave-master.store')}}";

            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                        if($('#experience-wise-leave-form input[name="id"]').val()){
                            var message = 'Timeoff Settings has been updated successfully';
                        }else{
                            var message = 'Timeoff Settings Leave has been created successfully';
                        }
                        swal({
                            title: "Success",
                            text: message,
                            type: "success",
                            confirmButtonText: "OK",
                            },function(){
                                $('#myModal').modal('toggle');
                                $('.form-group').removeClass('has-error').find('.help-block').text('');
                                table.ajax.reload();
                            });

                    } else {
                        console.log(data);
                        swal("Warning", data.error, "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });


        /* Experience Wise Leave Edit - Start */
        $("#experience-wise-leave-table").on("click", ".edit", function (e) {
                $('#myModal select[name="time_off_request_type_id"]').prop('selected',false);
                $('#myModal select[name="role_id[]"] ').val('').change();
                // $('#myModal select[name="accrual_day"]').prop('selected',false);
                // $('#myModal select[name="accrual_month"]').prop('selected',false);
                $('#myModal select[name="reset_month"]').prop('selected',false);
                $('#myModal select[name="reset_day"]').prop('selected',false);
                $('#myModal input[name="carry_forward_percentage"]').val('');
                $('#myModal input[name="carry_forward_expires_in_month"]').val('');
                $('#myModal input[name="encashment_percentage"]').val('');

                document.getElementById("carry-forward").style.borderColor = "#d2d6de";
                document.getElementById("expiring_month").style.borderColor = "#d2d6de";
                document.getElementById("reset-month").style.borderColor = "#d2d6de";
                document.getElementById("reset-day").style.borderColor = "#d2d6de";

                var id = $(this).data('id');
                var url = '{{ route("experience-wise-leave-master.single",":id") }}';
                var url = url.replace(':id', id);
                $('#experience-wise-leave-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                    '');
                $(".has-error .form-control").css({"border-color": "#d2d6de;"});
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal select[name="min_experience"]').val(parseInt(data.min_experience)).select2({ width: '100%',dropdownParent: $("#myModal") });
                        $('#myModal input[name="no_of_leaves"]').val(data.no_of_leaves)
                        $('#myModal select[name="time_off_request_type_id"]').val(data.time_off_request_type_id)
                        // $('#myModal select[name="accrual_day"]').val(data.accrual_day)
                        // $('#myModal select[name="accrual_month"]').val(data.accrual_month)
                        $('#myModal select[name="reset_month"]').val(data.reset_month)
                        $('#myModal select[name="reset_day"]').val(data.reset_day)
                        $('#myModal input[name="carry_forward_percentage"]').val(data.carry_forward_percentage)
                        $('#myModal input[name="carry_forward_expires_in_month"]').val(data.carry_forward_expires_in_month)
                        $('#myModal input[name="encashment_percentage"]').val(data.encashment_percentage)
                        if(data.active == 0){
                            $('#myModal input[name="active"]').prop('checked', false);
                        }else{
                            $("#myModal input[name='active']").prop('checked', true);
                        }
                        if(data.timeoff_roles[0].role_exception == 0){
                            $('#myModal input[name="role_exception"]').prop('checked', false);
                        }else{
                            $('#myModal input[name="role_exception"]').prop('checked', true);
                        }
                        if(data.timeoff_roles!=null && data.timeoff_roles.length>0)
                        {
                            $.each(data.timeoff_roles, function(key, value) {

                            $('#myModal select[name="role_id[]"]  option[value="'+value.role_id+'"]').prop("selected", true).change();
                            });
                        }
                        var isChecked = $('#role_button').is(':checked');
                        if(isChecked){
                            $('#myModal select[name="role_id[]"] ').prop('disabled',false);
                                if(data.timeoff_roles!=null && data.timeoff_roles.length>0)
                                {
                                    $.each(data.timeoff_roles, function(key, value) {
                                        $('#myModal select[name="role_id[]"]  option[value="'+value.role_id+'"]').prop("selected", true).change();
                                    });
                                }
                        }else{
                            $('.selected_role').val('').change();
                            $('#myModal select[name="role_id[]"] ').prop('disabled',true);
                        }
                        $('#role_button').on('change', function(e) {
                            e.preventDefault();
                            $('#myModal select[name="role_id[]"] ').val('').change();
                            var isNotEmptyRole = $('#myModal select[name="role_id[]"] ').val();
                            var isChecked = $('#role_button').is(':checked');
                            if(isChecked){
                                $('#myModal select[name="role_id[]"] ').prop('disabled',false);
                                if(data.timeoff_roles!=null && data.timeoff_roles.length>0)
                                {
                                    $.each(data.timeoff_roles, function(key, value) {
                                        $('#myModal select[name="role_id[]"]  option[value="'+value.role_id+'"]').prop("selected", true).change();
                                    });
                                }
                            }else{
                                $('.selected_role').val('').change();
                                $('#myModal select[name="role_id[]"] ').prop('disabled',true);
                            }
                        });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Experience Wise Leave")
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
        /* Experience Wise Leave Edit - End */

        /* Experience Wise Leave Delete - Start */
        $('#experience-wise-leave-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('experience-wise-leave-master.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Timeoff Settings has been deleted successfully';
            deleteRecord(url, table, message);
        });

        $('.add-new').click(function(){
            $('select#greater_than_value').select2({
                dropdownParent: $("#myModal"),
                width :'100%'
            }).prop('selected',false);
            $('#myModal select[name="time_off_request_type_id"]').prop('selected',false);
            $('#myModal select[name="role_id[]"] ').prop('disabled',true);
            // $('#myModal select[name="accrual_day"]').prop('selected',false);
            // $('#myModal select[name="accrual_month"]').prop('selected',false);
            $('#myModal select[name="reset_month"]').prop('selected',false);
            $('#myModal select[name="reset_day"]').prop('selected',false);
            $('#myModal input[name="carry_forward_percentage"]').val('');
            $('#myModal input[name="carry_forward_expires_in_month"]').val('');
            $('#myModal input[name="encashment_percentage"]').val('');
            $('#myModal select[name="role_id[]"] ').val('').change();
            $('#myModal select[name="reset_month"]').val('12')
            $('#myModal select[name="reset_day"]').val('2')
            document.getElementById("carry-forward").style.borderColor = "#d2d6de";
            document.getElementById("expiring_month").style.borderColor = "#d2d6de";
            document.getElementById("reset-month").style.borderColor = "#d2d6de";
            document.getElementById("reset-day").style.borderColor = "#d2d6de";

        });

        $('#role_button').on('change', function(e) {
            e.preventDefault();
            var isChecked = $('#role_button').is(':checked');
            if(isChecked){
                $('#myModal select[name="role_id[]"] ').prop('disabled',false);
            }else{
                $('.selected_role').val('').change();
                $('#myModal select[name="role_id[]"] ').prop('disabled',true);
            }
        });

        $('select#greater_than_value').select2({
            dropdownParent: $("#myModal"),
            width :'100%'
        });

        var $select = $('#myModal select[name="min_experience"]');
        $select.append($('<option selected disabled></option>').val('Select').html('Select'));
        for (i = 1; i <= 400; i++) {
            $select.append($('<option></option>').val(i).html(i));
        }
        /* Experience Wise Leave Delete - End */
});
</script>
@stop
