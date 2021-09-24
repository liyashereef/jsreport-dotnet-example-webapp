@extends('layouts.app')
@section('css')

<link href="{{ asset('plugins/full-calendar/core/main.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/full-calendar/daygrid/main.css') }}" rel="stylesheet">
<link href="{{ asset('css/ids.css') }}" rel="stylesheet">

<script src="{{ asset('plugins/full-calendar/core/main.js') }}"></script>
<script src="{{ asset('plugins/full-calendar/interaction/main.js') }}"></script>
<script src="{{ asset('plugins/full-calendar/daygrid/main.js') }}"></script>


<style>
/***Start* IDS Calendar scroll bar*/
    #content-div {
        width: 97%;
    }
    .col-form-label {
        padding-left: 2%;
    }
    .isCandidate{
        background: rgba(36, 169, 66, 0.62) !important;
        color: #383737 !important;
    }

    .booingWithPhoto:hover{
        background-color: #ffe690 !important;
        color: #383737 !important;
    }
    .booingWithPhoto{
        background-color: #ffe690 !important;
        color: #383737 !important;
    }
    #rescheduleModal {
        overflow-y: scroll
    }
    ::-webkit-scrollbar {
        width: 0.2em;
    }
    ::-webkit-scrollbar-button {
    background: #c1c1c1;
    }
    ::-webkit-scrollbar-track-piece {
        background: #c1c1c1;
    }
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
    }​

/***END* IDS Calendar scroll bar */
</style>


@stop

@section('content')

<!---START-- IDS scheduling view --->

   <!--- Start -- Page Title Section -->
    <div class="table_title">
        <div class="row">
            <h4 class="col-sm-9">View Schedule</h4>
            @can('reschedule_request')
            <div class="col-sm-3">
                <button id="toBeRescheduleModalButton" class="button btn btn-primary blue pull-right section-display" >Reschedule Request</button>
            </div>

            @endcan
        </div>
    </div>
    <!--- End -- Page Title Section -->

    <!--- Start -- Filter Section -->
    <div class="filter-div">
        <div class="row">
            {{ Form::open(array('id'=>'filter-form', 'class'=>'form-horizontal col-sm-6', 'method'=> 'POST')) }}
                <div id="ids_office_id" class="form-group row col-md-12">
                    <label for="ids_office_id" class="col-md-3 col-form-label">
                          Office Location
                    </label>
                    <div class="col-md-9">
                        {{-- {{ Form::select('office_id',[0=>'Please Select']+$offices, old('ids_office_id'),array('class'=> 'form-control select2', 'required'=>TRUE, 'id'=>'office')) }} --}}
                        <select id="office" name="ids_office_id" required="TRUE" class="form-control select2" required="true">
                            <option value="">Please Select</option>
                            @foreach($officeList as $office)
                                <option value="{{$office->id}}" data-isPhotoService="{{$office->is_photo_service}}">
                                    {{$office->name}} - {{$office->adress}}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-control-feedback">
                            <span class="help-block text-danger align-middle font-12"></span>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
    <!--- End -- Filter Section -->


    <!--- Start -- Calendar Section -->
    <div class="col-sm-12 section-display" id="calendar-section">
        <div class="row">

            <div class="col-md-10" style="padding: unset;">
                <div id='calendar'></div>
            </div> <!--- col-md-10 -->

            <div class="col-md-2"  style="padding: unset;">
                <div class="day-details-section" id="day-details-section">
                    <div class="day-details-title" id="day-details-title" title=""></div>
                    <!---START-- Day Slot List -->
                    <div class="day-slot-list" id="slot-list"> </div>
                    <!---END-- Day Slot List -->

                </div> <!--- day-details-section -->
            </div> <!--- col-md-2 -->


        </div> <!---row  -->
    </div> <!---col-sm-12 -->

    <!--- End -- Calendar Section -->


  <!---END-- IDS scheduling view --->



    @stop

    @include('idsscheduling::admin.partials.booking-details-modal')
    @section('scripts')

  <script>

    $(document).ready(function(){
    // $(".select2").select2();
    });

   const ids = {
        ref: {
            calerderDate : moment().format('YYYY-MM-DD'),
            calerderDefaultDate : new Date(),
            calendarEl : null,
            calendar : null,
            calendarEvents : [],
            //   bookedServiceId : null,
            //   bookedOfficeId : null,
            calenderHeight : null,
        },
        init() {
                //Initialize calendar managemet view
                // this.fetchCalendarEvent();
                // this.trigerCalendarClick();
                // this.initCalendar();
            //Event listeners
            this.registerEventListeners();
            //Calendar and day slot height
            var calenderHeight = $('#content-div').height();
            this.ref.calenderHeight = parseInt(calenderHeight) - 200;
        },
        registerEventListeners(){
                let root = this;

                /**Start** OnChange office
                * Initialize calendar
                * Fetch Office allocated services
                */
                $("#filter-form").on("change", "#office", function(e) {
                    //Load Calendar Event
                    root.ref.idsofficeId = $("#office").val();
                    $('#toBeRescheduleModal input[name="office_id"]').val(root.ref.idsofficeId);
                    if(root.ref.idsofficeId != 0 && root.ref.idsofficeId != null){
                        root.ref.calerderDefaultDate = new Date();
                        root.ref.calerderDate = moment().format('YYYY-MM-DD');
                        root.fetchCalendarEvent();
                        $("#calendar-section").removeClass("section-display");
                        $("#toBeRescheduleModalButton").removeClass("section-display");

                        /** Start- Fetch Office allocated services */
                        root.officeAllocatedServices($("#office").val());
                        /** End- Fetch Office allocated services */

                    }else{
                        $("#calendar-section").addClass("section-display");
                        $("#toBeRescheduleModalButton").addClass("section-display");
                    }
                });
                /**End** OnChange office */


                /**Start** Rescheduling request modal opening -  */
                $('#toBeRescheduleModalButton').on('click', function() {
                    $('#toBeRescheduleModal').modal();
                    $('#toBeRescheduleModal .modal-title').text("Reschedule Request");
                });
                /**End** Rescheduling request modal opening -  */


        },
        fetchCalendarEvent(){
                let root = this;
                let url = '{{ route("idsscheduling-calendar.office.booking") }}';
                $.ajax({
                    url: url,
                    data: {"ids_office_id":root.ref.idsofficeId,'date':root.ref.calerderDate},
                    type: 'GET',
                    success: function(data) {
                    root.ref.calendarEvents = data;
                    root.initCalendar();
                    },
                    error: function(xhr, textStatus, thrownError) {
                        if(xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false
                });
        },
        initCalendar() {
            let root = this;

            $("#calendar").empty();
            this.ref.calendarEl = document.getElementById('calendar');
            this.ref.calendar = new FullCalendar.Calendar(this.ref.calendarEl, {
                plugins: ['dayGrid','interaction'],
                header: {
                left: '',
                center: 'prev , title , next',
                right: ''
                },
                defaultView: 'dayGridMonth',
                views: {
                    dayGridMonth: {
                        columnFormat: 'dddd',
                    }
                },
                titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                    month: 'long',
                    year: 'numeric',
                },
                columnHeaderFormat : {
                    weekday: 'long'
                },
                customButtons: {
                    prev: {
                        text: 'Prev',
                        click: function(info) {
                            root.ref.calendar.prev();
                            var month = $('.fc-center h2').html();
                            root.trigerMonthChange(month);
                        }
                    },
                    next: {
                        text: 'Next',
                        click: function(info) {
                            root.ref.calendar.next();
                            var month = $('.fc-center h2').html();
                            root.trigerMonthChange(month);
                        }
                    },
                },

                selectable: true,
                defaultDate: root.ref.calerderDefaultDate,
                // navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                fixedWeekCount: false,
                height: root.ref.calenderHeight,
                events: root.ref.calendarEvents,
                dateClick: function(info) { // Fetch data on calender click.
                root.ref.calerderDate  = info.dateStr;
                root.trigerCalendarClick();

                },
                eventClick: function(info) { //On clicking an event

                },

            });
            this.ref.calendar.render()
            root.trigerCalendarClick();
        },
        trigerCalendarClick(){
            let root = this;
                let url = '{{ route("idsscheduling-calendar.office.slot-details") }}';
                $.ajax({
                    url: url,
                    data: {"ids_office_id":root.ref.idsofficeId,'calendar_date':root.ref.calerderDate},
                    type: 'GET',
                    success: function(data) {
                        if(data.success == true){
                            root.setDaySlotDataSet(data);
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        if(xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false
                });
                // root.fetchCaladerEvents(root.ref.calerderDate);
        },
        setDaySlotDataSet(data){

            let root = this;
            $('#slot-list').html();
                $('#day-details-title').html(data.slotTitle);

                var slotList = '';
                $.each(data.slots, function(index, value) {
                    $.each(value.slots, function(index, slot) {
                        let name = '';
                        let serviceName = '';
                        let className = '';
                        let jsCard = '';
                        let isCandidate = '';
                        // let day_value = value.day[0];
                        // if(value.day[0].slot != ''){
                        //     var slot_details = JSON.parse(atob(value.day[0].slot));
                        //     if(slot_details != ''){
                        //         name = slot_details.first_name+' '+slot_details.last_name;
                        //         if(slot_details.ids_services !=''){
                        //             serviceName = slot_details.ids_services.name;
                        //         }
                        //     }
                        // }

                            if(slot.status == 1){
                                className = 'open-slot';
                                slot_title = 'Open Slot'
                            }else if(slot.status == 2){
                                className = 'booked-slot';
                                slot_title = 'Booked Slot'
                                jsCard = 'js-card';
                            }else if(slot.status == 3){
                                className = 'rescheduled-request-slot';
                                slot_title = 'To Be Rescheduled'
                                jsCard = 'js-card';
                                if(slot.is_online_payment == 0){
                                    className = 'blocked-slot';
                                    slot_title = 'Online Payment Pending';
                                    jsCard = '';
                                }
                            }else{
                                className = 'blocked-slot';
                                slot_title = 'Temporarily Closed';
                                if(slot.is_online_payment == 0){
                                    slot_title = 'Online Payment Pending';
                                }
                            }
                            if(slot.booing_with_photo == 1 && slot.is_candidate!= 1){
                                className = className+' booingWithPhoto';
                            }
                            if(slot.is_candidate == 1){
                                className = className+' isCandidate';
                                isCandidate = 'isCandidate';
                            }
                            let start_time = moment(slot.start_time,"HH:mm:ss").format("hh:mm");
                            let start_time_type = moment(slot.start_time,"HH:mm:ss").format("A");

                            slotList += `<div class='card ${jsCard}' data-bookingid="${slot.booking_id}"
                            data-slotname="${slot.display_name}" data-bookingdate="${value.format_date}">
                                            <div class='container ${className}' title="${slot_title}">
                                                <div class="row" class="booking-item">
                                                    <div class="office-slot " id="office-slot">
                                                        <div>${start_time} <span style="font-size: 12px;"> ${start_time_type} </span></div>
                                                        <div>${value.intervel} <span style="font-size: 10px;"> Minutes </span> </div>
                                                    </div>
                                                    <div class="booking-details" id="booking-details" >
                                                        <div class="booked-by" id="booked-by">${slot.bookedBy}</div>
                                                        <div class="service-name" id="service-name">${slot.serviceName}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;

                    });
                });
                $('#slot-list').html(slotList);
                var slotListHeight = parseInt(root.ref.calenderHeight) - 110;
                $('#slot-list').css('height',slotListHeight);

        },
        officeAllocatedServices(officeId){
            let root = this;
            let selectBookedServiceId = false;
                var base_url = "{{route('ids-office-services', ':id')}}";
                var url = base_url.replace(':id', officeId);
                let isPhotoService = $('#office').find(':selected').attr('data-isPhotoService');
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
                            if(service.id == root.ref.bookedServiceId){
                                selectBookedServiceId = true;
                            }
                            let isServiceList = true;
                            if(isPhotoService == 0 && service.is_photo_service_required == 1){
                                isServiceList = false;
                            }
                            let isTax = 0;
                            let tax = 0;
                            let taxEffectiveFromDate = '';
                            if(service.tax_master && service.tax_master.tax_master_log){
                                    isTax = 1;
                                    tax = service.tax_master.tax_master_log.tax_percentage;
                                    taxEffectiveFromDate = service.tax_master.tax_master_log.effective_from_date;
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
                        if(selectBookedServiceId){
                            $('#rescheduleModal #idsServiceId').val(root.ref.bookedServiceId);
                        }else{
                            $('#rescheduleModal #idsServiceId').val();
                        }
                    }
                });
        },
        trigerMonthChange(monthYear){
            let root = this;

            // Current month set as
            let currentMonth = moment().format("MMMM");
            let monthYearArray = monthYear.split(' ');
            root.ref.calerderDate = moment('01-'+monthYear).format('YYYY-MM-DD');
            if(monthYearArray.length >= 1){
                if(currentMonth == monthYearArray[0]){
                    root.ref.calerderDate = moment().format('YYYY-MM-DD');
                }
            }

            let defaultDate = moment('01-'+monthYear).format('MM-DD-YYYY');
            root.ref.calerderDefaultDate =new Date(moment(defaultDate).format('LLL'));
            root.fetchCalendarEvent();

        }
   }

  // Code to run when the document is ready.
    $(function() {
      ids.init();
    });

    function trigerOnScheduleUpdate(reloadAll = false){
        if(reloadAll == true){
            ids.fetchCalendarEvent();
        }
         //Reload selected day event.
         ids.trigerCalendarClick();
    }
  </script>
    @include('idsscheduling::admin.partials.modal-script')
  @stop

