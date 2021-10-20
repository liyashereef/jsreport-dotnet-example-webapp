<script>
    const bookingModal = {
        ref: {
            bookedServiceId: null,
            bookedPhotoServiceId: null,
            bookedOfficeId: null,
            slotBookedDate: null,
            serviceFee: 0,
            tax: 0,
            bookedTax: 0,
            photoFee: 0,
            givenRate: 0,
            onlinePaymentReceived: 0,
            balanceFee: 0,
            totalFeePayable: 0,
            bookingData: {}
        },
        initialize() {
            //Event listeners
            this.registerEvents();
            this.maskGivenSection();
        },
        registerEvents() {
            let root = this;
            $('#no-show-penalty').hide();
            /**Start** On slotClick
             * set booked data and question answers on modal
             */
            $("body").on("click", ".js-card", function() {
                // root.maskGivenSection();
                /** Initial Setting
                 * `ids_reschedule_date_pre_val` For scrool issue datepicker triger.
                 * Disable office change.
                 */
                ids_reschedule_date_pre_val = '';
                $('#ids-slot-rescheduling-form #idsOfficeId').attr("readonly", true);
                $('#ids-slot-rescheduling-form #idsOfficeId').attr("disabled", true);

                $('#ids-slot-rescheduling-form #isCandidate').attr("readonly", true).attr("disabled", true);
                $('#ids-slot-rescheduling-form #candidate_requisition_no').attr("readonly", true).attr("disabled", true);
                $('#ids-slot-rescheduling-form #isFederalBilling').attr("readonly", true).attr("disabled", true);
                $('#ids-slot-rescheduling-form #federal_billing_employer').attr("readonly", true).attr("disabled", true);

                //Setting default value for select box
                $('#rescheduleModal #time_slot').empty().append($("<option></option>").attr("value", '').text('Please Select'));

                var bookingId = $(this).attr("data-bookingid");
                var slotName = $(this).attr("data-slotname");
                var bookingDate = $(this).attr("data-bookingdate");
                var office_id = $('#office').val();
                let isPhotoService = $('#office').find(':selected').attr('data-isPhotoService');
                root.officeAllocatedServices(office_id, isPhotoService);
                let otherDetails = {
                    'office_id': office_id,
                    'bookingId': bookingId,
                    'slotName': slotName,
                    'bookingDate': bookingDate
                };
                var slot_details = $(this).data('event');

                if (slot_details) {
                    var flag = slot_details.flag;
                    if (flag == 1 || flag == 3) { // 1=Booked 3=To Be Rescheduled
                        var slot = JSON.parse(atob(slot_details.slot));
                        let otherDetails = {
                            'slotName': slot.booked_display_time,
                            'bookingDate': slot.booked_display_date
                        };
                        $('#rescheduleModal').modal();
                        $('#ids-slot-rescheduling-form')[0].reset();
                        root.setRescheduleModalData(slot, otherDetails);
                    }
                } else {
                    var bookingDetailsUrl = "{{route('idsscheduling-admin.office.slot-single-booking')}}";
                    $.ajax({
                        url: bookingDetailsUrl,
                        type: 'GET',
                        data: {
                            'ids_booking_id': bookingId
                        },
                        success: function(slot) {
                            $('#rescheduleModal').modal();
                            $('#ids-slot-rescheduling-form')[0].reset();
                            root.setRescheduleModalData(slot, otherDetails);

                        },
                        error: function(xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }

                    });
                }


            });
            /**End** On slotClick */

            /**Start** On change rescheduleDate
             * Fetch free slot on modal
             */
            ids_reschedule_date_val = '';
            ids_reschedule_date_pre_val = '';
            $('#ids_reschedule_date').on('change', function() {
                ids_reschedule_date_val = $('#ids_reschedule_date').val();
                if (ids_reschedule_date_val !== ids_reschedule_date_pre_val) {

                    ids_reschedule_date_pre_val = ids_reschedule_date_val;
                    // paymentReceivedStatus();
                    var ids_office_id = $('#idsOfficeId').val();
                    var slot_booked_date = $(this).val();
                    root.totalFeePayableUpdate();
                    $('#rescheduleModal #time_slot').empty().append($("<option></option>").attr("value", '').text('Please Select'));
                    var id = $(this).val();
                    var url = "{{route('idsscheduling-admin.office.free-slot')}}";
                    if (slot_booked_date != '') {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            data: {
                                'ids_office_id': ids_office_id,
                                'slot_booked_date': slot_booked_date
                            },
                            success: function(data) {
                                $.each(data, function(index, slot) {
                                    $('#time_slot').append($("<option></option>")
                                        .attr("value", slot.id)
                                        .text(slot.display_name));
                                });
                            },
                            error: function(xhr, textStatus, thrownError) {
                                if (xhr.status === 401) {
                                    window.location = "{{ route('login') }}";
                                }
                            }
                        });
                    }
                }
            });
            /**End** On change rescheduleDate */

            $('#ids-slot-rescheduling-form #idsOfficeId').on('change', function() {
                /** Remove reschedule_date and selected time slot.  */
                $('#rescheduleModal #ids_reschedule_date').val('');
                $('#rescheduleModal #time_slot').empty().append($("<option></option>").attr("value", '').text('Please Select'));

                let isPhotoService = $(this).find(':selected').attr('data-isPhotoService');
                let bookedPhotoServiceId = $('#passportPhotoServiceId').val();
                let photoServiceRequired = $('#idsServiceId').find(':selected').attr('data-isPhotoServiceRequired');
                let isServiceHavePhoto = $('#idsServiceId').find(':selected').attr('data-isPhotoService');

                if (isPhotoService != 1 && bookedPhotoServiceId > 0) {
                    var officeName = '';
                    if ($(this).find(':selected').text() != '') {
                        var officeAddress = $(this).find(':selected').text();
                        var office = officeAddress.split(" - ");
                        var officeName = office[0].trim();
                    }

                    let swalMsg = 'Photo service not avaliable in ' + officeName.toLowerCase() + ' office. If you need to proceed, we will cancel passport photo service';
                    if (photoServiceRequired == 1) {
                        swalMsg += ' and ' + $('#idsServiceId').find(':selected').text().toLowerCase();
                    }

                    root.photoServiceConfirm(swalMsg.replace("<br>", " "), false);
                } else {
                    /** Start- Fetch Office allocated services */
                    root.officeAllocatedServices($("#idsOfficeId").val(), null);
                }
            });

            /**Start** IOffice change  disabled and enable */
            $('#ids-slot-rescheduling-form .is_office_change').on('click', function() {

                if ($('input[name=is_office_change]:checked', '#ids-slot-rescheduling-form').val() == 1) {
                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("readonly", false);
                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("disabled", false);
                } else if ($('input[name=is_office_change]:checked', '#ids-slot-rescheduling-form').val() == 0) {
                    /** If we set office change to NO
                     * Set Office select box as disabled.
                     * Set booked office and service as selected.
                     * Remove reschedule_date and selected time slot.
                     * Fetch Office allocated service.
                     */
                    $('#rescheduleModal #idsOfficeId').val(root.ref.bookedOfficeId);
                    root.officeAllocatedServices(root.ref.bookedOfficeId, null);
                    $('#rescheduleModal #idsServiceId').val(root.ref.bookedServiceId);
                    $('#rescheduleModal #passportPhotoServiceId').val(root.ref.bookedPhotoServiceId);
                    $('#rescheduleModal #ids_reschedule_date').val('');
                    $('#rescheduleModal #time_slot').empty().append($("<option></option>").attr("value", '').text('Please Select'));

                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("readonly", true);
                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("disabled", true);

                } else {
                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("readonly", true);
                    $('#ids-slot-rescheduling-form #idsOfficeId').attr("disabled", true);
                }

            });
            /**End** IOffice change  disabled and enable */

            $('#ids-slot-rescheduling-form #isCandidate').on('change', function() {
                let is_candidate = $("#isCandidate").val();
                if (is_candidate == 1) {
                    $("#candidate_requisition_no").show();
                    $('#candidate_requisition_no').prop('required', true);
                } else {
                    $('#candidate_requisition_no').prop('required', false);
                    $("#candidate_requisition_no").hide();
                }
            });

            $('#ids-slot-rescheduling-form #isFederalBilling').on('change', function() {
                let is_candidate = $("#isFederalBilling").val();
                if (is_candidate == 1) {
                    $("#federal_billing_employer").show();
                    // $('#federal_billing_employer').prop('required', true);
                } else {
                    // $('#federal_billing_employer').prop('required', false);
                    $("#federal_billing_employer").hide();
                }
            });

            /**Start** Delete slot booking */
            // $('#delete_slot').on('click', function(e) {
            //     var $form = $('#ds-slot-rescheduling-form');
            //     swal({
            //         title: "Are you sure?",
            //         text: "You can not undo this action. Proceed?",
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonClass: "btn-danger",
            //         confirmButtonText: "Yes",
            //         showLoaderOnConfirm: true,
            //         closeOnConfirm: false
            //     },
            //     function () {
            //         e.preventDefault();
            //         var id = $('#ids-slot-rescheduling-form').find('input[name="id"]').val();
            //         var url = "{{ route('idsscheduling-admin.office.slot-delete') }}";
            //         // var url = base_url.replace(':id', id);
            //         var formData = new FormData();
            //         formData.append("id",id);
            //         formData.append("is_canceled",0);
            //         $.ajax({
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             },
            //             url: url,
            //             data: formData,
            //             type: 'POST',
            //             success: function (data) {
            //                 if (data.success) {
            //                     //Reload calendar.
            //                     //  root.fetchCalendarEvent();
            //                     //Reload selected day event.
            //                     // root.trigerCalendarClick();

            //                     trigerOnScheduleUpdate(true);

            //                     $('#ids-slot-rescheduling-form')[0].reset();
            //                     $('#rescheduleModal').modal('hide');
            //                     $('#ids-slot-rescheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

            //                     swal({
            //                         title: "Deleted",
            //                         text: "Deleted successfully",
            //                         type: "success",
            //                         confirmButtonText: "OK",
            //                     },function(){

            //                     });
            //                 } else {
            //                     $('#ids-slot-rescheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            //                     // console.log(data);
            //                     associate_errors(data.error, $form, true);
            //                 }
            //             },
            //             fail: function (response) {
            //                 // console.log(data);
            //             },
            //             error: function (xhr, textStatus, thrownError) {
            //                 associate_errors(xhr.responseJSON.errors, $form, true);
            //             },
            //             contentType: false,
            //             processData: false,
            //         });

            //     });

            // });

            $('#delete_slot').on('click', function(e) {
                if (root.ref.onlinePaymentReceived == 0) {
                    root.cancelOrDeleteEntry(0, 0);
                } else {
                    $('#cancel-delete-confirm-form')[0].reset();
                    $('#cancelOrDeleteConfirmModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
                    $('#cancelOrDeleteConfirmModal').modal();
                    $('#cancelOrDeleteConfirmModal .modal-title').text("Cancel/Delete Confirmation");
                    $('#cancel-delete-confirm-form #cancelOrDelete').val(0);
                    $("#cancel-delete-confirm-form #ifRefundStatusYes").hide();
                    $('#total-fee').text(root.ref.givenRate);
                    $('#total-fee-paid').text(root.ref.onlinePaymentReceived);
                    $('#refund-fee').text(root.ref.onlinePaymentReceived);

                    // let balanceFee = parseFloat(root.ref.givenRate) - parseFloat(root.ref.onlinePaymentReceived);
                    // let balanceFeeStr = balanceFee.toFixed(2).toString().replace("-", "");
                    // if( parseFloat(balanceFee) >= 1){
                    //     $('#refund-fee').text(root.ref.onlinePaymentReceived);
                    // }else{
                    //     $('#refund-fee').text(0);
                    // }
                }

            });
            /**End** Delete slot booking */

            /**Start** Cancel slot booking */

            $('#cancel-delete-confirm-form').submit(function(e) {
                e.preventDefault();
                if ($('#cancel-delete-confirm-form input[name="refund_status"]').is(':checked')) {
                    let cancelOrDelete = $('#cancelOrDelete').val();
                    root.cancelOrDeleteEntry(true, cancelOrDelete);
                } else {
                    $('#cancel-delete-confirm-form #refund_status .help-block').text('Request for refund is required')
                }
            });

            $('#cancel_booking').on('click', function() {

                if (root.ref.onlinePaymentReceived == 0) {
                    root.cancelOrDeleteEntry(0, 1);
                } else {
                    $('#cancel-delete-confirm-form')[0].reset();
                    $('#cancelOrDeleteConfirmModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
                    $('#cancelOrDeleteConfirmModal').modal();
                    $('#cancelOrDeleteConfirmModal .modal-title').text("Cancel/Delete Confirmation");
                    $("#cancel-delete-confirm-form #ifRefundStatusYes").hide();
                    $('#cancel-delete-confirm-form #cancelOrDelete').val(1);
                    $('#total-fee').text(root.ref.givenRate);
                    $('#total-fee-paid').text(root.ref.onlinePaymentReceived);
                    $('#refund-fee').text(root.ref.onlinePaymentReceived);
                    // let balanceFee = parseFloat(root.ref.givenRate) - parseFloat(root.ref.onlinePaymentReceived);
                    // let balanceFeeStr = balanceFee.toFixed(2).toString().replace("-", "");
                    // if( parseFloat(balanceFee) >= 1){
                    //     $('#refund-fee').text(balanceFeeStr);
                    // }else{
                    //     $('#refund-fee').text(0);
                    // }
                }
            });
            /**End** Cancel slot booking */

            /**Start** Slot rescheduling form submission  */
            @can('ids_reschedule_appointment')
            $('#ids-slot-rescheduling-form').submit(function(e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('idsscheduling-admin.office.slot-update') }}";
                var formData = new FormData($('#ids-slot-rescheduling-form')[0]);
                if (formData.get('ids_office_id') == null) {
                    formData.append("ids_office_id", root.ref.bookedOfficeId);
                }
                if (formData.get('is_client_show_up') == 0) {
                    formData.append("ids_payment_method_id", '');
                    formData.append("ids_payment_reason_id", '');
                    formData.append("is_payment_received", '');
                    formData.append("payment_reason", '');
                    formData.append("is_mask_given", '');
                    formData.append("no_masks_given", '');
                }

                if (root.ref.bookedPhotoServiceId > 0) {
                    let isPhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
                    let isServiceHavePhoto = $('#idsServiceId').find(':selected').attr('data-isPhotoService');
                    if (isPhotoService != 1 || isServiceHavePhoto != 1) {
                        formData.append("passport_photo_service_id", '');
                    }
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            //Reload calendar.
                            if (formData.get('slot_booked_date') != null && formData.get('ids_office_slot_id') != null) {
                                reloadAll = true;
                            }
                            trigerOnScheduleUpdate(reloadAll);
                            $('#rescheduleModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#rescheduleModal').modal('hide');
                            swal({
                                title: "Updated",
                                text: "Updated successfully",
                                type: "success",
                                confirmButtonText: "OK",
                            }, function() {

                            });
                        } else {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            // console.log(data);
                            swal({
                                title: "Error",
                                text: data.message,
                                type: "warning",
                                confirmButtonText: "OK",
                            }, function() {

                            });
                            // associate_errors(data.error, $form, true);
                        }
                    },
                    fail: function(response) {
                        // console.log(data);
                    },
                    error: function(xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false,
                    processData: false,
                });
            });
            @endcan
            /**End** Slot rescheduling form submission -  */


            /*** Slot rescheduling form submission - Start */
            $('#ids-to-be-rescheduling-form').submit(function(e) {
                e.preventDefault();
                var $form = $(this);
                var url = "{{ route('idsscheduling-admin.office.to-be-rescheduling') }}";
                var formData = new FormData($('#ids-to-be-rescheduling-form')[0]);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            $('#toBeRescheduleModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
                            $('#toBeRescheduleModal').modal('hide');
                            swal({
                                title: "Updated",
                                text: "Updated successfully",
                                type: "success",
                                confirmButtonText: "OK",
                            }, function() {
                                // $("#slot-rescheduling-table").remove();
                                $('#ids-to-be-rescheduling-form')[0].reset();
                                $("#reschedule-form").trigger("submit");
                            });
                        } else {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            // console.log(data);
                            associate_errors(data.error, $form, true);
                        }
                    },
                    fail: function(response) {
                        // console.log(data);
                    },
                    error: function(xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                    contentType: false,
                    processData: false,
                });
            });
            /*** Slot rescheduling form submission - End */

            $('#ids-slot-rescheduling-form #idsPaymentReasonId').on('change', function() {
                root.PaymentReasonOther();
            });

            $('#ids-slot-rescheduling-form .is_payment_received').on('click', function() {
                root.paymentReceivedStatus();
            });

            $('#ids-slot-rescheduling-form .is_mask_given').on('click', function() {
                root.maskGivenSection();
            });

            $('#ids-slot-rescheduling-form .is_client_show_up').on('click', function() {
                root.cliectShowUpUpdates();
            });

            $('#ids-slot-rescheduling-form #idsServiceId').on('change', function() {
                let isPhotoService = $(this).find(':selected').attr('data-isPhotoService');
                let photoServiceRequired = $(this).find(':selected').attr('data-isPhotoServiceRequired');
                let officePhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
                let selectedPhotoServiceId = $('#passportPhotoServiceId').val();

                if (isPhotoService != 1 && selectedPhotoServiceId > 0 && officePhotoService == 1) {
                    let swalMsg = 'Photo service not avaliable along with ' + $(this).find(':selected').text().toLowerCase() + '. If you need to proceed, we will cancel passport photo service';
                    if (photoServiceRequired == 1) {
                        swalMsg += ' and ' + $('#idsServiceId').find(':selected').text().toLowerCase();
                    }
                    root.photoServiceConfirm(swalMsg, true);
                } else {
                    if (isPhotoService == 1 && officePhotoService == 1) {
                        $("#rescheduleModal #passport_photo_service_id").show();
                    } else {
                        $("#rescheduleModal #passport_photo_service_id").hide();
                    }
                    root.totalFeePayableUpdate();
                }

                if (photoServiceRequired == 1) {
                    $("#rescheduleModal #passportPhotoServiceId").prop('required', true);
                    $('#passport_photo_service_id .mandatory').show();
                } else {
                    $("#rescheduleModal #passportPhotoServiceId").prop('required', false);
                    $('#passport_photo_service_id .mandatory').hide();
                }
                // let isTax = $(this).find(':selected').attr('data-isTax');
                // let taxEffectiveFromDate = $(this).find(':selected').attr('data-taxEffectiveFromDate');

                // if(moment(root.ref.slotBookedDate).format('YYYY-MM-DD') >= moment(taxEffectiveFromDate).format('YYYY-MM-DD')){
                //     $("#taxIncluded").show();
                //     let percentage = $(this).find(':selected').attr('data-tax');
                //     $('#taxIncluded').text(percentage.substr(00, percentage.indexOf('.'))+'% tax included');
                // }else{
                //     $("#taxIncluded").hide();
                //     $('#serviceTaxIncluded').text('');
                // }
            });

            $('#ids-slot-rescheduling-form #passportPhotoServiceId').on('change', function() {
                root.totalFeePayableUpdate();
            });

            $('#ids-slot-rescheduling-form #refund_status_yes').on('click', function() {
                $("#ids-slot-rescheduling-form #ifRefundStatusYes").show();
                $("#ids-slot-rescheduling-form #refund_note").show();
            });

            $('#ids-slot-rescheduling-form #refund_status_no').on('click', function() {
                $("#ids-slot-rescheduling-form #ifRefundStatusYes").hide();
                $("#ids-slot-rescheduling-form #refund_note").show();
            });

            $('#cancel-delete-confirm-form #refund_status_yes').on('click', function() {
                $("#cancel-delete-confirm-form #ifRefundStatusYes").show();
            });

            $('#cancel-delete-confirm-form #refund_status_no').on('click', function() {
                $("#cancel-delete-confirm-form #ifRefundStatusYes").hide();
            });

            $('#ids-slot-rescheduling-form #isCandidate').on('change', function() {
                root.balanceFeeUpdate();
                root.deferredEntryNote();
            });

            $('#ids-slot-rescheduling-form #isFederalBilling').on('change', function() {
                root.balanceFeeUpdate();
                root.deferredEntryNote();
            });


        },
        officeAllocatedServices(officeId, isPhotoService) {
            let root = this;
            let selectBookedServiceId = false;
            let runphotoSectionUpdates = false;
            var base_url = "{{route('ids-office-services', ':id')}}";
            var url = base_url.replace(':id', officeId);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('#rescheduleModal #idsServiceId').empty().append($("<option></option>")
                        .attr("value", '')
                        .attr("data-isPhotoService", '')
                        .attr("data-isPhotoServiceRequired", '')
                        .attr("data-rate", 0)
                        .attr("data-isTax", 0)
                        .attr("data-tax", 0)
                        .attr("data-taxEffectiveFromDate", '')
                        .text('Please Select'));
                    if (isPhotoService == null) {
                        isPhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
                    }
                    $.each(data, function(index, service) {
                        runphotoSectionUpdates = true;
                        if (service.id == root.ref.bookedServiceId) {
                            selectBookedServiceId = true;
                        }
                        let isServiceList = true;
                        if (isPhotoService == 0 && service.is_photo_service_required == 1) {
                            isServiceList = false;
                        }
                        let isTax = 0;
                        let tax = 0;
                        let taxEffectiveFromDate = '';
                        if (service.tax_master && service.tax_master.tax_master_log) {
                            isTax = 1;
                            tax = service.tax_master.tax_master_log.tax_percentage;
                            taxEffectiveFromDate = service.tax_master.tax_master_log.effective_from_date;
                        }
                        if (isServiceList == true) {
                            $('#idsServiceId').append($("<option></option>")
                                .attr("value", service.id)
                                .attr("data-isPhotoService", service.is_photo_service)
                                .attr("data-isPhotoServiceRequired", service.is_photo_service_required)
                                .attr("data-rate", service.rate)
                                .attr("data-isTax", isTax)
                                .attr("data-tax", tax)
                                .attr("data-taxEffectiveFromDate", taxEffectiveFromDate)
                                .text(service.name + ' - $' + service.rate));
                        }

                    });
                    if (selectBookedServiceId) {
                        $('#rescheduleModal #idsServiceId').val(root.ref.bookedServiceId);
                    } else {
                        $('#rescheduleModal #idsServiceId').val();
                    }
                    if (runphotoSectionUpdates) {
                        root.managePhotoSection();
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
            });
        },
        cliectShowUpUpdates() {
            let root = this;
            $('#ids-slot-rescheduling-form #isCandidate').attr("readonly", true).attr("disabled", true);
            $('#ids-slot-rescheduling-form #candidate_requisition_no').attr("readonly", true).attr("disabled", true);
            $('#ids-slot-rescheduling-form #isFederalBilling').attr("readonly", true).attr("disabled", true);
            $('#ids-slot-rescheduling-form #federal_billing_employer').attr("readonly", true).attr("disabled", true);

            if ($('input[name=is_client_show_up]:checked', '#ids-slot-rescheduling-form').val() == 1) {
                $(".client_show_up_yes_section").show();
                let balanceFeeVal = $('#balanceFeeVal').val();
                if (balanceFeeVal < 0) {
                    $(".balance_fee_section").show();
                }
                $('#ids-slot-rescheduling-form #isCandidate').attr("readonly", false).attr("disabled", false);
                $('#ids-slot-rescheduling-form #candidate_requisition_no').attr("readonly", false).attr("disabled", false);
                $('#ids-slot-rescheduling-form #isFederalBilling').attr("readonly", false).attr("disabled", false);
                $('#ids-slot-rescheduling-form #federal_billing_employer').attr("readonly", false).attr("disabled", false);

            } else if ($('input[name=is_client_show_up]:checked', '#ids-slot-rescheduling-form').val() == 0) {
                $(".client_show_up_yes_section").hide();

                $("#rescheduleModal #isFederalBilling").val(root.ref.bookingData.is_federal_billing);
                $("#rescheduleModal #isCandidate").val(root.ref.bookingData.is_candidate);
                $("#rescheduleModal #candidate_requisition_no").val(root.ref.bookingData.candidate_requisition_no);
                $("#rescheduleModal #federal_billing_employer").val(root.ref.bookingData.federal_billing_employer);

                if (root.ref.bookingData.is_candidate == 1) {
                    $("#candidate_requisition_no").show();
                    $('#candidate_requisition_no').prop('required', true);
                } else {
                    $('#candidate_requisition_no').prop('required', false);
                    $("#candidate_requisition_no").hide();
                }

                if (root.ref.bookingData.is_federal_billing == 1) {
                    $("#federal_billing_employer").show();
                    // $('#federal_billing_employer').prop('required', true);
                } else {
                    // $('#federal_billing_employer').prop('required', false);
                    $("#federal_billing_employer").hide();
                }

            } else {
                $(".client_show_up_yes_section").hide();
            }
            root.balanceFeeUpdate();
        },
        paymentReceivedStatus() {
            if ($('input[name=is_payment_received]:checked', '#ids-slot-rescheduling-form').val() == 1) {
                $("#ids_payment_method_id").show();
                $(".payment_not_received_section").hide();
            } else if ($('input[name=is_payment_received]:checked', '#ids-slot-rescheduling-form').val() == 0) {
                $("#ids_payment_method_id").hide();
                $(".payment_not_received_section").show();
            } else {
                $("#ids_payment_method_id").hide();
                $(".payment_not_received_section").hide();
            }
        },
        PaymentReasonOther() {
            let idsPaymentReasonId = $('#idsPaymentReasonId').val();
            if (idsPaymentReasonId == 1) {
                $("#payment_reason").show();
            } else {
                $("#payment_reason").hide();
                $('#paymentReason').text('');
            }
        },
        maskGivenSection() {
            $('#rescheduleModal #noMasksGiven').val('');
            $(".mask-number-section").hide();
            if ($('input[name=is_mask_given]:checked', '#ids-slot-rescheduling-form').val() == 1) {
                $(".mask-number-section").show();
            } else if ($('input[name=is_mask_given]:checked', '#ids-slot-rescheduling-form').val() == 0) {
                $(".mask-number-section").hide();
            } else {

            }
        },
        setRescheduleModalData(slot, otherDetails) {
            let root = this;
            root.ref.bookingData = slot;
            var name = slot.first_name + " " + ((slot.last_name == null) ? '' : slot.last_name);
            $("#ids_payment_method_id").hide();
            $(".client_show_up_yes_section").hide();
            $(".payment_not_received_section").hide();
            $(".balance_fee_section").hide();
            $("#ifRefundStatusYes").hide();
            $('.online-payment-section').hide();
            $('#rescheduleModal .modal-title').text("Edit Details");
            $('#rescheduleModal #pre-scheduled-date').text(otherDetails.bookingDate);
            $('#rescheduleModal #pre-scheduled-time').text(otherDetails.slotName);
            $('#ids-slot-rescheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#rescheduleModal input[name="id"]').val(slot.id);
            $('#rescheduleModal input[name="refundStatuVal"]').val(slot.refund_status);
            $('#rescheduleModal input[name="first_name"]').val(slot.first_name);
            $('#rescheduleModal input[name="last_name"]').val(slot.last_name);
            $('#rescheduleModal input[name="email"]').val(slot.email);
            $('#rescheduleModal input[name="phone_number"]').val(slot.phone_number);
            $("#rescheduleModal #idsServiceId").val(slot.ids_service_id);
            $('#rescheduleModal #idsOfficeId').val(slot.ids_office_id);
            $("#rescheduleModal #idsPaymentMethodId").val(slot.ids_payment_method_id);
            $('#rescheduleModal #ids_notes').text(slot.notes);
            $("#rescheduleModal #idsPaymentReasonId").val(slot.ids_payment_reason_id);
            $("#rescheduleModal #passportPhotoServiceId").val(slot.passport_photo_service_id);
            $("#rescheduleModal #totalFeePayable").text('$' + slot.given_rate);

            //Set global varables
            root.ref.totalFeePayable = slot.given_rate;
            root.ref.givenRate = slot.given_rate;
            root.ref.onlinePaymentReceived = 0;
            if (slot.ids_online_payment) {
                root.ref.onlinePaymentReceived = slot.ids_online_payment.amount;
                root.balanceFeeUpdate();
            }
            root.ref.slotBookedDate = slot.slot_booked_date;
            root.ref.bookedServiceId = slot.ids_service_id;
            root.ref.bookedOfficeId = slot.ids_office_id;
            root.ref.bookedPhotoServiceId = slot.passport_photo_service_id;

            //Hide delete, cancel and update button after .
            let editUpTo = moment().subtract(7, "days").format("YYYY-MM-DD");
            if (editUpTo >= slot.slot_booked_date || slot.refund_status == 2 || slot.refund_status == 1) {
                $('#rescheduleModalFooter').addClass('section-display');
            } else {
                $('#rescheduleModalFooter').removeClass('section-display');
            }

            //If `Client Show Up` `yes` hide delete and cancel button.
            if (slot.is_client_show_up == 1) {
                $('#delete-cancel-section').hide();
            } else {
                $('#delete-cancel-section').show();
            }

            //`Client Show Up` management.
            $("#rescheduleModal #office_change_no").prop("checked", true);
            // $('#ids-slot-rescheduling-form #isCandidate').attr("readonly", true).attr("disabled", true);
            // $('#ids-slot-rescheduling-form #isFederalBilling').attr("readonly", true).attr("disabled", true);
            if (slot.is_client_show_up == 1) {
                $("#rescheduleModal #client_show_up_yes").prop("checked", true);
                $(".client_show_up_yes_section").show();
                // $('#ids-slot-rescheduling-form #isCandidate').attr("readonly", true).attr("disabled", true);
                // $('#ids-slot-rescheduling-form #isFederalBilling').attr("readonly", true).attr("disabled", true);
            } else if (slot.is_client_show_up == 0) {
                $("#rescheduleModal #client_show_up_no").prop("checked", true);
            } else {
                $(".client_show_up_yes_section").hide();
            }

            //Payment managament
            if (slot.is_payment_received == 1) {
                $("#rescheduleModal #payment_received_yes").prop("checked", true);
                $("#ids_payment_method_id").show();
                $(".payment_not_received_section").hide();
            } else if (slot.is_payment_received == 0) {
                $("#rescheduleModal #payment_received_no").prop("checked", true);
                $("#ids_payment_reason_id").show();
                $(".payment_not_received_section").show();
            }

            //Mask management.
            $('#rescheduleModal #noMasksGiven').val(slot.no_masks_given);
            if (slot.is_mask_given == 1) {
                $("#rescheduleModal #is_mask_given_yes").prop("checked", true);
                $(".mask-number-section").show();
            } else if (slot.is_mask_given == 0) {
                $("#rescheduleModal #is_mask_given_no").prop("checked", true);
                $(".mask-number-section").hide();
            } else if (slot.is_mask_given == null) {
                $(".mask-number-section").hide();
            }

            //`Request for refund` management.
            $("#ids-slot-rescheduling-form #refund_note").hide();
            if (slot.refund_status == 1 || slot.refund_status == 2) {
                $(".balance_fee_section").show();
                $("#rescheduleModal #refund_status_yes").prop("checked", true);
                $("#ids-slot-rescheduling-form #ifRefundStatusYes").show();
                $("#ids-slot-rescheduling-form #refund_note").show();
            } else if (slot.refund_status == 0) {
                $(".balance_fee_section").show();
                $("#rescheduleModal #refund_status_no").prop("checked", true);
                $("#ids-slot-rescheduling-form #ifRefundStatusYes").hide();
                $("#ids-slot-rescheduling-form #refund_note").show();
            } else {
                // $(".balance_fee_section").hide();
                // $("#ids-slot-rescheduling-form  #ifRefundStatusYes").hide();
            }

            //No show penality
            if (slot.cancelled_booking_id != null) {
                $('#no-show-penalty').hide();
            } else {
                $('#no-show-penalty').hide();
            }

            //Payment reasons
            if (slot.ids_payment_reason_id == 1) {
                $("#payment_reason").show();
                $('#rescheduleModal #paymentReason').text(slot.payment_reason);
            } else {
                $("#payment_reason").hide();
            }

            //Federal billing and candidate management.
            $("#rescheduleModal #isFederalBilling").val(slot.is_federal_billing);
            $("#rescheduleModal #isCandidate").val(slot.is_candidate);
            if (slot.is_candidate == 1) {
                $("#candidate_requisition_no").show();
                $("#rescheduleModal #candidate_requisition_no").val(slot.candidate_requisition_no);
                $('#candidate_requisition_no').prop('required', true);
            } else {
                $('#candidate_requisition_no').prop('required', false);
                $("#candidate_requisition_no").hide();
            }

            if (slot.is_federal_billing == 1) {
                $("#federal_billing_employer").show();
                $("#rescheduleModal #federal_billing_employer").val(slot.federal_billing_employer);
                // $('#federal_billing_employer').prop('required', true);
            } else {
                // $('#federal_billing_employer').prop('required', false);
                $("#federal_billing_employer").hide();
            }

            //--Start- Question answers section
            var questionAnswersHtml = "";
            $.each(slot.ids_custom_question_answers, function(index, value) {
                questionAnswersHtml += "<div class='form-group row'>";
                questionAnswersHtml += "<label class='col-sm-12'>" + value.ids_custom_questions_str + "</label>";
                questionAnswersHtml += " <div class='col-sm-12'>";
                if (value.ids_custom_option_id == 1) {
                    questionAnswersHtml += "<span class='view-form-element'>" + value.ids_custom_option_str + " (" + value.other_value + ")</span>";
                } else {
                    questionAnswersHtml += "<span class='view-form-element'>" + value.ids_custom_option_str + "</span>";
                }
                questionAnswersHtml += "</div></div>";
            });
            $('#questionAnswers').html(questionAnswersHtml);
            //--End-- Question answers section

            // Transaction history details log.
            let refundDetails = '';
            refundDetails += '<ul>';
            var isPending=false;
            $.each(slot.ids_transaction_history, function(index, value) {
                if (value.refund_status == null && value.user_id == null) {
                    refundDetails += "<li> $" + value.amount + " received through " + value.ids_payment_method.full_name + " payment on " + moment(value.created_at).format('MMMM Do YYYY, h:mm:ss A') + "</li>";
                }
                if (value.refund_status == null && value.user_id != null) {
                    refundDetails += "<li> $" + value.amount + " received through " + value.ids_payment_method.full_name + " on " + moment(value.created_at).format('MMMM Do YYYY, h:mm:ss A') + ", processed by " + value.user.name_with_emp_no + "</li>";
                }
                let message = '';
                if (value.refund_status == 0) {
                    message = 'request cancelled';
                } else if (value.refund_status == 1) {
                    message = 'requested';
                } else if (value.refund_status == 2) {
                    if(isPending ==true)
                    {
                        message = 'request approved from stripe';
                    }else{
                        message = 'request approved';
                    }

                } else if (value.refund_status == 3) {
                    message = 'request rejected';
                }else if(value.refund_status == 4){
                    isPending=true;
                    message = "request approved by "+value.user.name_with_emp_no+" and initiated to stripe";
                }else if(value.refund_status == 5){
                    isPending=true;
                    message = 'request pending from stripe';
                }else if(value.refund_status == 6){
                    message = 'request failed from stripe';
                } else {}
                if (message) {
                    refundDetails += '<li> Refund ($' + value.amount + ') ' + message;
                    var createdDate=value.created_at;
                    if(value.refund){
                        if(value.refund.refund_end_time && ( value.refund_status == 2 || value.refund_status == 6)){
                            createdDate=value.refund.refund_end_time;
                        }
                        if(value.refund.refund_start_time &&  value.refund_status == 4 ||  value.refund_status == 5){
                            createdDate=value.refund.refund_start_time;
                        }
                    }
                    if(value.refund_status == 4 || value.refund_status == 5 || value.refund_status == 6 || isPending ==true){
                        refundDetails += ' on ' + moment(createdDate).format('MMMM Do YYYY, h:mm:ss A') + ".";
                    }else{
                        refundDetails += " by " + value.user.name_with_emp_no+' on ' + moment(createdDate).format('MMMM Do YYYY, h:mm:ss A') + ".";
                    }
                    if (value.refund_note) {
                        refundDetails += " <br/> Refund Note : " + value.refund_note;
                    }
                    refundDetails += " </li>";
                }
            });
            refundDetails += '</ul>';
            if(slot.ids_transaction_history.length > 0){
                $('#refundDetails').show();
                $('#refundDetails').html(refundDetails);
            }else{
                $('#refundDetails').hide();
            }


            // Fee management
            root.ref.serviceFee = 0;
            root.ref.photoFee = 0;
            if (slot.ids_services.tax_master && slot.ids_services.tax_master.tax_master_log) {
                root.ref.tax = slot.ids_services.tax_master.tax_master_log.tax_percentage;
            }
            let isTax = 0;
            $.each(slot.ids_entry_amount_split_up, function(index, splitUp) {
                if (splitUp.type == 0 && splitUp.service_id == null) {
                    isTax = 1;
                    $("#taxIncluded").show();
                    root.ref.bookedTax = splitUp.tax_percentage;
                    let taxPercentageSplitUp = splitUp.tax_percentage.split(".");
                    if (taxPercentageSplitUp.length == 2) {
                        if (parseFloat(taxPercentageSplitUp[1]) == 0) {
                            root.ref.photoFee = parseFloat(taxPercentageSplitUp[0]);
                        }
                    }
                    $('#taxIncluded').text(root.ref.bookedTax + '% tax included');
                }
                if (splitUp.type == 1) {
                    root.ref.serviceFee = splitUp.rate;
                } else if (splitUp.type == 2) {
                    root.ref.photoFee = splitUp.rate;
                } else {

                }
            });

            // Photo service and fee update.
            if (root.ref.bookedPhotoServiceId > 0) {
                let isPhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
                let isServiceHavePhoto = $('#idsServiceId').find(':selected').attr('data-isPhotoService');
                if (isPhotoService != 1 || isServiceHavePhoto != 1) {
                    $("#rescheduleModal #passport_photo_service_id").show();
                    $('#passportPhotoServiceId').attr("readonly", true);
                    $('#passportPhotoServiceId').attr("disabled", true);
                    $('#passportPhotoServiceMessage').text('Passport photo service is not available at this location/service')
                    $('#passportPhotoServiceMessage').show();
                    root.totalFeePayableUpdate();
                } else {
                    root.managePhotoSection();
                }
            } else {
                root.managePhotoSection();
            }

        },
        photoServiceConfirm(swalMsg, isService) {
            let root = this;
            swal({
                    title: "Are you sure?",
                    text: swalMsg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: 'No',
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        /** Start- Fetch Office allocated services */
                        if (isService == false) {
                            root.officeAllocatedServices($("#idsOfficeId").val(), null);
                        }
                        $("#rescheduleModal #passportPhotoServiceId").val('');
                        $("#rescheduleModal #passport_photo_service_id").hide();
                        $("#rescheduleModal #passportPhotoServiceId").prop('required', false);
                        let photoServiceRequired = $('#idsServiceId').find(':selected').attr('data-isPhotoServiceRequired');
                        if (photoServiceRequired == 1) {
                            $("#rescheduleModal #idsServiceId").val('');
                            $("#rescheduleModal #passportPhotoServiceId").prop('required', false);
                            $('#passport_photo_service_id .mandatory').hide();
                        }
                        if (isService == true) {
                            root.totalFeePayableUpdate();
                        }
                    } else {
                        $("#rescheduleModal #idsServiceId").val(root.ref.bookedServiceId);
                        $('#rescheduleModal #idsOfficeId').val(root.ref.bookedOfficeId);
                        $('#rescheduleModal #passportPhotoServiceId').val(root.ref.bookedPhotoServiceId);
                        root.managePhotoSection();
                    }

                });
        },
        totalFeePayableUpdate() {
            let root = this;
            let serviceId = $('#idsServiceId').val();
            serviceChanged = false;
            if (serviceId != root.ref.bookingData.ids_service_id) {
                serviceChanged = true;
            }

            root.ref.serviceFee = $('#idsServiceId').find(':selected').attr('data-rate');
            root.ref.tax = $('#idsServiceId').find(':selected').attr('data-tax');
            let photoService = $('#passportPhotoServiceId').find(':selected').text();
            let photoServiceArray = photoService.split("$");

            root.ref.photoFee = 0;
            if (photoServiceArray.length == 2) {
                root.ref.photoFee = parseFloat(photoServiceArray[1]);
            }
            if (root.ref.bookedPhotoServiceId > 0) {
                let isPhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
                let isServiceHavePhoto = $('#idsServiceId').find(':selected').attr('data-isPhotoService');
                if (isPhotoService != 1 || isServiceHavePhoto != 1) {
                    root.ref.photoFee = 0;
                }
            }
            let totalFee = parseFloat(root.ref.serviceFee) + parseFloat(root.ref.photoFee);
            let taxFee = 0;

            $("#taxIncluded").hide();
            let taxEffectiveFromDate = $('#idsServiceId').find(':selected').attr('data-taxEffectiveFromDate');
            let bookingDate = $('#ids_reschedule_date').val();
            if (bookingDate == '') {
                bookingDate = root.ref.slotBookedDate;
            }

            if (serviceChanged == false) {
                bookedTax = root.ref.bookedTax;
                if (root.ref.bookedTax > 0) {
                    let taxPercentageSplitUp = root.ref.bookedTax.split(".");
                    if (taxPercentageSplitUp.length == 2) {
                        if (parseFloat(taxPercentageSplitUp[1]) == 0) {
                            bookedTax = taxPercentageSplitUp[0];
                        }
                    }
                    taxFee = (totalFee * bookedTax) / 100;
                    let totalTaxFeeArray = taxFee.toString().split(".");
                    if (totalTaxFeeArray.length == 2) {
                        let taxDecimal = totalTaxFeeArray[1].substr(0, 2);
                        taxFee = parseFloat(totalTaxFeeArray[0] + '.' + taxDecimal);
                    }
                    $("#taxIncluded").show();
                    $('#taxIncluded').text(bookedTax + '% tax included');
                }
            }

            if (moment(bookingDate).format('YYYY-MM-DD') >= moment(taxEffectiveFromDate).format('YYYY-MM-DD')) {
                if (serviceChanged) {
                    if (root.ref.tax > 0) {
                        tax = root.ref.tax;
                        let taxPercentageSplitUp = root.ref.tax.split(".");
                        if (taxPercentageSplitUp.length == 2) {
                            if (parseFloat(taxPercentageSplitUp[1]) == 0) {
                                tax = taxPercentageSplitUp[0];
                            }
                        }
                        taxFee = (totalFee * root.ref.tax) / 100;
                        let totalTaxFeeArray = taxFee.toString().split(".");
                        if (totalTaxFeeArray.length == 2) {
                            let taxDecimal = totalTaxFeeArray[1].substr(0, 2);
                            taxFee = parseFloat(totalTaxFeeArray[0] + '.' + taxDecimal);
                        }
                        $("#taxIncluded").show();
                        $('#taxIncluded').text(tax + '% tax included');
                    }
                }
            }

            let totalFeePayable = parseFloat(parseFloat(totalFee) + parseFloat(taxFee)).toFixed(3);
            if (totalFeePayable >= 0) {
                let totalFeePayableArray = totalFeePayable.toString().split(".");
                if (totalFeePayableArray.length == 2) {
                    let totalFeeDecimal = totalFeePayableArray[1].substr(0, 2);
                    totalFeePayable = parseFloat(totalFeePayableArray[0] + '.' + totalFeeDecimal);
                }
                let formattedFeePayable = '$' + totalFeePayable;
                $('#totalFeePayable').text(formattedFeePayable);
            } else {
                $('#totalFeePayable').text(0);
            }
            root.ref.totalFeePayable = totalFeePayable;

            root.balanceFeeUpdate();
            root.deferredEntryNote();
        },
        managePhotoSection() {
            let root = this;
            let isPhotoService = $('#idsOfficeId').find(':selected').attr('data-isPhotoService');
            let photoServiceRequired = $('#idsServiceId').find(':selected').attr('data-isPhotoServiceRequired');
            let isServiceHavePhoto = $('#idsServiceId').find(':selected').attr('data-isPhotoService');
            $('#passportPhotoServiceId').attr("readonly", false);
            $('#passportPhotoServiceId').attr("disabled", false);
            $('#passportPhotoServiceMessage').hide();
            if (isPhotoService == 1 && isServiceHavePhoto == 1) {
                $("#rescheduleModal #passport_photo_service_id").show();
                if (root.ref.bookedPhotoServiceId > 0) {
                    $("#rescheduleModal #passportPhotoServiceId").val(root.ref.bookedPhotoServiceId);
                }

                if (photoServiceRequired == 1) {
                    $("#rescheduleModal #passportPhotoServiceId").prop('required', true);
                    $('#passport_photo_service_id .mandatory').show();
                } else {
                    $("#rescheduleModal #passportPhotoServiceId").prop('required', false);
                    $('#passport_photo_service_id .mandatory').hide();
                }

            } else {
                $("#rescheduleModal #passport_photo_service_id").hide();
                $('#passportPhotoServiceId').val(" ");
                $("#rescheduleModal #passportPhotoServiceId").prop('required', false);
                $('#passport_photo_service_id .mandatory').hide();
            }
            root.totalFeePayableUpdate();
        },
        balanceFeeUpdate() {
            let root = this;
            let totalFeePayable = root.ref.totalFeePayable;
            $("#rescheduleModal #totalFeePaid").removeClass('total-fee-paid');
            $("#rescheduleModal #balanceFee").removeClass('balance-fee');
            let isCandidate = $('#isCandidate').val();
            let isFederalBilling = $('#isFederalBilling').val();
            if (root.ref.onlinePaymentReceived > 0) {

                $('.online-payment-section').show();
                $("#rescheduleModal #totalFeePaid").text('$' + root.ref.onlinePaymentReceived);
                $("#rescheduleModal #totalFeePaid").addClass('total-fee-paid');
                let balanceFee = parseFloat(totalFeePayable) - parseFloat(root.ref.onlinePaymentReceived);
                root.ref.balanceFee = parseFloat(balanceFee).toFixed(2);
                if (isCandidate == 1 || isFederalBilling == 1) {
                    let photoServiceId = $('#passportPhotoServiceId').val();
                    let photoService = $('#passportPhotoServiceId').find(':selected').text();
                    let photoServiceArray = photoService.split("$");

                    if (photoServiceId > 0 && photoServiceArray.length == 2) {
                        totalFee = parseFloat(photoServiceArray[1]);
                        taxFee = 0;
                        let taxEffectiveFromDate = $('#idsServiceId').find(':selected').attr('data-taxEffectiveFromDate');
                        let bookingDate = $('#ids_reschedule_date').val();
                        if (bookingDate == '') {
                            bookingDate = root.ref.slotBookedDate;
                        }
                        if (moment(bookingDate).format('YYYY-MM-DD') >= moment(taxEffectiveFromDate).format('YYYY-MM-DD')) {
                            if (root.ref.tax > 0) {
                                taxFee = (totalFee * root.ref.tax) / 100;
                                $("#taxIncluded").show();
                            }
                        }
                        totalFeePayable = totalFee + taxFee;

                        let balanceFee = parseFloat(totalFeePayable) - parseFloat(root.ref.onlinePaymentReceived);
                        if (parseFloat(balanceFee).toFixed(2) <= parseFloat(root.ref.onlinePaymentReceived)) {
                            root.ref.balanceFee = parseFloat(balanceFee).toFixed(2);
                        } else {
                            root.ref.balanceFee = parseFloat(root.ref.onlinePaymentReceived);
                        }

                    } else {
                        root.ref.balanceFee = 0 - parseFloat(root.ref.onlinePaymentReceived);
                    }
                }

                $('#rescheduleModal #balanceFeeVal').val(0);
                $('#balanceFeeSpan').html(' ');
                $("#rescheduleModal #balanceFee").text('$' + 0);
                if (parseFloat(root.ref.balanceFee) < 0) {
                    let balanceFeeStr = root.ref.balanceFee.toString().replace("-", "");
                    if (parseFloat(balanceFeeStr) > 0.1) {
                        $("#rescheduleModal #balanceFee").text('$' + balanceFeeStr + ' refund to client');
                        $("#rescheduleModal #balanceFee").addClass('balance-fee');
                        $('#balanceFeeSpan').html('(Refund)');
                        $('#rescheduleModal #balanceFeeVal').val(root.ref.balanceFee);
                    }
                } else {
                    if (parseFloat(root.ref.balanceFee) > 0.1) {
                        $("#rescheduleModal #balanceFee").text('$' + root.ref.balanceFee + ' to be paid by client');
                        $("#rescheduleModal #balanceFee").addClass('balance-fee');
                        $('#rescheduleModal #balanceFeeVal').val(root.ref.balanceFee);
                    }
                }
                $('.payment_received_section').show();
                $('.payment_not_received_section').show();
                $('.balance_fee_section').hide();
                $("#ids-slot-rescheduling-form  #ifRefundStatusYes").hide();

                if (parseFloat(root.ref.balanceFee) == 0) {
                    $('.payment_received_section').hide();
                    $('.payment_not_received_section').hide();
                } else if (parseFloat(root.ref.balanceFee) > 0) {
                    $('.payment_not_received_section').hide();
                    $('.balance_fee_section').hide();
                    $('#rescheduleModal .refund_status').prop('checked', false);
                } else {
                    $('.payment_received_section').hide();
                    $('.payment_not_received_section').hide();
                    $('.balance_fee_section').show();
                    if (isCandidate == 1 || isFederalBilling == 1) {
                        $(".payment_not_received_section").show();
                    }
                }
            } else {
                $('#rescheduleModal #balanceFeeVal').val(totalFeePayable);
                $('.payment_received_section').show();
            }
        },
        cancelOrDeleteEntry(isFormSubmitted, isCanceled) {

            if (isCanceled == 1) {
                swalTitle = "Are you sure to cancel this booking?";
            } else {
                swalTitle = "Are you sure to delete this booking?";
            }
            swal({
                    title: swalTitle,
                    text: "You can not undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function() {
                    // e.preventDefault();
                    var $form = $('#ds-slot-rescheduling-form');
                    var id = $('#ids-slot-rescheduling-form').find('input[name="id"]').val();
                    var url = "{{ route('idsscheduling-admin.office.slot-delete') }}";
                    var formData = new FormData();
                    formData.append("id", id);

                    if (isFormSubmitted) {
                        var cancelOrDelete = $('#cancelOrDelete').val();
                        var refundNote = $('#refundNote').val();
                        var isRefundInitiated = $('#cancel-delete-confirm-form input[name="refund_status"]:checked').val();
                        formData.append("is_canceled", cancelOrDelete);
                        formData.append("refund_note", refundNote);
                        formData.append("refund_status", isRefundInitiated);
                    } else {
                        formData.append("is_canceled", isCanceled);
                        // formData.append("refund_status",0);
                    }
                    if (isCanceled == 1) {
                        swalTitle = "Canceled";
                    } else {
                        swalTitle = "Deleted";
                    }
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        data: formData,
                        type: 'POST',
                        success: function(data) {
                            if (data.success) {
                                //Reload calendar.
                                //  root.fetchCalendarEvent();
                                //Reload selected day event.
                                // root.trigerCalendarClick();

                                //Triger calendar or schedule view.
                                trigerOnScheduleUpdate(true);

                                $('#cancel-delete-confirm-form')[0].reset();
                                $('#cancelOrDeleteConfirmModal').modal('hide');
                                $('#cancel-delete-confirm-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                                $("#cancel-delete-confirm-form  #ifRefundStatusYes").hide();
                                $('#ids-slot-rescheduling-form')[0].reset();
                                $('#rescheduleModal').modal('hide');
                                $('#ids-slot-rescheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                                swal({
                                    title: swalTitle,
                                    text: swalTitle + " successfully",
                                    type: "success",
                                    confirmButtonText: "OK",
                                }, function() {

                                });
                            } else {
                                $('#ids-slot-rescheduling-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                                // associate_errors(data.error, $form, true);
                                swal({
                                    title: "Error",
                                    text: data.message,
                                    type: "warning",
                                    confirmButtonText: "OK",
                                }, function() {

                                });
                            }
                        },
                        fail: function(response) {
                            // console.log(data);
                        },
                        error: function(xhr, textStatus, thrownError) {
                            associate_errors(xhr.responseJSON.errors, $form, true);
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        },
                        contentType: false,
                        processData: false,
                    });
                });


        },
        deferredEntryNote(){
            let root = this;
            let isCandidate = $('#ids-slot-rescheduling-form #isCandidate').val();
            let isFederalBilling = $('#ids-slot-rescheduling-form #isFederalBilling').val();
            let photoId = $('#ids-slot-rescheduling-form #passportPhotoServiceId').val();

            if(root.ref.onlinePaymentReceived == 0 && photoId > 0 && (isCandidate == 1 || isFederalBilling == 1)){
                taxFees = (root.ref.photoFee * root.ref.bookedTax) / 100;
                photoFee = parseFloat(parseFloat(root.ref.photoFee) + parseFloat(taxFees)).toFixed(3);
                let feeArray = photoFee.toString().split(".");
                if (feeArray.length == 2) {
                    let feeDecimal = feeArray[1].substr(0, 2);
                    photoFee = parseFloat(feeArray[0] + '.' + feeDecimal);
                }
                if(photoFee > 0){
                    $('#rescheduleModal #deferredEntryNote').text('Please collect passport photo fee $'+photoFee+' from the client.');
                }
            }else{
                $('#rescheduleModal #deferredEntryNote').text(' ');
            }
        }

    }

    // Code to run when the document is ready.
    $(function() {
        bookingModal.initialize();
    });
</script>
