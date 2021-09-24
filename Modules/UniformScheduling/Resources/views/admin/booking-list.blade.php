@extends('layouts.app')
@section('title') - Uniform Appointment Lists @stop
@section('css')
<link href="{{ asset('css/ids.css') }}?t=1" rel="stylesheet">
<style>
#content-div {
    width: 97%;
}
.admin-container{
    padding: 0% !important;
}
.service-name{
    white-space: nowrap !important;
    text-align: left;
}

.month-title-section{
    color: #fff !important;
    background: #003A63 !important;
    border: 1px solid #4a4848e8;
    height: 121px;
    width: 100%;
    text-align: center;
}
.month-title-section div{
    margin-top: 33%;
}
#rolling-forecast-dataset{
    border-top: 1px solid #4a4848e8;
}
#rolling-forecast-dataset div:nth-child(6n+6) {
    border-right: 1px solid #4a4848e8;
}
.month-section{
    border-left: 1px solid #4a4848e8;
    border-bottom: 1px solid #4a4848e8;
    height: 60px;
    width: 100%;
    padding: 0px;
}
.month-title{
    background-color: #f36905 !important;
    border-bottom: 1px solid #4a4848e8;
    color: #fff !important;
    text-align: center;
}
.month-data{
    text-align: center;
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 5%;
}
.display-none{
    display:none;
}

.table thead th {
    /* vertical-align: bottom; */
    border: 1px solid #00000066;
    /* width: 70px !important; */
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #f26321;
    color: #fff !important;
}
.ids-report-table tr td {
    padding: 1rem;
}



.record-center{
    text-align: center;
}
.slingle-line{
     white-space: nowrap;
}

div.dataTables_wrapper {
    width: 100% !important;
    margin: 0 auto;
}
.scrollX{
    overflow-x: scroll;
}

/* ::-webkit-scrollbar {

    width: 0.2em;
     height: 0.2em
    }
    ::-webkit-scrollbar-button {
    background: #716f6f
    }
    ::-webkit-scrollbar-track-piece {
        background: #716f6f
    }
    ::-webkit-scrollbar-thumb {
        background: #fff

    } */

</style>
@stop
@section('content')

    <!-- IDS Scheduling Report Form - Start -->
        <div class="table_title">
            <h4>Uniform Appointment Lists</h4>
        </div>

        {{-- <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    {{ Form::open(array('id'=>'report-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
                        <div id="start_date" class="form-group row col-sm-12">
                            <label for="start_date" class="col-sm-4 col-form-label">Start & End Date<span class="mandatory"> *</span></label>
                            <div class="col-sm-4">
                                {{ Form::text('start_date', null, array('class'=>'form-control datepicker','placeholder'=>'Start Date', 'id'=>'report_start_date')) }}
                                <div class="form-control-feedback" id="startDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                            <div class="col-sm-4">
                            {{ Form::text('end_date', null, array('class'=>'form-control datepicker','placeholder'=>'End Date', 'id'=>'report_end_date')) }}
                                <div class="form-control-feedback" id="endDateError"><span class="help-block text-danger align-middle font-12"></span></div>
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

                </div> <!-- col-sm-6 -->
            </div> <!-- row -->
        </div> <!-- col-sm-12 --> --}}

        <div class="filter-div">
            <div class="row">
                <div class="col-md-12">
                    {{ Form::open(array('id'=>'report-form', 'autocomplete'=>'off', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
                        <div class="row">
                            <div class="col-md-3">
                                <div id="start_date" class="form-group row ">
                                    <label for="start_date" class="col-md-5 col-form-label">Start Date<span class="mandatory"> *</span></label>
                                    <div class="col-md-7">
                                        {{ Form::text('start_date', null, array('class'=>'form-control datepicker', 'id'=>'report_start_date')) }}
                                        <div class="form-control-feedback" id="startDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div id="end_date" class="form-group row ">
                                    <label for="end_date" class="col-md-5 col-form-label">End Date<span class="mandatory"> *</span></label>
                                    <div class="col-md-7">
                                        {{ Form::text('end_date', null, array('class'=>'form-control datepicker', 'id'=>'report_end_date')) }}
                                        <div class="form-control-feedback" id="endDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row col-md-12" style="text-align:center">
                                    <div class="col-md-12">
                                        {{ Form::submit('Generate Report', array('class'=>'button btn btn-primary blue'))}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!-- IDS Scheduling Report Form - End -->

    <!-- Report table container - Start-->
    <div class="col-sm-12" >
        <div class="row">
            <table class="table table-bordered scrollX display-none" id="table">
                <thead>
                    <tr>
                        <!-- <th class="slingle-line" id="slNo">#</th> -->
                        <th class="slingle-line">Transaction Date</th>
                        <th class="slingle-line">Transaction Time</th>
                        <th class="slingle-line">Scheduled Date</th>
                        <th class="slingle-line">Scheduled Time</th>
                        <th class="slingle-line">Delta (In Days)</th>
                        <th class="slingle-line">First Name</th>
                        <th class="slingle-line">Last Name</th>
                        <th class="slingle-line">Email</th>
                        <th class="slingle-line">Phone</th>
                        <th class="slingle-line">Gender</th>
                        <th class="slingle-line">Office</th>
                        <th class="slingle-line">Client Show Up</th>
                        @foreach ($measurementPoints as $item)
                        <th class="slingle-line">{{$item->name}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div> <!-- row -->
    </div> <!-- col-sm-12 -->
    <!-- Report table container - End-->

    @stop
    @section('scripts')

<script>
    $('.select2').select2();
    const idsReportAnalytics = {
        ref: {
            idsofficeId : null,
            startDate : null,
            endDate : null,
            analyticsData : [],
            analyticsTable: null,
            dataTable : null,
            measurementPoints : {!! $measurementPoints !!},
            measurementDecimalPoints : []
        },
        init() {
            //Event listeners
            this.registerEventListeners();
            this.initialDataLoad();
        },
        initialDataLoad(){
            let today = moment().format('YYYY-MM-DD');
            let endDateMax = today;
            endDateMax = moment(moment(today, "YYYY-MM-DD").add(2, 'days')).format('YYYY-MM-DD');
            $('#report_start_date').val(today);
            $('#report_end_date').val(endDateMax);
            $("#report-form").trigger("submit");

        },
        registerEventListeners() {
            let root = this;
            $('#report-form').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = $(this).serializeArray();
                var trigerFunction = true;
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                $("#table").addClass('display-none');
                if($('#report_start_date').val() == ''){
                    form.find("[id='startDateError']").addClass('has-error').find('.help-block').text("Start date is required");
                    trigerFunction = false;
                }
                if($('#report_end_date').val() == ''){
                    form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date is required");
                    trigerFunction = false;
                }
                if($('#report_start_date').val() > $('#report_end_date').val()){
                    form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date must be greater than start date");
                    trigerFunction = false;
                }
                if(trigerFunction == true){
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    root.ref.startDate = $('#report_start_date').val();
                    root.ref.endDate = $('#report_end_date').val();
                    root.fetchReportDataEvent();
                }else{

                }


            });
        },

        fetchReportDataEvent(){
            let root = this;
            let url = '{{ route("uniform-admin.slot-booking.lists") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    // "ids_office_id":root.ref.idsofficeId,
                    'start_date':root.ref.startDate,
                    'end_date':root.ref.endDate,
                },
                type: 'GET',
                success: function(data) {
                  root.ref.analyticsData = data.result;
                  root.ref.measurementDecimalPoints = data.measurementDecimalPoints;

                  root.setReportData();
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                    }
                },
                contentType: false
            });
       },
       setReportData(){
        let root = this;

        if ($.fn.DataTable.isDataTable( '#table' )) {
            root.ref.dataTable.clear();
            root.ref.dataTable.destroy();
        }
        let slotList = '';
        $("#table").removeClass('display-none');

        $.each(root.ref.analyticsData, function(index, value) {
            var created_at = new Date(value.created_at);
            var booked_date = new Date(value.booked_date);

            const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

            let delta = Math.round(Math.abs((booked_date - created_at) / oneDay));

            slotList += `<tr>`;
            // slotList += `<td class="slingle-line record-center">${index + 1}</td>`;
            slotList += `<td class="slingle-line record-center" data-order="${value.created_at}">${moment(value.created_at).format("MMM D Y")}</td>`;
            slotList += `<td class="slingle-line record-center">${moment(value.created_at).format('LT') }</td>`;
            slotList += `<td class="slingle-line record-center" data-order="${value.booked_date}">${moment(value.booked_date).format("MMM D Y")}</td>`;
            slotList += `<td class="slingle-line record-center">${moment(value.booked_date+' '+value.start_time).format('LT')}</td>`;
            slotList += `<td class="slingle-line record-center">${delta}</td>`;
            slotList += `<td class="slingle-line ">${value.user.first_name}</td>`;
            slotList += `<td class="slingle-line ">${value.user.last_name}</td>`;
            slotList += `<td class="slingle-line">${value.email}</td>`;
            slotList += `<td class="slingle-line">${value.phone_number}</td>`;
            let gender = '';
            if(value.gender == 1){
                gender = 'Male';
            }else{
                gender = 'Female';
            }
            slotList += `<td class="slingle-line record-center">${gender}</td>`;
            slotList += `<td class="slingle-line">${value.uniform_scheduling_office.name}</td>`;
           let  client_show = '';
            if(value.is_client_show_up == 1){
                client_show = 'Yes';
            }else if(value.is_client_show_up == 0){
                client_show = 'No';
            }
            slotList += `<td class="record-center">${client_show}</td>`;

            $.each(root.ref.measurementPoints, function(index, point) {
                let measurementVal = '';
                let measurementDecimal = '';
                $.each(value.uniform_measurements, function(index, measurement) {
                    measurementDecimalVal = '';
                    if(point.id == measurement.uniform_scheduling_measurement_point_id){
                        measurementDecimal = measurement.measurement_values.split('.');
                        measurementVal = measurementDecimal[0];
                        if(measurementDecimal[1] != null && measurementDecimal[1] != 000){
                            $.each(root.ref.measurementDecimalPoints, function(measurementPointIndex, measurementPoint) {
                                if(measurementPoint == '0.'+measurementDecimal[1]){
                                     measurementDecimalVal = '('+measurementPointIndex+')';
                                }
                            });
                        }
                        measurementVal = measurementVal+measurementDecimalVal;
                    }
                });
                slotList += `<td>${measurementVal}</td>`;
            });
            slotList += `</tr>`;
        });
        $('#tableBody').html(slotList).after(function(e){
            root.initAnalyticsTable();
        });

       },
       initAnalyticsTable(){
            let root = this;
            var screenheight = screen.height;
            try{
                root.ref.dataTable = $('#table').DataTable({
                    dom: 'Blfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '  Excel',
                        },
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    destroy: true,
                    scrollY: screenheight*.5,
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                });

            } catch(e){
                console.log(e.stack);
            }
        }
    }

    // Code to run when the document is ready.
    $(function() {
        idsReportAnalytics.init();
    });

</script>



@stop
