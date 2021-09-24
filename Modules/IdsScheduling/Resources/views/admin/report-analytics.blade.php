@extends('layouts.app')
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
        <h4>IDS Analytics</h4>
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
            <!-- <div id="end_date" class="form-group row col-sm-12">
                            <label for="start_date" class="col-sm-4 col-form-label">End Date<span class="mandatory"> *</span></label>
                            <div class="col-sm-8">
                                {{ Form::text('end_date', null, array('class'=>'form-control datepicker', 'id'=>'report_end_date')) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                </div>
            </div> -->
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
                    <th class="" style="width: 150px !important;">First Name</th>
                    <th class="" style="width: 150px !important;">Last Name</th>
                    <th class="" style="width: 150px !important;">Email</th>
                    <th class="slingle-line">Phone</th>
                    <th class="slingle-line">Postal Code</th>
                    <th class="slingle-line">Office</th>
                    <th class="slingle-line">Service</th>
                    <th class="slingle-line">Service Fee</th>
                    <th class="slingle-line">Photo Service</th>
                    <th class="slingle-line">Photo Fee</th>
                    <th class="slingle-line">Total Fee</th>
                    <th class="slingle-line">No. Mask Provided</th>
                    <th class="slingle-line">Client Show Up</th>
                    <th class="slingle-line">Payment Recieved</th>
                    <th class="slingle-line">Payment Type</th>
                    <th class="slingle-line">Payment Reason</th>
                    <th class="slingle-line">Other Reason</th>

                    <!-- <th class="slingle-line">Payment Type</th> -->
                    @foreach($questions as $question)
                        <th class="slingle-line">{{$question->question}}</th>
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
                startDate : moment().subtract(7, 'days').format('YYYY-MM-DD'),
                endDate : moment().format('YYYY-MM-DD'),
                analyticsData : [],
                analyticsTable: null,
                dataTable : null,
                questions : {!! $questions !!},
            },
            init() {
                //Event listeners
                this.registerEventListeners();
                this.fetchReportDataEvent();
            },
            registerEventListeners() {
                let root = this;
                $('#report-form').submit(function (e) {
                    e.preventDefault();
                    var form = $(this);
                    var formData = $(this).serializeArray();
                    var trigerFunction = true;
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    if($('#report_start_date').val() == ''){
                        form.find("[id='startDateError']").addClass('has-error').find('.help-block').text("Start date is required");
                        trigerFunction = false;
                    }
                    if($('#report_end_date').val() == ''){
                        form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date is required");
                        trigerFunction = false;
                    }

                    if(trigerFunction == true){
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        root.ref.startDate = $('#report_start_date').val();
                        root.ref.endDate = $('#report_end_date').val();
                        root.ref.idsofficeId = $('#office').val();
                        root.fetchReportDataEvent();
                    }else{

                    }


                });
            },

            fetchReportDataEvent(){
                let root = this;
                let url = '{{ route("idsscheduling-admin.office.analytics-reports") }}';
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
                        root.ref.analyticsData = data;
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
                    slotList += `<tr>`;
                    // slotList += `<td class="slingle-line record-center">${index + 1}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.transactionAt}">${value.transactionDate}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.transactionTime}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.scheduledAt}">${value.scheduledDate}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.scheduledTime}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.delta}</td>`;
                    slotList += `<td class="" style="width: 150px !important;">${value.firstName}</td>`;
                    slotList += `<td class="" style="width: 150px !important;">${value.lastName}</td>`;
                    slotList += `<td class="" style="width: 150px !important;">${value.email}</td>`;
                    slotList += `<td class="slingle-line">${value.phone}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.postalCode}</td>`;
                    slotList += `<td class="slingle-line">${value.office}</td>`;
                    slotList += `<td class="">${value.service}</td>`;
                    slotList += `<td class="">${value.serviceFee}</td>`;
                    slotList += `<td class="">${value.photoService}</td>`;
                    slotList += `<td class="">${value.photoFee}</td>`;
                    slotList += `<td class="">${value.amount}</td>`;
                    slotList += `<td class="record-center">${value.noMasksGiven}</td>`;
                    slotList += `<td class="record-center">${value.clientShowUp}</td>`;
                    slotList += `<td class="record-center">${value.paymentRecieved}</td>`;
                    slotList += `<td class="record-center">${value.paymentType}</td>`;
                    slotList += `<td class="record-center">${value.paymentReasonId}</td>`;
                    slotList += `<td class="record-center">${value.paymentReason}</td>`;


                    // slotList += `<td class="slingle-line ">${value.paymentType}</td>`;

                    $.each(root.ref.questions, function(index, que) {

                        let questionAns = '';
                        $.each(value.questionAnswers, function(index, ans) {
                            if(que.id == ans.ids_custom_questions_id){
                                if(ans.ids_custom_option_id == 1){
                                    questionAns = ans.ids_custom_option_str +'('+ans.other_value+')';
                                }else{
                                    questionAns = ans.ids_custom_option_str;
                                }
                            }
                        });

                        slotList += `<td>${questionAns}</td>`;
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
            $("#report_start_date").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
            $("#report_end_date").val(moment().format('YYYY-MM-DD'));
        });

    </script>



@stop
