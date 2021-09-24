@extends('layouts.cgl360_facility_scheduling_layout')

@section('css')

<style>

    .table_title h4 {
        margin: 0px 0px 20px 15px;
        font-family: Montserrat;
        font-weight: bold;
        font-size: 16pt;
        color: rgb(51,63,80);
    }
    .table_title button{
        font-family: sans-serif !important;
    }
    #footer{
        margin-top: 4% !important;
    }
    .master-slot-container{
        margin: 1%;
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
        width: 170px;
        padding: 12px;
        background-color: #003A63 !important;
        color: #fff !important;
        font-weight: 700;
        cursor: pointer;
    }
    .slot-container button:hover{
        border: 1px solid;
        border-radius: 10px;

        background-color: #13486be0 !important;
        color: #fff !important;
    }
    .btn-light.disabled, .btn-light:disabled {
        border: 1px solid;
        background-color: #e0e0e0  !important;
        color: #04040461 !important;
    }
    .btn-light:disabled:hover{
        background-color: #e0e0e0  !important;
        color: #04040461 !important;
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
        text-align: center;
        display: block !important;
    }

</style>
@stop

@section('content')
<div class="container-fluid">
    <div class="table_title">
        <h4>Facility Scheduling </h4>
    </div>
    @include('facility::FacilityUser.partials.booking-filter')

    <div id="scheduling-table-container" class=" master-slot-container col-sm-12"></div>

    <!--Start-- Facility booking form  -->
        <div class="modal fade" id="bookingConfirmModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'booking-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                    <br>
                    <!-- {{ Form::hidden('office_id', null) }} -->
                    <div class="modal-body">

                        <div class="form-group row" id="facility">
                            <label id="facility_label" class="col-sm-3 control-label">Facility </label>
                            <label id="facility_name" class="col-sm-6 control-label view-form-element"> </label>
                        </div>

                        <div class="form-group row" id="service">
                            <label id="facility_service_label" class="col-sm-3 control-label"> Service</label>
                            <label id="facility_service" class="col-sm-6 control-label view-form-element">  </label>
                        </div>

                        <div class="form-group row" id="date">
                            <label id="date_label" class="col-sm-3 control-label"> Booking Date</label>
                            <label id="facility_date" class="col-sm-6 control-label view-form-element"> </label>
                        </div>

                        <div class="form-group row" id="facility">
                            <label id="slot_time_label" class="col-sm-3 control-label"> Slot Time</label>
                            <label id="slot_time" class="col-sm-6 control-label view-form-element"> </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>'to_be_rescheduling_mdl_save'))}}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <!--End-- Facility booking form -->
</div>

@stop

@section('scripts')

<script>

    const booking = {
        ref:{
          slotData:[],
          clickedSlotData:[],
          bookingWindow:0,
        },
        init(){
          this.registerEventListeners();
        },
        registerEventListeners() {
            let root = this;

            //Fetch allocated services of facility.
            $('.filterInputs').on('change', function(){
                root.hideSlotContanier();
            });

            $('#facilityId').on('change', function(){
                root.ref.bookingWindow = $('#facilityId').find(':selected').attr('data-bookingWindow');
                let facilityId = $('#facilityId').val();
                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
                let facilityPolicy = JSON.parse($('#facilityId').find(':selected').attr('data-facilityPolicy'));

                if(facilityPolicy.length >= 1){
                    $("#facility_policy").removeClass("section-display");
                    let facilityPolicyHtml = `<div class="col-sm-12 policy-section-title">Facility Policy</div>`;
                    facilityPolicyHtml += `<ol>`;
                    $.each(facilityPolicy, function(index, value) {
                        facilityPolicyHtml += `<li> ${value.policy}</li>`;
                    });
                    facilityPolicyHtml += `</ol>`;
                    $('#facility_policy').html(facilityPolicyHtml);
                }else{
                    $("#facility_policy").addClass("section-display");
                }

                $('#facilityServiceId').empty();
                $("#facility_service_id").addClass("section-display");

                if(singleServiceFacility == 0){

                    $("#facility_service_id").removeClass("section-display");
                    $('#facilityServiceId').empty().append($("<option></option>").attr("value",0).text('Please Select'));

                    $.ajax({
                        url: "{{route('facility.alocated-services')}}",
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "facility_id":facilityId,
                            },
                        type: 'GET',
                        success: function(data) {
                            if(data.length >0){
                                // $('#facilityServiceId').empty().append($("<option></option>").attr("value",0).text('Please Select'));
                                $.each(data, function(index, service) {
                                    $('#facilityServiceId').append($("<option></option>")
                                    .attr("value",service.id)
                                    .text(service.service));
                                });
                                // $("#facility_service_id").removeClass("section-display");
                            }else{
                                // $('#facilityServiceId').empty();
                                // $("#facility_service_id").addClass("section-display");
                            }

                        },
                        error: function(xhr, textStatus, thrownError) {
                            if(xhr.status === 401) {
                                window.location = "{{ route('facility.login') }}";
                            }
                        }
                    });
                }

            });

            //Facility details submitting
            $('#facility-form').submit(function (e){
                root.hideSlotContanier();
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();
                $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

                let facilityId = $('#facilityId').val();
                let facilityServiceId = $('#facilityServiceId').val();
                let bookingDate = $('#facility_booking_date').val();
                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
                let today = moment().format('YYYY-MM-DD');
                let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');
                let endDateMax = today;
                let bookingDayCount = parseInt(parseInt(root.ref.bookingWindow) - 1);
                if(bookingDayCount > 0){
                    endDateMax = moment(moment(today, "YYYY-MM-DD").add(bookingDayCount, 'days')).format('YYYY-MM-DD');
                }
                if(facilityId == '' || facilityServiceId == 0  ||
                bookingDate == '' || bookingDateFormat < today || bookingDateFormat > endDateMax){

                $.each(formData, (index, obj) => {
                    if(obj.name == "facility_id" && obj.value == ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Facility is required");
                    }else if(obj.name == "facility_service_id" && obj.value == 0 && facilityServiceId ==0 ){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Facility service is required");
                    }else if(obj.name == "booking_date" && obj.value == ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking date is required");
                    }else if(obj.name == "booking_date" && obj.value != '' && bookingDateFormat < today){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking date must be a greater than today");
                    }else if(obj.name == "booking_date" && obj.value != '' && bookingDateFormat > endDateMax && facilityId != ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking date can't be a greater than "+moment(endDateMax).format('MMMM D, YYYY'));
                    }
                });

                }else{
                    root.fetchSlotData();
                }
            });

            /**Start** On slotClick
            * set booked data and question answers on modal
            */
            $("body").on("click", ".slot-card", function(){
                root.ref.clickedSlotData = JSON. parse(atob($(this).data('event')));
                $('#bookingConfirmModal').modal();
                $('#bookingConfirmModal .modal-title').text("Facility Booking");
                // $('#booking-form')[0].reset();

                $('#bookingConfirmModal #facility_name').text($('#facilityId option:selected').text());

                $('#bookingConfirmModal #facility_date').text(root.ref.clickedSlotData.title);
                $('#bookingConfirmModal #slot_time').text(root.ref.clickedSlotData.displayName);

                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
                if(singleServiceFacility == 0){
                    $('#bookingConfirmModal #facility_service').text($('#facilityServiceId option:selected').text());
                }else{
                    $('#service').hide();
                }


            });

            /**Start** booking */
            $('#booking-form').submit(function (e){
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();

                let facilityId = $('#facilityId').val();
                let facilityServiceId = $('#facilityServiceId').val();
                let bookingDate = root.ref.clickedSlotData.date;
                let intervel = root.ref.clickedSlotData.intervel;
                let slotName = root.ref.clickedSlotData.slotName;
                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');

                $.ajax({
                        url: "{{route('facility.book-facility')}}",
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "facility_id":facilityId,
                            "facility_service_id":facilityServiceId,
                            "booking_date":bookingDate,
                            'single_service_facility':singleServiceFacility,
                            'slotName':slotName,
                            'intervel':intervel,
                            },
                        type: 'POST',
                        success: function(data) {

                            $("#bookingConfirmModal").modal("hide");
                            root.fetchSlotData();

                            if (data.success) {
                                var swalHtml = `Thank you for booking with us. Your appointment has been confirmed. Please check your email for more details.`;
                                swal({
                                    title: "Success",
                                    text : swalHtml,
                                    html: true,
                                    icon: "success",
                                    confirmButtonText: "OK",
                                });

                            }else{
                                if(data.message != null){
                                    swal({
                                        title: "Try Again",
                                        text: data.message,
                                        icon: "warning",
                                        confirmButtonText: "OK",
                                        });
                                 }
                            }
                        },
                        error: function(xhr, textStatus, thrownError) {
                            if(xhr.status === 401) {
                                window.location = "{{ route('facility.login') }}";
                            }
                        }
                    });

            });


        },
        fetchSlotData(){
            let root = this;
            let facilityId = $('#facilityId').val();
            let facilityServiceId = $('#facilityServiceId').val();
            let bookingDate = $('#facility_booking_date').val();
            let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
            let today = moment().format('YYYY-MM-DD');
            let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');
            $.ajax({
                url: "{{route('facility.booking-data')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "facility_id":facilityId,
                    "facility_service_id":facilityServiceId,
                    "booking_date":bookingDate,
                    'single_service_facility':singleServiceFacility,
                    },
                type: 'GET',
                success: function(data) {
                    root.ref.slotData = data;
                    if(root.ref.slotData.displayFormat.length <= 0){
                        swal({
                            title: "Try Again",
                            text: "Slot not available",
                            icon: "warning",
                            confirmButtonText: "OK",
                            });
                    }
                    root.setSlootContainer();
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('facility.login') }}";
                    }
                }
            });

        },
        setSlootContainer(){
            let root = this;
            //Display shot contanier
            root.showSlotContanier();
            var schedulingTableHtml = "";
            let slotTotalLength = 0;
            $.each(root.ref.slotData.displayFormat, function(index, value) {

                let daySlotDetails = [];
                daySlotDetails = {
                    title: value.title,
                    date: value.date,
                    intervel: value.intervel,
                };
                slotTotalLength = parseInt(slotTotalLength) + parseInt(value.slot.length);
                if(value.slot.length >= 1){
                    schedulingTableHtml +=` <div class="day-slot-container container-fluid ">
                                                <div class="all-container">
                                                    <div class="row slot-day-name">
                                                        <span class="col-sm-12"> ${value.title}  </span>
                                                        <p class="col-sm-12">${value.intervelTitle}</p>
                                                    </div>
                                                <div class="row slot-container">`;
                    $.each(value.slot, function(index, slotData) {
                        daySlotDetails['slotName'] = slotData.name;
                        daySlotDetails['displayName'] = slotData.display_name;
                        let daySlotDetailsEncoded = btoa(JSON.stringify(daySlotDetails));
                        if(slotData.booking_flag == 0){
                            schedulingTableHtml +=`<button type="button" title="Unavailable" class="btn btn-light slot-card" disabled data-event='${daySlotDetailsEncoded}'>${slotData.display_name}</button>`;
                        }else{
                            schedulingTableHtml +=`<button type="button" title="Available"  class="btn btn-light slot-card"  data-event='${daySlotDetailsEncoded}'>${slotData.display_name}</button>`;
                        }

                    });

                    schedulingTableHtml +=`     </div>
                                            </div>
                                        </div>`;
                }
            });
            $('#scheduling-table-container').html(schedulingTableHtml);

            if(slotTotalLength == 0){
                swal({
                    title: "Try Again",
                    text: "Slot not available",
                    icon: "warning",
                    confirmButtonText: "OK",
                });
            }
        },
        hideSlotContanier(){
            $('#scheduling-table-container').hide();
        },
        showSlotContanier(){
            $('#scheduling-table-container').show();
        },
    }

    // Code to run when the document is ready.
    $(function() {
        $('#facility-form')[0].reset();
        booking.init();
    });

</script>
@stop
