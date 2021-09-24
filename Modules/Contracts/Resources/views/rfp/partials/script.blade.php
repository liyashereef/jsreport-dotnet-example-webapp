@section('scripts')
    <script>
        $(function () {
            $('.select2').select2();
            rfpDetailsJsonStr = '{!! str_replace("'","\\'",json_encode($rfpDetails)) !!}'; //handle special characters
            rfpDetails = JSON.parse(JSON.stringify(rfpDetailsJsonStr));
            if (rfpDetails != null) {
                postalCode($('.postal-code'))
            }
            let showHideControlArr = [
                    '#site_visit_available_control',
                    '#q_a_deadline_available_control',
                    '#rfp_contact_title_available_control',
                    '#rfp_contact_address_available_control',
                    '#rfp_phone_number_available_control',
                    '#rfp_email_available_control',
            ];
            showHideControlArr.forEach(showHideControlId => {
                $(showHideControlId).change(function() {
                    showHideControl(showHideControlId);
                });
                showHideControl(showHideControlId);
            });
        });

        function showHideControl(showHideControlId) {
            let showHideControlArr = showHideControlId.split('_');
            showHideControlArr = showHideControlArr.slice(0,-2);
            let showHideElement = showHideControlArr.join('_');

            if(showHideControlId === '#site_visit_available_control') {
                showHideElement =  '#site_visit_deadline';
            } else if(showHideControlId === '#q_a_deadline_available_control'){
                showHideElement =  '#qa_deadline'
            }
            if($(showHideControlId+" option:selected").val() == 0) {
                $(showHideElement).find('input').val('');
                $(showHideElement).hide();
            } else {
                $(showHideElement).show();
            }
        }

        $(".add-submission-dates").click(function () {
            $id = ($(this).parents('.form-group').prev().data('id'));
            if (typeof $id == 'undefined') {
                $id = -1;
            }
            var controlid = $id+1;
            ($(this).parents('.form-group').prev().after('<div class="form-group row submit-date" data-id="' + ($id + 1) + '"><label for="added_label" class="col-sm-5 label_class" id="submission_label_name.' + ($id + 1) + '">{{ Form::text('submission_label_name[]',null,array('placeholder'=>'Label Name','class'=>'form-control ')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></label><div class="col-sm-6 label_value_class" id="submission_label_value.' + ($id + 1) + '"><input type="text" name="submission_label_value[]" placeholder="value" class="form-control datepicker addeddatepickers" id="datecontrol-'+controlid + '" /><div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('submission_label_value', ':message') !!}</div></div><div class="col-sm-1"><a href="javascript:void(0);" class="remove_button" data-id="' + ($id + 1) + '"  title="Remove field" onclick="removeSubmissionBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a></div></div>')).after(function(e){
                $("#datecontrol-"+controlid).datepicker({format: "yyyy-mm-dd", maxDate: "+900y"});
            });
                          
            $('body').on('focus', ".datepicker", function () {
                $(this).datepicker({format: "yyyy-mm-dd", maxDate: "+900y"});
                
            });
        });
        $(".add-execution-dates").click(function () {
            $id = ($(this).parents('.form-group').prev().data('id'));
            if (typeof $id == 'undefined') {
                $id = -1;
            }

            var execcontrolid = $id+1;
            ($(this).parents('.form-group').prev().after('<div class="form-group row execution-date" data-id="' + ($id + 1) + '"><label for="added_label" class="col-sm-5 label_class" id="execution_label_name.' + ($id + 1) + '">{{ Form::text('execution_label_name[]',null,array('placeholder'=>'Label Name','class'=>'form-control')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></label><div class="col-sm-6 label_value_class" id="execution_label_value.' + ($id + 1) + '"><input type="text" name="execution_label_value[]" placeholder="value" class="form-control datepicker addeddatepickers" id="execdatecontrol-'+execcontrolid + '" /><div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('execution_label_value', ':message') !!}</div></div><div class="col-sm-1"><a href="javascript:void(0);" class="remove_button"    data-id="' + ($id + 1) + '"  title="Remove field" onclick="removeExecutionBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a></div></div>')).after(function(e){
                $("#execdatecontrol-"+execcontrolid).datepicker({format: "yyyy-mm-dd", maxDate: "+900y"});

            });
            $('body').on('focus', ".datepicker", function () {
                $(this).datepicker({format: "yyyy-mm-dd", maxDate: "+900y"});
            });
        });

        
        $('#rfp-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#rfp-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('rfp.store') }}",
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                        
                        swal({

                            title: "Saved",
                            text: "The record has been saved",

                            type: "success",
                            confirmButtonText: "OK"
                            },
                            function(isConfirm){
                            if (isConfirm) {
                                $('#rfp-form')[0].reset();
                                window.location = "{{ route('rfp.summary') }}";
                            }
                        });
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    window.location = "{{ route('applyjob.logout') }}";
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#site_unionized select').on('change', function (e) {
            if ($("#site_unionized select option:selected").val() == '1') {
                $('#union_name').show();
            } else {
                $('#union_name').hide();
            }
        });

        $(".add-criteria").click(function () {
            $id = ($(this).parents('.form-group').prev().data('id'));
            ($(this).parents('.form-group').prev().after(
                '<div class="form-group row justify-content-center criteria-date" data-id="' + ($id + 1) + '">' +
                '<div class="col-sm-3 criteria_label_class" id="criteria_name.' + ($id + 1) + '">{{ Form::text('criteria_name[]',null,array('placeholder'=>'Criteria Name','class'=>'form-control pointcontrol')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></div><div class="col-sm-3 point_label_class" id="points.' + ($id + 1) + '">{{ Form::text('points[]',null,array('placeholder'=>'Points','class'=>'form-control')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></div><div class="col-sm-3 note_label_class" id="notes.' + ($id + 1) + '">{{ Form::text('notes[]',null,array('placeholder'=>'Notes','class'=>'form-control')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></div><div class="col-sm-1"><a href="javascript:void(0);" class="remove_button" data-id="' + ($id + 1) + '"  title="Remove field" onclick="removeCriteriaBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a></div></div>'));
        });

        function removeSubmissionBlock(block) {
            deleted_id = block.data('id');
            remaining_block = (block.parents('.form-group').nextAll('.submit-date'));
            block.parents('.form-group').remove().find('input').val('');
            remaining_block.each(function (key, row) {
                $(row).attr('data-id', deleted_id);
                $(row).find('.label_class').attr('id', 'submission_label_name.' + deleted_id);
                $(row).find('.label_value_class').attr('id', 'submission_label_value.' + deleted_id);
                $(row).find('.remove_button').attr('data-id', deleted_id);
                deleted_id++;
            });
        }

        function removeExecutionBlock(block) {
            deleted_id = block.data('id');
            remaining_block = block.parents('.form-group').nextAll('.execution-date');
            block.parents('.form-group').remove().find('input').val('');
            remaining_block.each(function (key, row) {
                $(row).attr('data-id', deleted_id);
                $(row).find('.label_class').attr('id', 'execution_label_name.' + deleted_id);
                $(row).find('.point_label_class').attr('id', 'point_label_name.' + deleted_id);
                $(row).find('.remove_button').attr('data-id', deleted_id);
                deleted_id++;
            });
        }

        function removeCriteriaBlock(block) {
            deleted_id = block.data('id');
            remaining_block = block.parents('.form-group').nextAll('.criteria-date');
            block.parents('.form-group').remove().find('input').val('');
            remaining_block.each(function (key, row) {
                $(row).attr('data-id', deleted_id);
                $(row).find('.criteria_label_class').attr('id', 'criteria_name.' + deleted_id);
                $(row).find('.point_label_class').attr('id', 'points.' + deleted_id);
                $(row).find('.note_label_class').attr('id', 'notes.' + deleted_id);
                $(row).find('.remove_button').attr('data-id', deleted_id);
                deleted_id++;
            });
        }

        $("#rfp-form").on("click", ".plotmap", function (e) {
            var rfp_site_name = $("input[name=rfp_site_name]").val();
            var rfp_site_address = $("input[name=rfp_site_address]").val();
            var rfp_site_city = $("input[name=rfp_site_city]").val();
            var rfp_site_postalcode = $("input[name=rfp_site_postalcode]").val();
            $('input[name=rfp_site_name]').val(rfp_site_name);
            $('input[name=rfp_site_address]').val(rfp_site_address);
            $('input[name=rfp_site_city]').val(rfp_site_city);
            $('input[name=rfp_site_postalcode]').val(rfp_site_postalcode);
        });

        function postalCode($this) {
            var letterNumber = /^[0-9a-zA-Z]+$/;
            if ($this.val().match(letterNumber)) {
                $('#postal-link').remove();
                $this.parents('.form-group').after('<div class="form-group row" id="postal-link">     <label for="rfp_site_postalcode" class="col-sm-5 col-form-label"></label><form action="{{ route('rfp.rfplink') }}" target="_blank" method="POST" id="map_view_submit">  {{csrf_field()}} {{ Form::hidden('rfp_site_name') }} {{ Form::hidden('rfp_site_address') }} {{ Form::hidden('rfp_site_city') }} {{ Form::hidden('rfp_site_postalcode') }}<div class="col-sm-6"> <input type="submit"  value="Plot Site" class="btn submit plotmap" /></div></form></div>');
            } else {
                $('#postal-link').remove();
            }
        }
        $(function() {

            
                var contacttitletext = $("input[name=rfp_contact_title_hidden]").val();
                if(contacttitletext!=""){
                    $('select[name=rfp_contact_title_available]').val("1");
                    
                    $("#rfp_contact_title").show().after(function(e){
                        $("#rfp_contact_title_id").val(contacttitletext);
                    });
                }else{
                    $('select[name=rfp_contact_title_available]').val("0");
                }

                var contactaddresstext = $("input[name=rfp_contact_address_hidden]").val();
                if(contactaddresstext!=""){
                    $('select[name=rfp_contact_address_available]').val("1");
                    
                    $("#rfp_contact_address").show().after(function(e){
                        $("#rfp_contact_address_id").val(contactaddresstext);
                    });
                }else{
                    $('select[name=rfp_contact_address_available]').val("0");
                }

                var contactaddressphone = $("input[name=rfp_phone_number_hidden]").val();
                if(contactaddressphone!=""){
                    $('select[name=rfp_phone_number_available]').val("1");
                    
                    $("#rfp_phone_number").show().after(function(e){
                        $("#rfp_phone_number_id").val(contactaddressphone);
                    });
                }else{
                    $('select[name=rfp_phone_number_available]').val("0");
                }

                var contactaddressemail = $("input[name=rfp_email_hidden]").val();
                if(contactaddressemail!=""){
                    $('select[name=rfp_email_available]').val("1");
                    
                    $("#rfp_email").show().after(function(e){
                        $("#rfp_email_id").val(contactaddressemail);
                    });
                }else{
                    $('select[name=rfp_email_available]').val("0");
                }

          
            
        });
    </script>
@stop
