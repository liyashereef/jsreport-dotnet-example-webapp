<script>

const bookingModal = {
        ref:{
            bookedServiceId : null,
            bookedOfficeId : null,
            bookedEntry : [],
            clickedSlotData:[],
        },
        initialize(){
            //Event listeners
            this.registerEvents();
        },
        registerEvents(){
            let root = this;


            /**Start** On slotClick
            * set booked data and question answers on modal
            */
            $("body").on("click", ".slot-card", function(){
                root.ref.clickedSlotData = $(this).data('event');
                root.getBookingEntry(root.ref.clickedSlotData.bookedEntryId);
            });

            // $("body").on("change", "#genderValue", function(){
            //     let gender = $(this).val();
            //     $('#label_6 .mandatory').remove();
            //     if(gender == 1){
            //         $('[name=point_value_6').removeAttr('required');
            //         // $('#label_6 .mandatory').remove();
            //     }else if(gender == 2){
            //         $("[name=point_value_6").attr("required", "true");
            //         $( "#label_6" ).append('<span class="mandatory">*</span>');
            //     }
            // });

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

            /**Start** Delete slot booking */
            $('#delete_slot').on('click', function(e) {
                var $form = $('#scheduling-form');
                swal({
                    title: "Are you sure?",
                    text: "You can’t undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    e.preventDefault();
                    var id = $('#scheduling-form').find('input[name="id"]').val();
                    var formData = new FormData();
                    formData.append("id",id);
                    formData.append("is_canceled",0);
                    root.deleteOrCancelBooking(formData);
                });

            });
            /**End** Delete slot booking */

            /**Start** Cancel slot booking */
            $('#cancel_booking').on('click', function(e) {
                var $form = $('#scheduling-form');
                swal({
                    title: "Are you sure to cancel this booking?",
                    text: "You can’t undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    e.preventDefault();
                    var id = $('#scheduling-form').find('input[name="id"]').val();
                    var formData = new FormData();
                    formData.append("id",id);
                    formData.append("is_canceled",1);
                    root.deleteOrCancelBooking(formData);
                });

            });
            /**End** Cancel slot booking */

            /*** Slot booking update - Start */
           $('#scheduling-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                var formData = new FormData($('#scheduling-form')[0]);
                var slotDetails = $('#scheduling-form #time_slots').find(':selected').attr('data-slotDetails');
                if(slotDetails != undefined){
                    slotDetails = JSON.parse(slotDetails);
                    formData.append("start_time",slotDetails.startTime);
                    formData.append("end_time",slotDetails.endTime);
                    formData.append("uniform_scheduling_office_timing_id",slotDetails.officeTimingId);
                }
                    root.updateBooking(formData);
            });
            /*** Slot booking update - End */

            $('#scheduling-form .is_client_show_up').on('click', function(){
                root.clientShowUpUpdates();
            });

            //Reschedule to another slot.
            // $("body").on("change", "#bookedDate", function(){
                slotResheduleDate = '';
            $('#bookedDate').on('change', function() {
                var bookedDate = $(this).val();
                if(bookedDate !== slotResheduleDate){

                    slotResheduleDate = bookedDate;
                    $('#scheduling-form #time_slots').empty().append($("<option></option>").attr("value",'').text('Please Select'));
                    root.getTimeSlots(bookedDate);
                }
            });


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
                        root.refetchSlotData(true);
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                }
            });
        },
        setBookingEntry(){
            let root = this;
            var otherDetails = root.ref.clickedSlotData;
            var slot = root.ref.bookedEntry;
            // console.log(root.ref.clickedSlotData);
            // console.log(root.ref.bookedEntry);
            var name = slot.user.first_name+" "+((slot.user.last_name == null) ? '' : slot.user.last_name);

            $(".client_show_up_yes_section").hide();
            $('#scheduleModal').modal();
            $('#scheduling-form')[0].reset();
            $('#scheduleModal .modal-title').text("Uniform Booked Details");
            $('#scheduleModal #pre-scheduled-date').text(otherDetails.title);
            $('#scheduleModal #pre-scheduled-time').text(otherDetails.displayName);
            $('#scheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#scheduleModal input[name="id"]').val(slot.id);
            $('#scheduleModal #firstName').text(slot.user.first_name);
            $('#scheduleModal #lastName').text(slot.user.last_name);
            $('#scheduleModal #genderValue').val(slot.gender);
            $('#scheduleModal #emailId').val(slot.email);
            $('#scheduleModal #phoneNumber').val(slot.phone_number);
            $('#scheduleModal #reviewNotes').text(slot.notes);
            let gender = slot.gender;
            $('#label_6 .mandatory').remove();
            // if(gender == 1){
            //     $('[name=point_value_6').removeAttr('required');
            // }else if(gender == 2){
            //     $("[name=point_value_6").attr("required", "true");
            //     $( "#label_6" ).append('<span class="mandatory">*</span>');
            // }
            if(gender == 1){
                $('[name=point_value_6').removeAttr('required');
                // $('#label_6 .mandatory').remove();
                $('#point_value_6').hide();
            }else if(gender == 2){
                $('#point_value_6').show();
                $("[name=point_value_6").attr("required", "true");
                $( "#label_6" ).append('<span class="mandatory">*</span>');
            }

            root.ref.bookedOfficeId = slot.uniform_scheduling_office_id;

            if(slot.is_client_show_up == 1){
                $("#scheduleModal #client_show_up_yes").prop("checked", true);
                $(".client_show_up_yes_section").show();
            }else if(slot.is_client_show_up == 0){
                $("#scheduleModal #client_show_up_no").prop("checked", true);
            }else{
                $(".client_show_up_yes_section").hide();
            }


            //--Start- Question answers section
            var questionAnswersHtml = "";

            $.each(slot.uniform_scheduling_custom_question_answer, function(index, value) {
                questionAnswersHtml += "<div class='form-group row'>";
                questionAnswersHtml += "<label class='col-sm-12'>"+value.custom_questions_str+"</label>";
                questionAnswersHtml += " <div class='col-sm-12'>";
                if(value.uniform_scheduling_custom_option_id == 1){
                    questionAnswersHtml += "<span class='view-form-element'>"+value.custom_option_str+" ("+value.other_value+")</span>";
                }else{
                    questionAnswersHtml += "<span class='view-form-element'>"+value.custom_option_str+"</span>";
                }
                questionAnswersHtml += "</div></div>";
            });

            $('#questionAnswers').html(questionAnswersHtml);
            //--End-- Question answers section

            $.each(slot.uniform_measurements, function(index, value) {
                let string = value.measurement_values;
                let array = string.split('.');
                let measurement = parseInt(value.measurement_values);
                if(measurement == 0){
                    measurement = null;
                }
                $('[name=point_value_'+value.uniform_scheduling_measurement_point_id+']').val(measurement);
                let decimal = array[1];
                if(array[1] == 000){
                    decimal = '0.0';
                }else{
                    decimal = '0.'+decimal;
                }
                console.log(array[0] + ' -- ' + decimal);
                $('[name=point_decimal_value_'+value.uniform_scheduling_measurement_point_id+']').val(decimal);

            });

        },
        refetchSlotData(triggerForm){
            $('#scheduling-form')[0].reset();
            $('#scheduleModal').modal('hide');
            $('#scheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            if(triggerForm){
                $("#filter-form").trigger("submit");
            }
        },
        deleteOrCancelBooking(formData){
            let root = this;
            var $form = $('#scheduling-form')
            var url = "{{ route('uniform-admin.booking.delete') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                data: formData,
                type: 'POST',
                success: function (data) {
                    if (data.success) {
                        root.refetchSlotData(true);
                        var msg = 'Deleted';

                        if(formData.get('is_canceled') == 1){
                            var msg = 'Canceled';
                        }
                        swal({
                            title: msg,
                            text: msg+" successfully",
                            type: "success",
                            confirmButtonText: "OK",
                        },function(){

                        });
                    } else {
                        $('#scheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                        associate_errors(data.error, $form, true);
                    }
                },
                fail: function (response) {
                    // console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        },
        updateBooking(formData){
            let root = this;
            var $form = $('#scheduling-form');
            var url = "{{ route('uniform-admin.booking.update') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: "Updated",
                            text: "Updated successfully",
                            type: "success",
                            confirmButtonText: "OK",
                        },function(){
                            root.refetchSlotData(data.reload);
                        });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        associate_errors(data.error, $form, true);
                    }
                },
                fail: function (response) {
                    // console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        },
        getTimeSlots(bookedDate){
            let root = this;
            var url = "{{route('uniform-admin.office-free.slots')}}"
            $.ajax({
                url: url,
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    bookedDate:bookedDate
                },
                type: 'GET',
                success: function(data) {
                    if(data.slots){
                        $('#scheduling-form #time_slots').empty().append($("<option></option>").attr("value",'').text('Please Select'));
                        $.each(data.slots, function(index, value) {
                            if(value.booking_flag == 1){
                                let slotDetails = [];
                                slotDetails = {
                                    startTime: value.start_time,
                                    endTime: value.end_time,
                                    officeTimingId: value.uniform_scheduling_office_timing_id,
                                };
                                $('#time_slots').append($("<option></option>")
                                .attr("value",value.uniform_scheduling_office_timing_id)
                                .attr("data-slotDetails",`${JSON.stringify(slotDetails)}`)
                                .text(value.display_name));
                            }

                        });
                    }else{
                        swal({
                            title: "Try Again",
                            text: "Data available",
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                        root.refetchSlotData(true);
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                }
            });
        },
        clientShowUpUpdates(){
            if($('input[name=is_client_show_up]:checked', '#scheduling-form').val() == 1){
                $(".client_show_up_yes_section").show();
            }else if($('input[name=is_client_show_up]:checked', '#scheduling-form').val() == 0){
                $(".client_show_up_yes_section").hide();
            }else{
                $(".client_show_up_yes_section").hide();
            }
        }

    }

 // Code to run when the document is ready.
    $(function() {
        bookingModal.initialize();
    });

    </script>
