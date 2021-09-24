
@extends('layouts.app')
@section('title') - IDS Refund  @stop
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
        .reject
        {
            color:black !important;
        }
        .js-card{
            cursor: pointer;
            text-decoration: underline;
        }
        .refundConfirm, .reject{
            font-size: 20px !important;
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
        <h4>IDS Refund List</h4>
    </div>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('id'=>'report-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
                <div class="row">
                    <div id="start_date" class="form-group row col-sm-6">
                        <label for="start_date" class="col-sm-3 col-form-label">Start & End Date<span class="mandatory"> *</span></label>
                        <div class="col-sm-4">
                            {{ Form::text('start_date', null, array('class'=>'form-control datepicker','placeholder'=>'Start Date', 'id'=>'report_start_date')) }}
                            <div class="form-control-feedback" id="startDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <div class="col-sm-4">
                            {{ Form::text('end_date', null, array('class'=>'form-control datepicker','placeholder'=>'End Date', 'id'=>'report_end_date')) }}
                            <div class="form-control-feedback" id="endDateError"><span class="help-block text-danger align-middle font-12"></span></div>

                        </div>
                    </div>

                    <div id="refund_status" class="form-group row col-sm-6">
                        <label for="refund_status" class="col-sm-3 col-form-label">Refund Status</label>
                        <div class="col-sm-8" id="">
                            {{ Form::select('refund_status[]',['1'=>'Requested','2'=>'Approved','3'=>'Rejected'],old('refund_status'),
                            array('class'=> 'form-control select2', 'id'=>'refundStatus','multiple'=>"multiple")) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div id="ids_office_id" class="form-group row col-sm-6">
                        <label for="ids_office_id" class="col-sm-3 col-form-label">Office Location</label>
                        <div class="col-sm-8">
                            {{ Form::select('ids_office_id[]',$officeList, old('ids_office_id'),array('class'=> 'form-control select2','id'=>'office','multiple'=>"multiple")) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="ids_service_id" class="form-group row col-sm-6">
                        <label for="ids_service_id" class="col-sm-3 col-form-label">Service</label>
                        <div class="col-sm-8" id="">
                            {{ Form::select('ids_service_id[]',$services,old('ids_service_id'),
                            array('class'=> 'form-control select2', 'id'=>'service','multiple'=>"multiple")) }}
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-12" >
                    <!-- <div class="col-md-2 offset-md-10"> -->
                    <div class="col-md-6 offset-md-2">
                        {{ Form::submit('Generate Report', array('class'=>'button btn btn-primary blue','style'=>'margin-left: 38%;'))}}
                    </div>
                </div>

                {{ Form::close() }}

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
                    {{-- <th class="slingle-line">Transaction Date</th> --}}
                    {{-- <th class="slingle-line">Transaction Time</th> --}}
                    <th class="">Scheduled Date</th>
                    <th class="">Scheduled Time</th>
                    {{-- <th class="slingle-line">Delta (In Days)</th> --}}
                    <th class="">First Name</th>
                    <th class="">Last Name</th>
                    <th class="">Email</th>
                    <th class="">Phone</th>
                    <th class="">Postal Code</th>
                    <th class="">Office</th>
                    <th class="">Service</th>
                    <th class="">Photo Service</th>
                    <th class="">Total Fee($)</th>
                    <th class="">Online Payment($)</th>
                    <th class="">Refund Amount($)</th>
                    <th class="">Refund Status</th>
                    <th class="">Initiated By</th>
                    <th class="">Initiated On </th>
                    <th class="">Completed By</th>
                    <th class="">Completed On </th>
                    @can('ids_refund_update_status')
                    <th class="">Action</th>
                    @endcan
                </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div> <!-- row -->
    </div> <!-- col-sm-12 -->
    <!-- Report table container - End-->

    <!--Start-- IDS Refund confirm -->
    <div class="modal fade" id="refundConfirmModel" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Refund Status Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'ids-refund-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                    <br>
                    <div class="modal-body">
                        <div class="form-group row" id="entry_id">
                            <div class="col-sm-9">
                                {{ Form::hidden('entry_id', null) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="refund_status">
                            <label for="refund_status" class="col-sm-3 control-label">Select Status</label>
                            <div class="col-sm-9">
                                {{ Form::select('refund_status',[''=>'Please Select Status',2=>'Refund Approved',3=>'Refund Rejected'], old('refund_status'),array('class'=> 'form-control', 'id'=>'idsRefundStatus','required'=>'true')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <!-- <div class="form-group row" id="refund_amount" >
                            <label for="refund_amount" class="col-sm-3 control-label">Refund Amount</label>
                            <div class="col-sm-9">
                                {{ Form::text('refund_amount', null, array('class'=>'form-control', 'placeholder'=>'Refund Amount','id'=>'refundAmount')) }}
                                <small class="help-block"></small>
                            </div>
                        </div> -->
                        <div class="form-group row" id="rejected_reason" >
                            <label for="rejected_reason" class="col-sm-3 control-label">Reason</label>
                            <div class="col-sm-9">
                                {{ Form::textArea('rejected_reason', null, array('class'=>'form-control', 'placeholder'=>'Reason','rows'=>3,'id'=>'rejectedReason')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>'refund_status_mdl_save'))}}
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!--End-- IDS Refund confirm -->

@stop
@include('idsscheduling::admin.partials.booking-readonly-modal')
@section('scripts')

    <script>
        $('.select2').select2();
        const idsReportAnalytics = {
            ref: {
                idsofficeId : null,
                startDate : null,
                endDate : null,
                revenueData : [],
                yearRevenuesData : [],
                analyticsTable: null,
                dataTable : null,
            },
            init() {
                $('#refundStatus').val(1).trigger('change');
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

                $("body").on("click", ".refundConfirm", function(){
                    $('#refundConfirmModel').modal();
                    $('#refundConfirmModel').find('.form-group').removeClass('has-error').find('.help-block').text('');
                    $('#ids-refund-form')[0].reset();
                    $('#refundConfirmModel input[name="entry_id"]').val($(this).data("id"));
                    $('#refundConfirmModel #rejected_reason').hide();
                    // $('#refundConfirmModel input[name="refund_amount"]').val('');
                    // $('#refundConfirmModel #refund_amount').hide();
                });
                @can('ids_refund_update_status')
                    $('#ids-refund-form').submit(function (e) {
                        e.preventDefault();
                        var $form = $(this);
                        var url = "{{ route('idsscheduling-admin.office.refund-confirm') }}";
                        var formData = new FormData($('#ids-refund-form')[0]);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: url,
                            type: 'POST',
                            data: formData,
                            success: function (data) {
                                if (data.success) {
                                    $('#refundConfirmModel').find('.form-group').removeClass('has-error').find('.help-block').text('');
                                    $('#refundConfirmModel').modal('hide');
                                    swal({
                                        title: "Refund status updated",
                                        text: "Refund status successfully updated",
                                        type: "success",
                                        confirmButtonText: "OK",
                                    },function(){
                                        $('#ids-refund-form')[0].reset();
                                        // $("#report-form").trigger("submit");
                                        root.fetchReportDataEvent();
                                    });
                                } else {
                                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                                    if(data.error){
                                        associate_errors(data.error, $form, true);
                                    }else{
                                        swal({
                                            title: "Error",
                                            text: data.message,
                                            type: "warning",
                                            confirmButtonText: "OK",
                                        },function(){

                                        });
                                    }
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
                    });
                @endcan
            },
            fetchReportDataEvent(){
                let root = this;
                let url = '{{ route("idsscheduling-admin.office.refund-list") }}';

                $.ajax({
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'start_date':root.ref.startDate,
                        'end_date':root.ref.endDate,
                        "ids_office_id":root.ref.idsofficeId,
                        "ids_service_id":$('#service').val(),
                        'refund_status':$('#refundStatus').val(),
                    },
                    type: 'GET',
                    success: function(data) {
                        root.ref.revenueData = data;
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

                $.each(root.ref.revenueData, function(index, value) {
                    let photoServiceName = '';
                    if(value.ids_passport_photo_service_with_trashed){
                        photoServiceName = value.ids_passport_photo_service_with_trashed.name;
                    }
                    let refundAmount = '';
                    // if(parseFloat(value.balance_fee) < 0){
                    //     let refundAmount = value.balance_fee;
                    // }
                    refundAmount = '';
                    let balanceFeeArray = value.balance_fee.split("-");
                    if(balanceFeeArray.length == 2){
                        refundAmount = parseFloat(balanceFeeArray[1]).toFixed(2);
                    }
                    slotList += `<tr>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.slot_booked_date}">${moment(value.slot_booked_date).format('ll')}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.slot_booked_date+' '+value.ids_office_slots.start_time}">${moment(value.slot_booked_date+' '+value.ids_office_slots.start_time).format('LT')}</td>`;
                    // slotList += `<td class="slingle-line record-center">${value.delta}</td>`;
                    slotList += `<td class="js-card" data-bookingData="${btoa(JSON.stringify(value.id))}">${value.first_name}</td>`;
                    slotList += `<td class="js-card" data-bookingData="${btoa(JSON.stringify(value.id))}">${value.last_name}</td>`;
                    slotList += `<td class="">${value.email}</td>`;
                    slotList += `<td class="">${value.phone_number}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.postal_code}</td>`;
                    slotList += `<td class="">${value.ids_office.name}</td>`;
                    slotList += `<td class="">${value.ids_services_with_trashed.name}</td>`;
                    slotList += `<td class="">${photoServiceName}</td>`;
                    slotList += `<td class="record-center" data-order="${value.given_rate}">${value.given_rate}</td>`;
                    // slotList += `<td class="record-center">${value.noMasksGiven}</td>`;
                    let onlineFee = '';
                    if(value.ids_online_payment){
                        onlineFee = value.ids_online_payment.amount;
                    }
                    slotList += `<td class="record-center" data-order="${onlineFee}">${onlineFee}</td>`;
                    slotList += `<td class="record-center" data-order="${refundAmount}">${refundAmount}</td>`;
                    slotList += '<td class="record-center" >';
                    let refundStatus = '';
                    if(value.refund_status == 1){
                        refundStatus = 'Requested';
                    }else if(value.refund_status == 2){
                        refundStatus = 'Approved';
                    }else if(value.refund_status == 3){
                        refundStatus = 'Rejected';
                    }else{
                        refundStatus = '';
                    }
                    slotList += refundStatus+'</td>';
                    slotList += `<td class="">${value.refund_initiated_by.name_with_emp_no}</td>`;
                    slotList += `<td class="record-center" data-order="${value.refund_initiated_date}">${moment(value.refund_initiated_date).format('ll')}</td>`;
                    slotList += '<td class="">';
                    if(value.refund_completed_by){
                        slotList += value.refund_completed_by.name_with_emp_no;
                    }
                    slotList += '</td>';
                    slotList += '<td class="">';
                    if(value.refund_completed_date){
                        slotList += moment(value.refund_completed_date).format('ll');
                    }
                    slotList += '</td>';
                    // slotList += `<td class="slingle-line record-center" data-order="${value.refund_completed_date}">${moment(value.refund_completed_date).format('ll')}</td>`;
                    @can('ids_refund_update_status')
                        slotList += '<td class="">';
                        var note='';
                        var historyArr=value.ids_transaction_history;
                        var lastRow = historyArr[historyArr.length-1];
                        if(lastRow.refund_note)
                        {
                            note=lastRow.refund_note;
                        }
                        if(value.refund_status == 1){
                            slotList += '<a href="#" class="refundConfirm fa fa-check" data-toggle="tooltip"  data-id="'+value.id+'" data-refundAmount="'+refundAmount+'" title="'+note+'"></a>';
                        }else if(value.refund_status == 3){
                           slotList += '<a href="#" class="fa fa-comments-o reject" data-toggle="tooltip"  title="'+note+'"></a>';
                        }
                        slotList += '</td>';
                    @endcan
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
                                exportOptions: {
                                    columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]
                                }
                            },
                        ],
                        'fnRowCallback': function(nRow, aData, index){
                            if(aData[13]=='Rejected'){
                                bg_color="#ff9999";
                                color="black";
                                $(nRow).css('background-color', bg_color).css('color',color);
                            }else if(aData[13]=='Approved'){
                                bg_color="rgb(37 169 66 / 62%)";
                                color="black";
                                $(nRow).css('background-color', bg_color).css('color',color);
                            }
                        },
                        lengthMenu: [
                            [10, 25, 50, 100, 500, -1],
                            [10, 25, 50, 100, 500, "All"]
                        ],
                        destroy: true,
                        // scrollY: screenheight*.5,
                        scrollX: true,
                        scrollCollapse: true,
                        paging: true,
                    });

                } catch(e){
                    console.log(e.stack);
                }
            }
        }

        // Code to run when the document is ready.
        $(function() {
            idsReportAnalytics.init();
            // $("#report_start_date").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
            // $("#report_end_date").val(moment().format('YYYY-MM-DD'));
        });

        $('#idsRefundStatus').on('change', function() {
            if($(this).val() == 3){
                $("#rejected_reason").show();
                //$("#refund_amount").hide();
            }else{
                $("#rejected_reason").hide();
                //$("#refund_amount").show();
                // var id =  $('#refundConfirmModel input[name="entry_id"]').val();
                // var url = "{{route('idsscheduling-admin.office.slot-single-booking-trashed')}}";
                // $.ajax({
                //     url: url,
                //     type: 'GET',
                //     data: {'ids_booking_id': id},
                //     success: function(result) {
                //         if(result)
                //         {
                //             $('#refundConfirmModel input[name="refund_amount"]').val(result.ids_online_payment.amount);
                //         }

                //     },
                //     error: function (xhr, textStatus, thrownError) {
                //         if (xhr.status === 401) {
                //             window.location = "{{ route('login') }}";
                //         }
                //     }

                // });

            }
        });

        $("body").on("click", ".js-card", function(){
            let bookingId = JSON.parse(window.atob($(this).attr("data-bookingData")));
            var bookingDetailsUrl = "{{route('idsscheduling-admin.office.slot-single-booking-trashed')}}";
                    $.ajax({
                        url: bookingDetailsUrl,
                        type: 'GET',
                        data: {'ids_booking_id': bookingId},
                        success: function(slot) {
                            console.log(slot);
                            let photoServiceName = '';
                            if(slot.ids_passport_photo_service_with_trashed){
                                photoServiceName = slot.ids_passport_photo_service_with_trashed.name;
                            }
                            refundAmount = '';
                            let balanceFeeArray = slot.balance_fee.split("-");
                            if(balanceFeeArray.length == 2){
                                refundAmount = parseFloat(balanceFeeArray[1]).toFixed(2);
                            }
                            $('#entryDetailsModal').modal();
                            $('#entryDetailsModal .modal-title').text("Appointment Details");
                            $('#entryDetailsModal #pre-scheduled-date').text(moment(slot.slot_booked_date).format('dddd, LL'));
                            $('#entryDetailsModal #pre-scheduled-time').text(slot.ids_office_slots.display_name);
                            $('#entryDetailsModal #firstName').text(slot.first_name);
                            $('#entryDetailsModal #lastName').text(slot.last_name);
                            $('#entryDetailsModal #email').text(slot.email);
                            $('#entryDetailsModal #phoneNumber').text(slot.phone_number);
                            $("#entryDetailsModal #serviceName").text(slot.ids_services_with_trashed.name);
                            $("#entryDetailsModal #photo-service").text(photoServiceName);
                            $('#entryDetailsModal #officeName').text(slot.ids_office.name +', '+slot.ids_office.adress);
                            $("#entryDetailsModal #totalFeePayable").text('$'+slot.given_rate);
                            $("#entryDetailsModal #totalFeePaid").text('$'+slot.ids_online_payment.amount);
                            $("#entryDetailsModal #refund").text('$'+parseFloat(refundAmount));
                            $("#entryDetailsModal #paymentIntent").text(slot.ids_online_payment.payment_intent);
                            $("#entryDetailsModal #stripEmail").text(slot.ids_online_payment.email);
                            $("#entryDetailsModal #paymentStarted").text(moment(slot.ids_online_payment.started_time).format('LLL'));
                            $("#entryDetailsModal #paymentEnded").text(moment(slot.ids_online_payment.end_time).format('LLL'));
                            let isCandidateLabel = 'No';
                            if(slot.is_candidate == 1){
                                isCandidateLabel = 'Yes (Requisition No : '+slot.candidate_requisition_no+')';
                            }
                            $("#entryDetailsModal #isCandidate").text(isCandidateLabel);
                            let isFederalBillingLabel = 'No';
                            if(slot.is_federal_billing == 1){
                                isFederalBillingLabel = 'Yes';
                            }
                            $("#entryDetailsModal #isFederalBilling").text(isFederalBillingLabel);
                            let refundDetails = '';
                            refundDetails += '<ul>';
                            $.each(slot.ids_transaction_history, function(index, value) {
                                if(value.refund_status == null && value.user_id == null){
                                    refundDetails += "<li> $"+value.amount+" received through "+value.ids_payment_method.full_name +" payment on "+moment(value.created_at).format('MMMM Do YYYY, h:mm:ss A')+"</li>";
                                }
                                if(value.refund_status == null && value.user_id != null){
                                    refundDetails += "<li> $"+value.amount+" received through "+value.ids_payment_method.full_name+" on "+moment(value.created_at).format('MMMM Do YYYY, h:mm:ss A')+", processed by "+value.user.name_with_emp_no+"</li>";
                                }
                                let message = '';
                                if(value.refund_status == 0){
                                    message = 'request cancelled';
                                }else if(value.refund_status == 1){
                                    message = 'requested';
                                }else if(value.refund_status == 2){
                                    message = 'request approved';
                                }else if(value.refund_status == 3){
                                    message = 'request rejected';
                                }else{
                                }
                                if(message){
                                    refundDetails += '<li> Refund ($'+value.amount+') '+message;
                                    refundDetails +=" by "+value.user.name_with_emp_no+' on '+moment(value.created_at).format('MMMM Do YYYY, h:mm:ss A')+'.';
                                    if(value.refund_note){
                                        refundDetails +=" <br/> Refund Note : "+value.refund_note;
                                    }
                                    refundDetails +=" </li>";
                                }
                            });
                            refundDetails += '</ul>';
                            $('#refundDetails').html(refundDetails);

                        },
                        error: function (xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }

                    });



        });
    </script>



@stop
