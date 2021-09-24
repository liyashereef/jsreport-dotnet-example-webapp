<script>

    $(function () {

    });

    $('#Contract-form').submit(function (e) {
            e.preventDefault();
            arr = [];
            var form = $(this);
            var form_values = form.serializeArray();
            each_data = form_values.map(function(data) {
                if(data.name !== '_token'){
                    var prepareData = {};
                    prepareData['question_id'] =  data.name.replace('answer_id_','');
                    prepareData['answer'] = (data.value !== undefined) ? data.value : null ;
                    answer_type = $('[name="'+data.name+'"]').data('answer-type');
                    prepareData['answer_type'] = (answer_type !== undefined) ? answer_type : null;
                    arr.push(prepareData);
                }
            });
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('capacitytool.store')}}",
                type: 'POST',
                data: {'arr':arr},
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        console.log(data);
                        swal({
                            title: 'Success',
                            text: 'New entry has been saved successfully',
                            icon: "success",
                            type: 'success',
                            button: "OK",
                        }, function () {
                            window.location = "{{ route('capacitytool') }}";
                        });

                    } else {
                        console.log('else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    // alert(xhr.status);
                    // alert(thrownError);
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
    });


    $('#capacity-tool-form').on('change', 'select', function (e) {

        var div_id = this.name.replace('answer_id_', 'question_');
        var question_id = this.name.replace('answer_id_', '');
        var answer_id = this.value;
        $('.parent_'+question_id).remove();
        if($(this).hasClass('project_name')){
            var project_name = $('option:selected', this).attr('rel');
            $('.parent_projectname').remove();
            var childQuestion ="<div class='form-group parent_projectname'>";
            childQuestion +="<div class='form-group row' >";
            childQuestion += "<label class='col-sm-5 col-form-label'>Project Name</label>";
            childQuestion += "<div class='col-sm-6'>";
            childQuestion += "<input type ='text' name='' value='"+project_name+"' required='required' class='form-control' readonly='readonly' >";
            childQuestion += "</div>";
            childQuestion += "<div class='form-control-feedback'><span class='help-block text-danger align-middle font-12'></span></div>";
            childQuestion += "</div>";
            childQuestion += "</div>";
            $(this).closest('div.form-group').after(childQuestion);
        }


        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('capacitytool.subquestion')}}",
            type: 'POST',
            data: {"question_id" : question_id,
                   "answer_id" : answer_id,
                    },
            success: function (data) {
                if (data) {
                    subQuestionArray = [];
                    $.each(data, function(key, value){
                    //console.log(value.answers);
                    var childQuestion ="<div class='form-group parent_"+value.parent_id+"'>";
                        childQuestion +="<div class='form-group row' id='question_"+value.id+"'>";
                        childQuestion += "<label for='question_id_"+value.id+"' class='col-sm-5 col-form-label'>"+value.question+"</label>";
                        childQuestion += "<div class='col-sm-6'>";
                        if(value.field_type == 'dropdown'){
                           childQuestion += "<select name='answer_id_"+value.id+"' class='form-control' required='required' data-answer-type='"+value.answer_type+"'>";
                           childQuestion += "<option selected='selected' value=''>Select</option>";
                           $.each(value.answers, function(option_key, option_value){
                            childQuestion += "<option value="+option_key+" title="+option_value+">"+option_value+"</option>";
                           });
                           childQuestion += "</select>";
                        }
                        else if(value.field_type == 'datepicker'){
                            childQuestion += "<input type ='text' name='answer_id_"+value.id+"' required='required' placeholder='"+value.question+"' class='form-control datepicker' data-answer-type='"+value.answer_type+"'>";
                        }else if(value.field_type == 'textarea'){
                            childQuestion += "<textarea maxlength='1000' name='answer_id_"+value.id+"' required='required' placeholder='"+value.question+"' class='form-control' data-answer-type='"+value.answer_type+"'></textarea>";
                        }
                        else if(value.field_type == 'hours'){
                            childQuestion += "<input type ='number' step='.01' name='answer_id_"+value.id+"' required placeholder='"+value.question+"' class='form-control' maxlength='5' data-answer-type='"+value.answer_type+"'>";
                        }else  if(value.field_type == 'project_name'){
                           childQuestion += "<select name='answer_id_"+value.id+"' class='form-control select2 project_name' required='required' data-answer-type='"+value.answer_type+"'>";
                           childQuestion += "<option selected='selected' value=''>Select</option>";

                           $.each(value.answers, function(option_key, option_value){
                            childQuestion += "<option value="+option_value.id+" rel='"+option_value.client_name+"' title="+option_value.project_number+">"+option_value.project_number+"</option>";
                           });
                           childQuestion += "</select>";
                        }
                        else{
                            childQuestion += "<input type ='text'  name='answer_id_"+value.id+"' required placeholder='"+value.question+"' class='form-control' maxlength='1000' data-answer-type='"+value.answer_type+"'>";
                        }
                        childQuestion += "</div>";
                        childQuestion += "<div class='form-control-feedback'><span class='help-block text-danger align-middle font-12'></span></div>";
                        childQuestion += "</div>";
                        childQuestion += "</div>";
                        subQuestionArray.push(childQuestion);
                        $(".datepicker").mask("9999-99-99");
                    });
                    //Reversing the order of subquestion array and insert the child question below the parent question
                    $.each(subQuestionArray.reverse(), function(key, value){
                        $(value).insertAfter('#'+div_id);
                        $(".select2").select2();
                    });
                } else {
                    console.log('else',data);
                }
            },
            error: function (xhr, textStatus, thrownError) {
                // alert(xhr.status);
                // alert(thrownError);
                console.log(xhr.status);
                console.log(thrownError);
            },
        });
    });



</script>
