@section('scripts')
<script>
     $(function () {

//For dropdown with search option
$('.select2').select2();
let q = globalUtils.uraQueryParamToJson(window.location.href);
let cids = globalUtils.decodeFromCsv(q.cIds);

var table = $('#emp-table').DataTable({
    processing: false,
    serverSide: true,
    responsive: true,
    ajax:{
        url: "{{ route('employee.exitterminationsummarylist') }}",
        data:{
            from: q.from,
            to:q.to,
            cids:cids
        }
    },
    dom: 'Blfrtip',
    buttons: [
        {
            extend: 'pdfHtml5',
            pageSize: 'A2',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            extend: 'excelHtml5',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            extend: 'print',
            pageSize: 'A2',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                stripHtml: false,
            }
        }
    ],
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    order: [
        [0, "desc"]
    ],
    lengthMenu: [
        [10, 25, 50, 100, 500, -1],
        [10, 25, 50, 100, 500, "All"]
    ],
    columns: [
       
        {data: 'id', name: 'id',"visible": false,"searchable": false},
        {data: 'unique_id', name: 'unique_id'},
        {data: 'regional_manager', name: 'regional_manager'},
        {data: 'date', name: 'date_raw.date',type: 'date'},
        {data: 'site_details', name: 'site_details'},
        {data: 'employee_details', name: 'employee_details'},
        {data: 'reason', name: 'reason'},
        {data: 'reason_details', name: 'reason_details'},
        {data: 'exit_interview_explanation', name: 'exit_interview_explanation'},

    ],


});

/* Page redirection to exit interview - Start */
$('#add-new-button').on('click',  function(e){
window.location='{{ route('employee.exittermination') }}';
});
/* Page redirection to exit interview - End */

function reasonChanged(id) {
        switch (id) {
            case "1":
                $("#resignation_reason_id").show();
                $("#termination_reason_id").hide();
                $("#termination_reason_id").find('select').val("");
                break;
            case "2":
                $("#termination_reason_id").show();
                $("#resignation_reason_id").hide();
                $("#resignation_reason_id").find('select').val("");
                break;

        }
    }

     $('#job-form select[name="customer_id"]').on('change', function () {
            id = $(this).val();
            var url = "{{ route('project.details',':id') }}"
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    $('#job-form input[name="client_name"]').val(data.client_name);
                    $('#job-form input[name="address"]').val(data.address);
                    $('#job-form input[name="city"]').val(data.city);
                    $('#job-form input[name="postal_code"]').val(data.postal_code);
                    //$('#job-form input[name="requester"]').val(data.requester_name);
                    //$('#job-form input[name="employee_num"]').val(data.requester_empno);

                },
                error: function () {}
            });
        });

         $('#employeeexitinterviewformm').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#employeeexitinterviewformm')[0]);;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('employee.exitinterview.store') }}",
                dataType: 'json',
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "Exit interview details has been successfully saved",
                             type: "success"
                        },
                        function () {
                            window.location = "{{ route('employee.exitterminationsummary') }}";
                        });
                    } else {
                        alert(data.message);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        function cancelExitInterview() {
        swal({
                title: "Are you sure?",
                text: "Are you sure to cancel this exit interview?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                window.location = "{{ route('employee.exitterminationsummary') }}";
            });
    }


     /* On changing projects employee name will be listed - Start */
     $('#project_list').on('change', function(e){
        var customer_id = $(this).val();
        var base_url = "{{route('exitinterviewalloction.list', ':id')}}";
        var url = base_url.replace(':id', customer_id);
        var employee_options = [];
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            data: {'id':customer_id},
            success: function (data) {
                if (data) {
                  data = sortByKey(data, 'first_name');
                  $('#employee_list').find('option').not(':first').remove();
                  $.each(data, function(key, value){
                        if(!value.employee_profile.employee_no) {
                         name = value.first_name+' '+(value.last_name !== null ? value.last_name : '')  
                     }
                     else {
                     name = value.first_name+' '+(value.last_name !== null ? value.last_name : '')+" ("+value.employee_profile.employee_no+")"
                     }
                    $('#employee_list').append("<option value="+value.id+">"+name+"</option>");
                     
                  });
                } else {
                    console.log(data);
                }
            },
        })
    });
    /* On changing projects employee name will be listed - End */

     function sortByKey(array, key) {
    return array.sort(function(a, b) {
        var x = a[key]; var y = b[key];
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    });
    }

      $('#timepicker').timepicki();
        $('.select2').select2();

        $(".checkBoxClass_shift").change(function () {
            if (!$(this).prop("checked")) {
                $("#ckbCheckAll").prop("checked", false);
            }
        });
        $(".checkBoxClass_days").change(function () {
            if (!$(this).prop("checked")) {
                $("#ckbsCheckAll").prop("checked", false);
            }
        });

        $('.reason_dropdown').on('change', function () {
            reasonChanged($(this).val());
        });

});

   
   
   
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@endsection
