@extends('layouts.app')
@section('content')
    <div class="table_title">
        <h4>Manual Timesheet Entry</h4>
    </div>
    <div class="container">
        {{ Form::open(array('url'=> '#','id'=>'manual_timesheet_entry_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        <section class="content">
            <div class="row">
                <label class="pt-2 pl-5">Choose Pay Period <span class="mandatory">*</span></label>
                <div class="col-md-2" id="payperiod_id">
                    <select  name="payperiod_id" class="form-control select2" >
                        <option value="" disabled selected>Select Pay Period</option>
                        @foreach($payperiodList as $data)
                            <option value={{$data->id}} @if($data->id == $previousPayperiodDetails['ppid']) selected @endif>
                                {{$data->pay_period_name}}
                                {{!empty($data->short_name)?('('.$data->short_name.')'):''}}
                            </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
                <label class="pt-2 pl-5"> Choose Week  <span class="mandatory">*</span></label>
                <div class="col-md-2" id="payperiod_week">
                    <select name="payperiod_week" class="form-control select2" required>
                        <option value="0" selected>Select Week</option>
                        <option value="1" @if(1 == $previousPayperiodDetails['week']) selected @endif>Week 1</option>
                        <option value="2" @if(2 == $previousPayperiodDetails['week']) selected @endif>Week 2</option>
                    </select>
                    <span class="help-block"></span>
                </div>
                <label class="pt-2 pl-5">  Choose Customer <span class="mandatory">*</span></label>
                <div class="col-md-2" id="customer_id">
                    <select  name="customer_id" class="form-control select2">
                        <option value="" disabled selected>Select Customer</option>
                        @foreach($customerList as $data)
                            <option value={{$data->id}}>{{$data->project_number}} - {{$data->client_name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>


            <table class="table mt-5">
                <thead>
                <tr>
                    <th>Employee</th>
                    <th></th>
                    <th>CPID List</th>
                    <th>Function</th>
                    <th>Activity Type</th>
                    <th>Activity Code</th>
                    <th>Rate/Hour</th>
                    <th>Hours</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="dynamic-rows">
                </tbody>
            </table>

            <div class="modal-footer">
                <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
                <a href="{{ url()->previous() }}" class="btn btn-primary blue">Cancel</a>
            </div>
        </section>
        {{ Form::close() }}
    </div>
    <template id="more-content">
        <tr class="new-fields el_fields" id="--name--_row_--position_num--" data-elid="--position_num--">
            <td id="employee_--position_num--">
                <select  name="employee[--position_num--]" class="form-control employee" onchange="getEmployeeTimesheet('--position_num--',{'method': 'threshold'});">
                    <option value=0 selected>Select Employee</option>
                </select>
                <small class="help-block"></small>
            </td>
            <td style="padding: 0px !important;">
                <i class="fa fa-info-circle pt-4"
                type="button"
                style="cursor: pointer;"
                onclick="getEmployeeTimesheet('--position_num--',{'method': 'showinfo'});"></i>
            </td>
            <td id="cpid_--position_num--">
                <select  name="cpid[--position_num--]" class="form-control cpid" onchange="getFunction('--position_num--');">
                    <option value=0 selected>Select CPID</option>
                </select>
                <small class="help-block"></small>
            </td>
            <td id="function_id_--position_num--">
                <input type="hidden" name="function_id[--position_num--]">
                <input type="text" name="function[]" class="form-control" placeholder="Select Function"  id="function_--position_num--" readonly>
                <small class="help-block"></small>
            </td>
            <td id="work_hour_type_--position_num--">
                <select  name="work_hour_type[--position_num--]" class="form-control workhourtype" onchange="getActivityCode('--position_num--');">
                    <option value=0 selected>Select Activity Type</option>
                </select>
                <small class="help-block"></small>
            </td>
            <td id="activity_code_--position_num--">
                <select  name="activity_code[--position_num--]" class="form-control">
                    <option value=0 selected>Select Activity Code</option>
                </select>
                <small class="help-block"></small>
            </td>
            <td id="rate_value_--position_num--">
                <input type="hidden" name="rate_value[--position_num--]">
                <input type="number" name="rate[]" class="form-control" placeholder="Select Rate"
                       id="rate_--position_num--" readonly>
                <small class="help-block"></small>
            </td>
            <td id="hours_--position_num--">
                <input type="text" name="hours[--position_num--]" class="form-control timemask" placeholder="00:00">
                <small class="help-block"></small>
            </td>
            <td>
                <a title="Add More" href="javascript:;" class="add_button" data-elid="--position_num--">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </td>
            <td>
                <a href="javascript:void(0);" class="remove_button" title="Remove" data-elid="--position_num--">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </a>
            </td>
        </tr>
    </template>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal Header</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

              <div id="thershold" class="p-3">

            </div>
              <p class="text-center">Approved Timesheets</p>
              <table class="table">
                <thead>
              <tr>
                <th scope="col">Project No</th>
                <th scope="col">Project Name</th>
                <th scope="col">Total Hrs</th>
                <th scope="col">Approved By</th>
              </tr>
            </thead>
            <tbody id="bodyid">
            </tbody>
            <tfoot id="footid">
            </tfoot>
            </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('js/moreel.js') }}"></script>
    <script>
        var customerSelected = 0;
        var employeeList= [];
        var cpidList = [];
        var activityTypeList = [];
        var threshold = {{$threshold}};

        $(function() {
            $('.select2').select2();
            dynamicRows();
        }); // document ready

        $('select[name="customer_id"]').on('change', function(e) {
            if ($('select[name="customer_id"]').val() != 0){

                if (customerSelected == 0) {
                    customerData();
                    customerSelected = $('select[name="customer_id"]').val();
                }
                else {
                    customerSelected = $('select[name="customer_id"]').val();
                    swal({
                            title: "Are you sure?",
                            text: "You will not be able to undo this action",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes, Proceed",
                            showLoaderOnConfirm: true,
                            closeOnConfirm: false
                        },
                        function () {
                            customerData();
                        });
                }
            } else {
                customerSelected = $('select[name="customer_id"]').val();
                dynamicRows();
                employeeList = [];
                cpidList = [];
                activityTypeList = [];
                addDropDown();
            }
        }); // customer select

        function addDropDown() {
            // Employee Drop Down
            var employeeOptionList = employeeList.map(employee => `<option value="${employee.id}">${employee.name_with_emp_no}</option>`);
            employeeOptionList = '<option value="0">Select Employee</option>' + employeeOptionList;
            $('.employee').find('option')
                    .remove()
                    .end()
                    .append(employeeOptionList)
                    .select2();

            // CPID dropdown
            var cpidOptionList = cpidList.map(cpid => `<option value="${cpid.cpid}" data-functionId="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.id: null}" data-functionName="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.name : null}">${cpid.cpid_lookup.cpid}</option>`);
            cpidOptionList = '<option value="0">Select CPID</option>' + cpidOptionList;
            $('.cpid').find('option')
                    .remove()
                    .end()
                    .append(cpidOptionList)
                    .select2();

            // Activity Type DropDown
            var activityTypeOptionList = activityTypeList.map(act => `<option value="${act.id}">${act.name}</option>`);
            activityTypeOptionList = '<option value="0">Select Activity Type</option>' + activityTypeOptionList;
            $('.workhourtype').find('option')
                    .remove()
                    .end()
                    .append(activityTypeOptionList)
                    .select2();

            // Activity Code DropDown
            var activityOptionList = '<option value="0">Select Activity Code</option>';
            var activitySelect = document.getElementsByName('activity_code[]');
            activitySelect.forEach(activity => {

                $('#'+ activity.id)
                    .find('option')
                    .remove()
                    .end()
                    .append(activityOptionList)
                    .select2();
            });
        }

        function getActivityCode(positionIndex) {

            if ($('select[name="work_hour_type['+ positionIndex +']"]').val() != 0 && $('select[name="customer_id"]').val() != 0) {
                var customer_id = $('select[name="customer_id"]').val();
                var work_hour_type_id = $('select[name="work_hour_type['+ positionIndex +']"]').val()

                var url = "{{ route('timetracker.manualtimesheetentry.activitycodelist',[':customer_id',':work_hour_type_id']) }}";
                url = url.replace(':customer_id', customer_id);
                url = url.replace(':work_hour_type_id', work_hour_type_id);

                $('select[name="work_hour_type['+ positionIndex +']"]').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            var activityCode = data.activityCode;
                            var activityOptionList = activityCode.map(activity => `<option value="${activity.id}">${activity.code}</option>`);
                            activityOptionList = '<option value="0">Select Activity Code</option>' + activityOptionList;
                            $('select[name="activity_code['+ positionIndex +']"]')
                                .find('option')
                                .remove()
                                .end()
                                .append(activityOptionList)
                                .select2();

                            if (data.activityCode.length == 1) {
                                $('select[name="activity_code['+ positionIndex +']"]').val(data.activityCode[0].id).trigger('change');
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
            }
        }

        function dynamicRows() {
            let divParam = {
                containerDiv: '#dynamic-rows',
                addButton: '.add_button',
                form: '#manual_timesheet_entry_form',
                afterAdd: function (el) {
                    var positionIndex = $(el).attr('data-elid');
                    //employee dropdown
                    var employeeOptionList = employeeList.map(employee => `<option value="${employee.id}">${employee.name_with_emp_no}</option>`);
                    $('#dynamic-rows>tr:last td select[name="employee['+ positionIndex +']"]').append(employeeOptionList);
                    $('#dynamic-rows>tr:last td select[name="employee['+ positionIndex +']"]').select2();

                    //cpid dropdown
                    var cpidOptionList = cpidList.map(cpid => `<option value="${cpid.cpid}" data-functionId="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.id: null}" data-functionName="${cpid.cpid_lookup.cpid_function ? cpid.cpid_lookup.cpid_function.name : null}">${cpid.cpid_lookup.cpid}</option>`);
                    $('#dynamic-rows>tr:last td select[name="cpid['+ positionIndex +']"]').append(cpidOptionList);
                    $('#dynamic-rows>tr:last td select[name="cpid['+ positionIndex +']"]').select2();

                    //Activity Type
                    var activityTypeOptionList = activityTypeList.map(act => `<option value="${act.id}">${act.name}</option>`);
                    $('#dynamic-rows>tr:last td select[name="work_hour_type['+ positionIndex +']"]').append(activityTypeOptionList);
                    $('#dynamic-rows>tr:last td select[name="work_hour_type['+ positionIndex +']"]').select2();

                    //Activity Type
                    $('#dynamic-rows>tr:last td select[name="activity_code['+ positionIndex +']"]').select2();

                    //Hours
                    $('#dynamic-rows>tr:last td .timemask').mask('99:99');
                }
            };

            let moreSteps = new MoreEl('step', divParam);
            moreSteps.initElDiv();
        }

        function customerData() {
            var customer_id = $('select[name="customer_id"]').val();
            var url = '{{ route("timetracker.manualtimesheetentry.employeelist",":customer_id") }}';
            var url = url.replace(':customer_id', customer_id);
            $('#customer_id').find('.form-group').removeClass('has-error').find('.help-block').text('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        // swal("Updated", "Customer has been updated successfully", "success");
                        swal.close();
                        dynamicRows();
                        employeeList = [];
                        cpidList = [];
                        activityTypeList = [];
                        employeeList = data.employeesList;
                        cpidList = data.cpidList;
                        activityTypeList = data.activityList;
                        addDropDown();
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

        function getFunction(positionIndex) {
            var functionName = $('select[name="cpid['+ positionIndex +']"]').children('option:selected').attr('data-functionName');
            var functionId = $('select[name="cpid['+ positionIndex +']"]').children('option:selected').attr('data-functionId');

            if (functionName !== 'null') {
                $('#function_'+positionIndex)
                    .val(functionName)
                    .prop('disabled', true);

                $('input[name="function_id['+ positionIndex +']"]')
                    .val(functionId);
            } else {
                $('#function_'+positionIndex)
                    .val('')
                    .prop('disabled', false);

                $('input[name="function_id['+ positionIndex +']"]')
                    .val('');
            }

            if ( $('select[name="cpid['+ positionIndex +']"]').val() != 0) {
                var cpidValue = $('select[name="cpid['+ positionIndex +']"]').val();
                var url = '{{ route("timetracker.manualtimesheetentry.rate",":cpid") }}';
                var url = url.replace(':cpid', cpidValue);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#rate_'+positionIndex).val(data).prop('disabled', true);
                            $('input[name="rate_value['+ positionIndex +']"]').val(data);
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
                $('#rate_'+positionIndex).val('').prop('disabled', false);
                $('input[name="rate_value['+ positionIndex +']"]').val('');
            }
        }

        $('#manual_timesheet_entry_form').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#manual_timesheet_entry_form')[0]);
            url = "{{ route('timetracker.manualtimesheetentry.store') }}";

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Manual timesheet entry has been created successfully",
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

        function getEmployeeTimesheet(positionIndex, opts) {
            var payperiod = $('select[name="payperiod_id"]').val()
            var week = $('select[name="payperiod_week"]').val();
            var user = $('select[name="employee['+ positionIndex +']"]').children('option:selected').val();
            var userName = $('select[name="employee['+ positionIndex +']"]').children('option:selected').text();

            if (null !== payperiod && week != null && null !== user) {
                var url = "{{ route('timetracker.manualtimesheetentry.employeecheck',[':payperiod',':week', ':user']) }}";
                url = url.replace(':payperiod', payperiod);
                url = url.replace(':week', week);
                url = url.replace(':user', user);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if(data.history.length != 0) {
                            if (opts.method == 'threshold') {
                                if (data.totalHour > threshold) {
                                    $('#myModal').modal('show');
                                    modalBody(userName, data);
                                    $('#thershold')
                                    .append(`<h3 class="text-center text-bold fw-bold">Warning</h3>
                                    <p class="text-center" style="font-weight: 700;">
                                    Total hours of this employee has exceeded the threshold
                                    of ${secondsToTimeString(threshold * 60)} hours</p>`);
                                }
                            }

                            if (opts.method == 'showinfo') {
                                $('#myModal').modal('show');
                                modalBody(userName, data);
                            }
                        } else {
                            if (opts.method == 'showinfo') {
                                swal("Alert", "This employee has no approved timesheets.", "warning");
                            }
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
                if (!payperiod) {
                    swal("Alert", "Please select a pay period", "warning");
                } else if (!week) {
                    swal("Alert", "Please select a week", "warning");
                } else {
                    swal("Oops", "Something went wrong", "warning");
                }
            }
        }

        function modalBody(userName, data) {
            $('#myModal .modal-title').text('Employee Name: '+ userName);
            $('#bodyid').empty();
            $('#footid').empty();
            $('#thershold').empty();
            var row = '';
            data.history.forEach(element => {
                row +=`<tr>
                    <td>${element.project_no}</td>
                    <td>${element.client_name}</td>
                    <td>${secondsToTimeString(element.total_hours * 60)}</td>
                    <td>${element.approved_by}</td>
                </tr>`;
            });
            $('#bodyid').append(row);
            rowtotal =  `<tr>
                    <th>Total Hours</th>
                    <td></td>
                    <td>${secondsToTimeString(data.totalHour * 60)}</td>
                    <td></td>
                </tr>`;
            $('#footid').append(rowtotal);
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
    </script>
@stop
@section('css')
    <style>
        body {
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }
    </style>
@endsection
