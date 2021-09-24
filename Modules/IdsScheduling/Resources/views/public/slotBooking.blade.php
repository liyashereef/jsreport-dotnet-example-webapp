@extends('layouts.cgl360_ids_scheduling_layout')
@section('title', 'IDS - '. config('app.name'))
@section('css')
<style>
    @media screen and (min-width: 580px){
        .sweet-alert {
            margin-left: -16%;
            width: 575px;
        }
    }
    .table_title h4 {
        margin-left: 14px;
    }
    .time-slot-label{
        border-top: 1px solid #524c4c6e !important;
        border-left: 1px solid #524c4c6e !important;
    }
    .time-slot-label > div{
        width: 161px !important;
        margin: 0% 0% 22% 0% !important;
    }
    .table thead th {
        border-bottom: 0px;
    }
    .ids-service-label p{
        color: #dc3545!important;
        font-size: 13px;
        font-weight: 500px;
        margin-top: -3%;
    }
    .swal-booking-notes li{
        text-align: left;
        margin-bottom: 7px;
    }
    .common-note{
        margin-top: 2%;
        font-weight: bold;
        /* text-align: center; */
        font-size: 16px;
        font-family: 'Montserrat';
    }
    .text-muted {
        color: #000000 !important;
    }

    .sweet-alert p {
        color: #000000 !important;
    }
    .service-description a{
        margin-left: 10px;
    }
    .service-description a:hover{
        color: #007bff;
        cursor: pointer;
        margin-left: 10px;
        text-decoration: underline;
    }
    .modal-header {
        background-color: rgb(144 150 153 / 18%) !important;
    }

</style>

@stop
@section('content')

    <!--Start-- IDS Scheduling Form --->
    <div class="table_title">
        <h4>IDS Scheduling</h4>
    </div>

    <div class="row">
    <!--Start- Filter Form IDS Scheduling --->
        <div class="col-sm-12 col-md-6">
            {{ Form::open(array('id'=>'schedule-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}

            <div id="pin_code" class="form-group row col-sm-12">
                <label for="pin_code" class="col-sm-5">To choose your closest fingerprinting office, please enter your postal code.</label>
                <div class="col-sm-7">
                {{ Form::text('pin_code', null, array('class'=>'form-control postal-code-mask', 'clearifnotmatch'=>'true', 'id'=>'ids_pin_code','required'=>TRUE)) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12" id="pincodeError"></span></div>
                </div>
            </div>
            <div class="search-form-component" style="display: none">

                <div id="ids_recommend_office_id" class="form-group row col-sm-12">
                    <label for="ids_recommend_office_id" class="col-sm-5">The following office is the nearest location to your postal code</label>
                    <div class="col-sm-7">
                        <span class='view-form-element' id="recommend_office_name"></span>
                        {{ Form::hidden('recommend_office_id', null, array('class'=>'form-control', 'clearifnotmatch'=>'true', 'id'=>'recommend_office_id')) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>

                <div id="" class="form-group row col-sm-12">
                    <label for="" class="col-sm-5">Estimated travel time</label>
                    <div class="col-sm-7">
                        <span class='view-form-element' id="recommend_estimated_travel_time"></span>
                    </div>
                </div>

                <div id="" class="form-group row col-sm-12">
                    <label for="" class="col-sm-5">Estimated kilometres from your location</label>
                    <div class="col-sm-7">
                        <span class='view-form-element' id="recommend_estimated_travel_distance"></span>
                    </div>
                </div>

                <div id="" class="form-group row col-sm-12">
                    <label for="" class="col-sm-5">Would you prefer to visit our recommended location or chose another location?</label>
                    <div class="col-sm-7">
                        <select id="is_recommended_office" name="is_recommended_office" required="TRUE" class="form-control" >
                            <option value="1">Choose  recommended location</option>
                            <option value="0">Choose another location</option>
                        </select>
                    </div>
                </div>

                <div class="section-display" id="other-office-section">

                    <div id="ids_office_id" class="form-group row col-sm-12">
                        <label for="ids_office_id" class="col-sm-5">Please select the location for your scheduled visit</label>
                        <div class="col-sm-7">
                            <select id="office" name="ids_office_id" class="form-control select2" >

                            </select>
                            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="" class="form-group row col-sm-12">
                        <label for="" class="col-sm-5">Estimated travel time</label>
                        <div class="col-sm-7">
                            <span class='view-form-element' id="estimated_travel_time"></span>
                        </div>
                    </div>

                    <div id="" class="form-group row col-sm-12">
                        <label for="" class="col-sm-5">Estimate Kilometer from your Location</label>
                        <div class="col-sm-7">
                            <span class='view-form-element' id="estimated_travel_distance"></span>
                        </div>
                    </div>
                </div>
                <div id="ids_service_id" class="form-group row col-sm-12" style="padding-bottom: 1.4%;">
                    <div class="col-sm-5 ids-service-label">
                        <label for="ids_service_id" >Please select the service.</label>
                        <p> Note: Ink Fingerprinting (including FBI) is not available.</p>
                    </div>
                    <div class="col-sm-7">
                        {{ Form::select('ids_service_id',[0=>'Please Select'], old('ids_service_id'),
                        array('class'=> 'form-control select2', 'required'=>TRUE, 'id'=>'service')) }}
                        <div class="form-control-feedback">
                            <span class="text-danger align-middle font-12" id="service-rate-text"></span>
                            <span class="align-middle font-12 service-description" id="service-description">  </span>
                            <span class="help-block text-danger align-middle font-12" ></span>
                        </div>
                    </div>
                </div>
                <div  id="passport-photo-section">

                    <div id="is_photo_service" class="form-group row col-sm-12 section-display">
                        <div class="col-sm-5 ids-service-label" style="padding-right: 0px !important;">
                            <label for="ids_service_id" >Would you like to add a set of passport photos?</label>
                            <p> Note: Please note, this is an OPTIONAL service and not a
                                requirement for any of our fingerprinting services.
                                You may be required to book a separate appointment
                                for passport photos at the sole discretion of our fingerprinting technician.
                            </p>
                        </div>
                        {{-- <label for="is_photo_service" class="col-sm-5">Would you like to add a set of passport photos?<span class="mandatory"> *</span></label> --}}
                        <div class="col-sm-7">
                            {{ Form::select('is_photo_service',[null=>'Please Select',1=>'Yes',0=>'No'], old('is_photo_service'),
                            array('class'=> 'form-control','id'=>'isPhotoService')) }}
                        </div>
                    </div>

                    <div id="passport_photo_service_id" class="section-display form-group row col-sm-12">
                        <label for="passport_photo_service_id" class="col-sm-5">How many photo sets would you like to purchase?<span class="mandatory"> *</span></label>
                        <div class="col-sm-7">
                            <select id="passportPhotoServiceId" name="passport_photo_service_id" class="form-control" >
                                <option value="">Please Select</option>
                                @foreach($photoServices as $services)
                                <option value="{{$services->id}}">{{$services->name }} - ${{$services->rate}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <div id="slot_booked_date" class="form-group row col-sm-12" >
                    <label for="slot_booked_date" class="col-sm-5">When would you like to schedule the visit?<span class="mandatory"> *</span></label>
                    <div class="col-sm-7">
                        {{ Form::text('slot_booked_date', null, array('class'=>'form-control datepicker', 'id'=>'ids_date')) }}
                        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                    </div>
                </div>

                <div class="form-group row col-sm-12" style="text-align:center">
                    <!-- <div class="col-sm-4"><input type="button" value="Schedule" class="btn cancel" id="schedule"></div> -->
                    <div class="col-sm-12">
                        {{ Form::submit('Search', array('class'=>'button btn btn-primary blue pull-right'))}}
                    </div>
                </div>
            </div>
        {{ Form::close() }}
        </div>
    <!--End- Filter Form IDS Scheduling --->

    <!--Start- IDS Scheduling Offie Details -  -->
        <div class="col-sm-12 col-md-6 ids-office-details-container ids-office-details-box" style="display: none">
            <div class="ids-office-details-container" id="office-detais" >
                <div class="row col-sm-12" >
                        <div id="office-name" class="col-sm-12 title"> </div>
                        <div class="col-sm-8" >
                            <div id="office-address" class="office-address">  </div>
                        </div>
                        <div class="col-sm-4">
                            <input class="button btn add-new pull-right" id="mapView" type="button" onclick="getOfficeMap()" value="Click Here To View Map" />
                            <input type="hidden" id="lat" />
                            <input type="hidden" id="lng" />
                        </div>
                </div>
                    <div class="row col-sm-12">
                        <div class="col-sm-4">
                            <div id="office-opening-hours" class="title"> Office Opening Hours </div>
                            @foreach($days as $day)
                                <p style="margin-bottom: 2px;">{{ $day->name }} <span class="start-end"> </span> </p>
                            @endforeach
                        </div>
                        <div class="col-sm-8">
                            <div id="office-special-instruction" class="title"> Office Instructions</div>
                            <div id="special-instruction"> </div>
                        </div>
                        <p class="col-sm-12 common-note" style="margin-top: 4%;"> ** Please note - we are not taking walk-in clients at this time to mitigate COVID 19 exposure ** </p>
                        <p class="col-sm-12 common-note" style="color: red;"> ** Please note - We have moved to online payments. To qualify for a refund, you must provide a minimum 24 hours notice. ** </p>

                    </div>
                </div>
            </div>

    <!--End- IDS Scheduling Offie Details --->
    </div>

    <!-- Map Modal Start-->
    <div id="mapModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalTitle">Office Location Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body" style="padding: 10px 10px;">
                        <div id="MapContainer" style="height: 500px; "></div>

                </div>

            </div>
        </div>
    </div>
    <!-- Map Modal End-->

    <!--Start- legend - -->
    <div class="row legend-main-box" id="legend-main-box" style="display: none">
        <div class="col-sm-12 col-md-1">
            <strong> Legend </strong>
        </div>
        <div class="col-sm-2">
            <span class="row">
                <div class="legend-box closed-slot" style="background-color: #e0e0e0 !important;"></div>
                <span class="legend-text col-sm-7 col-md-9 pull-right"> Closed & Booked Slots </span>
            </span>
        </div>
        <div class="col-sm-3">
            <span class="row">
                <div class="legend-box open-slot" style="background-color:#003A63 !important "></div>
                <span class="legend-text col-sm-7 col-md-10 pull-right"> Available Appointment Slots</span>
            </span>
        </div>
        {{-- <div class="col-sm-2">
            <span class="row">
                <div class="legend-box scheduled-slot"></div>
                <span class="legend-text col-sm-7 col-md-9 pull-right" > Existing Appointment</span>
            </span>
        </div>
        <div class="col-sm-3">
            <span class="row">
                <div class="legend-box search-slot"></div>
                <span class="legend-text col-sm-7 col-md-10 pull-right"> Open Time for Requested Date</span>
            </span>
        </div> --}}

    </div>
     <!--End- legend - -->

    <!--Start- Scheduling table container - -->
    <div class="row">
        <div id="scheduling-table-container" class="col-sm-12 public-container">
        </div>
    </div>
    <!--End- Scheduling table container - -->

    @include('idsscheduling::public.partials.booking-modal')
    @stop
    @section('scripts')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">

    /*--Start-- Declaring variables and Initial show/hiding elements. */
        ids_date_val = '';
        ids_date_pre_val = '';
        officePhotoService = false;
        servicePhotoService = false;
        isPhotoServiceRequired = false;
        totaFee = 0;
        serviceFee = 0;
        photoFee = 0;
        tax = 0;
        photoFeeTax = 0;
        $('#office').select2();
        $('#service').select2();
        $(".other-option-text").hide();
        $("#requisitionNumber").empty();
        $("#federalBillingEmployer").empty();
        $("#mapView").hide();
        $(".ids-office-details-container").hide();
        $(".legend-main-box").hide();
        $(".search-form-component").hide();
        $('#checkout-button').hide();
        $('#ids-slot-scheduling-form #payment-confirmation-message').hide();
        $('#ids-slot-scheduling-form #offline-payment-message').hide();

        var key="{{config('globals.ids_stripe_key')}}";
        var stripe = Stripe(key);

    /*--END-- Declaring variables and Initial show/hiding elements.*/

     /*--Start-- Pincode changes activities */
        $("#ids_pin_code").keyup(function(){
            $('#pincodeError').text('');
            $('#other-office-section').addClass('section-display');
            $('#schedule-form #is_recommended_office').val(1);

            var pincode =  $('#ids_pin_code').val();
            var pincodeReplace = pincode.replace(/_/g,'');

            $('#office').empty().append($("<option></option>")
            .attr("value",' ')
            .attr("data-isPhotoService",'')
            .text('Please Select'));

            if(pincodeReplace.length == 6){

                $('#schedule-form')[0].reset();
                $('#service').val(0).trigger('change');
                $('#service-rate-text').html('');
                $('#service-description').html('');
                $('#ids_pin_code').val(pincode);
                $("#scheduling-table-container").hide();
                $(".legend-main-box").hide();
                $('#is_photo_service').addClass('section-display');
                $('#passport_photo_service_id').addClass('section-display');
                $("#isPhotoService").attr("required", false);
                $("#passportPhotoServiceId").attr("required", false);

                $.ajax({
                    url: "{{route('ids-office.pincode-recommendation')}}",
                    data: {"pincode":pincodeReplace},
                    type: 'GET',
                    success: function(data) {
                        recomendedOffice = {};
                        officeLists = {};

                        if(data.success){
                            $(".search-form-component").show();
                            var officeId = $('#office').val();
                            if(officeId != ''){
                                $(".ids-office-details-container").show();
                            }

                            //Recomend
                            if(data.recomended_office){
                                recomendedOffice = data.recomended_office;
                                $('#schedule-form #recommend_office_name').text(data.recomended_office.name +'-'+data.recomended_office.adress);
                                $('#schedule-form input[name="recommend_office_id"]').val(data.recomended_office.id);
                                $('#schedule-form input[name="recommend_office_id"]').attr("data-isPhotoService",data.recomended_office.is_photo_service)
                                $('#schedule-form #recommend_estimated_travel_time').text(data.recomended_office.travel_time_text);
                                $('#schedule-form #recommend_estimated_travel_distance').text(data.recomended_office.distance_text);
                                setOfficeData(data.recomended_office);
                                getOfficeService(data.recomended_office.id,true);
                            }
                            //With out recomendation
                            if(data.offices){
                                officeLists = data.offices;
                                $('#office').empty().append($("<option></option>")
                                .attr("value",' ')
                                .attr("data-isPhotoService",'')
                                .text('Please Select'));

                                $.each(officeLists, function(index, office) {
                                    $('#office').append($("<option></option>")
                                    .attr("value",office.id)
                                    .attr("data-isPhotoService",office.is_photo_service)
                                    .text(office.name +' - '+office.adress));
                                });
                            }
                        }else{
                            $('#pincodeError').text(data.message);
                        }
                    }
                });

            }else{
                $(".search-form-component").hide();
                $(".ids-office-details-container").hide();
                $("#scheduling-table-container").hide();
                $(".legend-main-box").hide();
                $('#service-rate-text').html('');
                $('#service-description').html('');
            }
        });
        /*--End-- Pincode changes activities*/

        /*--Start-- Custom Question other option need a text box.*/
        function getOtherOptionText(data,questionId){
            var fieldId = '#option-text-'+questionId;
            if(data.value == 1){
                $(fieldId).show();
                $(fieldId).prop('required',true);
            }else{
                $(fieldId).hide();
                $(fieldId).removeAttr('required');
            }
        }
        /*--End-- Custom Question other option need a text box. */

        /* Scheduling table container - Start */
        $('#scheduling-table-container').on("click", ".slot-container .open-slot", function(e) {

            $('#ids-slot-scheduling-form #payment-confirmation-message').hide();
            $('#ids-slot-scheduling-form #offline-payment-message').hide();
            $("#checkout-button").attr('value', 'Pay');
            $("#checkout-button").hide();
            $("#mdl_save_change").show();

            var office_id = $('#office').val();
            var ids_recommend_office_id = $('#recommend_office_id').val();
            // var ids_recommend_office_id = $('#recommend_office_id').val();
            var is_recommended_office = $('#is_recommended_office').val();
            var service_id = $('#service').val();
            var postal_code = $('#ids_pin_code').val();
            // var ids_office_slot_id = $(this)[0].id;
            // var col_index = $(this).parent().children().index($(this));
            // var unformatted_slot_date = new Date($(this).closest('table').find('tr:first').find('th:eq('+col_index+')').text());
            // var slot_date = moment(unformatted_slot_date.toISOString()).format("YYYY-MM-DD");
            var ids_office_slot_id = $(this).attr("data-officeSlotId");
            // var col_index = $(this).parent().children().index($(this));
            // var unformatted_slot_date = new Date($(this).closest('table').find('tr:first').find('th:eq('+col_index+')').text());
            var slot_date = $(this).attr("data-bookingDate");
            var isPhotoService = $('#isPhotoService').val();
            var passportPhotoServiceId = $('#passportPhotoServiceId').val();

            $.ajax({
                url: "{{route('ids-office.slot-booking.fee-calculation')}}",
                data: {
                    "ids_service_id":service_id,
                    "passport_photo_service_id":passportPhotoServiceId,
                    "slot_booked_date":slot_date
                },
                type: 'POST',
                success: function(data) {
                    let feePayable = data.given_rate;
                    let label = 'Pay CA$'+feePayable;
                    $("#checkout-button").attr('value', label);
                    totaFee = data.given_rate;
                    serviceFee = data.service_fee;
                    photoFee = data.photo_fee;
                    tax = data.tax;
                    photoFeeTax = data.photo_fee_tax;
                }
            });


            $('#myModal #slotDetails').text(moment(slot_date).format('LL') + $(this).text());

            $('#myModal').find('input:text').val('');
            $('#myModal').modal();
            $('#ids-slot-scheduling-form')[0].reset();
            $("#requisitionNumber").empty();
            $("#federalBillingEmployer").empty();
            $('#myModal .modal-title').text("Add Details");
            $('#ids-slot-scheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal input[name="ids_office_id"]').val(office_id);
            if(is_recommended_office == 1){
                $('#myModal input[name="ids_office_id"]').val(ids_recommend_office_id);
            }
            $('#myModal input[name="ids_recommend_office_id"]').val(ids_recommend_office_id);
            $('#myModal input[name="ids_service_id"]').val(service_id);
            $('#myModal input[name="ids_office_slot_id"]').val(ids_office_slot_id);
            $('#myModal input[name="slot_booked_date"]').val(slot_date);
            $('#myModal input[name="postal_code"]').val(postal_code);
            $('#myModal input[name="cancelled_booking_id"]').val('');
            // $('#myModal input[name="cancellation_penalty"]').val(0);
            $('#myModal input[name="is_photo_service"]').val(isPhotoService);
            $('#myModal input[name="passport_photo_service_id"]').val(passportPhotoServiceId);

            $('.questionSelect').val('')
            $(".other-option-text").hide();

        });
        /* Scheduling table container - End */

        /* Service location on change event  - Start */
        $('#service').on('input', function() {
            $("#slot-scheduling-table").remove();
            $("#scheduling-table-container").hide();
            $(".legend-main-box").hide();
        });
        $('#passportPhotoServiceId').on('input', function() {
            $("#slot-scheduling-table").remove();
            $("#scheduling-table-container").hide();
            $(".legend-main-box").hide();
        });
        $('#service-description').on('click','a', function() {
            $('#serviceDescriptioModal').modal();
            $('#serviceDescriptioModal .modal-header h5').html($("#schedule-form #service option:selected").text())
            $('#serviceDescriptioModal .modal-body p').html($('#schedule-form #service').find(':selected').attr('data-description'))
        });

        /* Service location on change event  - End */

        /* Scheduling date on change event  - Start */
        $('#ids_date').on('change', function() {
            // event triggers when modal pop-up scroll
            ids_dat_val = $('#ids_date').val();
            // checks if there was an actual value change
            if( ids_dat_val !== ids_date_pre_val) {
                $("#slot-scheduling-table").remove();
                $("#scheduling-table-container").hide();
                $(".legend-main-box").hide();
                ids_date_pre_val = ids_dat_val;
            }
        });
        /* Scheduling date on change event  - End */

        /* Office location on change event - Start */
        $('#office').on('change', function() {
            $("#slot-scheduling-table").remove();
            $("#scheduling-table-container").hide();
            $("#legend-main-box").hide();
            $('#service-rate-text').html('');
            $('#service-description').html('');

            $('#isPhotoService').val(' ');
            $('#passportPhotoServiceId').val(' ');
            $('#passport-photo-section').addClass('section-display');
            $("#isPhotoService").attr("required", false);
            $("#passportPhotoServiceId").attr("required", false);


            var id = $(this).val();
            if(id == null || id == ' '){
                setOfficeData(recomendedOffice);
            }
            getOfficeService(id,false);

            $.each(officeLists, function(index, office) {
                if(office.id == id){
                    setOfficeData(office);
                }
            });

        });

        function setOfficeData(office){

            $(".ids-office-details-container").show();
            $('#office-name').html(office.name);
            $('#office-address').html(office.adress);
            $('#lat').val(office.latitude);
            $('#lng').val(office.longitude);
            if(office.is_photo_service == 1){
                officePhotoService = true;
            }
            if(office.latitude != null && office.longitude !=null){
                $("#mapView").show();
            }else{
                $("#mapView").hide();
            }
            $('.start-end').html('('+moment(office.office_hours_start_time,"HH:mm:ss").format("h:mm A") +' to '+ moment(office.office_hours_end_time,"HH:mm:ss").format("h:mm A")+')');
            $('#special-instruction').html(office.special_instructions);

            $('#schedule-form #estimated_travel_time').text(office.travel_time_text);
            $('#schedule-form #estimated_travel_distance').text(office.distance_text);

        }

        function getOfficeService(id,recommend){
            if(id == ' ' || id == null){
                id = recomendedOffice.id;
                //If another location select changes remove selected values.
                $('#schedule-form #estimated_travel_time').text('');
                $('#schedule-form #estimated_travel_distance').text('');
            }
            let isOfficePhotoService = '';
            if(recommend == true){
                isOfficePhotoService = $('#recommend_office_id').attr('data-isPhotoService');
            }else{
                isOfficePhotoService = $('#office').find(':selected').attr('data-isPhotoService');
            }

            $('#service').empty().append($("<option></option>").attr("value",0).text('Please Select'));
            var base_url = "{{route('ids-office-services', ':id')}}";
            var url = base_url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $.each(data, function(index, service) {
                        var rate = '';
                        var rateStr = service.rate;
                        var rateSplit = rateStr.split(".");
                        rate = service.rate;
                        if(rateSplit.length >=1){
                            if(rateSplit[1] == 0){
                                rate = rateSplit[0];
                            }
                        }
                        let isServiceList = true;
                        if(isOfficePhotoService == "0" && service.is_photo_service_required == 1){
                            isServiceList = false;
                        }
                        if(isServiceList == true){
                            $('#service').append($("<option></option>")
                            .attr("value",service.id)
                            .attr("data-isPhotoService",service.is_photo_service)
                            .attr("data-isPhotoServiceRequired",service.is_photo_service_required)
                            .attr("data-rate",rate)
                            .attr("data-description",service.description)
                            .text(service.name));
                        }
                    });
                }
            });
        }

        $('#schedule-form #is_recommended_office').on('change', function() {
            // $(".ids-office-details-container").hide();
            $("#scheduling-table-container").hide();
            $(".legend-main-box").hide();
            $('#is_photo_service').addClass('section-display');
            $('#passport_photo_service_id').addClass('section-display');
            $("#isPhotoService").attr("required", false);
            $("#passportPhotoServiceId").attr("required", false);
            $('#service').val(0).trigger("change");
            var id = $(this).val();
            $('#schedule-form #estimated_travel_time').text('');
            $('#schedule-form #estimated_travel_distance').text('');
            if(id == 1){
               $('#other-office-section').addClass('section-display');
               $('#office').val('').trigger("change");
            //    $('#estimated_travel_time').text('');
            //    $('#estimated_travel_distance').text('');
            }else{
                $('#other-office-section').removeClass('section-display');
            }
        });

        $('#schedule-form #service').on('change', function() {
            $("#scheduling-table-container").hide();
            $("#legend-main-box").hide();
            $('#ids_service_id').find('.help-block').html('');
            if($(this).val() != 0){
                $('#service-rate-text').html('Price: $'+$(this).find(':selected').attr('data-rate') + ' + Tax');
                //$(this).find(':selected').attr('data-description')
                $('#service-description').html('<a href="#"> Click here for description of the service </a>');
                 isOfficePhotoService = $('#office').find(':selected').attr('data-isPhotoService');
                if(isOfficePhotoService == undefined || isOfficePhotoService == ''){
                    isOfficePhotoService = $('#recommend_office_id').attr('data-isPhotoService');
                }

                let photoServiceRequired = $(this).find(':selected').attr('data-isPhotoServiceRequired');

                if($(this).find(':selected').attr('data-isPhotoService') == 1 && isOfficePhotoService == 1){
                    servicePhotoService = true;
                    if(photoServiceRequired == 1){
                        isPhotoServiceRequired = true;
                    }else{
                        isPhotoServiceRequired = false;
                    }
                    setPhotoService();
                }else{
                     servicePhotoService = false;
                     isPhotoServiceRequired = false;
                     setPhotoService();
                }
            }else{
                $('#service-rate-text').html('');
                $('#service-description').html('');
            }

        });

        /* Office location on change event - End */

        /* Scheduling form submission - Start */
        $('#schedule-form').submit(function (e) {
            e.preventDefault();
            $("#scheduling-table-container").hide();
            // $('#slot-scheduling-table').remove();
            var $form = $(this);
            var office = $('#office').val();
            var recommend_office_id = $('#recommend_office_id').val();
            var is_recommended_office = $('#is_recommended_office').val();
            if(office == '' || office == null || office == 0){
                office = recommend_office_id;
            }
            var service = $('#service').val();
            var date = $('#ids_date').val();
            var formData = $(this).serializeArray();
            var currentDate = moment().format('YYYY-MM-DD');
            var schedule_date = moment(date).format('YYYY-MM-DD');
            var end_date_max = moment(currentDate, "YYYY-MM-DD").add(19, 'days');
                end_date_max = moment(end_date_max).format("YYYY-MM-DD");
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            if(office == 0 || service == 0 || date == '' || schedule_date < currentDate || schedule_date > end_date_max){
                $.each(formData, (index, obj) => {
                    if(obj.name == "recommend_office_id" && obj.value == 0){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Recommended office is required");
                    }else if(obj.name == "ids_office_id" && obj.value == 0 && is_recommended_office == 0){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Office is required");
                    }else if(obj.name == "ids_service_id" && obj.value == 0){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Service is required");
                    }else if(obj.name == "slot_booked_date" && obj.value == ''){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Date is required");
                    }else if(obj.name == "slot_booked_date" && obj.value != '' && schedule_date < currentDate){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Date must be a future date");
                    }else if(obj.name == "slot_booked_date" && obj.value != '' && schedule_date > end_date_max){
                        $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Booking only available for 20 days from today");
                    }
                });
            }else{
                $("#scheduling-table-container").show();
                $.ajax({
                    url: "{{route('ids-office.slot-timings')}}",
                    data: {"ids_office_id":office, "slot_booked_date":date},
                    type: 'GET',
                    success: function(data) {
                        $("#legend-main-box").show();
                        var slot_color;
                        var slot_title;
                        var slot_id;
                        $('#scheduling-table-container').html(data.html);
                    }
                });
            }
        });
        /* Scheduling form submission - End */

        $('#isCandidate').on('change', function() {
            var id = $(this).val();
            if(id == 1){
                let html = `<input placeholder="Please enter your employee number" required="TRUE"
                class="form-control" id="candidate_requisition_no" name="candidate_requisition_no" type="text" >`;
                $("#requisitionNumber").append(html);
            }else{
                $("#requisitionNumber").empty();
            }
        });

        $('#isFederalBilling').on('change', function() {
            var id = $(this).val();
            if(id == 1){
                let html = `<input placeholder="Enter the name of your Employer" required="TRUE"
                class="form-control" id="federal_billing_employer" name="federal_billing_employer" type="text" >`;
                $("#federalBillingEmployer").append(html);
            }else{
                $("#federalBillingEmployer").empty();
            }

        });

        /* Slot scheduling form submission - Start */

        /* START *** Cancellation penalty  */
        // $('#ids-slot-scheduling-form').submit(function (e) {
        //     e.preventDefault();
        //     let email = $('#ids_email').val();
        //     let phone_number = $('#ids_phone_number').val();
        //     $.ajax({
        //         url: "{{route('ids.last-cancelled-entry')}}",
        //         data: {"email":email, "phone_number":phone_number},
        //         type: 'GET',
        //         success: function(data) {
        //             if(data.isPenalty == true){
        //                 $('#myModal input[name="cancelled_booking_id"]').val(data.entry.id);
        //                 $('#myModal input[name="cancellation_penalty"]').val(data.settings.cancellation_penalty);
        //                 let swalMsg = 'I agree to a $'+data.settings.cancellation_penalty+' charge on any subsequent';
        //                 swalMsg += 'visit should I cancel my appointment without '+data.settings.cancellation_penalty;
        //                 swalMsg += ' hours notice.(Note: Commissionaires will not process your transaction unless the fee is paid upon reschedule.)';
        //                 swal({
        //                     title: "Are you sure?",
        //                     text: swalMsg,
        //                     type: "warning",
        //                     showCancelButton: true,
        //                     confirmButtonClass: "btn-danger",
        //                     confirmButtonText: "Yes",
        //                     showLoaderOnConfirm: true,
        //                     closeOnConfirm: false
        //                 },
        //                 function() {
        //                     bookAppointment();
        //                 });
        //             }else{
        //                 bookAppointment();
        //             }
        //         }
        //     });
        // });
        /* END *** Cancellation penalty  */

        // $('#ids-slot-scheduling-form').submit(function (e) {
        //     e.preventDefault();
        //     var isCandidate=$('#ids-slot-scheduling-form #isCandidate').val();
        //     if(isCandidate ==1)
        //     {
        //         scheduleSlot();
        //     }else{
        //         doPayment();
        //     }

        // });
        /* Slot scheduling form submission - End */

        function setPhotoService(){
            $('#isPhotoService').val(null);
            $('#passportPhotoServiceId').val('');

            // alert(' servicePhotoService '+servicePhotoService);
            // alert(' officePhotoService '+officePhotoService);
            // alert(' isPhotoServiceRequired '+isPhotoServiceRequired);

            if(servicePhotoService == true && officePhotoService == true){
                $('#passport-photo-section').removeClass('section-display');
                $("#isPhotoService").attr("required", true);

                if(isPhotoServiceRequired == false){
                    $('#is_photo_service').removeClass('section-display');
                    $("#isPhotoService").attr("required", true);

                    $('#passport_photo_service_id').addClass('section-display');
                    $('#passportPhotoServiceId').val('');
                    $("#passportPhotoServiceId").attr("required", false);

                }else{
                    $('#is_photo_service').addClass('section-display');
                    $("#isPhotoService").attr("required", false);

                    $('#passport_photo_service_id').removeClass('section-display');
                    $("#passportPhotoServiceId").attr("required", true);
                }
            }else{
                $('#passport-photo-section').addClass('section-display');
                $("#isPhotoService").attr("required", false);
                $("#passportPhotoServiceId").attr("required", false);
            }


        }

        $('#schedule-form #isPhotoService').on('change', function() {
            if($(this).val() == 1){
                $('#passport_photo_service_id').removeClass('section-display');
                $("#passportPhotoServiceId").attr("required", true);
            }else{
                $('#passport_photo_service_id').addClass('section-display');
                $('#passportPhotoServiceId').val('');
                $("#passportPhotoServiceId").attr("required", false);
            }
        });


        $('#ids-slot-scheduling-form #isCandidate,#ids-slot-scheduling-form #isFederalBilling').on('change', function() {
            var isCandidate=$('#ids-slot-scheduling-form #isCandidate').val();
            var isFederalBilling=$('#ids-slot-scheduling-form #isFederalBilling').val();
            var photoId = $('#schedule-form #passportPhotoServiceId').val();
            if(isCandidate == 1 || isFederalBilling == 1)
            {
                $('#checkout-button').hide();
                $('#ids-slot-scheduling-form #mdl_save_change').show();
                $('#ids-slot-scheduling-form #payment-confirmation-message').hide();
            }else{
                $('#checkout-button').show();
                $('#ids-slot-scheduling-form #mdl_save_change').hide();
                $('#ids-slot-scheduling-form #payment-confirmation-message').show();
            }

            $('#ids-slot-scheduling-form #offline-payment-message').hide();
            if(photoId > 0 && (isCandidate == 1 || isFederalBilling == 1)){
                $('#ids-slot-scheduling-form #offline-payment-message').html("Passport photo fee $"+photoFeeTax+" must be paid in the office.");
                $('#ids-slot-scheduling-form #offline-payment-message').show();
            }
            // $('#ids-slot-scheduling-form #idsPaymentReasonId').prop('required',false);

        });

        $('#ids-slot-scheduling-form').submit(function (e) {
            e.preventDefault();
            var $form = $('#ids-slot-scheduling-form');
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
                    if (data.sessionId != null) {
                        $('#myModal').find('input:text').val('');
                        $('#myModal').modal('hide');
                        return stripe.redirectToCheckout({ sessionId: data.sessionId });
                    }
                    if (data.success) {
                    $('#myModal').find('input:text').val('');
                    $('#myModal').modal('hide');
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    var swalHtml = `<p> Thank you for booking with us. Your appointment has been
                                    confirmed. Please check your email for more details.</p>
                                    <br/><br/>
                                    <h5 style='text-align: left;margin-left: 23px;'> Please Note </h5>
                                    <ul class='swal-booking-notes'>
                                        <li>
                                            Clients must present two pieces of government issued ID for processing.
                                            At least one ID must contain a photo.
                                        </li>
                                        <li>All ID must be valid</li>
                                        <li>
                                            All ID must be original. If it is not original, it can be a certified copy,
                                            valid and must be translated in English with one ID containing a photo.
                                        </li>
                                        <li>
                                            We do not accept SIN card or red & white health cards.
                                            We accept green health cards as a second piece of ID only not as a primary.
                                        </li>
                                        <li>
                                            Upon arrival, you will be screened for fever. If your body temperature is 37.6°C (99.7°F) or higher,
                                            you will not be granted service and will be turned away from the site.
                                        </li>
                                        <li>
                                            Over the phone service will not be provided.
                                        </li>
                                        <li>
                                            Starting Jan 01, 2021, a surcharge of $2.50 will be applied to all services
                                            to cover additional costs incurred during the pandemic related to automated
                                            scheduling and PPE.
                                        </li>
                                        <li>
                                            Please note a $10 surcharge will be added to your invoice for no-shows or
                                            any cancellation with less than 2 hours notice.
                                        </li>
                                    </ul>`;
                    swal({
                        title: "Successful",
                        text : swalHtml,
                        html: true,
                        type: "success",
                        confirmButtonText: "OK",
                    },function(){
                        grecaptcha.reset();
                        $( "#schedule-form" ).trigger( "submit" );
                    });
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
                                    $( "#schedule-form" ).trigger( "submit" );
                                    grecaptcha.reset();
                                    if(data.modalHide == true){
                                        $('#myModal').modal('hide');
                                    }
                                    if(data.reload == true){
                                        location.reload();
                                    }
                            });
                        }
                    }
                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                    if(xhr.responseJSON.errors['g-recaptcha-response']){
                        $('#recaptcha-section').addClass('has-error').find('.help-block').text(xhr.responseJSON.errors['g-recaptcha-response'][0]);
                    }
                },
                contentType: false,
                processData: false,
            });
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
            <?php Log::channel('googleApi')->info(['Date' => Carbon::now()->format('Y-m-d'),'Time' => Carbon::now()->format('H:i:s'),'Service'=>'maps','Page'=>'slotBooking'])?>
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

        /* Slot scheduling form submission - End */

        // $('#ids-slot-scheduling-form').submit(function (e) {
        //     e.preventDefault();
        //         $('body').loading({
        //             stoppable: false,
        //             message: 'Please wait...'
        //         });
        //         fetch("{{route('ids-office.slot-booking')}}", {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN':'{{ csrf_token() }}',
        //         },
        //         body:  new FormData(document.getElementById("ids-slot-scheduling-form")),
        //         })
        //         .then(function(responce) {
        //             var isCandidate=$('#ids-slot-scheduling-form #isCandidate').val();
        //             var isFederalBilling=$('#ids-slot-scheduling-form #isFederalBilling').val();
        //             if(isCandidate == 1 || isFederalBilling == 1)
        //             {
        //                 $('#myModal').find('input:text').val('');
        //                 $('#myModal').modal('hide');
        //                 $('.form-group').removeClass('has-error').find('.help-block').text('');
        //                 var swalHtml = `<p> Thank you for booking with us. Your appointment has been
        //                     confirmed. Please check your email for more details.</p>
        //                     <br/><br/>
        //                     <h5 style='text-align: left;margin-left: 23px;'> Please Note </h5>
        //                     <ul class='swal-booking-notes'>
        //                         <li>
        //                             Clients must present two pieces of government issued ID for processing.
        //                             At least one ID must contain a photo.
        //                         </li>
        //                         <li>All ID must be valid</li>
        //                         <li>
        //                             All ID must be original. If it is not original, it can be a certified copy,
        //                             valid and must be translated in English with one ID containing a photo.
        //                         </li>
        //                         <li>
        //                             We do not accept SIN card or red & white health cards.
        //                             We accept green health cards as a second piece of ID only not as a primary.
        //                         </li>
        //                         <li>
        //                             Upon arrival, you will be screened for fever. If your body temperature is 37.6°C (99.7°F) or higher,
        //                             you will not be granted service and will be turned away from the site.
        //                         </li>
        //                         <li>
        //                             Over the phone service will not be provided.
        //                         </li>
        //                         <li>
        //                             Starting Jan 01, 2021, a surcharge of $2.50 will be applied to all services
        //                             to cover additional costs incurred during the pandemic related to automated
        //                             scheduling and PPE.
        //                         </li>
        //                         <li>
        //                             Please note a $10 surcharge will be added to your invoice for no-shows or
        //                             any cancellation with less than 2 hours notice.
        //                         </li>
        //                     </ul>`;
        //                 swal({
        //                     title: "Successful",
        //                     text : swalHtml,
        //                     html: true,
        //                     type: "success",
        //                     confirmButtonText: "OK",
        //                 },function(){
        //                     grecaptcha.reset();
        //                     $( "#schedule-form" ).trigger( "submit" );
        //                 });
        //             }else{
        //                 return response.json();
        //             }

        //         })
        //         .then(function(session) {
        //             if(session){
        //                 return stripe.redirectToCheckout({ sessionId: session.id });
        //             }
        //         })

        //         .then(function(result) {
        //         // If `redirectToCheckout` fails due to a browser or network
        //         // error, you should display the localized error message to your
        //         // customer using `error.message`.
        //         if (result) {
        //             // alert(result.error.message);
        //             console.log(result);
        //             // console.log(result.error);
        //         }
        //         })
        //         .catch(function(error) {
        //             console.log('Error:');
        //             console.log(error);
        //             // alert('Please try again')
        //         });
        // });



</script>
  @stop
