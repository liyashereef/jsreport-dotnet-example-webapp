@extends('layouts.app')
@section('css')
<link href="{{ asset('css/ids.css') }}?t=1" rel="stylesheet">
<style>
    #content-div {
        width: 97%;
    }

    .admin-container {
        padding: 0% !important;
    }

    .service-name {
        white-space: nowrap !important;
        text-align: left;
        font-size: 15px !important;
    }

    .month-title-section {
        color: #fff !important;
        background: #003A63 !important;
        border: 1px solid #4a4848e8;
        height: 121px;
        width: 100%;
        text-align: center;
    }

    .month-title-section div {
        margin-top: 33%;
    }

    #rolling-forecast-dataset {
        border-top: 1px solid #4a4848e8;
    }

    #rolling-forecast-dataset div:nth-child(6n+6) {
        border-right: 1px solid #4a4848e8;
    }

    .month-section {
        border-left: 1px solid #4a4848e8;
        border-bottom: 1px solid #4a4848e8;
        height: 60px;
        width: 100%;
        padding: 0px;
    }

    .month-title {
        background-color: #f36905 !important;
        border-bottom: 1px solid #4a4848e8;
        color: #fff !important;
        text-align: center;
    }

    .month-data {
        text-align: center;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 5%;
    }

    .display-none {
        display: none;
    }

    .table thead th {
        /* vertical-align: bottom; */
        border: 1px solid #00000066;
        width: 70px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f26321;
        color: #fff !important;
    }

    .ids-report-table tr td {
        padding: 1rem;
    }
</style>
@stop
@section('content')

<!-- IDS Scheduling Report Form - Start -->
<div class="table_title">
    <h4>IDS Forecast</h4>
</div>

<div class="col-sm-12">
    <div class="row">
        <div class="col-sm-6">
            {{ Form::open(array('id'=>'report-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
            <div id="start_date" class="form-group row col-sm-12">
                <label for="start_date" class="col-sm-4 col-form-label">Start Date<span class="mandatory"> *</span></label>
                <div class="col-sm-8">
                    {{ Form::text('start_date', null, array('class'=>'form-control datepicker', 'id'=>'report_start_date')) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                </div>
            </div>
            <div id="ids_office_id" class="form-group row col-sm-12">
                <label for="ids_office_id" class="col-sm-4 col-form-label">Office Location</label>
                <div class="col-sm-8">
                    {{ Form::select('ids_office_id[]',$officeList, old('ids_office_id'),array('class'=> 'form-control select2','id'=>'office','multiple'=>"multiple")) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                </div>
            </div>
            <div class="form-group row col-sm-12 justify-content-center" style="text-align:center">
                <div class="col-sm-12">
                    {{ Form::submit('Generate Report', array('class'=>'button btn btn-primary blue','style'=>'margin-left: 30%;'))}}
                </div>
            </div>
            {{ Form::close() }}
        </div> <!-- col-sm-6 -->
        <div class="col-sm-6">
            <div class="row display-none" id="rolling-forecast">
                <div class="col-sm-2 month-title-section " id="month-title-section">
                    <div> 12 Month Rolling Forecast</div>
                </div>
                <div class="col-sm-10">
                    <div class="row" id="rolling-forecast-dataset">

                    </div> <!-- row -->
                </div> <!-- col-sm-10 -->
            </div> <!-- row -->
        </div> <!-- col-sm-6 -->
    </div> <!-- row -->
</div> <!-- col-sm-12 -->
<!-- IDS Scheduling Report Form - End -->

<!-- Report table container - Start-->
<div class="col-sm-12">
    <div class="row">
        <div id="report-table-container" class="col-sm-12 admin-container"></div>
    </div> <!-- row -->
</div> <!-- col-sm-12 -->
<!-- Report table container - End-->

@stop
@section('scripts')
<script type="text/javascript">
    $('#office').select2(); //Added Select2 to office-ids listing
    /* Report form submission - Start */
    $('#report-form').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var office = $('#office').val();
        //var service = $('#service').val();
        var start_date = $('#report_start_date').val();
        var formData = $(this).serializeArray();
        var currentDate = new Date();
        var report_start_date = new Date(start_date);
        var end_date_max = moment(report_start_date, "YYYY-MM-DD").add(19, 'days');
        end_date_max = moment(end_date_max).format("YYYY-MM-DD");

        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        if (start_date == '') {
            $('#report-table-container').empty();
            $.each(formData, (index, obj) => {
                if (obj.name == "start_date" && obj.value == '') {
                    $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Start date is required");
                }
            });
        } else {
            $('#report-table').remove();
            $.ajax({
                url: "{{route('idsscheduling-admin.office.service-reports')}}",
                data: {
                    "start_date": start_date,
                    "end_date": end_date_max,
                    'ids_office_id': office
                },
                type: 'GET',
                success: function(data) {

                    var total = 0;
                    var reportTableHtml = "<table class='ids-report-table table center' id='report-table'>";
                    reportTableHtml += "<thead>";
                    reportTableHtml += "<tr><th class='head-border first-col days-slot '><div style='width: 161px !important;margin: 0% 0% 22% 25% !important;'><span>Service</span></div></th>";
                    $.each(data.display_date, function(index, value) {
                        var className = 'center days-slot head-border rotate';
                        if (value.weekdys == true) {
                            var className = 'center days-slot-weekdys head-border rotate';
                        }
                        reportTableHtml += "<th class='" + className + "'><span>" + value.name + "</span></th>";
                    });
                    reportTableHtml += "<th class='center days-slot head-border '><div><span>Total</span></div></th>";
                    reportTableHtml += "<tr>";
                    reportTableHtml += "</thead>";
                    reportTableHtml += "<tbody>";
                    $.each(data.reports, function(index, value) {
                        reportTableHtml += "<tr>";
                        reportTableHtml += "<td class='first-col time-slot service-name'>" + value.name + "</td>";
                        $.each(value.date, function(index, value) {
                            var display_value = '$' + value.fee;
                            if (value.fee == 0) {
                                display_value = '';
                            }
                            var tdClass = 'center rate';
                            if (value.off_day_class == 1) {
                                var tdClass = "center rate saturday_sundy";
                            }
                            reportTableHtml += "<td class='" + tdClass + "'>" + display_value + "</td>";
                        });
                        var service_total = '$' + value.service_total;
                        if (value.service_total == 0) {
                            service_total = '';
                        }
                        reportTableHtml += "<td class='center rate time-slot'>" + service_total + "</td>";
                        reportTableHtml += "<tr>";
                        total = parseFloat(total) + parseFloat(value.service_total);
                    });

                    reportTableHtml += "<tr>";
                    reportTableHtml += "<td class='center first-col days-slot'>Daily Total</td>";
                    $.each(data.date_wise_total, function(index, value) {
                        var display_value = '$' + value;
                        if (value == 0) {
                            display_value = '';
                        }
                        reportTableHtml += "<td class='center rate days-slot'>" + display_value + "</td>";
                    });
                    let totalFeePayableArray = total.toString().split(".");
                    if (totalFeePayableArray.length == 2) {
                        let totalFeeDecimal = totalFeePayableArray[1].substr(0, 2);
                        total = parseFloat(totalFeePayableArray[0] + '.' + totalFeeDecimal);
                    }
                    reportTableHtml += "<td class='center rate days-slot'>$" + total + "</td>";
                    reportTableHtml += "<tr>";

                    reportTableHtml += "</tbody>";
                    reportTableHtml += "</table>";
                    $('#report-table-container').append(reportTableHtml);

                    var rollingForecastDataSet = '';
                    $.each(data.months_forecast, function(index, value) {
                        var display_value = '$' + value.total_fee;
                        if (value.total_fee == 0) {
                            display_value = '';
                        }

                        rollingForecastDataSet += "<div class='col-sm-2 month-section'>";
                        rollingForecastDataSet += "<div class='month-title'>" + value.title + "</div>";
                        rollingForecastDataSet += "<div class='month-data' >" + display_value + "</div>";
                        rollingForecastDataSet += "</div>";
                    });
                    $('#rolling-forecast-dataset').html(rollingForecastDataSet);
                    $('#rolling-forecast').removeClass('display-none');
                },
                "error": function(xhr, textStatus, thrownError) {
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                }
            });
        }
    });
    /* Report form submission - End */

    $(function() {
        $("#report_start_date").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#report-form").trigger("submit");
    });
</script>

@stop
