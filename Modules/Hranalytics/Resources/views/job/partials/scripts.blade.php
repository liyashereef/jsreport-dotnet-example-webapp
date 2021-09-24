@section('scripts')
<script>
    function reasonChanged(id) {
       
        switch (id) {
            case "1":
                $("#reason_dropdown_2").show();
                $("#reason_dropdown_3,#resign_id,#terminate_id").hide();
                $("#reason_dropdown_3,#resign_id,#terminate_id").find('select').val("");
                break;
            case "2":
                $("#reason_dropdown_3").show();
                $("#reason_dropdown_2,#resign_id,#terminate_id").hide();
                $("#reason_dropdown_2,#resign_id,#terminate_id").find('select').val("");
                break;
            case "3":
            case "4":
            case "10":
            case "13":
                $("#resign_id,#terminate_id").hide();
                $("#resign_id,#terminate_id").find('select').val("");
                break;
            case "11":
                $("#resign_id").show();
                $("#reason_dropdown_3,#terminate_id").hide();
                $("#reason_dropdown_3,#terminate_id").find('select').val("");
                break;
            case "12":
                $("#terminate_id").show();
                $("#reason_dropdown_3,#resign_id").hide();
                $("#reason_dropdown_3,#resign_id").find('select').val("");
                break;
        }
    }
    $(function () {
       
        $('#timepicker').timepicki();
        $('.select2').select2();
        $('select[name="training_id"]').select2({ width: '100%' });
        
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

        $('#job-form').submit(function (e) {
            e.preventDefault();
            for (instance in CKEDITOR.instances)
                CKEDITOR.instances[instance].updateElement();
            var $form = $(this);
            var formData = new FormData($('#job-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('job.store') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: data.result,
                            icon: "success",
                            button: "OK",
                        }, function () {
                            @canany(['create-job','edit-job','delete-job','archive-job','job-approval','hr-tracking','job-attachement-settings','list-jobs-from-all','job-tracking-summary','candidate-schedule-summary'])
                                window.location = "{{ route('job') }}";
                            @endcan
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
    });

    function cancelJobReq() {
        swal({
                title: "Are you sure?",
                text: "Are you sure to cancel this job request?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                window.location = "{{ route('job') }}";
            });
    }

    function confirmJob(job_selector) {
        job_selected = job_selector.find('option:selected');
        if (job_selected.val() !== '') {
            swal({
                title: job_selected.text(),
                text: "You have selected the position being hired as '" + job_selected.text() + "'",
                type: "info",
                showCancelButton: false,
                confirmButtonText: "OK",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            });
        }
    }
</script>
@endsection
