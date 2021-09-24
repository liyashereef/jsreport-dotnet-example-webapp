
@extends('layouts.cgl360_uniform_scheduling_layout')

@section('css')

<style>
    body{
        font-family: 'Montserrat' !important;
    }
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
        margin-top: 1%;
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
        /* width: 170px; */
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
   .common-note{
        margin-top: 2%;
        font-weight: bold;
        text-align: center;
        font-size: 16px;
        font-family: 'Montserrat';
    }
    .swal-booking-notes li{
        text-align: left;
        margin-bottom: 7px;
    }
    .sweet-alert button, .sweet-alert button:hover, .swal-button, .swal-button:hover, .sweet-alert button.cancel, .sweet-alert button.cancel:hover {
    background-color: #01233c !important;
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    <div class="table_title">
        <h4>Uniform Scheduling </h4>
    </div>
    @include('uniformscheduling::public.partials.booking-filter')
    @include('uniformscheduling::public.partials.booking-modal')

    <div id="scheduling-table-container" class=" master-slot-container"></div>


</div>

@stop

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>

<script>

    const booking = {
        ref:{
          slotData:[],
          clickedSlotData:[],
          bookingWindow:{{config("globals.uniform_scheduling_future_days")}},
        },
        init(){
          this.registerEventListeners();
        },
        registerEventListeners() {
            $(".legend-main-box").hide();
            let root = this;

            //Fetch allocated services of facility.
            $('.filterInputs').on('change', function(){
                root.hideSlotContanier();
            });

            //Facility details submitting
            $('#uniformscheduling-form').submit(function (e){
                root.hideSlotContanier();
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();
                $form.find('.form-group').removeClass('has-error').find('.help-block').text('');


                let bookingDate = $('#uniform_booked_date').val();

                let today = moment().format('YYYY-MM-DD');
                let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');
                let endDateMax = today;
                endDateMax = moment(moment(today, "YYYY-MM-DD").add(parseInt(root.ref.bookingWindow), 'days')).format('YYYY-MM-DD');

                if(bookingDate == '' || bookingDateFormat < today || bookingDateFormat > endDateMax){

                    $.each(formData, (index, obj) => {
                        if(obj.name == "booked_date" && obj.value == ''){
                            $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking date is required");
                        }else if(obj.name == "booked_date" && obj.value != '' && bookingDateFormat < today){
                            $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking date must be a greater than today");
                        }else if(obj.name == "booked_date" && obj.value != '' && bookingDateFormat > endDateMax){
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
                // root.ref.clickedSlotData = JSON. parse(atob($(this).data('event')));
                $('#myModal').find('input:text').val('');
                $('#myModal').modal();
                $('#myModal .modal-title').text("Uniform Scheduling Details");
                $('#scheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $('.questionSelect').val('')
                $(".other-option-text").hide();
                root.ref.clickedSlotData = JSON. parse(atob($(this).data('event')));
                $('#myModal #slotDetails').text(moment(root.ref.clickedSlotData.date).format('LL') +' - '+root.ref.clickedSlotData.displayName);
                $('#myModal #firstName').text('{{\Auth::user()->first_name}}');
                $('#myModal #lastName').text('{{\Auth::user()->last_name}}');
                $('#myModal #emailText').val('{{\Auth::user()->email}}');
                $('#myModal #phoneNumber').val('{{\Auth::user()->employee->phone}}');


            });

            /*--Start-- Custom Question other option need a text box.*/

            $("body").on("change", ".questionSelect", function(){
                let questionSelectVal = $(this).val();
                let optionId = $(this).attr('id');
                let fieldId = '#option-text-'+$(this).attr('data-questionId');
                if(questionSelectVal == 1){
                    $(fieldId).show();
                    $(fieldId).prop('required',true);
                }else{
                    $(fieldId).hide();
                    $(fieldId).removeAttr('required');
                }
            });

            $("body").on("change", "#genderValue", function(){
                let gender = $(this).val();
                $('#label_6 .mandatory').remove();
                if(gender == 1){
                    $('[name=point_value_6').removeAttr('required');
                    // $('#label_6 .mandatory').remove();
                    $('#point_value_6').hide();
                }else if(gender == 2){
                    $('#point_value_6').show();
                    $("[name=point_value_6").attr("required", "true");
                    $( "#label_6" ).append('<span class="mandatory">*</span>');
                }
            });


            /*--End-- Custom Question other option need a text box. */


            /**Start** booking */
            $('#scheduling-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('uniform.book-slot') }}";
                var formData = new FormData($('#scheduling-form')[0]);
                formData.append('booked_date', root.ref.clickedSlotData.date);
                formData.append('intervel', root.ref.clickedSlotData.intervel);
                formData.append('uniform_scheduling_office_timing_id', root.ref.clickedSlotData.officeTimingId);
                formData.append('uniform_scheduling_office_id', root.ref.clickedSlotData.officeId);
                formData.append('start_time', root.ref.clickedSlotData.startTime);
                formData.append('end_time', root.ref.clickedSlotData.endTime);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {

                        if (data.success) {
                            $('#scheduling-form')[0].reset();
                            $('#myModal').find('input:text').val('');
                            $('#myModal').modal('hide');
                            $('.form-group').removeClass('has-error').find('.help-block').text('');

                        var swalHtml = `<p> Thank you for booking with us. Your appointment has been
                                        confirmed. Please check your email for more details.</p>
                                        <br/>
                                        `;
                        swal({
                            title: "Successful",
                            text : swalHtml,
                            html: true,
                            type: "success",
                            confirmButtonText: "OK",
                        });
                        root.fetchSlotData();
                        } else {

                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            if(data.error){
                                associate_errors(data.error, $form, true);
                            }
                            if(data.message != null){
                                swal({
                                    title: "Try Again",
                                    text: data.message,
                                    type: "warning",
                                    confirmButtonText: "OK",
                                },function(){
                                    root.fetchSlotData();
                                    if(data.modalHide == true){
                                        $('#myModal').modal('hide');
                                    }
                                });
                            }

                        }
                    },
                    fail: function (response) {
                        // console.log(response);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                    },
                    contentType: false,
                    processData: false,
                });

            });


        },
        fetchSlotData(){
            let root = this;
            let bookingDate = $('#uniform_booked_date').val();
            let today = moment().format('YYYY-MM-DD');
            let bookingDateFormat = moment(bookingDate).format('YYYY-MM-DD');
            let endDateMax = today;
                endDateMax = moment(moment(today, "YYYY-MM-DD").add(parseInt(root.ref.bookingWindow), 'days')).format('YYYY-MM-DD');
            $.ajax({
                url: "{{route('uniform.booking-data')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "booked_date":bookingDate,
                    "end_date":endDateMax,
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
                        window.location = "{{ route('uniform.login') }}";
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
                    officeTimingId: value.uniform_scheduling_office_timing_id,
                    officeId: root.ref.slotData.uniform_scheduling_office_id,
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
                        daySlotDetails['startTime'] = slotData.start_time;
                        daySlotDetails['endTime'] = slotData.end_time;
                        let daySlotDetailsEncoded = btoa(JSON.stringify(daySlotDetails));
                        if(slotData.booking_flag == 0){
                            schedulingTableHtml +=`<button type="button" title="Unavailable" class="btn btn-light slot-card" disabled data-event='${daySlotDetailsEncoded}'>${slotData.name}</button>`;
                        }else{
                            schedulingTableHtml +=`<button type="button" title="Available"  class="btn btn-light slot-card"  data-event='${daySlotDetailsEncoded}'>${slotData.name}</button>`;
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
            $(".legend-main-box").hide();
            $('#scheduling-table-container').hide();
        },
        showSlotContanier(){
            $(".legend-main-box").show();
            $('#scheduling-table-container').show();
        },
    }

    // Code to run when the document is ready.
    $(function() {
        $('#uniformscheduling-form')[0].reset();
        booking.init();
    });


 /* Office location map   - Start */

    function getOfficeMap(){
        var lat = $('#lat').val();
        var lng = $('#lng').val();
        $('#mapModal').modal();
        var officeName = $('#office-name').text();
        $('#mapModalTitle').html(officeName+' Location');

        if(lat != ' ' && lng != ' '){
        initialize(new google.maps.LatLng(lat, lng));
        }else{
        initialize(new google.maps.LatLng());
        }

    }

  /* Map  initialize */
        function initialize(cordinates) {
            var mapCenter = cordinates;
            var markerFlag = true;
            if(cordinates == '(0, 0)'){
                mapCenter = {
                    lat:{{config('globals.map_default_center_lat')}},
                    lng: {{config('globals.map_default_center_lng')}}
                }
                markerFlag = false;
            }

            var renderContainer = document.getElementById("MapContainer");
            var mapProp = {
                center: mapCenter,
                zoom: 15
            };
            var map = new google.maps.Map(renderContainer, mapProp, {
                gestureHandling: 'greedy',
            });

            //Marker in the Map
            if(markerFlag == true){
                var marker = new google.maps.Marker({
                    position: cordinates,
                });
                marker.setMap(map);
            }



        }
 /* Office location map   - End */

</script>
@stop
