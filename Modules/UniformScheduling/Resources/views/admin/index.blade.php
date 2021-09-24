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
</style>
<style>
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
            color: #fff !important;
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
            display: block !important;
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
    </style>
@stop
@section('content')

    <!-- IDS rescheduling Form - Start -->
        <div class="table_title">
            <div class="row">
                <h4 class="col-sm-9">View Schedule</h4>
            </div>
        </div>
        <div class="filter-div">
            <div class="row">
                <div class="col-md-7">
                {{ Form::open(array('id'=>'filter-form', 'autocomplete'=>'off', 'class'=>'form-horizontal', 'method'=> 'POST')) }}

                <div class="row">
                    <div class="col-md-5">
                        <div id="start_date" class="form-group row col-md-12">
                            <label for="start_date" class="col-md-3 col-form-label">Start Date</label>
                            <div class="col-md-9">
                                {{ Form::text('start_date', null, array('class'=>'form-control datepicker', 'id'=>'startDate')) }}
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div id="end_date" class="form-group row col-md-12">
                            <label for="end_date" class="col-md-3 col-form-label">End Date</label>
                            <div class="col-md-9">
                                {{ Form::text('end_date', null, array('class'=>'form-control datepicker', 'id'=>'endDate')) }}
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row col-md-12" style="text-align:center">
                            <div class="col-md-12">
                                {{ Form::submit('Search', array('class'=>'button btn btn-primary blue','style'=>'width: 100px;'))}}
                            </div>
                        </div>
                    </div>

                </div>

                {{ Form::close() }}
            </div>
        </div>
        </div>
    <!-- Rescheduling table container - Start-->
    <div class="row">
        <div id="scheduling-table-container" class="col-sm-12 admin-container"></div>
    </div>
    <!-- Rescheduling table container - End-->
    @include('uniformscheduling::admin.partials.booking-details-modal')
    @stop
    @section('scripts')

    <script>


    const booking = {
        ref:{
          slotData:[],
        //   clickedSlotData:[],
        //   bookedEntry:[],
          selectedStartDate:'',
          selectedEndDate:'',
        },
        init(){
          this.registerEventListeners();
          this.initialDataLoad();
        },
        initialDataLoad(){
            let today = moment().format('YYYY-MM-DD');
            let endDateMax = today;
            endDateMax = moment(moment(today, "YYYY-MM-DD").add(2, 'days')).format('YYYY-MM-DD');
            $('#startDate').val(today);
            $('#endDate').val(endDateMax);
            $( "#filter-form" ).trigger("submit");

        },
        registerEventListeners() {
            let root = this;

            //Fetch allocated services of facility.
            $('.filterInputs').on('change', function(){
                root.hideSlotContanier();
            });


            //Filter form submitting
            $('#filter-form').submit(function (e){
                root.hideSlotContanier();
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();
                let booking_start_date = $('#startDate').val();
                let booking_end_date = $('#endDate').val();
                $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

                if(booking_start_date == '' || booking_end_date == '' || booking_end_date < booking_start_date){

                    $.each(formData, (index, obj) => {
                        if(obj.name == "start_date" && obj.value == ''){
                            $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("Start date is required");
                        }else if(obj.name == "end_date" && obj.value == ''){
                            $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date is required");
                        }else if(obj.name == "end_date" && obj.value != '' && booking_end_date < booking_start_date){
                            $form.find("[id='" + obj.name + "']").addClass('has-error').find('.help-block').text("End date must be greater than start date");
                        }
                    });

                }else{
                    root.ref.selectedStartDate = booking_start_date;
                    root.ref.selectedEndDate = booking_end_date;
                    root.fetchSlotData();
                }
            });

            /**Start** On slotClick
            * set booked data and question answers on modal
            */
            // $("body").on("click", ".slot-card", function(){
            //     root.ref.clickedSlotData = $(this).data('event');
            //     root.getBookingEntry(root.ref.clickedSlotData.bookedEntryId);
            // });

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
            let booking_start_date = $('#startDate').val();
            let booking_end_date = $('#endDate').val();

            $.ajax({
                url: "{{route('uniform-admin.slot-timings-booking')}}",
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "start_date":booking_start_date,
                    "end_date":booking_end_date,
                },
                type: 'GET',
                success: function(data) {
                    root.ref.slotData = data.daySlotDetails;
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
                        officeTimingId: value.uniform_scheduling_office_timing_id,
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
                        daySlotDetails['startTime'] = slotData.start_time;
                        daySlotDetails['endTime'] = slotData.end_time;
                        daySlotDetails['bookedEntryId'] = slotData.booked_entry_id;
                        daySlotDetails['bookingFlag'] = slotData.booking_flag;

                        if(slotData.status == 1){
                            slot_class = 'white-bg';
                            slot_title = 'Open Slot'
                        }else if(slotData.status == 2){
                            slot_class = 'js-card';
                            slot_title = 'Scheduled Slot'
                        }else{ // For Blocked slot
                            slot_class = '';
                            slot_title = 'Temporarily Closed'
                        }

                        if(slotData.booking_flag == 0){
                            schedulingTableHtml +=`<button type="button" class="btn btn-light booking-row-button ${slot_class}"  title="${slot_title}" disabled data-event=''>${slotData.name}</button>`;
                        }else{
                            schedulingTableHtml +=`<button type="button" class="btn btn-light slot-card ${slot_class}"  title="${slot_title}"  data-event='${JSON.stringify(daySlotDetails)}'>${slotData.name}</button>`;
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
        getBookingEntry(bookingId){
            let root = this;
            var base_url = "{{route('uniform-admin.slot-single-booking',':id')}}"
                url = base_url.replace(':id', bookingId);
            $.ajax({
                url: url,
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                type: 'GET',
                success: function(data) {
                    if(data){
                        root.ref.bookedEntry = data;
                        root.setBookingEntry();
                    }else{
                        swal({
                            title: "Try Again",
                            text: "Data available",
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                        root.fetchSlotData();
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                }
            });
        },
        // setBookingEntry(){
        //     $('#scheduleModal').modal();
        //     $('#scheduleModal .modal-title').text("Uniform Booked Details");
        //     $('#scheduleModal #pre-scheduled-date').text(root.ref.clickedSlotData.title);
        //     $('#scheduleModal #pre-scheduled-time').text(root.ref.clickedSlotData.slotName);

        // },
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
            let booking_start_date = $('#startDate').val();
            let booking_end_date = $('#endDate').val();

            if(root.ref.selectedStartDate != booking_start_date ||
            root.ref.selectedEndDate != booking_end_date){
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
  @include('uniformscheduling::admin.partials.modal-script')
  @stop
