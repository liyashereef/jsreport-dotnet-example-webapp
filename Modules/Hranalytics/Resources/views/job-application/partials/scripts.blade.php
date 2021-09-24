@section('scripts')
<script>

    function emptyCheck(currentValue){
        if(currentValue == ""){
            return false;
        }else{
            return true;
        }
    }

   $('.select2').select2();

   $('#attachment-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);

        var has_file_to_upload = [];
        var message = 'Please upload all the chosen files';
        var all_file_inputs = $('#attachment-form :input[type="file"]');
        var file_input_values = [];
        all_file_inputs.each(function() {
            file_input_values.push($(this).val());
        });
        has_file_to_upload = file_input_values.filter(emptyCheck);
        var required_attachments='{{$session_obj['job']->required_attachments}}';
        var candidate_job_id='{{$session_obj['job']->id}}';
        var req_arr=(required_attachments.replace(/&quot;/g, ''))
        var total_required=0;
        var count=0;
        if (req_arr !=='null' && req_arr !==''){
            $.each(JSON.parse(req_arr), function( index, value ) {
                total_required++;
                if (!($('#attachment_file_name'+'\\.'+value).hasClass('success'))){
                    $('#attachment_file_name'+'\\.'+value).addClass('has-error').find('#attachment-validation').text('Please upload file');
                    $('html, body').animate({
                        scrollTop: $(".has-error").offset().top
                    }, 1000);
                }
                else{
                    count++;
                }
            });
            if(count==total_required && has_file_to_upload.length == 0){
                if($('#attachment-form').data('action')=='edit'){
                   showMessageIfUpdate($form);
                }else{
                   movetoNextTab(candidate_job_id)
                }
            }else{
                swal('Alert', message, 'warning');
            }
        }
        else {
            if(has_file_to_upload.length == 0){
                if($('#attachment-form').data('action')=='edit'){
                   showMessageIfUpdate($form);
                }else{
                   movetoNextTab(candidate_job_id)
                }
            }else{
                swal('Alert', message, 'warning');
            }
        }
     });


    function movetoNextTab(candidate_job_id)
    {
        var $form = $('#attachment-form');
        var view_url = '{{ route("applyjob.view", ":id") }}';
        view_url = view_url.replace(':id', candidate_job_id);
        $('#print-pdf-application').prop('href', view_url);
        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
        current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
        current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
             $('html, body').animate({
            scrollTop: $("ul").offset().top
            }, 1000);
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
    }

    $(".file_attachment_upload_btn").click(function(){
        var attachment_num=$(this).data('id')
        if(document.getElementById("attach_id_"+attachment_num).value == "") {
         swal({
                            title: "No file chosen",
                            text: "Please upload the file",
                            type: "warning",
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "OK",
                            showLoaderOnConfirm: true,
                            allowEscapeKey:false,
                        });
           return false;
        }
        var bar = $('#bar'+attachment_num);
        var percent = $('#percent'+attachment_num);
        var $form = $('#attachment-form');
        var formData = new FormData($('#attachment-form')[0]);
        formData.append('attachment_id', attachment_num);
        let url = "{{ route('applyjob.attachment') }}";
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                 onFileUploadSuccess(data.attachment_id);
                  var file_display = '<div class="status_upload'+data.attachment_id+' '+'success'+data.attachment_id+'"><br/>';
                  file_display += data.file_name
                  file_display +=  '</div>';
                    $('.status_upload'+data.attachment_id).replaceWith(file_display);
                    $('#attachment_file_name\\.'+data.attachment_id).addClass('success');
                    $('#attach_id_'+data.attachment_id).val('');
                } else {
                    console.log(data);
                    swal("Oops", "Could not upload", "warning");
                }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
              },
            contentType: false,
            processData: false,
        });

        function onFileUploadSuccess(attachment_id){
               var $form = $('#attachment-form');
               $form.find('#attachment_file_name\\.'+attachment_id).removeClass('has-error').find('.help-block').text('');
        }
    });
    function showMessageIfUpdate($form)
    {
        button_text = $form.find('button[type="submit"]').text();
        if (button_text == 'Update')
        {
            swal({
                title: "Success",
                text: "Candidate application details are successfully updated.",
                type: "info",
                confirmButtonClass: "btn-success",
                confirmButtonText: "OK",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
            },
                    function () {
                        window.location = "{{ route('candidate') }}"
                    });
        }
    }
    $(document).ready(function () {
        $('#job_post_finding_options').on('change', function () {
            if (this.value == 3)  // non deletable id
            {
                $("#job_post_referral").removeClass('hide-this-block');
            } else
            {
                $("#job_post_referral").addClass('hide-this-block');
            }
        });

        $('#guardlicence').on("change", function (e) {
         var threshold = (@json($lookups['threshold'] ));
         var currentDate=new Date();
         var today=new Date();
         var enteredLicenceDate = new Date(e.target.value);
         currentDate.setDate( currentDate.getDate()  );
         currentDate.setFullYear( currentDate.getFullYear() );
         currentDate.setMonth( currentDate.getMonth()- threshold );
         var diff_date=new Date((currentDate.getMonth()+1 ) + '/' + (currentDate.getDate()) + '/' + (currentDate.getFullYear()));
         if(enteredLicenceDate > diff_date && enteredLicenceDate < today)
         {
         $("#test_score_block").removeClass('hide-this-block')
         }
         else
         {
         $("#test_score_block").addClass('hide-this-block')
         $("#test_score_block").find('input').val('')
         }
        });
        if($("#wage_last_provider select option:selected").val() !== "") {
            securityProviderOther($("#wage_last_provider select option:selected").html());
        }
        $('.datepicker').on('click', function () {
            $(this).siblings('.gj-icon').click();
           });
        $('.datepicker').attr('readonly', true);
        //Email field should be readonly on edit
        if($('.mail').val())
        {
        $(".mail").prop("readonly", true);
        }
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
        $('textarea').on('keyup', function () {
            $(this).val($(this).val().replace(/[\r\n\v]+/g, ''));
        });
        $('#apply-job-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var imageValue = $('.candidate-image-element').val();
            if((imageValue == "" || imageValue == null) && ($('.candidate-image-div').is(':visible') == true) && ($('#image-element').attr('data-status') == "0")) {
                swal("Error", "Image field is mandatory.", "error");
                return false;
            }
            var formData = new FormData($('#apply-job-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            resize.croppie('result', {
                type: 'canvas',
                size: {width:512, height:512},
                quality: 1,
                circle: false
            }).then(function (img) {
                if(imageValue != "" && imageValue != null && ($('.candidate-image-upload').is(':visible') == true)) {
                    formData.append("image", img);
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('applyjob.store') }}",
                    type: 'POST',
                    data: formData,
                    statusCode: {
                        406: function (response) {
                            // window.location = "{{ route('applyjob.logout') }}";
                        }
                    },
                    success: function (data) {
                        if (data.success) {
                            if(image != "" && image != null && image != 'null' && ($('.candidate-image-upload').is(':visible') == true)) {
                                $(".candidate-image-div").html('<img style="border-radius: 50%;" src="'+img+'" data-status="1" height="100px" width="100px" name="image" id="image-element"/>');
                                $('.candidate-image-upload').hide();
                                $('.candidate-image-div').show();
                            }

                            current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                            current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                            current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                            $('html, body').animate({
                                scrollTop: $("form").offset().top
                            }, 1000);
                        } else if (data.job_applied) {
                            swal({
                                title: "Already submitted",
                                text: "It appears you have already applied a job in the past and are already logged in our database. There is no need to apply again.",
                                type: "warning",
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "OK",
                                showLoaderOnConfirm: true,
                                allowEscapeKey: false,
                            },
                            function () {
                                // window.location = "{{ route('applyjob.logout') }}"
                            });
                        } else {
                            alert('A server issue occured,please try after sometime: ' + data.message);
                            // window.location = "{{ route('applyjob.logout') }}";
                        }
                    },
                    fail: function (response) {
                        // window.location = "{{ route('applyjob.logout') }}";
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form);
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });
        $('#screening-uniform-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#screening-uniform-form')[0]);
            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('applyjob.storeUniform') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    let data =jQuery.parseJSON(response)
                    if (data.success) {
                       // showMessageIfUpdate($form);
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);
                    } else {
                        alert(data.message);
                    }
                },
                fail: function (response) {
                    swal("warning","Server error","warning")
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                    let errors=(xhr.responseJSON.errors)

                    $.each(errors, function( index, value ) {
                        //  alert( index + ": " + value );
                        swal("warning",value,"warning")
                    });
                }
            });
        })
        $('#screening-questions-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#screening-questions-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('applyjob.storescreeningquestion') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                       // showMessageIfUpdate($form);
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);
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

        $('#personality-inventory-form').on('keydown keypress',function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });


        $('#personality-inventory-form').on('submit',function (e) {

        e.preventDefault();

        var $form = $('#personality-inventory-form');//$(this);
        //var formData = new FormData($('#personality-inventory-form')[0]);
        //$form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        //console.log('pi',personality_inventory_array);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('applyjob.storepersonality') }}",
                type: 'POST',
                data: {'arr' : personality_inventory_array},
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        $('#questions_length').text('');
                        $('#personality_inventory_question_answer').empty();
                        $('#personality_inventory_question_answer').text('Personality Inventory Successfully Completed');
                        showMessageIfUpdate($form);
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);
                    } else {
                        console.log(data.message);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                //contentType: false,
                //processData: false,
            });
        });


        $('#competency-matrix-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        //console.log('cr',competencyRatingArr);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('applyjob.competencymatrix') }}",
                type: 'POST',
                data: {'arr' : competencyRatingArr},
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');

                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);
                    } else {
                        console.log(data.message);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                //contentType: false,
                //processData: false,
            });
        });


        $('body').on('click', '.backtab', function () {
            current_active_li = $('a[href="#' + $(this).parents('.tab-pane').prop('id') + '"]').parents('li');
            current_active_li.removeClass('active').removeClass('success').prev('li').find('a').trigger('click');
            current_active_li.find('a').addClass('disabled');
            current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
            $('html, body').animate({
                scrollTop: $("ul").offset().top
            }, 1000);
        });
        $('.add-previous-adresses').on('click', function () {
            var length_address = ($(".prev-address-container > label").length - 1 + 1);
            $.ajax({
                url: "{{ route('previousaddress.add') }}",
                type: 'GET',
                data: {id: length_address},
                success: function (data) {
                    $(".prev-address-container:last a.remove-previous-adresses").hide();
                    $('.add-previous-adresses').parents('.form-group').before(data);
                },
                dataType: 'html'
            });
        });

        $('body').on('click', 'a.remove-previous-adresses', function () {
            $(this).parents('.prev-address-container').remove();
            var length_address = ($(".prev-address-container > label").length - 1 + 1);
            if (length_address > 0) {
                $(".prev-address-container:last a.remove-previous-adresses").show();
            }
        });

        $('.add-position').on('click', function () {
            var length_address = ($(".position-container > label").length);
            $.ajax({
                url: "{{ route('position.add') }}",
                type: 'GET',
                data: {id: length_address},
                success: function (data) {
                    $(".position-container:last a.remove-position").hide();
                    var count1_position = ($(".position-container > label").length) + 1;
                    $('.position-container:last').after(data);
                    $('.position-container:last .remove-position').removeClass('hide-this-block');
                    var position = $('.position-container:last .pos').text().slice(0, -1) + count1_position;
                    $('.position-container:last .pos').text(position);
                },
                dataType: 'html'
            });
        });

        $('body').on('click', 'a.remove-position', function () {
            $(this).parents('.position-container').remove();
            var labelValue = $('.position-container:last .pos').text();
            var lastChar = labelValue[labelValue.length - 1];
            if (lastChar > 1) {
                $(".position-container:last a.remove-position").show();
            }
        });

        $('.add-reference').on('click', function () {
            var length_address = ($(".reference-container > label").length);
            $.ajax({
                url: "{{ route('reference.add') }}",
                type: 'GET',
                data: {id: length_address},
                success: function (data) {
                    $(".reference-container:last a.remove-reference").hide();
                    var count1_reference = ($(".reference-container > label").length) + 1;
                    $('.reference-container:last').after(data);
                    $('.reference-container:last .remove-reference').removeClass('hide-this-block');
                    var reference = $('.reference-container:last .pos').text().slice(0, -1) + count1_reference;
                    $('.reference-container:last .pos').text(reference);
                    $(".phone").mask("(999)999-9999");
                },
                dataType: 'html'
            });
        });


        $('body').on('click', 'a.remove-education', function () {
            $(this).parents('.education-container').remove();
            var labelValue = $('.education-container:last .pos').text();
            var lastChar = labelValue[labelValue.length - 1];
            if (lastChar > 1) {
                $(".education-container:last a.remove-education").show();
            }
        });

        $('body').on('click', 'a.remove-language', function () {
            $(this).parents('.language-container').remove();
            let otherlanguage = $("#otherlanguages").val();
            var labelValue = $('.language-container:last .pos').text();
            var lastChar = labelValue[labelValue.length - 1];
            if (lastChar > 1) {
                $(".language-container:last a.remove-language").show();

            }
            if(otherlanguage>0){
                    $("#otherlanguages").val(otherlanguage-1)
                }
        });


        $('.add-education').on('click', function () {
            var length_address = ($(".education-container > label").length);
            $.ajax({
                url: "{{ route('education.add') }}",
                type: 'GET',
                data: {id: length_address},
                success: function (data) {
                    $(".education-container:last a.remove-education").hide();
                    var count1_education = ($(".education-container > label").length) + 1;
                    $('.education-container:last').after(data);
                    $('.education-container:last .remove-education').removeClass('hide-this-block');
                    var education = $('.education-container:last .pos').text().slice(0, -1) + count1_education;
                    $('.education-container:last .pos').text(education);
                },
                dataType: 'html'
            });
        });

        $('.add-languages').on('click', function () {
            let length_address = ($(".language-container > label").length);
            if(length_address<=6){
                $("#otherlanguages").val(length_address-1)
            $.ajax({
                url: "{{ route('language.add') }}",
                type: 'GET',
                data: {id: length_address},
                success: function (data) {
                    $(".language-container:last a.remove-language").hide();
                    var count1_language = ($(".language-container > label").length) + 1;
                    $('.addlang').before(data);
                    $('.language-container:last .remove-language').removeClass('hide-this-block');
                    let language = $('.language-container:last .pos').text().slice(0, -1) + count1_language;
                    $('.language-container:last .pos').text(language);
                    $(".languageblockselectnew").select2();

                },
                dataType: 'html'
            });
            }else{
                swal("Warning","Exceeded maximum of 7 languages","warning")
            }

        });

        $('body').on('click', 'a.remove-reference', function () {
            $(this).parents('.reference-container').remove();
            var labelValue = $('.reference-container:last .pos').text();
            var lastChar = labelValue[labelValue.length - 1];
            if (lastChar > 1) {
                $(".reference-container:last a.remove-reference").show();
            }
        });

        $('#guard_licences').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#guard_licence_start_qstn").removeClass('hide-this-block');
                $("#guard_licence_expiry_qstn").removeClass('hide-this-block');
                $("#security_clearness_expiry_qstn").removeClass('hide-this-block');
            } else
            {
                $("#guard_licence_start_qstn").addClass('hide-this-block');
                $("#test_score_block").addClass('hide-this-block');
                $("#test_score_block").find('input').val('');
                $("#guard_licence_start_qstn").find('input').val('');
                $("#guard_licence_expiry_qstn").addClass('hide-this-block');
                $("#guard_licence_expiry_qstn").find('input').val('');
                $("#security_clearness_expiry_qstn").addClass('hide-this-block');
                $("#security_clearness_expiry_qstn option[value='']").attr('selected', true)
                ;
            }
        });

        $('#use_of_forces').on('change', function() {
            if (this.value == 'Yes')
            {
                $("#use_of_force_question").removeClass('hide-this-block');
            }
            else
            {
                $("#use_of_force_question").addClass('hide-this-block');
                $("#force_certifications").val('');
                $("#use_of_force_question").find('input').val('');
            }
        });

        $('#current_available').on('change', function () {
            if (this.value == 'Full-Time (Around 40 hours per week)')
            {
                $("#availability_explanation").addClass('hide-this-block');
                $("#availability_explanation").find('textarea').val('');
            } else
            {
                $("#availability_explanation").removeClass('hide-this-block');
            }
        });
        $('#position_availibility').on('change', function () {
            if (this.value == 1)
            {
                $("#floater_hours").removeClass('hide-this-block');
                $("#floater_hours").find('input').val('');
            } else
            {
                $("#floater_hours").addClass('hide-this-block');
            }
        });
        $('#shift_work').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#explanation_restrictions").addClass('hide-this-block');
                $("#explanation_restrictions").find('textarea').val('');
            } else
            {
                $("#explanation_restrictions").removeClass('hide-this-block');
            }
        });
        $('#no_clearances').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#no_clearance_explanation").removeClass('hide-this-block');
            } else
            {
                $("#no_clearance_explanation").addClass('hide-this-block');
                $("#no_clearance_explanation").find('textarea').val('');
            }
        });
        $('#transport_limit').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#explanation_transport_limit").removeClass('hide-this-block');
            } else
            {
                $("#explanation_transport_limit").addClass('hide-this-block');
                $("#explanation_transport_limit").find('textarea').val('');
            }
        });
        $('#current_employee').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#current_employee_qstn").removeClass('hide-this-block');
            } else
            {
                $("#current_employee_qstn").addClass('hide-this-block');
                $("#current_employee_qstn").find('input').val('');
            }
        });
        $('#applied_job').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#applied_job_qstn").removeClass('hide-this-block');
            } else
            {
                $("#applied_job_qstn").addClass('hide-this-block');
                $("#applied_job_qstn").find('input').val('');
            }
        });
        $('#employed_job').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#employed_job_qstn").removeClass('hide-this-block');
            } else
            {
                $("#employed_job_qstn").addClass('hide-this-block');
                $("#employed_job_qstn").find('input').val('');
            }
        });
        $('#canadian_army').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#canadian_army_qstn").removeClass('hide-this-block');
            } else
            {
                $("#canadian_army_qstn").addClass('hide-this-block');
                $("#canadian_army_qstn").find('input').val('');
            }
        });
        $('#asked_resign').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#explanation_dismissed").removeClass('hide-this-block');
            } else
            {
                $("#explanation_dismissed").addClass('hide-this-block');
                $("#explanation_dismissed").find('textarea').val('');
            }
        });
        $('#limitation').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#limitation_explain").removeClass('hide-this-block');
            } else
            {
                $("#limitation_explain").addClass('hide-this-block');
                $("#limitation_explain").find('textarea').val('');
            }
        });
        $('#criminal_td').on('change', function () {
            if (this.value == 'Yes')
            {
                $("#crime").removeClass('hide-this-block');
            } else
            {
                $("#crime").addClass('hide-this-block');
                $("#crime").find('input').val('');
            }
        });

        $('#work_status_in_canada').on('change',function(){
            if($('#work_status_in_canada :selected').text() == 'Landed Immigrant')
            {
                $("#landed_immigrant").removeClass('hide-this-block');
            }else{
                $("#landed_immigrant").addClass('hide-this-block');
            }
        });

        $('input[type=radio][name=social_insurance_number]').on('change',function(){
            if( this.value == 1)
            {
                $("#sin_expiry_date_status_div").removeClass('hide-this-block');
            }else{

                $("#sin_expiry_date_status_div").addClass('hide-this-block');
                $("#sin_expiry_date_div").addClass('hide-this-block');
            }
        });

        $('#security_clearance').on('change',function(){
            if($('#security_clearance :selected').text() == 'Yes')
            {
                $("#security_clearance_type_div").removeClass('hide-this-block');
                $("#security_clearance_expiry_date_div").removeClass('hide-this-block');
            }else{
                $("#security_clearance_type_div").addClass('hide-this-block');
                $("#security_clearance_type_div").find('input').val('');
                $("#security_clearance_expiry_date_div").addClass('hide-this-block');
                $("#security_clearance_expiry_date_div").find('input').val('');
            }
        });


        $('input[type=radio][name=sin_expiry_date_status]').on('change',function(){
            if( this.value == 1)
            {
                $("#sin_expiry_date_div").removeClass('hide-this-block');
            }else{

                $("#sin_expiry_date_div").addClass('hide-this-block');
            }
        });

        if($('#wage_last_provider_other label').find('.mandatory').length <= 0){
            $('#wage_last_provider_other label').append('<span class="mandatory">*</span>');
        }

    });

    $('input[name="same_address_check"]').on("click",function(e){
            let address = $('input[name="address"]').val()+","+
                $('input[name="city"]').val()+","+
                $('input[name="postal_code"]').val();

            if($(this).is(":checked")){
                $('textarea[name="shipping_address"]').val(address)
                $('textarea[name="shipping_address"]').prop("readonly",true)
            }else{

                $('textarea[name="shipping_address"]').prop("readonly",false)
            }
        })

    $('.wage_provider').change(function() {
        var security_provider=$(this);
        if((security_provider.val()!='')&&(security_provider.val()!="15"))
        {
            $(".security_provider_details").val("");
            $("#security_provider_details_block").removeClass("hide-this-block");
        }
        else
        {
            $(".security_provider_details").val("");
            $("#security_provider_details_block").addClass("hide-this-block");
        }
        });


       //  $('.wage_provider').trigger('change');

        if(($('.wage_provider').val()==""))
       {
          $("#security_provider_details_block").addClass("hide-this-block");
       }

     /**
     * Generate Questions with appended security name
     * @param {type} security_name
     * @returns {undefined}
     */
        function replacingSecurityDetailsHtml(security_name)
        {
        $("#security_provider_details_block").removeClass("hide-this-block");
        $( "#security_provider_strengths").find($("label")).html('What were  the strengths of'+' '+ security_name +'?'+'<span class="mandatory">*</span>');
        $( "#security_provider_notes").find($("label")).html('What do you hope to get from Commissionaires that you feel '+' '+ security_name +' was not providing?'+'<span class="mandatory">*</span></span>');
        $( "#rate_experience").find($("label")).html('How would you rate your experience at'+' '+ security_name +'?'+'<span class="mandatory">*</span>');
        }

     /**
     *
     * @param {type} security_namewage_provider
     * @returns {undefined}
     */
    function securityProviderOther(security_name, security_val) {
        if (security_name == "Other") {
            $("#wage_last_provider_other").removeClass("hide-this-block");
            $("[name='wage_last_provider_other']").attr('required',true);
        } else {
            $("#wage_last_provider_other").addClass("hide-this-block");
            $("[name='wage_last_provider_other']").attr('required',false);
            if(security_val !== "") {
                replacingSecurityDetailsHtml(security_name);
            }
        }
    }

    /**
     *
     * @param {type} $this
     * @returns {undefined}
     */
    function validateFileSize($this) {
        var input, file;
        input = $this;
        file = input.files[0];
        //console.log((3 * 1024 * 1024), file.size);
        if ((3072 * 1024) < (file.size)) {
            swal({
                            title: "Bigger File",
                            text: "Please upload a file of size below 3MB",
                            type: "warning",
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "OK",
                            showLoaderOnConfirm: true,
                            allowEscapeKey:false,
                        });
            input.value = "";
        }
    }


  function removeAttachment(candidate_id,attachment_id) {
    swal({
    title: "Are you sure?",
    text: "You will not be able to recover this file",
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Yes, delete it",
    closeOnConfirm: false
    },
        function(){
        var url = "{{ route('candidate.remove-attachment',[':candidate_id',':attachment_id']) }}";
            url = url.replace(':candidate_id', candidate_id);
            url = url.replace(':attachment_id', attachment_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $("#upload_file_div_"+attachment_id).show();
                    $("#upload_btn_div_"+attachment_id).show();
                    $("#attachment_remove_div_"+attachment_id).hide();
                    $("#attachment_name_div_"+attachment_id).hide();
                    $('#attachment_file_name\\.'+attachment_id).removeClass('success');
                },
                dataType: 'html'
            });
        swal("Deleted", "File has been deleted.", "success");
    });

  }
  function removeTestScoreDocument(item)
  {
    $(item).parents().eq(2).empty().append('<input type="file" class="form-control" name="test_score_document_id"><div class="form-control-feedback">{!! $errors->first('test_score_document_id') !!}<span class="help-block text-danger align-middle font-12"></span></div>')

  }

  function removeForceDocument(item)
  {
    $(item).parents().eq(2).empty().append('<input type="file" class="form-control" name="force_file" onchange="validateFileSize(this);"><div class="form-control-feedback">{!! $errors->first('force_file') !!}<span class="help-block text-danger align-middle font-12"></span></div>')
  }

</script>
<style type="text/css">
   .candidate-screen a.score-document
    {
        background: #e9ecef !important;
        color:#007bff;
    }
</style>
@stop
