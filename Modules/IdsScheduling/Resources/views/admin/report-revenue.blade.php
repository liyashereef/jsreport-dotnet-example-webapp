@extends('layouts.app')
@section('title') - IDS Revenue  @stop
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
        <h4>IDS Revenue Report</h4>
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

                <div id="ids_service_id" class="form-group row col-sm-12">
                    <label for="ids_service_id" class="col-sm-4 col-form-label">Service</label>
                    <div class="col-sm-8" id="">
                        {{ Form::select('ids_service_id[]',$services,old('ids_service_id'),
                        array('class'=> 'form-control select2', 'id'=>'service','multiple'=>"multiple")) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>

                <div id="is_payment_received" class="form-group row col-sm-12">
                    <label for="is_payment_received" class="col-sm-4 col-form-label">Payment Received</label>
                    <div class="col-sm-8" id="">
                        <input type="radio" class="is_payment_received" name="is_payment_received" id="payment_received_yes"  value="1" >&nbsp;Yes&nbsp;&nbsp;
                        <input type="radio" class="is_payment_received" name="is_payment_received" id="payment_received_no"  value="0" >&nbsp;No&nbsp;&nbsp;
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>

                <div id="ids_payment_method_id" class="form-group row col-sm-12 display-none">
                    <label for="ids_payment_method_id" class="col-sm-4 col-form-label">Payment Methods</label>
                    <div class="col-sm-8">
                        {{ Form::select('ids_payment_method_id[]',$paymentMethods, old('ids_payment_method_id'),
                        array('class'=> 'form-control select2','id'=>'paymentMethods','multiple'=>"multiple")) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>

                <div id="ids_payment_reason_id" class="form-group row col-sm-12 display-none">
                    <label for="ids_payment_reason_id" class="col-sm-4 col-form-label">Payment Reasons</label>
                    <div class="col-sm-8">
                        {{ Form::select('ids_payment_reason_id[]',$paymentReasons+[1=>'Other'], old('ids_payment_reason_id'),array('class'=> 'form-control select2','id'=>'paymentReasons','multiple'=>"multiple")) }}
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
                        <div> 12 Month Rolling Revenue</div>
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
                    <th class="slingle-line">Office</th>
                    <th class="slingle-line">Service</th>
                    <th class="slingle-line">Photo Service</th>
                    {{-- <th class="slingle-line">No. Mask Provided</th> --}}
                    {{-- <th class="slingle-line">Client Show Up</th> --}}
                    <th class="slingle-line">Total Fees($)</th>
                    <th class="">Online Payment($)</th>
                    <th class="slingle-line">Refund($)</th>
                    <th class="">Refund Status</th>
                    <th class="slingle-line">Balance Due($)</th>
                    <th class="">Payment Recieved</th>
                    <th class="">Payment Type</th>
                    <th class="slingle-line">Payment Reason</th>
                    <th class="slingle-line">Other Reason</th>
                    <th class="slingle-line">Processed By</th>
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
                revenueData : [],
                revenueCancelledOrDeletedData : [],
                yearRevenuesData : [],
                analyticsTable: null,
                dataTable : null,
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

                $('#report-form .is_payment_received').on('click', function() {
                    root.paymentReceivedStatus();
                });
            },
            paymentReceivedStatus(){
                if($('input[name=is_payment_received]:checked', '#report-form').val() == 1){
                    $("#ids_payment_method_id").removeClass('display-none');
                    $("#ids_payment_reason_id").addClass('display-none');
                }else if($('input[name=is_payment_received]:checked', '#report-form').val() == 0){
                    $("#ids_payment_method_id").addClass('display-none');
                    $("#ids_payment_reason_id").removeClass('display-none');
                }else{
                    $("#ids_payment_method_id").addClass('display-none');
                    $("#ids_payment_reason_id").addClass('display-none');
                }
            },
            fetchReportDataEvent(){
                let root = this;
                let url = '{{ route("idsscheduling-admin.office.revenue-reports") }}';
                let ids_payment_method_id = $('#paymentMethods').val();
                let ids_payment_reason_id = $('#paymentReasons').val();
                let is_payment_received = $("input[name='is_payment_received']:checked").val();

                if(is_payment_received != null && is_payment_received <=1){
                    if(is_payment_received ==1){
                        ids_payment_reason_id = null;
                    }else{
                        ids_payment_method_id =null;
                    }
                }else{
                    ids_payment_method_id =null;
                    ids_payment_reason_id = null;
                }
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
                        "ids_payment_method_id":ids_payment_method_id,
                        "ids_payment_reason_id":ids_payment_reason_id,
                        'is_payment_received':$("input[name='is_payment_received']:checked").val()
                    },
                    type: 'GET',
                    success: function(data) {
                        root.ref.revenueData = data.revenues;
                        root.ref.yearRevenuesData = data.months_revenue;
                        root.ref.revenueCancelledOrDeletedData = data.revenues_canceled_or_deleted;
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
                    slotList += `<tr>`;
                    // slotList += `<td class="slingle-line record-center">${index + 1}</td>`;
                    // slotList += `<td class="slingle-line record-center" data-order="${value.transactionAt}">${value.transactionDate}</td>`;
                    // slotList += `<td class="slingle-line record-center">${value.transactionTime}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.slot_booked_date+' '+value.ids_office_slots.start_time}">${moment(value.slot_booked_date).format('ll')}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.ids_office_slots.start_time}">${moment(value.slot_booked_date+' '+value.ids_office_slots.start_time).format('LT')}</td>`;
                    // slotList += `<td class="slingle-line record-center">${value.delta}</td>`;
                    slotList += `<td class="">${value.first_name}</td>`;
                    slotList += `<td class="">${value.last_name}</td>`;
                    slotList += `<td class="">${value.email}</td>`;
                    slotList += `<td class="">${value.phone_number}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.postal_code}</td>`;
                    slotList += `<td class="slingle-line">${value.ids_office.name}</td>`;

                    slotList += `<td class="">${value.ids_services.name}</td>`;
                    var photoService='';
                    if(value.ids_passport_photo_service_with_trashed)
                    {
                        photoService=value.ids_passport_photo_service_with_trashed.name;
                    }
                    slotList += `<td class="">${photoService}</td>`;
                    // slotList += `<td class="record-center">${value.noMasksGiven}</td>`;
                    // slotList += `<td class="record-center">${value.clientShowUp}</td>`;
                    slotList += `<td class="record-center" data-order="${value.given_rate}">${value.given_rate}</td>`;
                    let onlineFee = '';
                    if(value.ids_online_payment){
                        onlineFee = value.ids_online_payment.amount;
                    }
                    slotList += `<td class="record-center" data-order="${onlineFee}">${onlineFee}</td>`;
                    let refundFee = '';
                    let balanceDue = '';
                    if(value.balance_fee < 0){
                        refundFee = parseFloat(value.balance_fee * -1).toFixed(2);
                    }
                    if(value.balance_fee > 0){
                        balanceDue = parseFloat(value.balance_fee).toFixed(2);
                    }
                    slotList += `<td class="record-center" data-order="${refundFee}">${refundFee}</td>`;
                    var refundStatus = '';
                    if (value.refund_status ==0) {
                        refundStatus='Not Requested';
                    }else if (value.refund_status ==1) {
                        refundStatus='Requested';
                    }else if (value.refund_status ==2) {
                        refundStatus='Approved';
                    }else if (value.refund_status ==3) {
                        refundStatus='Rejected';
                    }
                    slotList += `<td class="">${refundStatus}</td>`;
                    slotList += `<td class="record-center" data-order="${balanceDue}">${balanceDue}</td>`;
                    var paymentRecieved = '';
                    if(value.is_online_payment_received == 1 || value.is_payment_received == 1){
                        paymentRecieved = 'Yes';
                    }else{
                        paymentRecieved = 'No';
                    }
                    slotList += `<td class="record-center">${paymentRecieved}</td>`;
                    var paymentMethods = '';
                    if(value.ids_payment_methods_with_trashed){

                        if(value.is_online_payment_received == 1)
                        {
                            var isOfflineOrOnline='Online-Stripe and ';
                        }else{
                            var isOfflineOrOnline='';
                        }
                        paymentMethods = isOfflineOrOnline+value.ids_payment_methods_with_trashed.full_name;
                    }else{
                        if(value.is_online_payment_received == 1)
                        {
                            paymentMethods='Online-Stripe';
                        }
                    }
                    slotList += `<td class=""> ${paymentMethods}</td>`;
                    var paymentReason = '';
                    if(value.ids_payment_reasons_with_trashed){
                        paymentReason = value.ids_payment_reasons_with_trashed.name;
                    }
                    slotList += `<td class="">${paymentReason}</td>`;
                    var otherReason = '';
                    if(value.payment_reason){
                        otherReason = value.payment_reason;
                    }
                    slotList += `<td class="">${otherReason}</td>`;
                    // slotList += `<td class="slingle-line ">${value.paymentType}</td>`;
                    var updatedBy = '';
                    if(value.updated_by){
                        updatedBy = value.updated_by.name_with_emp_no;
                    }
                    slotList += `<td class="">${updatedBy}</td>`;
                    slotList += `</tr>`;
                });
                $.each(root.ref.revenueCancelledOrDeletedData, function(index, value) {
                    slotList += `<tr>`;
                    // slotList += `<td class="slingle-line record-center">${index + 1}</td>`;
                    // slotList += `<td class="slingle-line record-center" data-order="${value.transactionAt}">${value.transactionDate}</td>`;
                    // slotList += `<td class="slingle-line record-center">${value.transactionTime}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.slot_booked_date+' '+value.ids_office_slots.start_time}">${moment(value.slot_booked_date).format('ll')}</td>`;
                    slotList += `<td class="slingle-line record-center" data-order="${value.ids_office_slots.start_time}">${moment(value.slot_booked_date+' '+value.ids_office_slots.start_time).format('LT')}</td>`;
                    // slotList += `<td class="slingle-line record-center">${value.delta}</td>`;
                    slotList += `<td class="">${value.first_name}</td>`;
                    slotList += `<td class="">${value.last_name}</td>`;
                    slotList += `<td class="">${value.email}</td>`;
                    slotList += `<td class="">${value.phone_number}</td>`;
                    slotList += `<td class="slingle-line record-center">${value.postal_code}</td>`;
                    slotList += `<td class="slingle-line">${value.ids_office.name}</td>`;

                    slotList += `<td class="">${value.ids_services.name}</td>`;
                    var photoService='';
                    if(value.ids_passport_photo_service_with_trashed)
                    {
                        photoService=value.ids_passport_photo_service_with_trashed.name;
                    }
                    slotList += `<td class="">${photoService}</td>`;
                    // slotList += `<td class="record-center">${value.noMasksGiven}</td>`;
                    // slotList += `<td class="record-center">${value.clientShowUp}</td>`;
                    slotList += `<td class="record-center" data-order="${value.given_rate}">${value.given_rate}</td>`;
                    var onlineFee = '';
                    if(value.ids_online_payment){
                        onlineFee = value.ids_online_payment.amount;
                    }
                    slotList += `<td class="record-center" data-order="${onlineFee}">${onlineFee}</td>`;
                    let refundFee = '';
                    let balanceDue = '';
                    if(value.balance_fee < 0){
                        refundFee = parseFloat(value.balance_fee * -1).toFixed(2);
                    }
                    if(value.balance_fee > 0){
                        balanceDue = parseFloat(value.balance_fee).toFixed(2);
                    }
                    slotList += `<td class="record-center" data-order="${refundFee}">${refundFee}</td>`;

                    var refundStatus = '';
                    if (value.refund_status ==0) {
                        refundStatus='Not Requested';
                    }else if (value.refund_status ==1) {
                        refundStatus='Requested';
                    }else if (value.refund_status ==2) {
                        refundStatus='Approved';
                    }else if (value.refund_status ==3) {
                        refundStatus='Rejected';
                    }
                    slotList += `<td class="">${refundStatus}</td>`;
                    slotList += `<td class="record-center" data-order="${balanceDue}">${balanceDue}</td>`;
                    var paymentRecieved = '';
                    if(value.is_online_payment_received == 1 || value.is_payment_received == 1){
                        paymentRecieved = 'Yes';
                    }else{
                        paymentRecieved = 'No';
                    }
                    slotList += `<td class="record-center">${paymentRecieved}</td>`;
                    var paymentMethods = '';
                    if(value.ids_payment_methods_with_trashed){

                       if(value.is_online_payment_received == 1)
                       {
                           var isOfflineOrOnline='Online-Stripe and ';
                       }else if(value.is_online_payment_received == 2)
                       {
                           var isOfflineOrOnline='Offline and ';
                       }else{
                           var isOfflineOrOnline='';
                       }
                       paymentMethods = isOfflineOrOnline+value.ids_payment_methods_with_trashed.full_name;
                   }else{
                       if(value.is_online_payment_received == 1)
                       {
                           paymentMethods='Online-Stripe';
                       }
                   }
                    slotList += `<td class=""> ${paymentMethods}</td>`;
                    var paymentReason = '';
                    if(value.ids_payment_reasons_with_trashed){
                        paymentReason = value.ids_payment_reasons_with_trashed.name;
                    }
                    slotList += `<td class="">${paymentReason}</td>`;
                    var otherReason = '';
                    if(value.payment_reason){
                        otherReason = value.payment_reason;
                    }
                    slotList += `<td class="">${otherReason}</td>`;
                    // slotList += `<td class="slingle-line ">${value.paymentType}</td>`;
                    var updatedBy = '';
                    if(value.updated_by){
                        updatedBy = value.updated_by.name_with_emp_no;
                    }
                    slotList += `<td class="">${updatedBy}</td>`;
                    slotList += `</tr>`;
                });
                $('#tableBody').html(slotList).after(function(e){
                    root.initAnalyticsTable();
                });


                var rollingForecastDataSet = '';
                $.each(root.ref.yearRevenuesData, function(index, value) {
                    var display_value = '$'+value.total_fee;
                    if(value.total_fee == 0){
                        display_value = '';
                    }
                    rollingForecastDataSet += "<div class='col-sm-2 month-section'>";
                    rollingForecastDataSet += "<div class='month-title'>"+value.title+"</div>";
                    rollingForecastDataSet += "<div class='month-data' >"+display_value+"</div>";
                    rollingForecastDataSet += "</div>";
                });
                $('#rolling-forecast-dataset').html(rollingForecastDataSet);
                $('#rolling-forecast').removeClass('display-none');

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
            $("#report_start_date").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
            $("#report_end_date").val(moment().format('YYYY-MM-DD'));
        });

    </script>



@stop
