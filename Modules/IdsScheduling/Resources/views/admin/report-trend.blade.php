@extends('layouts.app')
@section('css')
<style>
    #content-div {
        width: 97%;
    }
    .admin-container{
        padding: 0% !important;
    }

    .display-none{
        display:none;
    }

    .slingle-line{
         white-space: nowrap;
    }
    .record-center{
        text-align: center;
    }

    #tableHead tr th{
        background-color: #f36905;
        color: #fff !important;
    }
    .week-day-headding{
        background-color: #003A63 !important;
        color: #fff !important;
    }
    .week-day-data {
        background-color: lightyellow !important;
    }
    #tableHead tr th, #tableBody tr td {
        border : 1px solid #524c4c6e !important;
    }

    #tableHead th.rotate > span{
        transform: rotate(180deg);
        -webkit-transform: rotate(-180deg);
        -moz-transform: rotate(-180deg);
        writing-mode: vertical-rl;
        width: 25px;
        vertical-align:top;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f26321;
        color: #fff !important;
    }

    div.dataTables_wrapper {
        width: 100% !important;
        margin: 0 auto;
    }
    .scrollX{
        overflow-x: scroll;
    }
    .js-chart-area{
        height: 500px !important;
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
            <h4>IDS Trends</h4>
        </div>

        <div class="col-sm-12">
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

                </div> <!-- col-sm-6 -->
            </div> <!-- row -->
        </div> <!-- col-sm-12 -->
    <!-- IDS Scheduling Report Form - End -->

    <!-- Report table container - Start-->
    <div class="col-sm-12" >
        <div class="row  " style="overflow-x:auto !important">
            <table class="table table-bordered scrollX display-none" id="table">
                <thead id="tableHead">

                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div> <!-- row -->
        <div id="js-chart" class="js-chart-area" ></div>
    </div> <!-- col-sm-12 -->
    <!-- Report table container - End-->

    @stop
    @section('scripts')
    <script src="{{ asset('js/highcharts/highcharts.js') }}"></script>
    <script src="{{ asset('js/highcharts/exporting.js') }}"></script>
    <script src="{{ asset('js/highcharts/export-data.js') }}"></script>

<script>
    $('.select2').select2();
    const idsReportTrends = {
        ref: {
            idsofficeId : null,
            startDate : moment().subtract(7, 'days').format('YYYY-MM-DD'),
            endDate : moment().format('YYYY-MM-DD'),
            dataTableRecords : [],
            trendsRecords : [],
            dataTable : null,
            checkedLocationIds : [],
        },
        init() {
            //Event listeners
            this.registerEventListeners();
            this.fetchReportDataEvent();
        },
        registerEventListeners() {
            let root = this;
            //Trend report filter
            $('#report-form').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = $(this).serializeArray();
                var trigerFunction = true;
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                //Start date validation
                if($('#report_start_date').val() == ''){
                    form.find("[id='startDateError']").addClass('has-error').find('.help-block').text("Start date is required");
                    trigerFunction = false;
                }
                //End date validation
                if($('#report_end_date').val() == ''){
                    form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date is required");
                    trigerFunction = false;
                }
                //Fetch Trend report data
                if(trigerFunction == true){
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    root.ref.startDate = $('#report_start_date').val();
                    root.ref.endDate = $('#report_end_date').val();
                    root.ref.idsofficeId = $('#office').val();
                    root.fetchReportDataEvent();
                }

            });

        },
        fetchReportDataEvent(){
            let root = this;
            let url = '{{ route("idsscheduling-admin.office.trends-reports") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "ids_office_id":root.ref.idsofficeId,
                    'start_date':root.ref.startDate,
                    'end_date':root.ref.endDate,
                },
                type: 'GET',
                success: function(data) {
                  root.ref.trendsRecords = data;
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
            let tableTitle = '';
            let tableBody = '';

            $("#table").removeClass('display-none');

            //IDS trends set table head.
            tableTitle += `<tr>
                            <th class="slingle-line ">Location</th>`;
                            $.each(root.ref.trendsRecords.displayDate, function(index, value) {
                                let thClass = "slingle-line record-center rotate";
                                if(value.weekdys == true){
                                    thClass = "slingle-line record-center rotate week-day-headding  ";
                                }
                                tableTitle += `<th class="${thClass}"><span>${value.name}</span></th>`;
                            });

            tableTitle += `</tr>`;

            //IDS trends set table head.
            $('#tableHead').html(tableTitle);

           //IDS trends set table body data.
            $.each(root.ref.trendsRecords.trendsData, function(index, value) {
                tableBody += `<tr>
                            <td class="slingle-line" data-order="${value.office}">${value.office}</td>`;
                // IDS date wise data.
                $.each(value.dateWiseData, function(index, dateVal) {
                    let count = '';
                    if(dateVal.count > 0){
                        count = dateVal.count;
                    }
                    let bodyClass = 'slingle-line record-center';
                    if(dateVal.offDayClass == 1){
                        bodyClass = "slingle-line record-center week-day-data";
                    }
                    tableBody += `<td class="${bodyClass}" data-order="${dateVal.count}">${count}</td>`;
                });
                tableBody += `</tr>`;

            });
            //IDS trends set table body.
            $('#tableBody').html(tableBody).after(function(e){
                root.initTrendsTable();
                root.initHighcharts();
            });

       },
       initTrendsTable(){
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
                    searching: false,
                    destroy: true,
                    ordering: false,
                    paging: false,
                });
            } catch(e){
                console.log(e.stack);
            }
        },
        initHighcharts(){
            let root = this;
            let seriesData = new Array();
            let indexKey = 0;
            //Highchart series data.
            $.each(root.ref.trendsRecords.trendsChartData, function(index, value) {
                seriesData[indexKey] = {
                    'name':value.locationName,
                    'data':value.dateWiseCount
                    };
                    indexKey++;
            });

            //Highchart initialized.
            let chart = '';
            let startDate = moment(root.ref.startDate).format("LL");
            let endDate =  moment(root.ref.endDate).format("LL");
            let subTitle = 'From '+startDate+' To '+endDate;
            chart = Highcharts.chart('js-chart', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'IDS Trends Reports'
                },
                subtitle: {
                    text: subTitle
                },
                xAxis: {
                    categories: root.ref.trendsRecords.trendsChartCategories
                },
                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'Booking count'
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: true
                    }
                },
                series: seriesData,
                exporting: {
                        buttons: {
                            contextButton: {
                                menuItems: ['viewFullscreen','separator','downloadPNG', 'downloadJPEG']
                            }
                        },
                    fallbackToExportServer: false
                },
            });
        }
    }

    // Code to run when the document is ready.
    $(function() {
        idsReportTrends.init();
        $("#report_start_date").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#report_end_date").val(moment().format('YYYY-MM-DD'));
    });

</script>

  @stop
