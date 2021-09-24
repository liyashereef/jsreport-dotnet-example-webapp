@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Time Off Request Form</h4>
</div>
<section>
    {{-- Time off form starts here --}}
    <div class="row">
        {{-- <div class="col-sm-1"></div> --}}
        <div class="col-sm-11">
            {{-- Project Number Starts Here --}}
            <div class="form-group row">
                <div class="col-sm-3 pl-4">
                    <label for="project_number">Project Number</label>
                </div>
                <div class="col-sm-9 text-sm-left">
                    <select id="project_number" class="form-control">
                        <option value="-1">Select a Customer</option>
                        @foreach ($customerlist as $customers)
                        <option value="{{ $customers->id}}">{{$customers->project_number}} - {{$customers->client_name}}</option>
                        @endforeach
                    </select>
                    <small class="help-block"></small>
                </div>
            </div>
            {{-- Project Number Ends Here --}}

            {{-- Site Name stars here --}}
            {{-- <div class="form-group row">
                <div class="col-sm-6 pl-5">
                    <label for="site_name">Site Name</label>
                </div>
                <div class="col-sm-9 text-sm-left">
                    {{ Form::text('site_name',$value = null,array('class'=>'form-control','maxlength'=>'50','required'=>true))}}
            <small class="help-block"></small>
        </div>
    </div> --}}
    {{-- Site Name Ends Here --}}

    {{-- Employee Id Starts here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="employee_id">Employee</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <select id="employee_id" class="form-control">
                <option value="-1">Select an Employee</option>
                @foreach ($employeelist as $key=>$value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <small class="help-block"></small>
        </div>
    </div>
    {{-- Employee Id ends here --}}

    {{-- pay roll at the site starts here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="your_pay_rate_at_the_site">Your Pay Rate at the Site</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <select id="your_pay_rate_at_the_site" class="form-control">
                <option value="-1">No Rates to Show</option>
            </select>
        </div>
    </div>
    {{-- Pay roll at the site ends here --}}

    {{-- Start date starts here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="start_date">Start Date</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <input id="start_date" class="form-control datepicker" placeholder="Start Date" type="text">
            <small class="help-block"></small>
        </div>
    </div>
    {{-- Start date Ends here --}}

    {{-- Start time stars here            --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="start_time">Start Time</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <input id="start_time" class="form-control" placeholder="Start Time" type="text">
            <small class="help-block"></small>
        </div>
    </div>
    {{-- Start time ends here --}}

    {{-- End date starts here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="end_date">End Date</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <input id="end_date" class="form-control datepicker" placeholder="End Date" type="text">
            <small class="help-block"></small>
        </div>
    </div>
    {{-- End date ends here --}}

    {{-- End time starts here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="end_time">End Time</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <input id="end_time" class="form-control" placeholder="End Time" type="text">
            <small class="help-block"></small>
        </div>
    </div>
    {{-- end time ends here --}}

    {{-- reasons start here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4">
            <label for="reason">Reason</label>
        </div>
        <div class="col-sm-9 text-sm-left">
            <select id="timeoff_reason" class="form-control">
                <option value="-1">Select a reason</option>
                @foreach ($reasonlist as $reasons)
                <option value="{{ $reasons->id}}">{{$reasons->reason}}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{-- Reasons ends here --}}
    <div class="form-group row">
        <div class="col-sm-3 pl-4"></div>
        <div class="col-sm-6">
            <input id="submit" class="btn btn-primary" type="button" value="Submit">
        </div>
    </div>
</div>
<div class="col-sm-1"></div>
</div>
</section>
{{-- Time off form ends here --}}
@endsection
@section('scripts')
{{-- removing button default style --}}
<style>
    .btn:focus,.btn:active {
        outline: none !important;
        box-shadow: none;
    }
</style>

{{-- swal url --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
$(function () {

    $('#start_date').datepicker({
        "format": "yyyy-mm-dd",
        "setDate": new Date(),
    });

    $('#end_date').datepicker({
        "format": "yyyy-mm-dd",
        "setDate": new Date(),
    });

    $('#start_time').timepicki({
        start_time: ["09", "00", "AM"]
    });

    $('#end_time').timepicki({
        start_time: ["10", "00", "AM"]
    });

    $('#employee_id').select2();
    $('#project_number').select2();
    $('#timeoff_reason').select2();
    $('#your_pay_rate_at_the_site').select2();
});


$('#start_date').on('change', function (evt) {
    var selectedDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    if (selectedDate < today) {
        $('#start_date').val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops',
            text: "Please select today's date",
        });
    }

    if (endDate != '' && endDate < selectedDate) {
        $('#start_date').val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops',
            text: 'End date is less than start date',
        });
    }
});

$('#end_date').on('change', function (evt) {
    var selectedDate = $('#end_date').val();
    var startDate = $('#start_date').val();

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    if (selectedDate < today) {
        $('#end_date').val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops',
            text: "Please select today's date",
        });
    }
    if (startDate > selectedDate) {
        $('#end_date').val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops',
            text: 'Start date is greater than end date',
        });
    }
});

$(document).ready(function () {
//submit time off form starts here
    $("#submit").on("click", function () {
        //change time to 24hr function
        function am_pm_to_hours(time) {
            if (time == '') {
                return false;
            }
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            var AMPM = time.match(/\s(.*)$/)[1];
            if (AMPM == "PM" && hours < 12)
                hours = hours + 12;
            if (AMPM == "AM" && hours == 12)
                hours = hours - 12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if (hours < 10)
                sHours = "0" + sHours;
            if (minutes < 10)
                sMinutes = "0" + sMinutes;
            return (sHours + ':' + sMinutes);
        }

        $projectNumber = $('#project_number').val();
        $employeeId = $('#employee_id').val();
        $pay = $('#your_pay_rate_at_the_site').val();
        $startdate = $('#start_date').val();
        $starttime = am_pm_to_hours($('#start_time').val());
        $enddate = $('#end_date').val();
        $endtime = am_pm_to_hours($('#end_time').val());
        $reason = $('#timeoff_reason').val();

        if ($projectNumber == '-1') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill project number',
            });
        } else if ($employeeId == '-1') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill employee',
            });
        } else if ($pay == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill your pay rate at the site',
            });
        } else if ($startdate == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill start date',
            });
        } else if ($starttime == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill start time',
            });
        } else if ($enddate == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill end date',
            });
        } else if ($endtime == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill end time',
            });
        } else if ($reason == -1) {
            Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Please fill reason',
            });
        } else {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{route('timeoff.timeoffRequestFormstore')}}",
                data: {'project_id': $projectNumber,
                    'employee_id': $employeeId,
                    'pay_rate': $pay,
                    'start_date': $startdate,
                    'start_time': $starttime,
                    'end_date': $enddate,
                    'end_time': $endtime,
                    'reason_id': $reason},
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                        });
                        $('#project_number').val(-1).trigger('change');
                        $('#employee_id').val(-1).trigger('change');
                        $('#your_pay_rate_at_the_site').val(-1).trigger('change');
                        $('#start_date').val('');
                        $('#start_time').val('');
                        $('#end_date').val('');
                        $('#end_time').val('');
                        $('#timeoff_reason').val(-1).trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alert',
                            text: data.message,
                        });
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    if (xhr.responseText) {
                        var jsonResponse = JSON.parse(xhr.responseText);
                        var msg = (jsonResponse.message !== '')? jsonResponse.message: 'Something went wrong';
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alert',
                            text: msg,
                        });
                    } else {
                        console.log(thrownError);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alert',
                            text: 'Something went wrong',
                        });
                    }
                }

            });
        }
    });
//submit form function ends here

// pay roll loaded on customer selection starts
    $('#project_number').on('change', function () {
        var url = '{{ route("timeoff.timeoffPayRoll", ["project_id" => ":projectId"]) }}';
        url = url.replace(':projectId', $('#project_number').val());
        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                console.log(data);
                if (data.length == 0) {
                    var options = '<option selected="selected" value="">No Rates to Show</option>';
                    $("#your_pay_rate_at_the_site").html(options);
                } else {
                    var options = '<option selected="selected" value="">Select the Rate</option>';
                    for (var key in data) {
                        options += '<option value="' + data[key]['id'] + '">' + data[key]['value'] + '</option>';
                    }
                    $("#your_pay_rate_at_the_site").html(options);
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        })
    });
// pay roll loaded on customer selection ends

// $('#project_number').on('change', function() {

//     if ($('#employee_id').val() == -1) {
//         Swal.fire({
//                 icon: 'error',
//                 title: 'Employee Missing',
//                 text: 'please select an employee',
//            });
//     } else {
//             console.log('project success');
//             var url = '{{ route("timeoff.timeoffPayRoll", ["project_id" => ":projectId", "employee_id" => ":employeeId"]) }}';
//             url = url.replace(':projectId', $('#project_number').val());
//             url = url.replace(':employeeId', $('#employee_id').val());
//         $.ajax({
//                  url:url,
//                  method: 'GET',
//                  success: function (data) {
//                  console.log('success');
//                  console.log('data length', data.length);
//                 if (data.length == 0) {
//                     var options = '<option selected="selected" value="">No Rates to Show</option>';
//                     $("#your_pay_rate_at_the_site").html(options);
//                  } else {
//                     var options = '<option selected="selected" value="">Select the Rate</option>';
//                     for (var key in data) {
//                         console.log(data[key]);
//                         options += '<option value="'+ data[key]['p_standard'] +'">'+ data[key]['p_standard']+'</option>';
//                     }
//                  $("#your_pay_rate_at_the_site").html(options);
//                 } 
//             },
//              error: function (xhr, textStatus, thrownError) {
//              console.log(xhr.status);
//             console.log(thrownError);
//             }
//         })
//      }

// });

// $('#employee_id').on('change', function() {

//             if ($('#project_number').val() == -1) {
//                     // Swal.fire({
//                     //         icon: 'error',
//                     //         title: 'Project Missing',
//                     //         text: 'please select a project',
//                     //     });
//             } else {
//                 console.log('employee success');
//                 var url = '{{ route("timeoff.timeoffPayRoll", ["project_id" => ":projectId", "employee_id" => ":employeeId"]) }}';
//                 url = url.replace(':projectId', $('#project_number').val());
//                 url = url.replace(':employeeId', $('#employee_id').val());
//                 $.ajax({
//                     url:url,
//                     method: 'GET',
//                     type: 'json',
//                     success: function (data) {
//                         console.log('success');
//                         console.log('data length', data.length);
//                         if (data.length == 0) {
//                             var options = '<option selected="selected" value="">No Rates to Show</option>';
//                             $("#your_pay_rate_at_the_site").html(options);
//                         } else {
//                             var options = '<option selected="selected" value="">Select the Rate</option>';
//                              for (var key in data) {
//                                 console.log(data[key]);
//                                 options += '<option value="'+ data[key]['p_standard'] +'">'+ data[key]['p_standard']+'</option>';
//                             }
//                         $("#your_pay_rate_at_the_site").html(options);
//                         }
//                         },
//                     error: function (xhr, textStatus, thrownError) {
//                         console.log(xhr.status);
//                         console.log(thrownError);
//                     }
//                 })
//             }
//         })

});
</script>
@endsection


