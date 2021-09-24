<script type="text/javascript">
    $(function () {
        $('.payperiod_element, #customer_element').select2();

        @if (isset($scheduleFound) && $scheduleFound == false)
            getPayperiodList();
        @else
            var parent_div_width = document.getElementById("table_content").offsetWidth;
            $('.payperiod_element option:eq(0)').prop('selected',true).trigger('change').trigger('update');
            var screenwidth = (parent_div_width - 115);
            $("#scrollarea").css("max-width",screenwidth+"px");
        @endif
    });

    //load payperiod
    function getPayperiodList() {
        var selected_class = '';
        $.ajax({
            type: "GET",
            url: "{{route('scheduling.getPayperiodByLastAndPast')}}",
            data: {},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if(response.success) {
                    $('#payperiod_element').append('<option value="">Select Pay Period</option>');
                    var result = response.data;
                    if (result.length > 0) {
                        $.each(result, function (index, value) {
                            selected_class = '';
                            if(response.currentPayperiodId == value.id) {
                                selected_class = 'selected';
                            }
                            $('.payperiod_element').append('<option value=' + value.id + ' ' + selected_class + '>' + value.pay_period_name +' (' + value.short_name + ')</option>');
//                            $('.payperiod_element').append('<option value=' + value.id + ' ' + selected_class + '>' + value.pay_period_name + ' (' + value.start_date + ', ' + value.end_date + ')</option>');
                        });
                        $('.payperiod_element').select2();
                    }
                    $('.employee_schedule_tbl').show();
                    $('#schedule-summary').html('');
                    $('#emp-schedule-tbl').show();
                    $('.emp-schedule-with-data').hide();
                    $(".employee_schedule_tbl").attr("style", "max-width: 100%;");

                    if(response.currentPayperiodId !='') {
                        $('.payperiod_element').trigger('change');
                    }
                }
            }
        });
    }

    //payperiod on-change event
    $('.payperiod_element, #customer_element, .largerCheckbox').on('change', function () {
//        clear_schedule_table_content();
        $('.employee_schedule_tbl').hide();
        getScheduleList();
    });

    //load schedule data
    function getScheduleList() {
        var payperiod_id = [];
        var payperiods = $('.payperiod_element').select2('data');
        if(payperiods) {
            payperiods.forEach(element => {
                payperiod_id.push(element.id);
            });
        }
        var schedule_id = $('#schedule_element').val();
        var customer_id = $('.largerCheckbox').val();
        @if (isset($scheduleFound) && $scheduleFound == false)
        clear_schedule_table_content();
        if((payperiod_id == '' || payperiod_id === null || payperiod_id == undefined) && (schedule_id === '' || schedule_id == undefined)) {
            return false;
        }
        @endif

        $.ajax({
            type: "GET",
            url: "{{route('scheduling.shedule-details')}}",
            data: {'payperiod_id': payperiod_id, 'schedule_id': schedule_id, 'customer_id': customer_id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if(schedule_id === '') {
                    customer_filter = $('.largerCheckbox').val();
                    var res = customer_filter.filter( function(n) { return !this.has(n) }, new Set(customer_id));
                    if(res.length > 0) {
                        $('.schedule-widget').parent().closest(".dashboard-tables").remove();
                    }
                    let noSearchValueMissMatch = response.noSearchValueMissMatch;
                    if(!noSearchValueMissMatch) {
                        response.body == '';
                    }
                }

                $('.employee_schedule_tbl').show();
                $('.schedule_tbl_body').html(response.body);
                $('#schedule-summary').html(response.summary);
                $('.schedule_tbl_header').html(response.header);
                $(".employeeblock").html(response.employee)
                if(response.body == '') {
                    $('.approval_button_div').hide();
                    $('.emp-schedule-no-data').show();
                    $('.emp-schedule-with-data').hide();
                    clear_schedule_table_content();
                }else {
                    $('.emp-schedule-with-data').show();
                    $('.emp-schedule-no-data').hide();
                    $('.approval_button_div').show();
                }

                if(response.scheduleApprovalButton === false) {
                    $('.approval_button_div').hide();
                }
            }
        });
    }

    //clear body content
    function clear_schedule_table_content() {
        $(".employee_schedule_tbl").attr("style", "max-width: 100%;");
    }

    //approval button click modal trigger
    $('#approve_btn_element').on('click', function () {
        clear_modal_content();
        $("#approval_note_modal").modal("show");
        setTimeout(() => {
            $('#approvalModalLabel').text('Approve Schedule');
            $('.approval_note_modal_save').show();
        }, 200);
    });

    //approval click from modal
    $('#approval_note_modal_save').on('click', function () {
        var schedule_id = $('#schedule_element').val();
        var status_note = $('#schedule_note').val();

        if(status_note === '') {
            swal("Oops", "Reason note cannot be empty", "warning");
            return false;
        }

        let popup_text = "", temp_txt = "";
        $(".overlaps-block").each(function() {
            temp_txt +="<tr><td>" +$(this).attr('username')+"</td><td>" +$(this).attr('date')+"</td><td>" +$(this).attr('timing')+"</td></tr>";
        });

        if(temp_txt != "") {
            popup_text = "You won't be able to undo this action. <br /><br /><div style='color:red;overflow: scroll;max-height: 500px;max-width:500px;'><h5>Overlapping found for the following </h5><div style='padding-top:5px;'><table class='table' style='min-width:500px !important;'><thead><tr><th>Employee</th><th>Date</th><th>Timing</th></tr></thead><tbody>"+temp_txt+"</tbody></table></div></div>";
        }else{
            popup_text = "You won't be able to undo this action";
        }

        swal({
            title: "Do you want to approve?",
            html: true,
            text: popup_text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        }, function () {
            $.ajax({
                type: "POST",
                url: "{{route('scheduling.approve')}}",
                data: {'schedule_id': schedule_id, 'status_note':status_note},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    status = 'error';
                    if(response.success) {
                        $("#approval_note_modal").modal("hide");
                        status = 'success';
                        $('.schedule-status').text('Approved');
                        $('.schedule-status').addClass('reason_1');
                        $('.schedule-status').attr('title', 'Reason Notes : ' + status_note);

                        if(response.reject_approved_schedules != undefined && (response.reject_approved_schedules == "true" || response.reject_approved_schedules == true)) {
                            $(".approve-btn").hide();
                        }else{
                            $('.approval_button_div').hide();
                        }
                    }
                    swal("Approved", response.msg, status);
                }
            });
        });
    });

    //reject button click modal trigger
    $('#cancel_btn_element').on('click', function () {
        clear_modal_content();
        $("#approval_note_modal").modal("show");
        setTimeout(() => {
            $('#approvalModalLabel').text('Reject Schedule');
            $('.reject_note_modal_save').show();
        }, 200);
    });

    $('#reject_note_modal_save').on('click', function () {
        var schedule_id = $('#schedule_element').val();
        var status_note = $('#schedule_note').val();

        if(status_note === '') {
            swal("Oops", "Reason note cannot be empty", "warning");
            return false;
        }

        swal({
            title: "Do you want to reject?",
            text: "You won't be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        }, function () {
            $.ajax({
                type: "POST",
                url: "{{route('scheduling.reject')}}",
                data: {'schedule_id': schedule_id, 'status_note':status_note},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    status = 'error';
                    if(response.success) {
                        $("#approval_note_modal").modal("hide");
                        status = 'success';
                        $('.approval_button_div').hide();
                        $('.schedule-status').text('Rejected');
                        $('.schedule-status').addClass('reason_2');

                        if(response.status_note != undefined && response.status_note != "") {
                            $('.schedule-status').attr('title', 'Reason Notes : ' + response.status_note);
                        }else{
                            $('.schedule-status').attr('title', 'Reason Notes : ' + status_note);
                        }
                    }
                    swal("Rejected", response.msg, status);
                }
            });
        });
    });

    //hide modal buttons
    function clear_modal_content() {
        $('#schedule_note').val("");
        $('.approval_note_modal_save').hide();
        $('.reject_note_modal_save').hide();
    }

    $("#export_to_image").click(function() {
        var schedule_id = $('#schedule_element').val();

        //remove styles before image creation
        $('#schedule_tbl_header').css('overflow-x','visible');
        $('#schedule_tbl_body').css('overflow-x','visible');
        $('.schedule_user_header').css('padding-bottom','0px');

        var target = $('#table_content');
        var useWidth = target.prop('scrollWidth');
        var useHeight = target.prop('scrollHeight');
        html2canvas(target.get(0),{
            height: useHeight,
            width: useWidth,
        }).then(function (canvas) {
            saveAs(canvas.toDataURL(), "site_schedule.png");

            //add previously removed styles back
            $('#schedule_tbl_header').css('overflow-x','scroll');
            $('#schedule_tbl_body').css('overflow-x','hidden');
            $('.schedule_user_header').css('padding-bottom','16px');
        });
    });

    function saveAs(uri, filename) {
        var link = document.createElement('a');
        if (typeof link.download === 'string') {
          link.href = uri;
          link.download = filename;

          //Firefox requires the link to be in the body
          document.body.appendChild(link);

          //simulate click
          link.click();

          //remove the link when done
          document.body.removeChild(link);
        } else {
          window.open(uri);
        }
    }

    (function() {
      var target = $(".schedule_tbl_body");
      $(".schedule_tbl_header").scroll(function() {
        target.prop("scrollTop", this.scrollTop)
              .prop("scrollLeft", this.scrollLeft);
      });
    })();
</script>
