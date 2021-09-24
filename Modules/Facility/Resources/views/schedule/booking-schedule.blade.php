@extends('layouts.app')

@section('css')
<style>
    #footer{
        margin-top: 4% !important;
    }
    .table_title{
        /* margin: 15px 0px; */
    }
    .section-display{
        display:none;
    }
    .master-slot-container{
        /* margin: 1%; */
        width: auto;
    }
    .day-slot-container{
        background-color: #d5d1d15e;
        border: 1px solid
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
        /* height: 80px; */
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
        border: 1px solid; */
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
    .booking-row-button{
        height: 46px;
        margin-top: 22px !important;
    }

    .container {
        border: 2px solid #e9ecef;
        /* background-color: #f9fbfc; */
        border-radius: 5px;
        padding: 10px 13px;
        margin: 10px 0;
    }

    .container::after {
        content: "";
        clear: both;
        display: table;
    }

    .booked-on{
        font-size: 13px;
    }
    .booked-by{
        font-weight: bold;
    }

    .clicked-slot-booking-details .container .btn-danger{
        margin-top: 30px;
    }
    .booked-contact-info{
        display: block;
        color: #878787;
        font-size: 15px;
        height: 20px;
    }
</style>
@stop

@section('content')

<div class="container-fluid" style="padding: 0px !important;">

    <!--- Start -- Page Title Section -->
    <div class="row table_title">
        <div class="col-md-12">
            <h4 style="float: left;">View Facility Schedule</h4>
        </div>
    </div>
    <!--- End -- Page Title Section -->

    <div class="row">
            <!--- Start -- Filter Section -->
            @include('facility::schedule.booking-filter')
            <!--- End -- Filter Section -->
            <div id="scheduling-table-container" class="master-slot-container col-sm-12"></div>
    </div>

    <!--- Start -- Booking details modal -->
    @include('facility::schedule.booking-modal')
    <!--- End -- Booking details modal -->

</div>

@stop

@section('scripts')
<script>

$(document).ready(function(){
    $(".select2").select2();
    });

    const booking = {
        ref:{
          slotData:[],
          clickedSlotData:[],
          selectedStartDate:'',
          selectedEndDate:'',
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

                let facilityId = $('#facilityId').val();
                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');

                $('#facilityServiceId').empty();
                $("#facility_service_id").addClass("section-display");

                if(singleServiceFacility == 0){

                    $("#facility_service_id").removeClass("section-display");
                    $('#facilityServiceId').empty().append($("<option></option>").attr("value",0).text('Please Select'));

                    $.ajax({
                    url: "{{route('cbs.booking.facility-service')}}",
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "facility_id":facilityId,
                        },
                    type: 'GET',
                    success: function(data) {
                        if(data.length >0){

                            $.each(data, function(index, service) {
                                $('#facilityServiceId').append($("<option></option>")
                                .attr("value",service.id)
                                .text(service.service));
                             });
                        }else{
                            $('#facilityServiceId').empty();
                            $('#facilityServiceId').empty().append($("<option></option>").attr("value",0).text('Please Select'));

                        }

                        }
                    });
                }

            })

            //Facility details submitting
            $('#facility-form').submit(function (e){
                root.hideSlotContanier();
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();

                let facilityId = $('#facilityId').val();
                let facilityServiceId = $('#facilityServiceId').val();
                let bookingDate = $('#facility_booking_date').val();
                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
                let today = moment().format('YYYY-MM-DD');
                let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');

                let booking_start_date = $('#facility_booking_start_date').val();
                let booking_end_date = $('#facility_booking_end_date').val();

                $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

                if(facilityId == '' || facilityServiceId == 0  ||
                booking_start_date == '' || booking_end_date == '' ||
                booking_end_date < booking_start_date){

                $.each(formData, (index, obj) => {
                    if(obj.name == "facility_id" && obj.value == 0){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Facility is required");
                    }else if(obj.name == "facility_service_id" && obj.value == 0 && facilityServiceId ==0 ){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Facility service is required");
                    }else if(obj.name == "booking_start_date" && obj.value == ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Start date is required");
                    }else if(obj.name == "booking_end_date" && obj.value == ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date is required");
                    }else if(obj.name == "booking_end_date" && obj.value != '' && booking_end_date < booking_start_date){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date must be greater than start date");
                    }
                });

                }else{
                    root.ref.selectedStartDate = booking_start_date;
                    root.ref.selectedEndDate = booking_end_date;
                    root.ref.facilityServiceId = facilityId;
                    root.ref.facilityId = facilityServiceId;
                    root.fetchSlotData();
                }
            });

            /**Start** On slotClick
            * set booked data and question answers on modal
            */
            $("body").on("click", ".slot-card", function(){
                root.ref.clickedSlotData = $(this).data('event');
                $('#bookingConfirmModal').modal();
                $('#bookingConfirmModal .modal-title').text("Facility Booked Details");
                // $('#booking-form')[0].reset();
                $('#bookingConfirmModal #facility_name').text($('#facilityId option:selected').text());
                $('#bookingConfirmModal #facility_date').text(root.ref.clickedSlotData.title+' '+root.ref.clickedSlotData.slotName);

                let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
                if(singleServiceFacility == 0){
                    $('#bookingConfirmModal #facility_service').text($('#facilityServiceId option:selected').text());
                }else{
                    $('#service').hide();
                }
                let bookingEntries = '';
                $.each(root.ref.clickedSlotData.bookedEntries, function(index, values) {
                    let created_at = moment(values.created_at).format('LLLL');
                    bookingEntries +=`<div class="container col-sm-12 row" id="${values.id}">
                                <div class="col-sm-11" >
                                    <div>
                                        <span class="booked-by"> ${values.facility_user.first_name } </span>
                                        <span class="booked-on pull-right"> ${created_at} </span>
                                    </div>
                                    <div class="booked-contact-info">Phone : ${values.facility_user.phoneno }</div>
                                    <div class="booked-contact-info">Email : ${values.facility_user.email }</div>
                                </div>
                                <div class="col-sm-1">
                                    <button class="btn btn-danger" data-bookingId="${values.id}" id="deleteBooking"> X </button>
                                </div>
                            </div>`;

                });
                $('#clicked-slot-booking-details').html(bookingEntries);

            });

            $("body").on("click", "#deleteBooking", function(){
                let bookingId = $(this).attr('data-bookingId');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                },
                function () {
                    root.deleteBookingEntry(bookingId);
                });

            });

        },
        fetchSlotData(){
            let root = this;
            let facilityId = $('#facilityId').val();
            let facilityServiceId = $('#facilityServiceId').val();
            let booking_start_date = $('#facility_booking_start_date').val();
            let booking_end_date = $('#facility_booking_end_date').val();
            let singleServiceFacility = $('#facilityId').find(':selected').attr('data-serviceAvaliable');
            // let today = moment().format('YYYY-MM-DD');
            // let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');
            $.ajax({
                url: "{{route('cbs.booking-data')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "facility_id":facilityId,
                    "facility_service_id":facilityServiceId,
                    "booking_start_date":booking_start_date,
                    "booking_end_date":booking_end_date,
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

                slotTotalLength = parseInt(slotTotalLength) + parseInt(value.slot.length);

                if(value.slot.length >= 1){

                    let daySlotDetails = [];
                    daySlotDetails = {
                        title: value.title,
                        date: value.date,
                        intervel: value.intervel,
                    };
                    let slotContainerClass ='';
                    if(value.is_today == true){
                        slotContainerClass ='today';
                    }

                    schedulingTableHtml +=` <div class="day-slot-container container-fluid ">
                                                <div class="all-container">
                                                    <div class="row slot-day-name">
                                                        <span class="col-sm-12"> ${value.title}  </span>
                                                        <p class="col-sm-12">${value.intervelTitle}</p>
                                                    </div>
                                                <div class="row slot-container ${slotContainerClass}">`;
                    $.each(value.slot, function(index, slotData) {
                        daySlotDetails['slotName'] = slotData.name;
                        daySlotDetails['displayName'] = slotData.display_name;
                        daySlotDetails['bookedEntries'] = slotData.booked;

                        if(slotData.booked_count == 0){
                            schedulingTableHtml +=`<button type="button" class="btn btn-light booking-row-button" disabled data-event=''>${slotData.display_name}</button>`;
                        }else{
                            let buttonName = slotData.display_name+' <br> '+slotData.booked_count+' Booking';
                            schedulingTableHtml +=`<button type="button" class="btn btn-light slot-card"  data-event='${JSON.stringify(daySlotDetails)}'>${buttonName}</button>`;
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
                    type: "warning",
                    confirmButtonText: "OK",
                });
            }
        },
        deleteBookingEntry(bookingId){
            let root = this;
            $.ajax({
                url: "{{route('cbs.booking-data.delete')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "facility_booking_id":bookingId,
                    },
                type: 'DELETE',
                success: function(data) {
                    $('#'+bookingId).hide();
                    root.fetchSlotData();
                    if(data.success) {
                        swal("Deleted", "Booking has been deleted successfully", "success");
                    }else {
                        swal("Warning", "This booking cannot be deleted", "warning");
                    }
                }
            });
        },
        hideSlotContanier(){
            let root = this;
            let facilityId = $('#facilityId').val();
            let facilityServiceId = $('#facilityServiceId').val();
            let booking_start_date = $('#facility_booking_start_date').val();
            let booking_end_date = $('#facility_booking_end_date').val();

            if(
            root.ref.selectedStartDate != booking_start_date ||
            root.ref.selectedEndDate != booking_end_date ||
            root.ref.selectedEndDate != booking_end_date ||
            root.ref.facilityId != facilityId ||
            root.ref.facilityServiceId != facilityServiceId
            ){
                $('#scheduling-table-container').hide();
            }

        },
        showSlotContanier(){
            $('#scheduling-table-container').show();
        },
    }

    // Code to run when the document is ready.
    $(function() {
        booking.init();
    });

</script>
@stop
