<script src="{{ asset('js/moreel.js') }}"></script>
<script>

function getCpidRow(key, create = false){
   let _value = (create == false) ? key : '';
   return `
    <tr>
        <td>
            <div class='form-group' id='cpid_allocation_${key}'>
            <input type='hidden' name='row-no[]' class='row-no' value='${_value}'>
            <select class='form-control cpid-select2' name='cpid_${key}'>
                <option value='' selected>Choose cpid</option>
                @foreach($lookups['cpidLookup'] as $id=>$cpids)
                    <option value='{{$cpids->id}}'>{{$cpids->cpid}} 
                    @php if(!empty($cpids->position)) { @endphp
                        ( {{$cpids->position->position }} )
                        @php } @endphp
                    @php if(!empty($cpids->cpidFunction)) { @endphp
                     -{{$cpids->cpidFunction->name }}
                    @php } @endphp
                    </option>
                @endforeach
            </select>
                <small class='help-block'></small>
            </div>
        </td>
    </tr>`;
  }

    $(function() {
          $.fn.dataTable.ext.errMode = 'throw';
          var custid=$('input[name="id"]').val();
          var url = '{{ route("qrcode.getAll",":id") }}';
          var url = url.replace(':id', custid);
        try{
        var table = $('#qrcode-table').DataTable({
             bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns:[0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'QR code location');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'qrcode',
                    name: 'qrcode'
                },
                   {
                    data: 'location',
                    name: 'location'
                },
                   {
                    data: 'no_of_attempts',
                    name: 'no_of_attempts'
                },
                {
                    data: 'no_of_attempts_week_ends',
                    name: 'no_of_attempts_week_ends'
                },
                {
                    data: 'tot_no_of_attempts_week_day',
                    name: 'tot_no_of_attempts_week_day'
                },
                {
                    data: 'tot_no_of_attempts_week_ends',
                    name: 'tot_no_of_attempts_week_ends'
                },

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-qid=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-qid=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
          } catch(e){
            console.log(e.stack);
        }

/* Incident Subject Tabel - Start  */
        $.fn.dataTable.ext.errMode = 'throw';
          var custid=$('input[name="id"]').val();
          var url = '{{ route("customer-incident-mapping.list",":id") }}';
          var url = url.replace(':id', custid);
        try{
            prioritytable = $('#customer-incident-table').DataTable({
             bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns:[0, 1, 2, 3, 4]
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {
                    data: 'subject_with_trashed.subject',
                    name: 'subject_with_trashed.subject'
                },
                   {
                    data: 'category_with_trashed.name',
                    name: 'category_with_trashed.name'
                },

                {data: 'incident_response_time', name: 'incident_response_time', render: function (incident_response_time) {return incident_response_time / 60 + ' Hour(s)'  }},
                {
                    data: 'incident_priority.value',
                    name: 'incident_priority.value'
                },
                {
                    data: 'sop',
                    name: 'sop'
                },


                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
          } catch(e){
            console.log(e.stack);
        }

/* Incident Subject Tabel - End  */

        $("#qrcode-table").on("click", ".edit", function (e) {
            var qid = $(this).data('qid');
            var url = '{{ route("qrcode.single",":id") }}';
            var url = url.replace(':id', qid);
            $('#myModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal').find('#qrcode_active').show();
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="qrcodeid"]').val(data.id)
                        $('#myModal input[name="customerids"]').val($('input[name="id"]').val());
                        $('#myModal input[name="qrcode"]').val(data.qrcode)
                        $('#myModal input[name="no_of_attempts"]').val(data.no_of_attempts)
                        $('#myModal input[name="no_of_attempts_week_ends"]').val(data.no_of_attempts_week_ends)
                        $('#myModal input[name="tot_no_of_attempts_week_day"]').val(data.tot_no_of_attempts_week_day)
                        $('#myModal input[name="tot_no_of_attempts_week_ends"]').val(data.tot_no_of_attempts_week_ends)
                        $('#myModal input[name="location"]').val(data.location)
                        $("#myModal #location_enabled").val(data.location_enable_disable).trigger('change');
                        $("#myModal #picture_enabled").val(data.picture_enable_disable).trigger('change');
                        $('#myModal').find('input[name="qrcode_active"]').prop('checked',data.qrcode_active);
                        $("#myModal #picture_mandatory_id").val(data.picture_mandatory).trigger('change');
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit QR code: " + data.qrcode)
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#add-qrcode').click(function(){
        $("#myModal").modal();
        $('#myModal').find('input,select').val('').change();
        $('#myModal input[name="customerids"]').val($('input[name="id"]').val());
        $('#myModal').find('.form-group').removeClass('has-error').find('.help-block').text('');

        });

            $('#qrcode-table').on('click', '.delete', function (e) {
                var id = $(this).data('qid');
                var base_url = "{{ route('qrcode.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'QR code location deleted successfully';
                deleteRecord(url, table, message);
            });

     $('#btnSubmit').on('click', function(e) {
        e.preventDefault();
        var $form = $(this).parents('form:first');;
        var qrcode = $("#qr_code").val();
        var  location = $("#locations").val();
        var  customerid =  $('#myModal input[name="customerids"]').val();
        var  qrcodeid =  $('#myModal input[name="qrcodeid"]').val();
        var location_enable_disable = $("#location_enabled").val();
        var  picture_enable_disable = $("#picture_enabled").val();
        var  picture_mandatory = $("#picture_mandatory_id").val();
        var active_status = $('#myModal input[name="qrcode_active"]').prop("checked");
        var  qrcode_active = (active_status)? 1: 0;
        var  attempts_week_day = $("#attempts_week_day").val();
        var  attempts_week_ends = $("#attempts_week_ends").val();
        var  tot_attempts_week_day = $("#tot_attempts_week_day").val();
        var  tot_attempts_week_ends = $("#tot_attempts_week_ends").val();

       $.ajax({
                  url: "{{route('qrcode.store')}}",
                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                        data: {
                        'customerid':customerid,
                        'qrcode': qrcode,
                        'location': location,
                        'no_of_attempts': attempts_week_day,
                        'no_of_attempts_week_ends': attempts_week_ends,
                        'tot_no_of_attempts_week_day': tot_attempts_week_day,
                        'tot_no_of_attempts_week_ends': tot_attempts_week_ends,
                        'location_enable_disable': location_enable_disable,
                        'picture_enable_disable': picture_enable_disable,
                        'qrcode_active': qrcode_active,
                        'picture_mandatory':picture_mandatory,
                        'qrcodeid':qrcodeid
                    },
                success: function (data) {
                    if (data.success) {
                        $('#myModal').find('#qrcode_active').hide();
                        swal("Success", "QR code saved successfully", "success");
                            $('#myModal').modal('hide');
                            table.ajax.reload();
                    } else {
                           swal("Warning", "Something went wrong", "warning");

                    }
                },
                error: function (xhr, textStatus, thrownError) {
                     associate_errors(xhr.responseJSON.errors, $form,true);
                },
            });

});


  /* Incident Mapping Tab - Start*/
        $('[aria-controls="incidentSubjectTab"], #edit-priority').on('click', function(e){
           if(this.href !=null){
               var clicked =1;
           }
          var custid=$('input[name="id"]').val();
          var base_url = "{{route('customer-incident-priority.check',':id')}}";
          var url = base_url.replace(':id', custid);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data.status);
                      if(((clicked ==1)  && (data.status == 2)) || ((clicked ==null))){
                        $('#priorityModal').modal();
                        $('#priorityModal #priority-table tr').remove('.tr-priority');
                        var  priority_html= '';
                        $.each(data.response, function(key, value) {
                            var isLastElement = data.response.length -1;
                            if(key==0){
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Less than or equal to</td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="high" onchange="updateResponseTime(this)"  min="1"  value="'+value.response_time+'"  name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;Hours</td></tr>';

                            }else if(key == isLastElement){
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Greater than</td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="low" readonly min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;Hours</td></tr>';

                            }else{
                              //  priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Less than</td><td><input type="number" class="form-control" style="display: inline;width:10%;" id="medium" onchange="updateResponseTime(this)" min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" />&nbsp;and Greater than <input type="number" class="form-control" style="display: inline;width:10%;" id="medium_range" disabled />&nbsp;</td></tr>';
                                priority_html +='<tr class="tr-priority"><td>'+value.value+'</td><td>Greater than </td><td><input type="number" class="form-control" style="display: inline;width:12%;" id="medium_range" disabled />&nbsp;Less than or equal to &nbsp;&nbsp;&nbsp;&nbsp;<input type="number" class="form-control" style="display: inline;width:12%;" id="medium" onchange="updateResponseTime(this)" min="1" value="'+value.response_time+'" name="response_time[]" /><input type="hidden" value="'+value.priority_id+'" name="priority_id[]" /><input type="hidden" value="'+value.id+'" name="id[]" /></td></tr>';

                            }

                        });
                        $('#priorityModal #priority-table').append(priority_html);
                        $('#high').trigger('onchange');
                      }
                    }
                });
        });

            /*Filters for Permanent and STC customer list in dropdown - Start*/
            $('select[name="time_sheet_approver_id"]').select2();
            $('select[name="time_sheet_approver_id"]').on('change', function(e){
            $('#time_sheet_approver_email').show();
            url = "{{route('customer.allocatteduseremail',':userid')}}";
            var userId = $(this).val();
            // userid = $('#time_sheet_entry_notification_user_id').val();
            if($(this).val()){
                url = url.replace(':userid', userId);
            }
            $.ajax({
                url:url,
                method: 'GET',
                success: function (data) {
                    $('input:text[name="time_sheet_approver_email"]').val(data.email);
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
        });
        /*Filters for Permanent and STC customer list in dropdown - End*/

        $('#incident_response_time').on('input', function(e) {
           customer_details = {!! json_encode($single_customer_details); !!};
           customer_details.customer_priority.sort( function ( a, b ) { return  a.priority.priority_order - b.priority.priority_order; } );
           if(customer_details.customer_priority.length > 0){
            var res_hr = $('#incident-mapping-form input[name="incident_response_time"]').val();
            if(res_hr > 0){
            $.each(customer_details.customer_priority, function(key, value) {
             if((res_hr * 60 ) <= value.response_time ){
                $('#priority').val(value.priority.value);
                $('#priority_id').val(value.priority.id);
                return false;
             }else{
                $('#priority').val(value.priority.value);
                $('#priority_id').val(value.priority.id);
             }
            });
            }else{
                $('#priority').val('');
            }
           }else{
                 swal({title: "Alert", text: "Please set incident priority", type: "warning"} );
           }

        });

        $('#prioritySubmit').on('click', function(e) {
        var customer_id = $('#customer-form input[name="id"]').val();
        e.preventDefault();
        var $form = $('#priority-form');
        var formData = new FormData($('#priority-form')[0]);
        formData.append('customer_id', customer_id);
        if(/\D/.test($('#high').val()) || /\D/.test($('#medium').val())){
            swal("Warning","Please add a valid response time","warning");
        }else if($('#high').val() =='' || $('#low').val() =='' || $('#medium').val() =='' ){
            swal("Warning", "Please add response time", "warning");
        }else if($('#medium').val() <= $('#high').val()){
            swal("Warning", "Medium priority reponse time should be greater than high priority reponse time", "warning");
        }else{
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('customer-incident-priority.store') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident priority has been saved", type: "success"},
                         function () {
                             location.reload();
                         }
                     );
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });
        }
       });


       $('#incidentSubjectMapping').on('click', function(e) {
        var id = $('#customer-form input[name="sid"]').val();
        var customer_id = $('#customer-form input[name="id"]').val();
        e.preventDefault();
        var $form = $('#incident-mapping-form');
        var formData = new FormData($('#incident-mapping-form')[0]);
        formData.append('id', id);
        formData.append('customer_id', customer_id);
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('customer-incident-mapping.store') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident subject has been saved", type: "success"});
                     $('#incidentPriorityModal').modal('hide');
                     prioritytable.ajax.reload();
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });

       });


    });


    $('#add-incident-subject').click(function(){
        $("#incidentPriorityModal").modal();
        $('#incidentPriorityModal input[name="sid"]').val('');
        $('#incidentPriorityModal').find('input,select').val('').change();
        $('#incidentPriorityModal textarea[name="sop"]').val('');
        $('.select2').select2();
        $('#incidentPriorityModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
    });

/*    $('#edit-priority').click(function(){
        $("#priorityModal").modal();
        $('#priorityModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
    }); */
      $('#incident-recipient').on('click', function(e){
          var custid=$('#customer-form input[name="id"]').val();
           var base_url = "{{route('customer-incident-recipient.list',':id')}}";
           var url = base_url.replace(':id', custid);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                         let divParam = {
                           containerDiv: '#dynamic-rows',
                           addButton: '.add_button',
                           form: '#recipient-form',
                         };
                           let moreSteps = new MoreEl('step', divParam);
                           if(data.data.length!=0){
                           moreSteps.initElDiv(true);
                            }
                            else
                            {
                            moreSteps.initElDiv();
                            }
                            $("#recepientModal").modal();
                            for(let i = 0; i < data.data.length; i++) {
                                console.log(data)
                            let emailSelector = 'input[name="email['+i+']"]';
                            let highSelector = 'input[name="high['+i+']"]';
                            let mediumSelector = 'input[name="medium['+i+']"]';
                            let lowSelector = 'input[name="low['+i+']"]';
                            let amendmentSelector = 'input[name="amendment['+i+']"]';
                                let email = data.data[i].email;
                                let high = data.data[i].High?true:false;
                                let low = data.data[i].Low?true:false;
                                let medium = data.data[i].Medium?true:false;
                                let amendment_notification = data.data[i].amendment_notification?true:false;
                                let newSteps = moreSteps.addRow();
                                $(newSteps).find(emailSelector).val(email);
                                $(newSteps).find(highSelector).prop('checked', high);
                                $(newSteps).find(mediumSelector).prop('checked', medium);
                                $(newSteps).find(lowSelector).prop('checked', low);
                                $(newSteps).find(amendmentSelector).prop('checked', amendment_notification);
                            }


                      }
                });
        });
        $('#recipientSubmit').on('click', function(e) {
        var customer_id = $('#customer-form input[name="id"]').val();
        e.preventDefault();
        var $form = $('#recipient-form');
        var formData = new FormData($('#recipient-form')[0]);
        formData.append('customer_id', customer_id);
         $('.form-group').removeClass('has-error').find('.help-block').text('');
         url = "{{ route('customer-incident-recipient.store') }}";
         $.ajax({
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                 'accept': 'application/json',
             },
             url: url,
             type: 'POST',
             data: formData,
             success: function (data) {
                 if (data.success) {
                     swal({title: "Saved", text: "Customer incident recipient has been saved", type: "success"},
                         function () {
                             location.reload();
                         }
                     );
                 } else {
                     console.log(data.success);
                 }
             },
             fail: function (response) {
                console.log(response);
             },
             error: function (xhr, textStatus, thrownError) {
                 associate_errors(xhr.responseJSON.errors, $form, true);
             },
             contentType: false,
             processData: false,
         });
       });


    $('#incident-recipient').click(function(){
        $("#recepientModal").modal();
        $('#recepientModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
    });

    $("#customer-incident-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            console.log(id);
            var url = '{{ route("customer-incident-mapping.single",":id") }}';
            var url = url.replace(':id', id);
            $('#incidentPriorityModal').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#incidentPriorityModal input[name="sid"]').val(data.id)
                        $('#incidentPriorityModal input[name="priority_id"]').val(data.priority_id)
                        $('#incidentPriorityModal select[name="subject_id"]').val(data.subject_id);
                        $('#incidentPriorityModal select[name="category_id"]').val(data.category_id);
                        $('#incidentPriorityModal input[name="incident_response_time"]').val(data.incident_response_time/60)
                        $('#incidentPriorityModal input[name="priority"]').val(data.incident_priority.value)
                        $('#incidentPriorityModal textarea[name="sop"]').val(data.sop)
                        $("#incidentPriorityModal").modal();
                        $('.select2').select2();
                        $('#incidentPriorityModal .modal-title').text("Edit Incident Subject")
                    } else {

                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#customer-incident-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('customer-incident-mapping.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Incident Subject deleted successfully';
                deleteRecord(url, prioritytable, message);
            });

     /*Incident Mapping Tab - End*/

    $(function() {
        $('#incident_reset_btn').on('click', function(e) {
            e.preventDefault();
            let customerId = $('input[name="id"]').val();
            swal({
                title: 'Are you sure?',
                text: "It will be permanently deleted",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }, function() {
                $.ajax({
                    url: "{{ route('customer.reset_incident_logo') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'customer_id': customerId
                    },
                    success: function(data) {
                        $('#incident_report_logo_el').val('');
                        if (data.success) {
                            $('#incident-logo-section').hide();
                            swal('Deleted', 'Your file has been deleted.', 'success');
                        } else {
                            // swal("Oops", "Something went wrong", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        // swal("Oops", "Something went wrong", "warning");
                    },
                });

            });

        });

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#customer-table').DataTable();
        } catch (e) {
            console.log(e.stack);
        }

        function hasValidIncidentLogo() {
            let fileEl = $('#incident_report_logo_el');
            let file = fileEl[0].files[0];
            if (!file) {
                return true; //allow empty logo
            }
            //check valid image
            if (!file.type.match('image.*')) {
                return false;
            }
            //todo:check file dimensions
            return true;
        }

         /*Filters for Permanent and STC customer - Start*/
         $('#filter').on('change', 'input[name=customer-contract-type]', function() {
            var customer_type = $('input[name="customer-contract-type"]:checked').val();
            var customer_status = $('input[name="customer-status"]:checked').val();
            var url = "{{ route('customer.list',[':customer_type',':customer_status']) }}";
            url = url.replace(':customer_type', customer_type);
            url = url.replace(':customer_status', customer_status);
            table.ajax.url(url).load();
        });

        $('#filterActive').on('change', 'input[name=customer-status]', function() {
            var customer_type = $('input[name="customer-contract-type"]:checked').val();
            var customer_status = $('input[name="customer-status"]:checked').val();
            var url = "{{ route('customer.list',[':customer_type',':customer_status']) }}";
            url = url.replace(':customer_type', customer_type);
            url = url.replace(':customer_status', customer_status);
            table.ajax.url(url).load();
        });


        /*Filters for Permanent and STC customer - End*/

        function submitCustomerForm(table,e,message){
            if($('input[name="is_nmso_account"]').is(':checked') && ($('select[name="security_clearance_lookup_id"]').val() == null || $('select[name="security_clearance_lookup_id"]').val() == '')) {
                $('#security_clearance_lookup_id').addClass('has-error');
                return false;
            }

            formSubmit($('#customer-form'), "{{ route('customer.store') }}", table, e, message).then(function(data){
             let pane = $('#customer-form .has-error:first').closest('.tab-pane');
             if(pane.length > 0){
                 let paneId = $(pane).attr('id');
                 let targetLink = $('a[href="#'+paneId+'"]');

                 if(targetLink.length >0){
                    targetLink.trigger('click');
                 }
             }
             if(data.success){
                window.location.href = "{{route('customer')}}";
             }
            });
        }
        /* Customer Store - Start*/
        $('#customer-form').submit(function(e) {
            e.preventDefault();
            if ($('#customer-form input[name="id"]').val()) {
                var message = 'Customer has been updated successfully';

            } else {

                var message = 'Customer has been created successfully';
            }
            $('#customer-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            if (!hasValidIncidentLogo()) {
                swal("Warning", "Invalid Incident logo", "warning");
                return false;
            }
            var title = $("#title").val();
            if(title=="")
            {
                submitCustomerForm(table, e, message);
            }
            else{

                submitCustomerForm(table, e, message);

            }

        });
        /* Customer Store - End*/

        $(function() {
            $("input[id*='radiusfence']").keydown(function(event) {


                if (event.shiftKey == true) {
                    event.preventDefault();
                }

                if ((event.keyCode >= 48 && event.keyCode <= 57) ||
                    (event.keyCode >= 96 && event.keyCode <= 105) ||
                    event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                    event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

                } else {
                    event.preventDefault();
                }

                if ($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                    event.preventDefault();
                //if a decimal has been added, disable the "."-button

            });
        });
        $('input[name="mobile_security_patrol_site"]').on("click", function(event) {
            if (this.checked == true) {
                $("#geo_fence_satellite").show();
                //$("#geo_fence_satellite").prop("checked", true);
            } else {
                $("#geo_fence_satellite").hide();
                $("#geo_fence_satellite").prop("checked", false);
            }
        });

        $('#motion_sensor_incident_subject_id').select2();
        $('input[name="motion_sensor_enabled"]').on("click", function(event) {
            if (this.checked == true) {
                $("#motion_sensor_incident_subject").show();
                //$("#geo_fence_satellite").prop("checked", true);
            } else {
                $("#motion_sensor_incident_subject").hide();
                $("#motion_sensor_incident_subject_id").val(null).trigger('change');
            }
        });

        $('.nav-tabs').on('click', function(e){
            if(e.target.getAttribute("href") === '#landingPage') {
                $('#landingPage').css('display','block');
            }else {
                $('#landingPage').css('display','none');
            }
        });

        /* Customer Edit - Start*/
        $("#customer-table").on("click", ".edit", function(e) {

            var id = $(this).data('id');
            var base_url = "{{route('customer.edit',':id')}}";
            var url = base_url.replace(':id', id);
            window.location = url;

        });
        /* Customer Edit - End*/



        $('input[name="mobile_security_patrol_site"]').on("click", function(event) {
            //alert("Here");
        })

        /* Get region data */
        $('select[name="region_lookup_id"]').on('change', function() {
            var id = $(this).val();
            $('.region-description').val("");
            if ($.isNumeric(id)) {
                var base_url = "{{route('region.single',':id')}}";
                var url = base_url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('.region-description').val(data.region_description);
                    }
                });
            }
        });
        /* Get region data */

        /* Customer Delete - Start*/
        $('#customer-table').on('click', '.delete', function(e) {
            var id = $(this).data('id');
            var base_url = "{{route('customer.destroy',':id')}}"
            var url = base_url.replace(':id', id);
            var message = 'Customer has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Customer Delete - End*/

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function() {

            $('#interval_check').hide();
            $('#guard_tour_duration').hide();
            $('input:text[name="billing_address"]').prop('readonly', false);
        });



        /*Hide alert -start*/
        /*$("#import-success-alert").fadeTo(2000, 500).slideUp(500, function(){
        $("#import-success-alert").slideUp(500);
        });*/
        /*Hide alert -end*/

        /* Show/Hide fields - Start */
        // $('#shift_journal').find('input').change(function() {
        //     if($(this).is(":checked")) {
        //         $('#time_shift_enabled').show();
        //     }else{
        //         $('#time_shift_enabled').hide();
        //         $('#time_shift_enabled').find('input').prop('checked',false);
        //     }
        // });
        $('#guard_tour').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#interval_check').show();
            } else {
                $('#interval_check').hide();
                $('#guard_tour_duration').hide();
                $('#interval_check').find('input').prop('checked', false);
                $('#duration').val('');
            }
        });
        $('#interval_check').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('input[name="guard_tour_enabled"]').prop("checked", true);
                $('#guard_tour').show();
                $('#guard_tour_duration').show();
            } else {
                $('#guard_tour_duration').hide();
                $('#guard_tour_duration').find('input').prop('checked', false);
                $('#duration').val('');
            }
        });
        $('#overstay_enabled').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#overstay_time').show();

            } else {
                $('#overstay_time').hide();
                $('#timepicker').val('');

            }
        });

        $('#employee_rating_response').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#employee_rating_response_time').show();

            } else {
                $('#employee_rating_response_time').hide();
                $('#timepicker').val('');

            }
        });
         $('#qr_patrol_enabled').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#qr_picture_limit').show();
                $('#qr_interval_check').show();
                $('#qr_daily_activity_report').show();


            } else {
                $('#qr_picture_limit').hide();
                $('#qr_interval_check').hide();
                $('#qr_daily_activity_report').hide();
                $('#qr_duration').hide();
                 $('#pic_limit').val('');
                $('#qrduration').val('');
                 $('#qr_interval_check').find('input').prop('checked', false);

            }
        });
          $('#qr_interval_check').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#qr_duration').show();

            } else {
                $('#qr_duration').hide();
                $('#qrduration').val('');

            }
        });
        $('#qr_daily_activity_report').find('input').change(function() {
            if ($(this).is(":checked")) {
                $('#qr_recipient_email').show();

            } else {
                $('#qr_recipient_email').hide();
                $('#qr_recipient_email').val('');

            }
        });
        $('#key_management_enabled').find('input').change(function() {
            if ($(this).is(":checked")) {

                $('#key_management_signature').show();
                $('#key_management_image_id').show();

            } else {
                $('#key_management_signature').hide();
                $('#key_management_image_id').hide();
            }
        });
        /* Show/Hide fields - End */

        /*Clear MapContainer previous data - Start*/
        $("#modal_cancel").click(function() {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });

        $("#modal-close").click(function() {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });
        /*Clear MapContainer previous data - End*/


        /* Map Location click&Submit - Start */

        $('#latlong_submit').on('click', function(e) {
            id = $("#rowid").val();
            lat = $("#lat").val();
            long = $("#long").val();
            radius = $("#radius").val();
            if (radius != 0 && radius < 150) {
                swal("", "The radius should be either 0 or greater than or equal to 150mtrs", "warning");
            } else {
                $.ajax({
                    url: "{{route('customer.updateLatLong')}}",
                    type: 'POST',
                    data: {
                        'id': id,
                        'lat': lat,
                        'long': long,
                        'radius': radius
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            swal("Success", "The geo location details updated successfully", "success");
                            //$('#message').html(data.payload);
                            $('#mapModal').modal('hide');
                            $("#MapContainer").html("");
                            table.ajax.reload();
                        } else {
                            //alert(data);

                            swal("Oops", "The geo location details updation was unsuccessful", "warning");
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            }
        });
        var markers = [];
        /* Function for add/edit fence - Start */
        function initializefence(myCenter, radius, jqdata) {
            var renderContainer = document.getElementById("mapfencelocation");
            var mapProp = {
                center: myCenter,
                gestureHandling: 'greedy',
                zoom: 7
            };

            var input = document.getElementById('addressfence');
            //new google.maps.places.Autocomplete(input);

            var map = new google.maps.Map(renderContainer, mapProp, {
                gestureHandling: 'greedy',
            });


            //Marker in the Map
            if (jqdata == null) {
                var marker = new google.maps.Marker({
                    position: myCenter,
                    draggable: true,
                    //animation: google.maps.Animation.DROP,
                });
                //marker.setMap(map);
            }


            //Circle in the Map
            var circle = new google.maps.Circle({
                center: myCenter,
                map: map,
                radius: 1000, // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 1,
                draggable: true,
                editable: true
            });
            //circle.setMap(map);



            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function() {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radiusfence").val()));

            });

            //Add listner to change latlong value on dragging the marker

            var bounds = new google.maps.LatLngBounds();


            var collection = [];
            var i = 0;
            $.each(jqdata, function(key, value) {
                i++;
                var id = value[0];
                var title = value[1];
                var address = value[2];
                var lat = value[3];
                var long = value[4];
                var rad = parseInt(value[5]);
                var visitcount = parseInt(value[6]);
                var contractualVisits = parseInt(value[8]);
                //Circle in the Map
                var dycenter = new google.maps.LatLng(lat, long);

                var dyncircle = new google.maps.Circle({
                    center: dycenter,
                    map: map,
                    radius: rad, // IN METERS.
                    fillColor: '#FF6600',
                    fillOpacity: 0.3,
                    strokeColor: "#FFF",
                    strokeWeight: 1,
                    draggable: true,
                    editable: true
                });
                if (i == 1) {
                    dyncircle.setMap(map);
                }


                var dynmarker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, long),
                    map: map,
                    draggable: true,
                    raiseOnDrag: true,
                    radius: rad,
                    fillColor: '#FF6600',
                    label: {
                        color: 'black',
                        fontWeight: 'normal',
                        fontSize: '10',
                        text: title,
                    },
                });
                dynmarker.id = id;
                dynmarker.setMap(map);
                if (i == 1) {
                    map.setCenter(new google.maps.LatLng(lat, long));
                    //dynmarker.setCenter(new google.maps.LatLng(lat, long));
                }
                bounds.extend(dynmarker.position);
                dynmarker.addListener('dragend', function(event) {
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    $('#latfence').val(event.latLng.lat());
                    $('#longfence').val(event.latLng.lng());
                    $('#contractual_visit').val(contractualVisits);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
                    {
                    $('#radiusfence').val(rad);
                    }
                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                });

                dynmarker.addListener('click', function(event) {
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    // $('#radiusfence').val(radiusfence);
                    $('#latfence').val(event.latLng.lat());
                    $('#longfence').val(event.latLng.lng());
                    // if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    // {

                    // }
                    // else
                    // {
                    // $('#radiusfence').val(rad);
                    // }
                    $('#radiusfence').val(rad);
                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                    $('#contractual_visit').val(contractualVisits);
                });

                //Add event listner on drag event of marker
                dynmarker.addListener('drag', function(event) {
                    dyncircle.setOptions({
                        center: {
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        }
                    });
                });
                markers.push(dynmarker);

                //Add listner to change radius value on field
                dyncircle.addListener('radius_changed', function(event) {
                    $('#radiusfence').val(dyncircle.getRadius());
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
                    {
                        $('#latfence').val(lat);
                        $('#longfence').val(long);
                    }
                    //

                    $('#radiusfence').val(dyncircle.getRadius());

                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                });
                //Add event listner on drag event of circle
                dyncircle.addListener('drag', function(event) {

                    dynmarker.setOptions({
                        position: {
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        }
                    });
                    $('#title').val(title);
                    $('#addressfence').val(address);
                    if($("#whichfence").val()>0 && $('#latfence').val()!="")
                    {

                    }
                    else
                    {
                        $('#latfence').val(lat);
                        $('#longfence').val(long);
                    }
                    $('#radiusfence').val(dyncircle.getRadius());
                    $('#visitsfence').val(visitcount);
                    $("#booleanedit").val("1");
                    $("#whichfence").val(id);
                    $("#addnewfence").val(id);
                });
                //Add event listner on drag event of circle


                //changing the radius of circle on changing the numeric field value
                $("#radius").on("change paste keyup keydown", function() {
                    //radius = $("#radius").val();
                    dyncircle.setRadius(Number($("#radiusfence").val()));

                });

            });
        // map.fitBounds(bounds);




        }

        /* Function for add/edit fence - Start */
        function initialize(myCenter, radius) {
            var renderContainer = document.getElementById("MapContainer");
            var mapProp = {
                center: myCenter,
                zoom: 8
            };
            var map = new google.maps.Map(renderContainer, mapProp, {
                gestureHandling: 'greedy',
            });

            //Marker in the Map
            var marker = new google.maps.Marker({
                position: myCenter,
                draggable: true,
                //animation: google.maps.Animation.DROP,
            });
            marker.setMap(map);

            //Circle in the Map
            var circle = new google.maps.Circle({
                center: myCenter,
                map: map,
                radius: radius, // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 1,
                //draggable: true,
                editable: true
            });
            circle.setMap(map);

            //Add listner to change latlong value on dragging the marker
            marker.addListener('dragend', function(event) {
                $('#lat').val(event.latLng.lat());
                $('#long').val(event.latLng.lng());
            });

            //Add event listner on drag event of marker
            marker.addListener('drag', function(event) {
                circle.setOptions({
                    center: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    }
                });
            });

            //Add listner to change radius value on field
            circle.addListener('radius_changed', function() {
                $('#radius').val(circle.getRadius());
            });

            //Add event listner on drag event of circle
            circle.addListener('drag', function(event) {
                marker.setOptions({
                    position: {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng()
                    }
                });
            });

            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function() {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radius").val()));

            });

        }
        /* Function for add/edit fence - End */

        $('#add-fence').on('click', function(e) {
            $('#add-fence').hide();
            $('.radius').show();
        });

        /* Function for concatinating address and populate in billing address if checkbox is checked - Start */
        $("#check_same_address").click(function() {
            if ($("input[name=address]").val().length <= 0 || $("input[name=city]").val().length <= 0 || $("input[name=province]").val().length <= 0 || $("input[name=postal_code]").val().length <= 0) {
                swal("Warning", "Please enter address details", "warning");
                $(this).prop('checked', false);
            }
            if (this.checked) {
                var address = '';
                var city = '';
                var province = '';
                var postal_code = '';
                if ($("input[name=address]").val().length > 0)
                    var address = $("input[name=address]").val() + ', ';
                if ($("input[name=city]").val().length > 0)
                    var city = $("input[name=city]").val() + ', ';
                if ($("input[name=province]").val().length > 0)
                    var province = $("input[name=province]").val() + ', ';
                var postal_code = $("input[name=postal_code]").val();
                var full_addr = address + city + province + postal_code;
                $('input:text[name="billing_address"]').val(full_addr);
                $('input:text[name="billing_address"]').prop('readonly', true);
            } else {
                $('input:text[name="billing_address"]').val('');
                $('input:text[name="billing_address"]').prop('readonly', false);
            }
        });
        /* Function for concatinating address and populate in billing address if checkbox is checked - End */

        $('#customer-table').on('click', '.map_location', function(e) {
            $('#mapModal').off('shown.bs.modal');
            id = $(this).data('id');
            postal_code = $(this).data('postal_code');
            url = 'https://maps.google.com/maps/api/geocode/json?address=' + postal_code + '&sensor=false&key={{config('
            globals.google_api_key ')}}';
            if (($(this).data('lat') == '' || $(this).data('lat') == null) || ($(this).data('long') == '' || $(this).data('long') == null)) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    crossDomain: true,
                    dataType: 'json',
                    success: function(data) {
                        if (data.results.length > 0) {
                            lat = data.results[0].geometry.location.lat;
                            long = data.results[0].geometry.location.lng;
                            $('#lat').val(lat);
                            $('#long').val(long);
                        } else {
                            lat = {{config('globals.map_default_center_lat')}};
                            long = {{config('globals.map_default_center_lng')}};
                            $('#lat').val(lat);
                            $('#long').val(long);
                        }
                        setFence($(this));
                    },
                    error: function(xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            } else {
                lat = $(this).data('lat');
                long = $(this).data('long');
                $('#lat').val(lat);
                $('#long').val(long);
                setFence($(this));
            }

            function setFence(curr_obj) {
                radius = Number((curr_obj.data('radius') != '' && curr_obj.data('radius') != null) ? curr_obj.data('radius') : 0);
                $('#rowid').val(id);
                $('#radius').val(radius);
                $('.radius').show();
                if (radius == 0) {
                    $('.radius').hide();
                    $('#add-fence').show();
                }
                $('#mapModal').modal();
                $('#mapModal').on('shown.bs.modal', function(e) {
                    initialize(new google.maps.LatLng(lat, long), radius);
                });
            }
        });

        /* Prepopulating employee details on choosing select2 - Start */
        $('#requester_id').on('change', function() {
            if ($(this).val() == '') {
                $('input:text[name="requester_position"]').val('');
                $('input:text[name="requester_empno"]').val('');
            }
            var url = '{{ route("user.formattedUserDetails", ["id" => ":user_id"]) }}';
            url = url.replace(':user_id', $(this).val());
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    $('input:text[name="requester_position"]').val(data.position).prop('readonly', 'true');
                    $('input:text[name="requester_empno"]').val(data.employee_no).prop('readonly', 'true');
                },
                error: function(xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });

        });
        /* Prepopulating employee details on choosing select2 - End */


        /* Map Location click&Submit - End */

        /* CPID Allocation - Add - Start */

        $('#remove-cpid-allocation').hide();
        $("#add-cpid-allocation").on("click", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }

            var customer_cpid_allocation_new_row = getCpidRow($next_row_no,true);
                
            $(".customer-cpid-allocation-table tbody").append(customer_cpid_allocation_new_row);
            $('.cpid-select2').select2({ width: '40%' });
            $(".customer-cpid-allocation-table").find('tr:last').find('.row-no').val($next_row_no);

            $("#valid_until_" + $next_row_no + ">input").datepicker({
                format: "yyyy-mm-dd",
                maxDate: "+900y"
            });

            $(".datepicker").mask("9999-99-99");

            if ($last_row_no > 0 || $last_row_no == undefined) {
                $('#remove-cpid-allocation').show();
            }
        });
        /* CPID Allocation - Add - End */
        /* CPID Allocation - Remove - Start */
        $("#remove-cpid-allocation").on("click", function(e) {
            $last_row_no = $(".customer-cpid-allocation-table").find('tr:last .row-no').val();
            if ($last_row_no > -1) {
                $(".customer-cpid-allocation-table").find('tr:last').remove();
                if ($last_row_no == 0) {
                    $('#remove-cpid-allocation').hide();
                }
            } else {
                $('#remove-cpid-allocation').hide();
            }
        });
        /*CPID Allocation - Remove - End */

        var rendermap = function(customer_id) {

            $.ajax({
                type: "get",
                url: "{{route('customer.fencelistarray')}}",
                data: {
                    "customer_id": customer_id
                },
                success: function(response) {

                }
            }).done(function(data) {

                var latitiude = 43.93667009577818;
                var longitude = -79.65423151957401;
                var radius = 1000;
                $("#latfence").val(latitiude);
                $("#longfence").val(longitude);
                $("#radiusfence").val(radius);
                var jqdata = jQuery.parseJSON(data);

                initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
            });
        }




        $("#fencebuttonreset").on("click", function(event) {
            $("#title").val("");
            $("#addressfence").val("");
            $("#booleanedit").val("0");
            $("#whichfence").val("0");
            $("#visitsfence").val("0");
            $("#radiusfence").val("");

            $("#addnewfence").val("-1");
            $("#contractual_visit").val("0");
        });

        $("#cancel_customer_modal").on("click", function(event) {
            $("#title").val("");
            $("#addressfence").val("");
            $("#booleanedit").val("0");
            $("#whichfence").val("0");
            $("#visitsfence").val("0");
            $("#radiusfence").val("");

            $("#addnewfence").val("-1");
            $("#contractual_visit").val("0");
        });

        /*Add new fence*/
        $('select[name="industry_sector_lookup_id"]').select2({ width: '100%' });
        $('select[name="region_lookup_id"]').select2({ width: '100%' });



        var placeSearch, autocomplete;

        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name'
        };

        var blurorfocusout = function(event) {
            var latfence = $("#latfence").val();
            if (latfence == "") {
                swal("Please enter a valid address");
            }
        }
        $("#addressfence").on('blur', function(event) {
            blurorfocusout(event);
        });

        $("#addressfence").on('focusout', function(event) {
            blurorfocusout(event);
        });

        var renderFences = function(customer_id) {
            $.ajax({
                type: "get",
                url: "{{route('customer.fencelist')}}",
                data: {
                    "customerid": customer_id
                },
                success: function(response) {
                    $("#fencerowsedit").html(response);
                }
            }).done(function(response) {
                var latitiude = 43.93667009577818;
                var longitude = -79.65423151957401;
                var radius = 1000;

                $.ajax({
                    type: "get",
                    url: "{{route('customer.fencelistarray')}}",
                    data: {
                        "customer_id": customer_id
                    },
                    success: function(response) {
                        var jqdata = jQuery.parseJSON(response);
                        initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                    }
                });
                $('.disablefences').on("click", function(event) {
                    var fenceid = $(this).attr("attr-fencerowid");
                    var process = $(this).attr("attr-process");
                    swal({
                            title: "Are you sure?",
                            text: "You will be disabling an active fence",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'Yes, I am sure',
                            cancelButtonText: "No, cancel it",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "post",
                                    url: "{{route('customer.disablefence')}}",
                                    data: {
                                        "fenceid": fenceid,
                                        "process": process
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        swal("Updated", "Successfully updated", "success");
                                    }
                                }).done(function(ev) {
                                    renderFences(customer_id);
                                });


                            } else {
                                e.preventDefault();
                            }
                        });

                });

                $(".savedfences").on("click", function($event) {
                    var fenceid = $(this).attr("attr-fencerowid");
                    let customerId = $('input[name="id"]').val();
                    swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this fence",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'Yes, I am sure',
                            cancelButtonText: "No, cancel it",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm) {

                            if (isConfirm) {
                                $.ajax({
                                    type: "get",
                                    url: "{{route('customer.removefencelist')}}",
                                    data: {
                                        "fenceid": fenceid
                                    },
                                    success: function(response) {
                                        // $('a[data-id="'+id+'"]').get(0).click();
                                        if (response == "Deleted") {
                                            $("#fencerow-" + fenceid).remove();
                                            renderFences(customerId);
                                            swal("Removed", "Successfully updated", "success");
                                        }
                                    }
                                });


                            } else {
                                e.preventDefault();
                            }
                        });

                });

                $(".editfences").on("click", function($event) {

                    var fenceid = $(this).attr("attr-fencerowid");
                    var fencename = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencename");
                    var fenceaddress = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencedesc");
                    var fencevisitcount = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-visitcount");
                    var latitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-latitiude");
                    var longitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-longitude");
                    var radius = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-radiusfence");
                    var contractualVisit = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-contractractual");

                    $('#title').val(fencename);
                    $("#addressfence").val(fenceaddress);
                    $("#visitsfence").val(fencevisitcount);
                    $('#latfence').val(latitude);
                    $('#longfence').val(longitude);
                    $('#radiusfence').val(radius);
                    $('#contractual_visit').val(contractualVisit);

                    $("#booleanedit").val("1");
                    $("#whichfence").val(fenceid);
                });
                $("#title").val("");
                $("#booleanedit").val("0");
                $("#whichfence").val("0");


            })
        }
        var removeMarkers = function(fenceid) {

            for (var i = 0; i < markers.length; i++) {
                console.log(markers.length);
                if (markers[i].id == fenceid) {
                    //Remove the marker from Map
                    markers[i].setMap(null);

                    //Remove the marker from array.
                    markers.splice(i, 1);
                    // map.removeMarker(map.markers[i]);
                    return;
                }
            }
            //addMapmarker(map);
        }
        var addMapmarker = function(map) {
            var id = $("input[name=id]").val();
            $.ajax({
                type: "get",
                url: "{{route('customer.fencelistarray')}}",
                data: {
                    "customer_id": id
                },
                success: function(response) {
                    var jqdata = jQuery.parseJSON(response);

                    jqdata.forEach(element => {
                        var id = element[0];
                        var title = element[1];
                        var lat = element[2];
                        var long = element[3];

                        var radius = element[4];
                        console.log(map);
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, long),
                            map: map,
                            draggable: true,
                            raiseOnDrag: true,
                        });

                    });
                }
            })
        }

        /* Display single row on adding cpid - Start */
        $('document').ready(function() {
        if($('input[name="id"]').val() == null){
            if ($('#person_data[document_type]').val() == ''){}
            $(".nav-tabs li:not(:first-child)").removeClass('active')
            $(".tab-content div.tab-pane:not(:first-child)").removeClass('active')
            $('.landingPageTab').css('display','none');
            $('#landingPage').css('display','none');
            $('.qrcodeTab').css('display','none');
            $('.incidentSubjectTab').css('display','none');
            $('#landingPage').css('display','none');
            $('#user_tab').addClass('active show');
            $('#userTab').addClass('active');
            $('[href="#cpidTab"]').closest('li').show();
            $('#remove-cpid-allocation').show();
            $('#customer-cpid-allocation tbody').find('tr').remove();
            $('#customer-qrcode-location').find('li').remove();
             $("#subjects").val(null).trigger('change')
            var latitiude = 43.93667009577818;
            var longitude = -79.65423151957401;
            var radius = 1000;
            $('fencerowsedit').css('display', 'none');
            $("#latfence").val(latitiude);
            $("#longfence").val(longitude);
            $("#radiusfence").val(radius);
            jqdata = [];
            initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);

            $('#fencenew').css('display', 'block');
            //$('.fenceinputs').hide();

            $customer_cpid_first_row = getCpidRow(0)
            $('#customer-cpid-allocation tbody').append($customer_cpid_first_row);
            $('.cpid-select2').select2({ width: '40%' });
           /* $customerQrcodeFirstRow = "<li value='0'>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_0'>" +
                "<input type='text' name='qr-location-row[]' class='row-no' value='0'>" +
                "<label for='qrcode_0' class='control-label'>QR Code<span class='mandatory'>*</span></label>" +
                "<input class='form-control qrcode' placeholder='QR Code' name='qrcode_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_0'>" +
                "<label for='location_0' class='control-label'>Checkpoint<span class='mandatory'>*</span></label>" +
                "<input class='form-control location' placeholder='Checkpoint' name='location_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_0'>" +
                "<label for='no_of_attempts_0' class='control-label'>Number of Attempts <span class='mandatory'>*</span></label>" +
                "<input class='form-control no_of_attempts_week_day' placeholder='Number of Attempts' name='no_of_attempts_0' type='text'>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_enable_disable_0'>" +
                "<label for='location_enable_disable_0' class='control-label'>Enable/Disable Location<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='location_enable_disable_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +
                "<div class='row'>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable_0'>" +
                "<label for='picture_enable_disable_0' class='control-label'>Enable/Disable Picture<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_enable_disable_0' onchange='getval(this,0);'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Enable</option>" +
                "<option value='0'>Disable</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_active_0'>" +
                "<label for='qrcode_active_0' class='control-label'>Active<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='qrcode_active_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Activate</option>" +
                "<option value='0'>De-activate</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</div>" +

                "<div class='form-group' id='picture_mandatory_0' style=display:none>" +
                "<label for='picture_mandatory_0' class='col-lg-4 control-label'>Picture Mandatory<span class='mandatory'>*</span></label>" +
                "<select class='form-control' name='picture_mandatory_0' id='picture_mandatory_id_0'>" +
                "<option value='' selected='selected'>Select</option>" +
                "<option value='1'>Yes</option>" +
                "<option value='0'>No</option>" +
                "</select>" +
                "<small class='help-block'></small>" +
                "</div>" +
                "</li>";
            $("#fieldList").append($customerQrcodeFirstRow); */

        }
        });
        /* Display single row on adding cpid - End */

    });

    $("#basement_mode").on("click", function(event) {
        var isChecked = $('#basement_mode').is(':checked');
        if ($("#basement_mode").is(':checked')) {
            $(".basement_mode").show();
        } else {
            $(".basement_mode").hide();
            $('input[name="basement_interval"]').val("");
            $('input[name="basement_noofrounds"]').val("");
        }

    });
    $('document').ready(function() {
        $(".binterval").mask("99:99");
    });

    $(document).ready(function() {
        $("#customer-form").on("click", function() {
            var toggler = document.getElementsByClassName("caret");
            var i;

            for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
            }
        });

        $("#landingPage #tabList #editTab").on("click", function(e) {
            console.log(e);
            console.log('clicked tab edit');
        });

        $("#landingPage #tabList #editActiveTab").on("click", function(e) {
            console.log(e);
            console.log('active tab');
        });

    });

    $('#master_customer').select2();

    $('document').ready(function(){
        if($('input[name="id"]').val() == null){
            $("#requester_id").select2();
            $("#requester_id").val('').trigger('change');
        setTimeout(() => {
            $('input[name="fence_interval"]').val("5");
            $('select[name="contractual_visit_unit"] option[value="2"]').prop('selected',true)
            $('input[name="geo_fence"]').prop("checked","checked");
            $('input[name="customer_type"]').val("1")

        }, 200);

        }
    });

    function getval(sel, key) {

        if (sel.value == 1) {
            //alert('yes');
            $('#picture_mandatory').show();
            //document.getElementById('#picture_mandatory_'+key).required = true;
        } else {
            //alert('no');
            $('#picture_mandatory').hide();
            //document.getElementById('#picture_mandatory_'+key).required = false;
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel='stylesheet' type='text/css' href='{{ asset("css/timepicki.css") }}' />

<script>
function open_new_configuration() {
    $(".close").trigger('click');
    var customer_id = $('input[name="id"]').val();
    let url = "{{ route('landing_page.new_configuration_window',['customer_id' => ''])}}" + customer_id + '';
    window.open(url);
}

function edit_tab(tab_id) {
    $(".close").trigger('click');
    let url = "{{ route('landing_page.new_configuration_window',['tab_id' => ''])}}" + tab_id + '';
    window.open(url);
}

function delete_tab(tab_id) {
    swal({
             title: "Are you sure?",
            text: "You will not be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, remove",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },function() {
            $.ajax({
                type: "POST",
                url: "{{route('landing_page.removeTab')}}",
                data: {'tabid': tab_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status == "success") {
                        $('#tabName' + tab_id).remove();
                    }
                    swal(response.status_msg,response.msg, response.status);
                }
            });
    });
}

function edit_activeTab(prevTabId) {
   name = 'li'+'#tabName' + prevTabId + ' a.editActiveTab';
   status = document.querySelector(name).getAttribute("value");
   var customer_id = $('input[name="id"]').val()
   $.ajax({
            type: "POST",
            url: "{{route('landing_page.saveTabActiveStatus')}}",
            data: {
                'customerid': customer_id,
                'tabid': prevTabId,
                'status': (status==1)?0:1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === "success") {
                    swal({
                        title: "Success",
                        text: response.msg,
                        type: response.status,
                        confirmButtonText: "OK"
                    });

                    if (status == 1) {
                    $(name).removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                    document.querySelector(name).setAttribute("value", "0");
                    } else {
                    $(name).removeClass("fa-toggle-off fa-2x").addClass("fa-toggle-on fa-2x");
                    document.querySelector(name).setAttribute("value", "1");
                }
                }else {
                    swal(response.status_msg, response.msg, response.status);
                }
            }
        });

}

$('document').ready(function(){
        var id = $("input[name=id]").val();
        if(id){
        var customerStatus;
            $('#user_tab').addClass('active show');
            $('#userTab').addClass('active');
            $('.landingPageTab').removeClass('active');
            $('.landingPageTab').css('display','block');
            $('#landingPage').css('display','none');
            $('#fencenew').css('display', 'none');
            //$('.fenceinputs').show();
            var base_url = "{{route('customer.single',':id')}}";
            var url = base_url.replace(':id', id);
            $('.fencerow').remove();
             $("#subjects").val(null).trigger('change');
            $('#customer-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $(".customer-cpid-allocation-table tbody tr").remove();
            $(".customer-qrcode-location-list li").remove();

            $("#fencerowsedit").css('display', 'block');
            $('input[name="basement_mode"]').prop("checked", false);
            $('input[name="basement_interval"]').val("");
            $('input[name="basement_noofrounds"]').val("");
            $('input[name="geo_fence"]').prop("checked", false);
            $('input[name="mobile_security_patrol_site"]').prop("checked", false);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log(data)
                    if (data) {
                        $('input[name="id"]').val(data.id)
                        $('input[name="customer_type"]').val("1")
                        $('input[name="project_number"]').val(data.project_number)
                        $('input[name="client_name"]').val(data.client_name)
                        $('input[name="contact_person_name"]').val(data.contact_person_name)
                        $('input[name="contact_person_email_id"]').val(data.contact_person_email_id)
                        $('input[name="contact_person_phone"]').val(data.contact_person_phone)
                        $('input[name="contact_person_phone_ext"]').val(data.contact_person_phone_ext)
                        $('input[name="contact_person_cell_phone"]').val(data.contact_person_cell_phone)
                        $('input[name="contact_person_position"]').val(data.contact_person_position)
                        $('select[name="requester_name"]').val(data.requester_name);
                        if (data.geo_fence_satellite == 1) {
                            $('input[name="geo_fence_satellite"]').prop("checked", true);
                        } else {
                            $('input[name="geo_fence_satellite"]').prop("checked", false);
                        }
                        //image preview
                        if (data.incident_report_logo && data.incident_report_logo.length > 0) {
                            $('#incident-logo-section').show();
                            let baseName = data.incident_report_logo.split('/').reverse()[0];
                            $('#incident-logo-section .image-info').text(baseName)
                        }
                        $('select[name="contractual_visit_unit"] option[value="'+data.contractual_visit_unit+'"]').prop('selected',true)
                        $('input[name="fence_interval"]').val(data.fence_interval)
                        $('input[name="recruiting_match_score_for_sending_mail"]').val(data.recruiting_match_score_for_sending_mail);


                        $.ajax({
                            type: "get",
                            url: "{{route('customer.fencelistarray')}}",
                            data: {
                                "customer_id": id
                            },
                            success: function(response) {

                            }
                        }).done(function(data) {
                            var latitiude = 43.93667009577818;
                            var longitude = -79.65423151957401;
                            var radius = 1000;
                            $("#latfence").val(latitiude);
                            $("#longfence").val(longitude);
                            $("#radiusfence").val(radius);
                            var jqdata = jQuery.parseJSON(data);

                            //initializefence(new google.maps.LatLng(latitiude, longitude), radius, jqdata);
                        });
                        if(data.subject_allocation!=null && data.subject_allocation.length>0)
                        {
                            $.each(data.subject_allocation, function(key, subject) {

                            $('#subjects  option[value="'+subject.subject_id+'"]').prop("selected", true).change();
                        })
                        }

                        $('input[name="is_nmso_account"]').prop("checked", false);
                        $('#security_clearance_lookup_id').css('display', 'none');
                        if(data.stc_details != null) {
                            if(data.stc_details.nmso_account == "yes") {
                                $('#security_clearance_lookup_id').css('display', 'block');
                                $('input[name="is_nmso_account"]').prop("checked", true);
                                $('select[name="security_clearance_lookup_id"]').val(data.stc_details.security_clearance_lookup_id);
                            }
                        }

                        if (data.requester_position != null && data.requester_empno != null && isNaN(data.requester_name)) {
                            //case when requestername is string
                            $("#select[name='requester_name'] option").each(function() {
                                var str = $(this).text();
                                if (str.indexOf(data.requester_empno) >= 0) {

                                    var val = $(this).val();
                                    $('select[name="requester_name"]').val(val);
                                    $('input[name="requester_position"]').val(data.requester_position);
                                    $('input[name="requester_empno"]').val(data.requester_empno);
                                    return false;
                                } else {
                                    $('select[name="requester_name"]').val('');
                                    $('input[name="requester_position"]').val('');
                                    $('input[name="requester_empno"]').val('');
                                }
                            });
                        } else if (data.requester_details != null && data.requester_details.employee.employee_position != null) {
                            $('input[name="requester_position"]').val(data.requester_details.employee.employee_position.position);
                            $('input[name="requester_empno"]').val(data.requester_details.employee.employee_no);
                        } else if (data.requester_details != null && data.requester_details.employee.employee_position == null) {
                            $('input[name="requester_position"]').val('');
                            $('input[name="requester_empno"]').val(data.requester_details.employee.employee_no);
                        } else {
                            $('input[name="requester_position"]').val('');
                            $('input[name="requester_empno"]').val('');
                        }
                        $('input[name="status"').val(data.status);
                        $('input[name="city"]').val(data.city)
                        $('input[name="postal_code"]').val(data.postal_code)
                        $('input[name="province"]').val(data.province)
                        $('input[name="address"]').val(data.address)
                        $('textarea[name="description"]').val(data.description);
                        $('input[name="proj_open"]').val(data.proj_open);
                        $('input[name="proj_expiry"]').val(data.proj_expiry);
                        $('input[name="arpurchase_order_no"]').val(data.arpurchase_order_no);
                        $('input[name="arcust_type"]').val(data.arcust_type);
                        $('select[name="industry_sector_lookup_id"]').val(data.industry_sector_lookup_id).select2({ width: '100%' });
                        $('select[name="region_lookup_id"]').val(data.region_lookup_id);
                        $('select[name="region_lookup_id"]').trigger('change');
                        $('input[name="billing_address"]').val(data.billing_address);
                        $('input[name="same_address_check"]').prop("checked", false);

                        if(data.stc) {
                            $('input[name="stc"]').prop("checked", true);
                        }else{
                            $('input[name="stc"]').prop("checked", false);
                        }
                        var full_address = data.address + ', ' + data.city + ', ' + data.province + ', ' + data.postal_code;

                        if (data.billing_address != null) {
                            if (full_address.trim() === data.billing_address.trim()) {
                                $('input[name="same_address_check"]').prop("checked", true);
                            }
                        }
                        if (data.show_in_sitedashboard) {
                            $('input[name="show_in_sitedashboard"]').prop("checked", true);
                        } else {
                            $('input[name="show_in_sitedashboard"]').prop("checked", false);
                        }
                        if (data.guard_tour_enabled) {
                            $('input[name="guard_tour_enabled"]').prop("checked", true);

                        } else {
                            $('input[name="guard_tour_enabled"]').prop("checked", false);
                        }
                        if (data.guard_tour_duration) {
                            $('input[name="guard_tour_enabled"]').prop("checked", true);
                            $('input[name="interval_check"]').prop("checked", true);
                            $('#guard_tour').show();
                            $('#interval_check').show();
                            $('#guard_tour_duration').show();
                            $('input[name="guard_tour_duration"]').val(data.guard_tour_duration);
                        } else {
                            $('input[name="interval_check"]').prop("checked", false);
                            $('#interval_check').hide();
                            $('#guard_tour_duration').hide();
                        }
                        if (data.shift_journal_enabled) {
                            $('input[name="shift_journal_enabled"]').prop("checked", true);
                        } else {
                            $('input[name="shift_journal_enabled"]').prop("checked", false);
                        }
                        if (data.facility_booking) {
                            $('input[name="facility_booking"]').prop("checked", true);
                        } else {
                            $('input[name="facility_booking"]').prop("checked", false);
                        }
                        customerStatus = data.active;
                        if (data.active) {
                            $('input[name="active"]').prop("checked", true);
                        } else {
                            $('input[name="active"]').prop("checked", false);
                        }

                        if (data.time_shift_enabled) {
                            $('input[name="time_shift_enabled"]').prop("checked", true);
                            // $('#time_shift_enabled').show();

                        } else {
                            $('input[name="time_shift_enabled"]').prop("checked", false);
                            // $('#time_shift_enabled').hide();

                        }
                        if (data.overstay_enabled) {
                            $('input[name="overstay_enabled"]').prop("checked", true);
                            $('#overstay_time').show();
                            $('input[name="overstay_time"]').val(data.overstay_time);
                        } else {
                            $('input[name="overstay_enabled"]').prop("checked", false);
                            $('#overstay_time').hide();
                            $('input[name="overstay_time"]').val(data.overstay_time);
                        }

                        if (data.employee_rating_response) {
                            $('input[name="employee_rating_response"]').prop("checked", true);
                            $('#employee_rating_response_time').show();
                            $('input[name="employee_rating_response_time"]').val(data.employee_rating_response_time);
                        } else {
                            $('input[name="employee_rating_response"]').prop("checked", false);
                            $('#employee_rating_response_time').hide();
                            $('input[name="employee_rating_response_time"]').val(data.employee_rating_response_time);
                        }
                         if (data.qr_patrol_enabled) {
                            $('input[name="qr_patrol_enabled"]').prop("checked", true);
                             $('#qr_picture_limit').show();
                             $('#qr_interval_check').show();
                             $('#qr_daily_activity_report').show();
                             $('input[name="qr_picture_limit"]').val(data.qr_picture_limit);

                        } else {
                            $('input[name="qr_patrol_enabled"]').prop("checked", false);
                             $('#qr_picture_limit').hide();
                             $('#qr_interval_check').hide();
                             $('#qr_daily_activity_report').hide();
                            
                        }
                        $('input[name="rec_onboarding_threshold_days"]').val(data.rec_onboarding_threshold_days);
                        if (data.qr_daily_activity_report) {
                            $('input[name="qr_daily_activity_report"]').prop("checked", true);
                            $('#qr_recipient_email').show();
                            $('input[name="qr_recipient_email"]').val(data.qr_recipient_email);
                            $('input[name="qr_recipient_email"]').prop('title', data.qr_recipient_email);
                            
                        }else{
                            $('input[name="qr_daily_activity_report"]').prop("checked", false);
                            $('#qr_recipient_email').hide();
                        }
                        if (data.qr_interval_check) {
                            $('input[name="qr_interval_check"]').prop("checked", true);
                            $('#qr_duration').show();
                            $('input[name="qr_duration"]').val(data.qr_duration);
                        } else {
                            $('input[name="qr_interval_check"]').prop("checked", false);
                             $('#qr_duration').hide();

                        }
                        if (data.key_management_enabled) {
                            $('input[name="key_management_enabled"]').prop("checked", true);
                            $('#key_management_signature').show();
                            $('#key_management_image_id').show();
                            if(data.key_management_signature){
                                $('input[name="key_management_signature"]').prop("checked", true);
                            }else{
                                $('input[name="key_management_signature"]').prop("checked", false);
                            }
                            if(data.key_management_image_id){
                                $('input[name="key_management_image_id"]').prop("checked", true);
                            }else{
                                $('input[name="key_management_image_id"]').prop("checked", false);
                            }

                        } else {
                            $('input[name="key_management_enabled"]').prop("checked", false);
                            $('#key_management_signature').hide();
                            $('#key_management_image_id').hide();

                        }

                        if (data.motion_sensor_enabled) {
                            $('input[name="motion_sensor_enabled"]').prop("checked", true);
                            $('#motion_sensor_incident_subject').show();
                            if(data.motion_sensor_incident_subject){
                                $('#motion_sensor_incident_subject_id')
                                    .val(data.motion_sensor_incident_subject).trigger('change');
                            }
                        } else {
                            $('input[name="motion_sensor_enabled"]').prop("checked", false);
                            $('#motion_sensor_incident_subject').hide();
                        }
                        if (data.visitor_screening_enabled) {
                            $('input[name="visitor_screening_enabled"]').prop("checked", true);
                        } else {
                            $('input[name="visitor_screening_enabled"]').prop("checked", false);
                        }
                        if (data.time_sheet_approver_id) {

                            $('select[name="time_sheet_approver_id"]').val(data.time_sheet_approver_id).trigger('change');
                            $('#time_sheet_approver_email').show();

                        }else{
                            $('#time_sheet_approver_email').hide();
                        }

                        let _selCType =data.customer_type != null ? data.customer_type.id:null;
                        $('select[name="customer_type_id"] option[value="'+_selCType +'"]').prop('selected', true);

                        $.each(data.cpids, function(key, value) {
                            var customer_cpid_allocation_edit_row = '';
                            customer_cpid_allocation_edit_row = getCpidRow(key);

                            $(".customer-cpid-allocation-table tbody").append(customer_cpid_allocation_edit_row);
                            $('select[name="position_' + key + '"] option[value="' + value.position_id + '"]').prop('selected', true);
                            $('select[name="cpid_' + key + '"] option[value="' + value.cpid + '"]').prop('selected', true);
                            $('.cpid-select2').select2({ width: '40%' });
                        });

                        if (data.cpids.length >= 1) {
                            $('#remove-cpid-allocation').show();
                        }
                        if (data.basement_mode == 1) {
                            $('input[name="basement_mode"]').trigger("click");
                            $('input[name="basement_interval"]').val(data.basement_interval);
                            $('input[name="basement_noofrounds"]').val(data.basement_noofrounds);
                        }
                        if (data.geo_fence == 1) {
                            $('input[name="geo_fence"]').trigger("click");
                        }
                        if (data.mobile_security_patrol_site == 1) {
                            $('input[name="mobile_security_patrol_site"]').trigger("click");
                        }
                     /*   $.each(data.qrcode_locations, function(key, value) {
                            //alert(value.qrcode);
                            var customer_qrcode_allocation_edit_row = '';
                            customer_qrcode_allocation_edit_row =
                                $("#fieldList").append("<li value=" + key + ">" +
                                    "<div class='row'>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_" + key + "'>" +
                                    "<input type='text' name='qr-location-row[]' class='row-no' value=" + key + ">" +
                                    "<label for='qrcode_" + key + "' class='control-label'>QR Code <span class='mandatory'>*</span></label>" +
                                    "<input class='form-control qrcode' placeholder='QR Code' name='qrcode_" + key + "' type='text'>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_" + key + "'>" +
                                    "<label for='location_" + key + "' class='control-label'>Checkpoint<span class='mandatory'>*</span></label>" +
                                    "<input class='form-control location' placeholder='Checkpoint' name='location_" + key + "' type='text'>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "</div>" +
                                    "<div class='row'>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='no_of_attempts_" + key + "'>" +
                                    "<label for='no_of_attempts_" + key + "' class='control-label'>Number of Attempts <span class='mandatory'>*</span></label>" +
                                    "<input class='form-control no_of_attempts_week_day' placeholder='Number of Attempts' name='no_of_attempts_" + key + "' type='text'>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='location_enable_disable_" + key + "'>" +
                                    "<label for='location_enable_disable_" + key + "' class='control-label'>Enable/Disable Location<span class='mandatory'>*</span></label>" +
                                    "<select class='form-control' name='location_enable_disable_" + key + "'>" +
                                    "<option value='' selected='selected'>Select</option>" +
                                    "<option value='1'>Enable</option>" +
                                    "<option value='0'>Disable</option>" +
                                    "</select>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "</div>" +
                                    "<div class='row'>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='picture_enable_disable_" + key + "'>" +
                                    "<label for='picture_enable_disable_" + key + "' class='control-label'>Enable/Disable Picture<span class='mandatory'>*</span></label>" +
                                    "<select class='form-control' name='picture_enable_disable_" + key + "' onchange='getval(this," + key + ");'>" +
                                    "<option value='' selected='selected'>Select</option>" +
                                    "<option value='1'>Enable</option>" +
                                    "<option value='0'>Disable</option>" +
                                    "</select>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "<div class='form-group col-xs-6 col-sm-6 col-md-6 col-lg-6' id='qrcode_active_" + key + "'>" +
                                    "<label for='qrcode_active_" + key + "' class='control-label'>Active<span class='mandatory'>*</span></label>" +
                                    "<select class='form-control' name='qrcode_active_" + key + "'>" +
                                    "<option value='' selected='selected'>Select</option>" +
                                    "<option value='1'>Activate</option>" +
                                    "<option value='0'>De-activate</option>" +
                                    "</select>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "</div>" +
                                    "<div class='form-group' id='picture_mandatory_" + key + "'>" +
                                    "<label for='picture_mandatory_" + key + "' class='control-label'>Picture Mandatory<span class='mandatory'>*</span></label>" +
                                    "<select class='form-control' name='picture_mandatory_" + key + "' id='picture_mandatory_id_" + key + "'>" +
                                    "<option value='' selected='selected'>Select</option>" +
                                    "<option value='1'>Yes</option>" +
                                    "<option value='0'>No</option>" +
                                    "</select>" +
                                    "<small class='help-block'></small>" +
                                    "</div>" +
                                    "</li>");

                            $('input[name="qrcode_' + key + '"]').val(value.qrcode).prop('readonly', true);
                            $('input[name="location_' + key + '"]').val(value.location).prop('readonly', true);
                            $('input[name="no_of_attempts_' + key + '"]').val(value.no_of_attempts_week_day).prop('readonly', true);
                            $('select[name="picture_enable_disable_' + key + '"] option[value="' + value.picture_enable_disable + '"]').prop('selected', true);
                            $('select[name="picture_mandatory_' + key + '"] option[value="' + value.picture_mandatory + '"]').prop('selected', true);
                            $('select[name="location_enable_disable_' + key + '"] option[value="' + value.location_enable_disable + '"]').prop('selected', true);
                            $('select[name="qrcode_active_' + key + '"] option[value="' + value.qrcode_active + '"]').prop('selected', true);

                            if (value.picture_enable_disable == 0) {
                                $('#picture_mandatory_' + key).hide();
                            }

                        });*/

                        $("#requester_id").select2();
                    } else {
                        alert(data);

                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            }).done(function(event) {
                $.ajax({
                    type: "get",
                    url: "{{route('customer.fencelist')}}",
                    data: {
                        "customerid": id
                    },
                    success: function(response) {
                        $("#fencerowsedit").html(response);
                    }
                }).done(function($event) {
                    $('.disablefences').on("click", function(event) {
                        var fenceid = $(this).attr("attr-fencerowid");
                        var process = $(this).attr("attr-process");
                        swal({
                                title: "Are you sure?",
                                text: "You will be disabling an active fence",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText: 'Yes, I am sure',
                                cancelButtonText: "No, cancel it",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $.ajax({
                                        type: "post",
                                        url: "{{route('customer.disablefence')}}",
                                        data: {
                                            "fenceid": fenceid,
                                            "process": process
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        success: function(response) {
                                            swal("Updated", "Successfully updated", "success");
                                        }
                                    }).done(function(ev) {
                                        renderFences(id);
                                    });


                                } else {
                                    e.preventDefault();
                                }
                            });

                    });
                    $(".savedfences").on("click", function($event) {
                        var fenceid = $(this).attr("attr-fencerowid");
                        let customerId = $('input[name="id"]').val();
                        swal({
                                title: "Are you sure?",
                                text: "You will not be able to recover this fence",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText: 'Yes, I am sure',
                                cancelButtonText: "No, cancel it",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm) {

                                if (isConfirm) {
                                    $.ajax({
                                        type: "get",
                                        url: "{{route('customer.removefencelist')}}",
                                        data: {
                                            "fenceid": fenceid
                                        },
                                        success: function(response) {
                                            // $('a[data-id="'+id+'"]').get(0).click();
                                            if (response == "Deleted") {
                                                $("#fencerow-" + fenceid).remove();
                                                renderFences(customerId);
                                                swal("Removed", "Successfully updated", "success");
                                            }
                                        }
                                    });


                                } else {
                                    e.preventDefault();
                                }
                            });

                    });

                    $(".editfences").on("click", function($event) {

                        var fenceid = $(this).attr("attr-fencerowid");
                        var fencename = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencename");
                        var fenceaddress = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-fencedesc");
                        var fencevisitcount = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-visitcount");
                        var latitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-latitiude");
                        var longitude = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-longitude");
                        var radius = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-radiusfence");
                        var contractualVisit = $('div').find('[attr-id="' + fenceid + '"]').attr("attr-contractractual");

                        $('#title').val(fencename);
                        $("#addressfence").val(fenceaddress);
                        $("#visitsfence").val(fencevisitcount);
                        $('#latfence').val(latitude);
                        $('#longfence').val(longitude);
                        $('#radiusfence').val(radius);
                        $('#contractual_visit').val(contractualVisit);

                        $("#booleanedit").val("1");
                        $("#whichfence").val(fenceid);

                    });
                });


            }).done(function(event) {
                $.ajax({
                    type: "get",
                    url : "{{route('customer.getLandingPageDetails')}}",
                    data: {
                        "customerid": id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        var prevTabId = null;
                        var prevWidget = null;
                        var widgetNum = 0;
                        $('#tabList').remove();
                        $('#tab').append('<ul class="list-group" style="padding-bottom: 2rem;white-space: nowrap;" id="tabList"></ul>');
                        if (customerStatus == 1) {
                            $('#newTabContainer').html('<input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary" style="float: right; margin-right: 1%;"/>');
                            $.each(data, function(tabKey, tabValue) {
                                //console.log(tabValue.default_tab_structure);
                                if (prevTabId != tabValue.id) {
                                    prevTabId = tabValue.id;
                                    $('#tabList').append(
                                        '<li style="list-style-type: none;" class="custom-list-group-item" id=tabName'+prevTabId+'>'
                                        +'<span class="caret"></span>'+ tabValue.tab_name
                                        +'<span onclick="edit_activeTab('+prevTabId+')"><a href="#" class="editActiveTab fa fa-toggle-on fa-2x" style="float: right; padding-right: 1rem;" value="'+tabValue.active+'"></a></span>'
                                        +'<span onclick="delete_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-trash fa-lg" style="float: right; padding-right: 1rem;padding-top: 0.3em;" data-id="'+tabValue.id+'"></a></span>'
                                        +'<span onclick="edit_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-pencil fa-lg" style="float: right; padding-right: 1rem;padding-top: 0.3em;" data-id="'+tabValue.id+'"></a></span>'
                                        +'</li>');
                                    if (tabValue.active == 0) {
                                        $('#tabList #tabName'+prevTabId+' a.editActiveTab').removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                                    }

                                    $('#tabName'+prevTabId).append('<ul class="nested" style="margin-top:3% !important;" id="nested'+prevTabId+'"></ul>');
                                }
                                $.each(tabValue.tabDetails, function(widgetkey, widgetvalue) {
                                    if (prevWidget != widgetkey) {
                                        widgetNum++;
                                        $('#nested'+prevTabId).append('<li class="custom-list-group-item nest1" id="nest'+widgetNum+'"><span class="caret"></span>'+widgetkey+'</li>');
                                        $('#nest'+widgetNum).append('<ul class="nested" id="colName'+widgetNum+'"></ul>');
                                        $('#colName'+widgetNum).append('<table class="table" id="table'+widgetNum+'">'
                                                                        +'<thead>'
                                                                        +'<tr>'
                                                                        +'<th scope="col">Filed Display Name</th>'
                                                                        +'<th scope="col">Sort By</th>'
                                                                        +'</tr>'
                                                                        +'</thead>'
                                                                        +'<tbody>'
                                                                        +'</tbody>'
                                                                        +'</table>');
                                    }
                                    $.each(widgetvalue, function(key, value) {
                                        $.each(value, function(k,v){
                                            console.log(v);
                                            $('#table'+widgetNum).append('<tr>'
                                                                    +'<td class="text-center">'+v.field_display_name+'</td>'
                                                                    +'<td class="text-center">'+[(v.default_sort == 1)? '<span class="fa fa-check"></span>':'<span class="fa fa-times"></span>']+'</td>'
                                                                    +'</tr>');
                                        });
                                    });
                                })
                            });
                        } else {
                            $('#newTabContainer').html('<h4 style="text-align: center; padding-bottom:6rem;">Please activate customer to edit landing page</h4>');
                        }

                    }//success function end
                });
            });
    }
});

function updateResponseTime(result){
    if(result.id == 'medium'){
        $('#priorityModal #low').val(result.value);
    }else if(result.id == 'high'){
        $('#priorityModal #medium_range').val(result.value);
    }

}

$('input[name="is_nmso_account"]').on('change', function(){
    $('select[name="security_clearance_lookup_id"]').val('');
    if($(this).is(':checked')) {
        $('#security_clearance_lookup_id').css('display', 'block');
    }else{
        $('#security_clearance_lookup_id').css('display', 'none');
    }
});
</script>
