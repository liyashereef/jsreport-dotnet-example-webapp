@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    #table-id .fa {
        /* margin-left: 11px; */
    }
    .wrapper {
         margin-top: 70px !important;
    }
    .table_title h4 {
        margin-left: 10px;
    }
    .width-100{
        width: 100%;
    }
    .textRight{
        text-align: right;
    }

    .record-center{
        text-align: center;
    }
    .slingle-line{
        white-space: nowrap;
    }
    strong{
        font-size: 14px;
    }
    .custom-datepicker {
        font-size: 14px !important;
        font-weight: bold;
    }

    .card-screening{
        cursor: pointer;
        min-height: 88px !important;
    }
    .card-screening label{
        cursor: pointer;
    }
    .remove-pointer, .remove-pointer label{
        cursor:default;
    }
    .pr-2-date {
        padding-right: 1.5rem!important;
    }
    .failed{
        background-color: #dc3545 !important;
        color: white !important;
    }
    #content-div {
        width: 97%;
    }
    ::-webkit-scrollbar {
        width: 10px !important;
        height: 10px !important;
    }
</style>
<link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet">

@stop

@section('content')
<div class="table_title">
    <h4>Visitor Management</h4>
</div>
<div id="message"></div>

<div class="content-component px-3 pt-3 content2 ">
    <!-- top card area -->
    <div class="row mainlink-component card-view-section mb-2  position-relative">
        <div class="position-absolute icons-top-left d-flex flex-column">

        </div>
        <div class="col-xl-2  col-lg-3 col-md-4 col-sm-6 col-12 pl-1 pr-1 pb-2">
            <div class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex ">
                        <span class="d-flex flex-column b-right-color pr-2-date">
                            <input id="startdate"  width="100%" value="{{$startdate}}" class="custom-datepicker" />
                            <label for="" class="mb-0 text-white label-name"><strong>Start Date</strong></label>
                        </span>
                        <span class="d-flex flex-column pl-2" style="padding-left: 1.5rem!important;">
                            <input type="text" id="enddate" width="100%"  value="{{$enddate}}" class="custom-datepicker" />
                            <label for="" class="mb-0 text-white label-name"><strong>End Date</strong></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div  data-id=""  class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4 width-100">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s ">Total Scanned</label>
                        </span>
                    </div>
                    <div class="d-flex width-100">
                        <span class="d-flex flex-column  pr-2 width-100 textRight">
                            <label for="" id="totalScanned" class="mb-0 text-white label-day">0</label>
                        </span>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div  data-id=""  class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s">Total Scanned</label>

                            <label for="" id="totalScanned"
                                class="mb-0 text-white label-day">$0</label>
                            </label>

                        </span>
                    </div>
                   {{-- <img src="{{asset('images/visitors.png') }}" alt="date" class="position-absolute logos-top-card"> --}}
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div  data-id="1"  class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4 width-100">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s ">Visitors Passed</label>
                            <label for="" id="passedCount" class="mb-0 text-white label-day">0</label>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div  data-id="0" class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4 width-100">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s ">Visitors Failed</label>
                            <label for="" id="failedCount" class="mb-0 text-white label-day">0</label>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div data-id="3" class="card card-screening">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex width-100">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s ">Currently Check In</label>
                            <label for="" id="checkedInCount" class="mb-0 text-white label-day">0</label>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- card area -->
</div>

<div class="row">
    <div class="col-md-2">
        <nav>
            <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
                <a class="nav-item nav-link expense" id="tableTitle" href="#">Total Visitor Scanned Lists</a>
            </div>
        </nav>
    </div>
    <div class="col-md-5">
        <div class="col-md-12 row border p-2 m-0">
            <label for="employee-filter" class="col-md-2 align-self-end p-0">Customers</label>
            <div class="col-md-10">
                {!!Form::select('customerId', [null=>'Please Select'] + $project_list,null, ['class' => 'form-control','id'=>'customerId'])!!}
                <span class="help-block"></span>
            </div>
        </div>
    </div>
</div>


<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Visitor Sreening Attempted Questions and Answers</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <table class="table">
                <thead>
                    <tr>
                        <th> Question </th>
                        <th> Answer </th>
                    </tr>
                </thead>
                <tbody id="attemptedRecords">
                </tbody>
            </table>
        </div>

      </div>

    </div>
  </div>

  <!---End---- Create new visitor ---- Modal -->

<table class="table table-bordered" id="table">
    <thead>
        <tr id="theadTr">

        </tr>
    </thead>
    <tbody id="tableBody">

    </tbody>
</table>



@stop
@section('scripts')

<script>
    $('#customerId').select2();
    const screeningSubmissions = {
        ref: {
            processedData : [],
            questions : [],
            dataTable : null,
            type : null,
            customerId : null,
            startDate : null,
            endDate : null,
        },
        init() {
            //Event listeners
            this.registerEventListeners();
        },
        registerEventListeners() {
            let root = this;
            //Start and end date initialize
            $('#startdate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                    let startdate = $('#startdate').val();
                    if(root.ref.startDate != startdate){
                        root.ref.startDate = startdate;
                        root.fetchReportDataEvent();
                    }
                },
            });
            $('#enddate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                    let enddate = $('#enddate').val();
                    if(root.ref.endDate != enddate){
                        root.ref.endDate = enddate;
                        root.fetchReportDataEvent();
                    }
                },
            });
            root.fetchReportDataEvent();


            $("#tableBody").on("click", ".view", function(e) {
                var id = $(this).data('id');
                var base_url = "{{route('client-visitor.screening-submission.attemptedQuestionAndAnswers', ':id')}}";
                var url = base_url.replace(':id', id);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:url,
                    type: 'GET',
                    success: function (data) {
                    if(data){
                        root.openModal(data);
                    }
                    },
                    fail: function (response) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                    contentType: false,
                    processData: false,
                });
            });

            $('.card-screening').on('click', function(e){

                if($(this).attr('data-id') != undefined){
                    root.ref.type = $(this).attr('data-id');
                    root.ref.customerId = $('#customerId').val();
                    root.ref.startDate = $('#startdate').val();
                    root.ref.endDate = $('#enddate').val();
                    root.fetchReportDataEvent();
                    let title = "";
                    if(root.ref.type == ''){
                        title = "Visitor Scanned Lists";
                    }else if(root.ref.type == 1){
                        title = "Visitor Passed Lists";
                    }else if(root.ref.type == 0){
                        title = "Visitor Failed Lists";
                    }else if(root.ref.type == 3){
                        title = "Currently Checked-In Visitors";
                    }else{
                        title = "";
                    }
                    $('#tableTitle').text(title);

                }
            });

            $('#customerId').on('change', function(e){
                root.ref.customerId = $('#customerId').val();
                root.fetchReportDataEvent();
            });

        },

        fetchReportDataEvent(){
            let root = this;
            root.ref.startDate = $('#startdate').val();
            root.ref.endDate = $('#enddate').val();
            let url = '{{ route("client-visitor.screening-submission.list") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "passed":root.ref.type,
                    "customer_id":root.ref.customerId,
                    'start_date':root.ref.startDate,
                    'end_date':root.ref.endDate,
                },
                type: 'GET',
                success: function(data) {
                    $('#totalScanned').text(data.totalScanned);
                    $('#passedCount').text(data.passedCount);
                    $('#failedCount').text(data.failedCount);
                    $('#checkedInCount').text(data.checkedInCount);
                    root.ref.processedData = data.list;
                    root.ref.questions = data.questions;
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

            $("#table").removeClass('display-none');
            let thQuestions= '';
            thQuestions += `<th class="slingle-line" >Created Date</th>
            <th class="slingle-line">Created Time</th>
            <th>Client</th>
            <th>Visitor</th>
            <th>Result</th>`;

            $.each(root.ref.questions, function(index, question) {
                thQuestions +=`<th class="slingle-line"> ${question}</th>`;
            });
            $('#theadTr').html(thQuestions);

            let rowsEntry = '';
            $.each(root.ref.processedData, function(index, value) {
                rowsEntry += `<tr>`;
                rowsEntry += `<td class="" >${value.created_date}</td>`;
                rowsEntry += `<td class="" >${value.created_time}</td>`;
                rowsEntry += `<td class="slingle-line">${value.client_name_and_number}</td>`;
                // rowsEntry += `<td class="">${value.visitor_log_screening_template_name}</td>`;
                rowsEntry += `<td class="slingle-line">${value.visitor_name}</td>`;
                let className = '';
                if(value.passed == 0){
                    className = 'failed';
                }
                rowsEntry += `<td class="${className}">${value.passed_str}</td>`;

                $.each(root.ref.questions, function(key, question) {
                    let questionAns = '';
                    $.each(value.screening_question_answers, function(indexKey, questionAnswer) {
                        if(question == questionAnswer.visitor_log_screening_template_question_str){
                            if(questionAnswer.answer == 1){
                                questionAns = 'Yes';
                            }else if(questionAnswer.answer == 0){
                                questionAns = 'No';
                            }else{
                                questionAns = '';
                            }

                        }

                    });
                    rowsEntry += `<td>${questionAns}</td>`;
                });
                // rowsEntry += `<td class=""><a href="#" class="view fa fa-eye"  data-id='${value.id}' ></a></td>`;
                rowsEntry += `</tr>`;
            });

            $('#tableBody').html(rowsEntry).after(function(e){
                root.initDataTable();
            });

        },
        initDataTable(){
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
        },
        openModal(data){

            if(data != null){
                let attemptedRecords = '';
                $.each(data, function(index, value) {
                    let answer = '';
                    if(value.answer == 1){
                        answer = 'Yes';
                    }else{
                        answer = 'No';
                    }
                    attemptedRecords += `<tr>`;
                    attemptedRecords += `<td> ${value.visitor_log_screening_template_question_str} </td>`;
                    attemptedRecords += `<td> ${answer} </td>`;
                    attemptedRecords += `</tr>`;


                });
                $('#attemptedRecords').html(attemptedRecords);
                $("#myModal").modal();
            }else{

            }
        },

    };


    // Code to run when the document is ready.
    $(function() {
        screeningSubmissions.init();
    });

</script>


@stop
