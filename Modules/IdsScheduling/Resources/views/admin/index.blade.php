@extends('layouts.app')

@section('css')
<link href="{{ asset('css/ids.css') }}" rel="stylesheet">
<style>
    #content-div {
        width: 97%;
    }
    .table thead th {
        border: 1px solid #524c4c6e;
    }
    .filter-div {
        padding-bottom: 15px;
    }
    .col-form-label {
        padding-left: 2%;
    }
    .master-slot-container{
            /* margin: 1%; */
            width: auto;
        }
        .day-slot-container{
            background-color: #d5d1d15e;
            border: 1px solid;
        }
        .container-fluid {
            margin-bottom: 5px;
        }
        .day-slot-container .all-container{

        }
        .slot-day-name{
            background-color: #f36905;
            padding: 10px 10px;
            color: #ffffff;
        }
        .slot-day-name span{
            font-weight: bold;
        }
        .slot-day-name p{
            margin-top: 0;
            margin-bottom: 0px;
        }
        .slot-container{
            background-color: #ffffff;
        }
        .slot-container button{
            margin: 12px 5px;
            border: 1px solid;
            border-radius: 10px;
            /* width: 95px; */
            padding: 12px;
            background-color: #003A63;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .slot-container button:hover{
            border: 1px solid;
            border-radius: 10px;

            background-color: #13486be0;
            color: #fff !important;
        }
        .btn-light.disabled, .btn-light:disabled {
            border: 1px solid;
            background-color: #e0e0e0;
            color: #04040494 !important;
        }
        .btn-light:disabled:hover{
            background-color: #e0e0e0 ;
            color: #04040494 !important;
        }
        .slot-details{
            border: 1px solid;
            border-radius: 10px;
            margin: 12px;
            padding: 12px;
            text-align:center;
            width: 95px;
            background-color: #ffffff;
            cursor: pointer;
        }
        .today {
            background: #E6E6FA !important;
        }
        .facility-policy-section{
            border: 1px solid #15151561;
            width: 99%;
            margin-left: -2%;
        }
        .policy-section-title{
            color: #f36905;
            font-weight: bold;
            margin-bottom: 1%;
            margin-top: 1%;
        }
        .modal-footer {
            text-align: right;
        }
        .slot-not-available{
            height: 50px;
            padding: 13px 1% 1% 1%;
            background-color: #ffffff5e;
            padding-top: 13px;
            padding-left: 25px;
        }
        .white-bg{
            background-color: white !important;
        }
        .tobe-rescheduled-btn{
            background-color: red !important;
        }
        .isCandidate{
            /* background-color: #21a71d !important; */
            background: rgba(36, 169, 66, 0.62) !important;
            color: #383737 !important;
        }
        .slot-container .isCandidate:hover{
            background: rgba(36, 169, 66, 0.62) !important;
            color: #383737 !important;
        }
        .booingWithPhoto{
            /* background-color: #f9f204 !important; */
            background: #ffe690 !important;
            color: #383737 !important;
        }
        .slot-container .booingWithPhoto:hover{
            /* background-color: #f9f204 !important; */
            background: #ffe690 !important;
            color: #383737 !important;
        }
        #rescheduleModal {
            overflow-y: scroll
        }
</style>
@stop
@section('content')

    <!-- IDS rescheduling Form - Start -->
        <div class="table_title">
            <div class="row">
                <h4 class="col-sm-9">View Schedule</h4>
                @can('reschedule_request')
                <div class="col-sm-3">
                    <button id="toBeRescheduleModalButton" class="button btn btn-primary blue pull-right " >Reschedule Request</button>
                </div>

                @endcan
            </div>
        </div>
        <div class="filter-div">
            <div class="row">
                <div class="col-md-6">
                {{ Form::open(array('id'=>'reschedule-form', 'autocomplete'=>'off', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
                <div id="ids_office_id" class="form-group row col-md-12">
                    <label for="ids_office_id" class="col-md-3 col-form-label">Office Location</label>
                    <div class="col-md-9">
                        {{-- {{ Form::select('ids_office_id',[0=>'Please Select']+$offices, old('ids_office_id'),array('class'=> 'form-control select2', 'required'=>TRUE, 'id'=>'office')) }} --}}
                        <select id="office" name="ids_office_id" required="TRUE" class="form-control select2" required="true">
                            <option value="">Please Select</option>
                            @foreach($officeList as $office)
                                <option value="{{$office->id}}" data-isPhotoService="{{$office->is_photo_service}}">
                                    {{$office->name}} - {{$office->adress}}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>
                <div id="start_date" class="form-group row col-md-12">
                    <label for="start_date" class="col-md-3 col-form-label">Start Date</label>
                    <div class="col-md-9">
                        {{ Form::text('start_date', null, array('class'=>'form-control datepicker', 'id'=>'reschedule_start_date')) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>
                <div id="end_date" class="form-group row col-md-12">
                    <label for="end_date" class="col-md-3 col-form-label">End Date</label>
                    <div class="col-md-9">
                        {{ Form::text('end_date', null, array('class'=>'form-control datepicker', 'id'=>'reschedule_end_date')) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>
                <div class="form-group row col-md-12" style="text-align:center">
                    <div class="col-md-12">
                        {{ Form::submit('Search', array('class'=>'button btn btn-primary blue','style'=>'width: 100px;'))}}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        </div>
    <!-- Rescheduling table container - Start-->
    <div class="row">
        <div id="rescheduling-table-container" class="col-sm-12 admin-container"></div>
    </div>
    <!-- Rescheduling table container - End-->
    @include('idsscheduling::admin.partials.booking-details-modal')
    @stop
    @section('scripts')
    <script type="text/javascript">
     $('#office').select2();
     $("#toBeRescheduleModalButton").hide();

        ids_reschedule_date_val = '';
        ids_reschedule_date_pre_val = '';

        start_date_val = '';
        start_date_pre_val = '';

        end_date_val = '';
        end_date_pre_val = '';

        /* Scheduling date on change event  - Start */
        $('#reschedule_start_date').on('change', function() {
            start_date_val = $('#reschedule_start_date').val();
            if(start_date_val !== start_date_pre_val){
                start_date_pre_val = start_date_val;
                $("#scheduling-table-container").remove();
            }
        });

        $('#reschedule_end_date').on('change', function() {
            end_date_val = $('#reschedule_end_date').val();
            if(end_date_val !== end_date_pre_val){
                end_date_pre_val = end_date_val;
                $("#scheduling-table-container").remove();
            }

        });

        $('#toBeRescheduleModalButton').on('click', function() {
            $('#toBeRescheduleModal').modal();
            $('#toBeRescheduleModal .modal-title').text("Reschedule Request");
        });

        /* Scheduling date on change event  - End */
        $(".select2").select2();
        /* Office location on change event - Start */
        // $('#office').on('input', function() {
        $("#reschedule-form").on("change", "#office", function(e) {

            $("#scheduling-table-container").remove();
            $('#scheduleModal #service').empty().append($("<option></option>").attr("value",0).text('Please Select'));
            var id = $(this).val();
            var base_url = "{{route('ids-office-services', ':id')}}";
            var url = base_url.replace(':id', id);
            let isPhotoService = $(this).find(':selected').attr('data-isPhotoService');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('#rescheduleModal #idsServiceId').empty().append($("<option></option>")
                    .attr("value",'')
                    .attr("data-isPhotoService",'')
                    .attr("data-isPhotoServiceRequired",'')
                    .attr("data-rate",0)
                    .attr("data-isTax",0)
                    .attr("data-tax",0)
                    .attr("data-taxEffectiveFromDate",'')
                    .text('Please Select'));

                    $.each(data, function(index, service) {

                        let isServiceList = true;
                        if(isPhotoService == 0 && service.is_photo_service_required == 1){
                            isServiceList = false;
                        }
                        let isTax = 0;
                        let tax = 0;
                        let taxEffectiveFromDate = '';
                        if(service.tax_master && service.tax_master.tax_master_log){
                            // if(moment(moment(service.tax_master.tax_master_log.effective_from_date).format('YYYY-MM-DD')).isBefore(moment().format('YYYY-MM-DD'))){
                                isTax = 1;
                                tax = service.tax_master.tax_master_log.tax_percentage;
                                taxEffectiveFromDate = service.tax_master.tax_master_log.effective_from_date;
                            // }
                        }
                        if(isServiceList == true){
                            $('#idsServiceId').append($("<option></option>")
                            .attr("value",service.id)
                            .attr("data-isPhotoService",service.is_photo_service)
                            .attr("data-isPhotoServiceRequired",service.is_photo_service_required)
                            .attr("data-rate",service.rate)
                            .attr("data-isTax",isTax)
                            .attr("data-tax",tax)
                            .attr("data-taxEffectiveFromDate",taxEffectiveFromDate)
                            .text(service.name));
                        }
                    });

                }
            });
        });
        /* Office location on change event - End */


        /* Rescheduling form submission - Start */
        $('#reschedule-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var office = $('#office').val();
            //var service = $('#service').val();
            var start_date = $('#reschedule_start_date').val();
            var end_date = $('#reschedule_end_date').val();
            var formData = $(this).serializeArray();
            var currentDate = new Date();
            var reschedule_start_date = new Date(start_date);
            var reschedule_end_date = new Date(end_date);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            if(office == 0 || reschedule_end_date < reschedule_start_date || (start_date !='' && end_date=='') || (start_date =='' && end_date!='')){
                $.each(formData, (index, obj) => {
                    if(obj.name == "ids_office_id" && obj.value == 0){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Office is required");
                    }else if(obj.name == "end_date" && obj.value != '' && reschedule_end_date < reschedule_start_date){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date must be a greater than start date");
                    }else if(obj.name == "end_date"  && obj.value == '' && start_date !=''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date is required");
                    }
                    else if(obj.name == "start_date"  && obj.value == '' && end_date !=''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Start date is required");
                    }
                });
            }else{
                $('#slot-rescheduling-table').remove();
                $.ajax({
                    // url: "{{route('idsscheduling-admin.office.slot-details')}}",
                    url: "{{route('idsscheduling-admin.office.slot-timings')}}",
                    data: {"ids_office_id":office, "start_date":start_date, "end_date":end_date},
                    type: 'GET',
                    success: function(data) {

                        var slot_color;
                        var slot_title;
                        var slot_id;
                        let schedulingTableHtml = '';
                        let slotData =data.daySlotDetails;

                        schedulingTableHtml = `<div id="scheduling-table-container" class=" master-slot-container" style="">`;
                            if(data.daySlotDetails && slotData.length != 0){
                                $.each(data.daySlotDetails, function(index, value) {

                                schedulingTableHtml += `<div class="day-slot-container container-fluid ">
                                        <div class="all-container">
                                            <div class="row slot-day-name">
                                                <span class="col-sm-12"> ${value.format_date}</span>
                                                <p class="col-sm-12">${value.intervel_text}</p>
                                            </div>
                                            <div class="row slot-container">`;
                                                $.each(value.slots, function(index, slot) {
                                                    let className = '';
                                                    if(slot.status == 1){
                                                        slot_class = 'white-bg';
                                                        slot_title = 'Open Slot'
                                                    }else if(slot.status == 2){
                                                        slot_class = 'js-card';
                                                        slot_title = 'Scheduled Slot';
                                                        if(slot.booing_with_photo == 1 && slot.is_candidate!= 1){
                                                            slot_class = slot_class+' booingWithPhoto';
                                                        }
                                                        if(slot.is_candidate == 1){
                                                            slot_class = slot_class+' isCandidate';
                                                        }
                                                    }else if(slot.status == 3){
                                                        slot_class = 'tobe-rescheduled-btn js-card';
                                                        slot_title = 'To Be Rescheduled'
                                                        if(slot.is_online_payment == 0){
                                                            slot_class = '';
                                                            slot_title = 'Online Payment Pending';
                                                        }
                                                        // if(slot.booing_with_photo == 1 && slot.is_candidate!= 1){
                                                        //     slot_class = slot_class+' booingWithPhoto';
                                                        // }
                                                        // if(slot.is_candidate == 1){
                                                        //     slot_class = slot_class+' isCandidate';
                                                        // }
                                                    }else{ // For Blocked slot
                                                        slot_class = '';
                                                        slot_title = 'Temporarily Closed';
                                                        if(slot.is_online_payment != null){
                                                            slot_title = 'Online Payment Pending';
                                                        }
                                                    }

                                                    if(slot.status == 2 || slot.status == 3 && slot.is_online_payment != 0){
                                                        schedulingTableHtml += `<button type="button"
                                                        class="btn btn-light ${slot_class}"
                                                        title="${slot_title}"
                                                        data-bookingid="${slot.booking_id}"
                                                        data-bookingdate="${value.format_date}"
                                                        data-slotname="${slot.display_name}"
                                                        >
                                                        ${slot.title}
                                                        </button>`;
                                                    }else{
                                                        schedulingTableHtml += ` <button type="button"
                                                        title="${slot_title}"
                                                        class="btn btn-light ${slot_class}" disabled >
                                                        ${slot.title}
                                                        </button>`
                                                    }
                                                });
                                                schedulingTableHtml += `</div>
                                        </div>
                                    </div>`;

                                });
                            }else{
                                schedulingTableHtml += ` <div class="day-slot-container container-fluid slot-not-available">
                                    <div class="all-container">
                                        <div class="row slot-container">
                                            Slot not available.
                                        </div>
                                    </div>
                                </div>`
                            }
                            schedulingTableHtml += `</div>`;

                            $('#rescheduling-table-container').html(schedulingTableHtml);

                            //To-Be-Reschedule modal.
                            $("#toBeRescheduleModalButton").show();
                            $('#toBeRescheduleModal input[name="office_id"]').val(office);
                            // ids_service_id
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                });
            }
        });
        /* Rescheduling form submission - End */

        /* Slot scheduling form submission - Start */
        $('#ids-slot-scheduling-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('ids-office.slot-booking') }}";
            var formData = new FormData($('#ids-slot-scheduling-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                    $('#scheduleModal').find('input:text').val('');
                    $('#scheduleModal').modal('hide');
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    swal({
                        title: "Saved",
                        text: "Scheduled successfully",
                        type: "success",
                        confirmButtonText: "OK",
                    },function(){
                        $( "#reschedule-form" ).trigger( "submit" );
                    });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        associate_errors(data.error, $form, true);
                    }
                },
                fail: function (response) {

                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
                contentType: false,
                processData: false,
            });
        });
        /* Slot scheduling form submission - End */

        function trigerOnScheduleUpdate(reloadAll = false){
            $("#reschedule-form").trigger( "submit" );
        }

  </script>
  @include('idsscheduling::admin.partials.modal-script')
  @stop
