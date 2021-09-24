@extends('layouts.app')
@section('content')
    <!-- Style for status -->
    <style>
        .not-approved {
            color: #808080 !important;
        }

        .approved {
            color: #008000 !important;
        }

        .status {
            cursor: default !important;
        }

        .copy {
            margin-top: 20% !important;
        }

        #timesheet-tabs {
            margin: 0px 0px 3px 1px;
        }

        #timesheet-tabs .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #f48452;
        }
        .timesheet-filters{
            background: #f9f1ec;
            padding: 11px 5px;
        }
        .timesheet-filters .filter-text{
            position: absolute;
            top: 1;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .export_btn_ta{
            min-width: 130px;
            text-align: center;
        }
        .fa{
            cursor:pointer
        }
    </style>

    <div class="table_title">
        <h4>Manual Timesheet Report</h4>
    </div>

    <div class="timesheet-filters mb-2">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3"><label class="filter-text">Pay Period</label></div>
                    <div class="col-md-8">
                        <select class="form-control option-adjust timesheet-filter" name="pay-period" id="payperiod-filter">
                            <option value="">All</option>
                            @foreach($payperiod_list as $each_payperiod)
                                <option value="{{$each_payperiod->id}}" @if($each_payperiod->id == $current_payperiod->id) selected
                                        @endif>{{$each_payperiod->pay_period_name}}
                                    {{!empty($each_payperiod->short_name)?('('.$each_payperiod->short_name.')'):''}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Week</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control option-adjust timesheet-filter" id="e-week-filter">
                            <option selected value="">Select Week</option>
                            <option value="1">Week 1</option>
                            <option value="2">Week 2</option>
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Customer</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control option-adjust timesheet-filter" id="e-customer-filter">
                            <option selected value="">Select Customer</option>
                            @foreach($allocated_customers as $allocated_customer)
                                <option value="{{$allocated_customer->id}}">
                                    {{$allocated_customer->project_number}} - {{$allocated_customer->client_name}}</option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Employee</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control option-adjust timesheet-filter" id="e-employee-filter">
                            <option selected value="">Select Employee</option>
                            @foreach($employeeLookupList as $key=>$employees)
                            <option value="{{$key}}">{{$employees}}</option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="add-new mb-3" data-title="Add New Office Address" id="addnew">Add
            <span class="add-new-label">New</span>
        </div>
        <a  href="javascript:void(0)"
            id="timesheet-vision-export"
            class="add-new buttons-excel buttons-html5 export_btn_ta float-right">Vision Export</a>
    </div>

    <div class="container-fluid">
        <table id="resulttable" class="table table-bordered dataTable no-footer dtr-inline" style="width:100%">
            <thead>
            <tr>
                <th></th>
                <th>Employee Id</th>
                <th>Employee Name</th>
                <th>Role</th>
                <th>Project Number</th>
                <th>Client</th>
                <th>Week</th>
                <th>Pay Period</th>
                <th>CPID</th>
                <th>Function</th>
                <th>Activity Type</th>
                <th>Activity Code</th>
                <th>Total Hours</th>
                {{-- @can('show_total_earnings') --}}
                <th>Total Earnings</th>
                {{-- @endcan --}}
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'manualEntryUpdateForm','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id',null) }}
                <div class="modal-body">
                    <div class="form-group" id="employee">
                        <label for="employee" class="col-sm-3 control-label">Employee</label>
                        <div class="col-sm-9">
                            {!!Form::select('employee', [null=>'Please Select']+ $employeeList,null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="payperiod">
                        <label for="employee" class="col-sm-3 control-label">Pay Period</label>
                        <div class="col-sm-9">
                            {!!Form::select('payperiod', [null=>'Please Select']+ $payperiodList,null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="week">
                        <label for="employee" class="col-sm-3 control-label">Week</label>
                        <div class="col-sm-9">
                            {!!Form::select('week', [null=>'Please Select', 1 => 'Week 1', 2 => 'Week 2'],null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="customer">
                        <label for="customer" class="col-sm-3 control-label">Customer</label>
                        <div class="col-sm-9">
                            {!!Form::select('customer', [null=>'Please Select']+$customerList,null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="cpid">
                        <label for="cpid" class="col-sm-3 control-label">CPID List</label>
                        <div class="col-sm-9">
                            {!!Form::select('cpid', [null=>'Please Select'],null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="functionId">
                        <label for="function" class="col-sm-3 control-label">Function</label>
                        <div class="col-sm-9">
                            {{ Form::hidden('functionId',null) }}
                            {{ Form::text('function',null,array('class'=>'form-control required')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="activityType">
                        <label for="activityType" class="col-sm-3 control-label">Activity Type</label>
                        <div class="col-sm-9">
                            {!!Form::select('activityType', [null=>'Please Select']+$activityList,null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="activityCode">
                        <label for="activityCode" class="col-sm-3 control-label">Activity Code</label>
                        <div class="col-sm-9">
                            {!!Form::select('activityCode', [null=>'Please Select'],null, ['class' => 'form-control required'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="rateId">
                        <label for="rate" class="col-sm-3 control-label">Rate/Hour</label>
                        <div class="col-sm-9">
                            {{ Form::hidden('rateId',null) }}
                            {{ Form::text('rate',null,array('class'=>'form-control required')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="hour">
                        <label for="hour" class="col-sm-3 control-label">Hours</label>
                        <div class="col-sm-9">
                            {{ Form::text('hour', null,array('class'=>'form-control required', 'placeholder' => '00:00')) }}
                            <small class="help-block"></small>
                        </div>
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
@stop


@section('scripts')
    <script>
        $(function() {
            ['#payperiod-filter','#e-week-filter','#e-customer-filter','#e-employee-filter'].forEach(function(item){
                $(item).select2();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#resulttable').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    responsive: false,
                    bProcessing: false,
                    ajax: {
                        "url": "{{ route('timetracker.manualtimesheetreport.list') }}",
                        "data": function(d) {
                            return $.extend({}, d, collectFilterData());
                        },
                        "error": function(xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        },
                        'dataFilter': function(response) {
                            return response;
                        },
                    },
                    lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            "visible": false,
                        },
                        {
                            data: 'employee_no',
                            name: 'employee_no'
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: null,
                            name: 'role',
                            render: function(data, type, row, meta) {
                                return uppercase(data.role.replace('_', ' '));
                            },
                        },
                        {
                            data: 'project_number',
                            name: 'project_number'
                        },
                        {
                            data: 'client_name',
                            name: 'client_name'
                        },
                        {
                            data: 'payperiod_week',
                            name: 'payperiod_week'
                        },
                        {
                            data: 'pay_period_name',
                            name: 'pay_period_name'
                        },
                        {
                            data: 'cpid',
                            name: 'cpid'
                        },
                        {
                            data: 'function',
                            name: 'function'
                        },
                        {
                            data: 'activity_type',
                            name: 'activity_type'
                        },
                        {
                            data: 'activity_code',
                            name: 'activity_code'
                        },
                        {
                            data: 'total_hours',
                            name: 'total_hours'
                        },
                        {
                            data: 'total_earnings',
                            name: 'total_earnings',
                            defaultContent:'--'
                        },
                        {
                            data: null,
                            name: 'action',
                            sortable: false,
                            render: function (o) {
                                var actions = '';
                                actions = '<a href="#" class="edit fa fa-edit" data-id=' + o.id + ' onclick="editEntry(this);"></a>&nbsp;&nbsp;';
                                actions += '<a  class="edit fa fa-trash removeEntry" data-id=' + o.id + ' ></a>';
                                return actions;
                            },
                        }
                    ]

                });

            } catch (e) {
                console.log(e.stack);
            }

            $(".timesheet-filter").change(function() {
                table.ajax.reload();
            });

            $('#timesheet-vision-export').click(function(e){
                e.preventDefault();
                let queryString = $.param(collectFilterData());
                let url = "{{route('timesheet.export-approved-vision')}}";
                window.open(url+'?'+queryString,'_blank');
            });

            $('#myModal input[name="hour"]').mask("99:99");
        }); // document ready - end

        $(document).on("click",".removeEntry",function(e){
            e.preventDefault();
            let id=$(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){

                if (isConfirm){
                    swal("Success", id, "success");
                    $.ajax({
                        type: "post",
                        url: "{{route("timetracker.manualtimesheetreport.trash")}}",
                        data:{"id":id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            var data=jQuery.parseJSON(response);
                            if(data.code==200){
                                swal("Success", "Removed successfully", "success");
                            }else{
                                swal("Warning", "System Error", "warning");

                            }
                            $("#payperiod-filter").trigger("change")
                        }
                    });

                } else {
                swal("Cancelled", "Cancelled", "error");
                    e.preventDefault();
                }
            });
        })
        function collectFilterData() {
                return {
                    payperiod: $("#payperiod-filter").val(),
                    customer: $('#e-customer-filter').val(),
                    employee: $('#e-employee-filter').val(),
                    week: $('#e-week-filter').val(),
                    is_manual: 1
                }
            }


        // go to manual timesheet entry form
        $('#addnew').on('click', function(e) {
            let url = "{{ route('timetracker.manualtimesheetentry') }}";
            window.open(url);
        }); // add new

        function editEntry(row) {
            var id=$(row).attr('data-id');
            var url= '{{route("timetracker.manualtimesheetreport.edit",":id")}}';
            var url = url.replace(":id", id);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:url,
                type: 'GET',
                success: function (data) {
                    $('#myModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
                    if (data) {
                        //id
                        $('#myModal input[name="id"]').val(data.entry.id);
                        // employee
                        $('#myModal select[name="employee"]').val(data.entry.user_id).select2();
                        // pay period
                        $('#myModal select[name="payperiod"]').val(data.entry.payperiod_id).select2();
                        // week
                        $('#myModal select[name="week"]').val(data.entry.payperiod_week).select2();
                        // customer
                        $('#myModal select[name="customer"]').val(data.entry.customer_id).select2();

                        // cpid select
                        var cpidOptionList = [];
                        cpidOptionList = data.cpidList.map(cpid => `<option value="${cpid.cpid}" data-functionId="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.id: null}" data-functionName="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.name : null}">${cpid.cpid_lookup.cpid}</option>`);
                        cpidOptionList = '<option value="0">Select CPID</option>' + cpidOptionList;
                        $('#myModal select[name="cpid"]')
                        .find('option').remove().end()
                        .append(cpidOptionList);
                        $('#myModal select[name="cpid"]').val(data.cpid).select2();

                        // function select
                        var functionName = $('#myModal select[name="cpid"]').children('option:selected').attr('data-functionName');
                        $('#myModal input[name="function"]').val(functionName).prop('disabled', true);
                        var functionId = $('#myModal select[name="cpid"]').children('option:selected').attr('data-functionId');
                        $('#myModal input[name="functionId"]').val(functionId);

                        // activity type
                        $('#myModal select[name="activityType"]').val(data.entry.work_hour_type_id).select2();

                        //Activity Code
                        var activityOptionList = [];
                        activityOptionList = data.activityCode.map(activity => `<option value="${activity.id}">${activity.code}</option>`);
                        activityOptionList = '<option value="0">Select Activity Code</option>' + activityOptionList;
                        $('#myModal select[name="activityCode"]')
                        .find('option').remove().end()
                        .append(activityOptionList);
                        $('#myModal select[name="activityCode"]').val(data.entry.work_hour_activity_code_customer_id).select2();

                        // rate
                        $('#myModal input[name="rate"]').val(data.rate).prop('disabled', true);
                        $('#myModal input[name="rateId"]').val(data.rate);
                        // hour
                        var total_time = secondsToTimeString(data.entry.hours * 60);
                        $('#myModal input[name="hour"]').val(total_time);
                        $('#myModal').modal();
                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
        }

        function secondsToTimeString(seconds) {
            var seconds = parseInt(seconds, 10); // don't forget the second param
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds - (hours * 3600)) / 60);
            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            return hours + ':' + minutes;
        }

        $('#myModal select[name="customer"]').on('change', function() {
                $('#myModal select[name="cpid"]').val(0).trigger('change');
                $('#myModal input[name="function"]').val('').prop('disabled', false);
                $('#myModal input[name="functionId"]').val('');
                $('#myModal input[name="rate"]').val('').prop('disabled', false);
                $('#myModal input[name="rateId"]').val(null);

            if ($('#myModal select[name="customer"]').val() != 0){

                var customer_id=$('#myModal select[name="customer"]').val();
                var url= '{{route("timetracker.manualtimesheetreport.edit.customer",":customer_id")}}';
                var url = url.replace(":customer_id", customer_id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            var cpidOptionList = [];
                            cpidOptionList = data.map(cpid => `<option value="${cpid.cpid}" data-functionId="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.id: null}" data-functionName="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.name : null}">${cpid.cpid_lookup.cpid}</option>`);
                            cpidOptionList = '<option value="0">Select CPID</option>' + cpidOptionList;
                            $('#myModal select[name="cpid"]')
                            .find('option').remove().end()
                            .append(cpidOptionList);
                            activityCode();
                        } else {
                            console.log('error in else',data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    },
                });
            }
        });

        $('#myModal select[name="cpid"]').on('change', function() {
            if ($('#myModal select[name="cpid"]').val() != 0){

                $('#myModal input[name="function"]').val('').prop('disabled', false);
                $('#myModal input[name="functionId"]').val('');
                $('#myModal input[name="rate"]').val('').prop('disabled', false);
                $('#myModal input[name="rateId"]').val(null);

                var functionName = $('#myModal select[name="cpid"]').children('option:selected').attr('data-functionName');
                var functionId = $('#myModal select[name="cpid"]').children('option:selected').attr('data-functionId');

                if (functionName != 'null') {
                    $('#myModal input[name="function"]').val(functionName).prop('disabled', true);
                    $('#myModal input[name="functionId"]').val(functionId);
                }

                var cpidValue = $('#myModal select[name="cpid"]').val();
                var url = '{{ route("timetracker.manualtimesheetentry.rate",":cpid") }}';
                var url = url.replace(':cpid', cpidValue);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="rate"]').val(data).prop('disabled', true);
                            $('#myModal input[name="rateId"]').val(data);
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
            } else {
                $('#myModal input[name="function"]').val('').prop('disabled', false);
                $('#myModal input[name="functionId"]').val('');
                $('#myModal input[name="rate"]').val('').prop('disabled', false);
                $('#myModal input[name="rateId"]').val(null);
            }
        });

        $('#myModal select[name="activityType"]').on('change', function() {
            if ($('#myModal select[name="customer"]').val() !== null) {
                activityCode();
            }
        });

        function activityCode() {

            if ($('#myModal select[name="customer"]').val() !== null && $('#myModal select[name="activityType"]').val() !== null) {
                var customer_id = $('#myModal select[name="customer"]').val();
                var work_hour_type_id = $('#myModal select[name="activityType"]').val();

                var url = "{{ route('timetracker.manualtimesheetreport.activitycodelist',[':customer_id',':work_hour_type_id']) }}";
                url = url.replace(':customer_id', customer_id);
                url = url.replace(':work_hour_type_id', work_hour_type_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            var activityOptionList = [];
                            activityOptionList = data.activityCode.map(activity => `<option value="${activity.id}">${activity.code}</option>`);
                            activityOptionList = '<option value="0">Select Activity Code</option>' + activityOptionList;
                            $('#myModal select[name="activityCode"]')
                            .find('option').remove().end()
                            .append(activityOptionList);
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

            }
        }

        //Update the entry
        $('#manualEntryUpdateForm').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#manualEntryUpdateForm')[0]);
            url = "{{ route('timetracker.manualtimesheetreport.update') }}";

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        $('#myModal').modal('hide');
                        swal({
                            title: "Updated",
                            text: "Manual Timesheet Entry has been updated successfully",
                            type: "success",
                            confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('timetracker.manualtimesheetreport') }}";
                        });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        })

    </script>
@stop
