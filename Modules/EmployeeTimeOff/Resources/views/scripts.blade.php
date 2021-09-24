<script>

    /* Additional Attachment add and remove - Start */
    $('.add_attachment').click(function() {
        $('#upload-attachment-table').find('tbody').append('<tr class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><td class="data-list-disc attachment"><input type="file" class="form-control" name="time_off_attachment[]" required></td><td class="data-list-disc attachment-button"><a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> Remove Attachment</a></td></tr>');
        refreshSideMenu();
    });
    $('#upload-attachment-table').on('click', '.remove_attachment' ,function() {
        $(this).closest('tr').remove();
    });
    /* Additional Attachment add and remove - End */

    //Add search option in select dropdown
    $('select').select2();

    //Disable first option
    $('select').find('option[value=""]').attr("disabled", "disabled");

    /* Show/Hide other reason field - Start */
    $('#leave_reason').on('change', function(){
        var reason = $(this).val();
        if(reason == 0){
            $('#other_reason').show();
        }else{
            $('#other_reason input').val('');
            $('#other_reason').hide();
        }
    });
    /* Show/Hide other reason field - End */

    /* Show/Hide vacation pay dropdown field - Start */
    // $('#leave_reason').on('change', function(){
    //     var reason = $(this).val();
    //     if(reason == 0){
    //         $('#vacation_pay_yes').show();
    //     }else{
    //         $('#vacation_pay_yes input select').val('');
    //         $('#vacation_pay_yes').hide();
    //     }
    // });
    /* Show/Hide vacation pay dropdown field - End */

    /* Show/Hide vacation payperiod field - Start */
    $('#request_type').on('change', function(){
        var request_type = $(this).find('option:selected').text();
        refreshSideMenu();
        if(request_type == 'Vacation Request'){
            $('#vacation_request').show();
        }else{
            $('#vacation_request').find('input').val('');
            $('#vacation_request select').find('option[value=""]').prop('selected',true).trigger('change');
            $('#vacation_request').hide();
        }
    });
    /* Show/Hide vacation payperiod field - End */

    /* Show/Hide vacation payperiod field - Start */
    $('#leave_period').on('change', function(){
        var leave_period = $(this).find('option:selected').text();
        if(leave_period == 'Yes'){
            $('#vacation_pay_yes').show();
        }else{
            $('#vacation_pay_yes').find('input').val('');
            $('#vacation_pay_yes select').find('option[value=""]').prop('selected',true).trigger('change');
            $('#vacation_pay_yes').hide();
        }
    });
function findCalc(id)
{
    var url= '{{ route('absence.summaryDetails', ["id"=>":id"]) }}';
        url = url.replace(':id', id);
     $.ajax({
            url: url,
            type: "GET",
            success: function(data){
                if(data)
                {
                    html_element='<tr><td></td><td>Claimed</td><td>Approved</td><td>Rejected</td><td>Remaining</td></tr>';
                    $.each(data, function( index, timeoff ) {
                        html_element+='<tr><td>'+timeoff['type']+'</td><td>'+timeoff['days_requested']+'</td><td>'+timeoff['days_approved']+'</td><td>'+timeoff['days_rejected']+'</td><td>'+timeoff['days_remaining']+'</td>';
                        $("#timeoff-data").empty();
                        $('#timeoff-data').append(html_element);
                    });
                }
            }
        });
}

    /* Fetch user details on changing the employee dropdown - Start */
    $('#employee_no').on('change', function(){
        var id = $(this).val();
        if(id){
        var url = '{{ route("user.formattedUserDetails", ["id"=>":id"]) }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                    $('#first_name').text(data.first_name);
                    $('#last_name').text(data.last_name);
                    $('#employee_role_id').val(data.employee_role_id);
                    $('#employee_address').text(data.employee_address);
                    $('#employee_city').text(data.employee_city);
                    $('#employee_postal_code').text(data.employee_postal_code);
                    $('#phone').text(data.phone);
                    $('#employee_work_email').text(data.employee_work_email);
                    $('#current_project_wage').text(data.current_project_wage);
                    $('#employee_dob').text(data.employee_dob);
                    $('#age').text(data.age);
                    $('#employee_doj').text(data.employee_doj);
                    $('#project_number').text(data.project_number);
                    $('#client_name').text(data.client_name);
                    $('#service_length').text(data.service_length);
                    $('#employee_vet_status').text(data.employee_vet_status);
                    $('#employee_rating').text(data.employee_rating);
                    // $('#security_clearance').text(data.security_clearance);
                    // $('#valid_until').text(data.valid_until);
                    $('#position').text(data.position);
                    $('#security_clearance_div').html('');
                    //alert(JSON.stringify(data.all_security_clearance));
                    element = '';
                    if(data.all_security_clearance)
                    {
                        element = '';
                        $.each(data.all_security_clearance,function(index,value){
                            //alert(JSON.stringify(value));
                            element = '<div class="data-list-line  row security_clearance">';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance</div>';
							element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="security_clearance">';
                            element +=	value.security_clearance_lookups.security_clearance+'</div>';
							element +='</div>';
							element +='<div class="data-list-line  row security_clearance">';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance Expiry</div>';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="valid_until">';
                            element +=value.valid_until+'</div>';
							element +='</div>';
                            //alert(element);
                            $('#security_clearance_div').append(element);

                        });
                    }else{
                        element = '<div class="data-list-line  row security_clearance">';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance</div>';
							element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="security_clearance">';
                            element +=	'--</div>';
							element +='</div>';
							element +='<div class="data-list-line  row security_clearance">';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 form-control-feedback">Clearance Expiry</div>';
                            element +='<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 data-list-disc" id="valid_until">';
                            element +='--</div>';
							element +='</div>';
                            //alert(element);
                            $('#security_clearance_div').append(element);
                    }
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                swal("Oops", "Something went wrong", "error");
            },
        });
      }else{
        $( "#profile .data-list-disc").text('');
      }
    });
    /* Fetch user details on changing the employee dropdown - End */

    /* Fetch project details on changing the project dropdown - Start */
    $('#project_no').on('change', function(){
        var id = $(this).val();
        if(id){
        var url = '{{ route("customer.formattedProjectDetails", ["id"=>":id"]) }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                    $('#super_visor_id').val(data.supervisor_id);
                    $('#supervisor_name').text(data.supervisor_full_name);
                    $('#supervisor_phone').text(data.supervisor_phone);
                    $('#supervisor_email').text(data.supervisor_email);
                    $('#area_manager_id').val(data.area_manager_id);
                    $('#area_manager_name').text(data.area_manager_full_name);
                    $('#area_manager_phone').text(data.area_manager_phone);
                    $('#area_manager_email').text(data.area_manager_email);
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                swal("Oops", "Something went wrong", "error");
            },
        });
       }else{
         $( "#supervisor_id .data-list-disc").text('');
         $( "#areamanager_id .data-list-disc").text('');
         
       } 
    });
    /* Fetch project details on changing the project dropdown - End */

    /* Fetch project details on changing the request type dropdown - Start */
    $('#esa_standard').on('change', function(){
        var id = $(this).val();
        var url = '{{ route("time-off-category.single", ["id"=>":id"]) }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                    $('#overview').val(data.description);
                    $('#category_reference').val(data.reference);
                    $('#permitted_days').val(data.allowed_days);
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                swal("Oops", "Something went wrong", "error");
            },
        });
        if(id == null){
            $('#overview').val('');
        }
    });
    /* Fetch project details on changing the request type dropdown - End */

    /* Total Hours Away calculation on changing total shift and average shift - Start */
    $('#total_shifts').on('input', function(){
        var total_shifts = $(this).val();
        var average_shifts = $('#average_shifts').val();
        if( total_shifts !== '' && average_shifts !== ''){
            $('#hours_away').val(total_shifts*average_shifts);
        }else{
            $('#hours_away').val('');
        }
    });
    $('#average_shifts').on('input', function(){
        var average_shifts = $(this).val();
        var total_shifts = $('#total_shifts').val();
        if( total_shifts !== '' && average_shifts !== ''){
            $('#hours_away').val(total_shifts*average_shifts);
        }else{
            $('#hours_away').val('');
        }
    });
    /* Total Hours Away calculation on changing total shift and average shift - End */

    /* Days Rejected calculation on changing Days Requested and Days Approved By HR - Start */
    $('#requested_days').on('input', function(){
        var requested_days = $(this).val();
        var approved_days = $('#approved_days').val();
        if( requested_days !== '' && approved_days !== ''){
            $('#rejected_days').val(requested_days-approved_days);
        }else{
            $('#rejected_days').val('');
        }
    });
    $('#approved_days').on('input', function(){
        var approved_days = $(this).val();
        var requested_days = $('#requested_days').val();
        if( requested_days !== '' && approved_days !== ''){
            $('#rejected_days').val(requested_days-approved_days);
        }else{
            $('#rejected_days').val('');
        }
    });
    /* Days Rejected calculation on changing Days Requested and Days Approved By HR - End */

    /* Calculating leave days requested - Start */
    /*$('#requested_start_date').on('change', function(){
        var start_date = $(this).val();
        var end_date = $('#expected_return_date').val();
        if( start_date !== '' && end_date !== ''){
            datediff(start_date,end_date);
        }else{
            $('#requested_days').val('');
        }
        $('#approved_days').val('');
    });
    $('#expected_return_date').on('change', function(){
        var start_date = $('#requested_start_date').val();
        var end_date = $(this).val();
        if( start_date !== '' && end_date !== ''){
            datediff(start_date,end_date);
        }else{
            $('#requested_days').val('');
        }
        $('#approved_days').val('');
    });

     function datediff(start_date, end_date) {
        var start_date = new Date(start_date);
        var end_date = new Date(end_date);
        var timeDiff = Math.abs(end_date.getTime() - start_date.getTime());
        var days_difference = Math.ceil(timeDiff / (1000 * 3600 * 24));
        $('#requested_days').val(days_difference);
    }*/
    /* Calculating leave days requested - End */

    /* Employee time off Store - Start*/
        $('#time-off-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
             formData = new FormData($('#time-off-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('timeoff.store',['module' => 'employeeTimeOff']) }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        
                        if(data.result)
                        {
                            swal({
                                title: "Success",
                                text: "Employee time off request has been successfully updated",
                                type: "success"
                            }, function() {
                                window.location = "{{ route('time-off.details') }}";
                            });
                        }else{
                            swal({
                                title: "Success",
                                text: "Employee time off request has been successfully created",
                                type: "success"
                            }, function() {
                                window.location = "{{ route('time-off.details') }}";
                            });
                        }
                        
                    } else {
                        //alert(data);
                        console.log(data);
                    }
                },
                fail: function (response) {
                    //alert('here');
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
    /* Employee time off Store - End*/
</script>
