{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Templates')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Site Supervisor Templates</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'templates.add','id'=>'template-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <div class="form-group row" id="template_name">
            <input type="hidden" name="id" value="{{$template_arr['id'] ?? ''}}"/>
            <label class="col-form-label col-md-2" for="template_name">Template Name </label>
            <div class=" col-md-10">
                <input type="text" class="form-control" placeholder="Name" name="template_name" value="{{$template_arr['template_name'] ?? ''}}" required>
                <span class="help-block"></span>
            </div>
        </div>

        <div class="form-group row" id="template_description">
            <label class="col-form-label col-md-2" for="template_description">Description</label>
            <div class=" col-md-10">
                <textarea class="form-control" name="template_description" id="" cols="145" rows="4" placeholder="Description"  name="template_description" >{{$template_arr['template_description'] ?? ''}}</textarea>
                <span class="help-block"></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-form-label col-md-2 col-xs-12 col-sm-12">Effective Date </label>
            <label class="col-form-label control-label col-xs-12 col-md-1 col-sm-6">From <span class="mandatory">*</span></label>
            <div class="col-md-3 col-xs-12 col-sm-6">
                <div class="form-group input-group date" data-provide="datepicker" id="start_date">
                    <input type="text" class="form-control datepicker" name="start_date" value="{{$template_arr['start_date'] ?? ''}}" required>
                    <span class="help-block"></span>
                </div>
            </div>
            <label class=" col-form-label col-md-1 col-sm-12 col-xs-12"></label>
            <label class=" col-form-label control-label col-xs-12 col-md-1 col-sm-6">To <span class="mandatory">*</span></label>
            <div class="col-md-3 col-xs-12 col-sm-6">
                <div class="form-group input-group date" data-provide="datepicker" id="end_date">
                    <input type="text" class="form-control datepicker" name="end_date" value="{{$template_arr['end_date'] ?? ''}}" required>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>

        <h4 class="color-template-title">Template Questions</h4>
        <div class="table-responsive">
            <table class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info">
                <thead>
                    <tr>
                        <th class="sorting_disabled" style="white-space: nowrap">#</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Question Category</th>
                        <th class="sorting_disabled" >Parent Questions</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Question Text</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Answer Type</th>
                        <th class="sorting_disabled">Multi Answer</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 100px;">Show If</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 115px;">Score</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="template-questions">
                    @if(isset($template_form_arr))
                        @foreach($template_form_arr as $key=>$template_form)
                        {{-- tr replaced from js --}}
                        <tr role="row" class="template-row">
                            @include('admin::partials.questionrow')
                        </tr>
                        {{-- tr replaced from js --}}
                        @endforeach
                    @else
                        <tr role="row"  class="template-row">
                            @include('admin::partials.questionrow')
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
            <a href="{{ route('templates') }}" class="btn btn-primary blue">Cancel</a>
        </div>

    </section>
    {{ Form::close() }}
</div>
@stop


@section('js')
<script>

    $(function () {
        questionsObj = {
            childArray: [],
            questionElementCount: {{$last_template_position  ?? 1}},
            questionRowHtml: "",

            disableChildAsParent: function(){
                //function to update visiblity of child and parent option for "Parent Question" select
                $("tr.template-row .cls-parent-question-select option").each(function( index ) {
                    if($.inArray($(this).val(),questionsObj.childArray) >= 0){
                        $(this).hide();
                        $(this).removeClass('parent-option');
                        $(this).addClass('child-option');
                        var selectVar = $(this).closest('select');
                        if( selectVar.val() == $(this).val()){
                            selectVar.prop('selectedIndex',0)
                        }
                    } else{
                        $(this).removeClass('child-option');
                        $(this).addClass('parent-option');
                        var slnoVal = $(this).parents('tr').find('.cls-slno input[type="hidden"]').val()

                        var option_count = $(this).parents().find('.cls-parent-question-select option[value="'+slnoVal+'"]:selected').length;
                        if(option_count == 0){
                            $(this).show();
                        }
                    }
                });
            },
            updateChildArray: function(){
                //function to keep track of all child question in an array
                $('tr.template-row').each(function( index ) {
                    var eachOptionValue = $(this).find("select.cls-parent-question-select").val();
                    var slnoHiddenVal = $(this).find(".cls-slno input[type='hidden']").val();
                    if(eachOptionValue !== "NA" && $.inArray(slnoHiddenVal,questionsObj.childArray) < 0){
                        questionsObj.childArray.push(slnoHiddenVal);
                    } else if(eachOptionValue == "NA"){
                        var valueIndex = questionsObj.childArray.indexOf(slnoHiddenVal);
                        if (valueIndex > -1) {
                            questionsObj.childArray.splice(valueIndex, 1);
                        }
                    }
                });
            },
            showFirstLastParent:function(){
                //show first and last option for a new question added
                $("tr.template-row .cls-parent-question-select").each(function( index ) {
                    $(this).find('option.parent-option').not(':first,:last').hide();
                });
            },
            updateParentQuestionSelect: function(){
                questionsObj.updateChildArray();
                questionsObj.disableChildAsParent(); //hide children options
                questionsObj.showFirstLastParent(); //hide all parents except first and last
            },
            startLoading: function(){

                $('body').loading({

                    stoppable: false,
                    message: 'Please wait...'

                });



            },
            endLoading: function(){
                $('body').loading('stop');
            },
            addQuestion: function(currObj){
                questionsObj.startLoading();
                this.questionElementCount++;
                var htmlStr = questionsObj.questionRowHtml;
                $('#template-questions').append(htmlStr);
                var newHtmlObj = $('#template-questions tr:last');
                this.prepareNextRow(newHtmlObj);
                questionsObj.endLoading();
            },
            removeQuestion: function(currObj){
                // Get the position of row to be removed
                var removedPos = $(currObj).closest('tr').find('input[name="position[]"]').val();
                var removedPosition = $(currObj).closest('tr').prevAll().length+1
                //Append id value to yes and no textbox to show validation messages
                 position_num=removedPosition-1;
                 $(currObj).closest('tr').nextAll().each(function( index,value ) {
                 $(value).find('.yes_value').attr("id", 'yes_value_'+position_num);
                 $(value).find('.no_value').attr("id", 'no_value_'+position_num);
                 position_num++;
              });
                //Remove the row from each and every option
                $(document).find('.parent_question option[value="'+removedPos+'"]').each(function(){
                    var questionTypeSelect  = $(this).closest('tr').find('.cls-question-type select');
                    var answerType = $(this).closest('tr').find('.answer_type');
                    if($(this).prop('selected')){
                        $(this).closest('tr').find('.question_type select').removeAttr('readonly');
                        $(this).closest('tr').find('.question_type select option').show();
                        $(answerType).find('.parent-question-option').show();
                        $(answerType).find('.child-question-option').hide();
                        $(answerType).find('option').removeAttr('selected');
                        $(answerType).find('option:first').attr('selected');
                    }
                    $(this).remove();
                });
                // Make the parent question of this row to NA to change properties of parents if any
                var parentQuestionOption =  $(currObj).closest('tr').find('.cls-parent-question');
                $(parentQuestionOption).find('option').removeAttr('selected');
                $(parentQuestionOption).find('option:first').prop('selected',true)
                $(parentQuestionOption).find('select').trigger('change');
                $(currObj).closest('tr').remove();
                questionsObj.updateParentQuestionSelect();
            },
            getQuestionRow:function(){
                var htmlText = '<tr role="row" class="template-row">'+$('#template-questions tr:first').html()+'</tr>';
                return htmlText.replace(/checked="checked"/g, "");
            },
            prepareNextRow: function(htmlObj){
                //reset values
                $(htmlObj).find(".cls-element-id").val('');
                $(htmlObj).find(".cls-question-type option:first").attr('selected');
                $(htmlObj).find(".cls-question-text textarea").html('');
                $(htmlObj).find(".cls-multiple-answers").removeAttr('checked');
                $(htmlObj).find(".cls-show-if").hide();
                $(htmlObj).find(".cls-show-if input[type='radio']").removeAttr('checked');
                $(htmlObj).find(".fa-minus").show();
                $(htmlObj).find(".question_type select").removeAttr('readonly');
                $(htmlObj).find(".question_type select option").show();
                $(htmlObj).find(".cls-parent-question-select").removeAttr('readonly');
                $(htmlObj).find(".cls-score input[type='text']").val('');
                $(htmlObj).find(".cls-score input[type='text']").prop('required',true);
                $(htmlObj).find(".question_type dropdown").attr('id','question_type_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".parent_question").attr('id','parent_question_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".question_text").attr('id','question_text_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".answer_type").attr('id','answer_type_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".yes_value").attr('id','yes_value_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".no_value").attr('id','no_value_'+(this.questionElementCount*1-1));

                var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                $(htmlObj).find(".cls-slno").html(positionHtml);

                $(".cls-length-id").val($('.template-row').length);
                //   var idHtml = '<input type="hidden" name="position[]" value="'+$('.template-row').length+'"/>';
                // $(htmlObj).find(".cls-length-id").html(positionHtml);

                 //Append id value for the newly added yes and no textbox to show validation messages
                position_num=$('.template-row').length-1
                $(".yes_value:last").attr("id", 'yes_value_'+position_num);
                $(".no_value:last").attr("id", 'no_value_'+position_num);
                var total_count = $('.cls-slno' ).length-1;
                var parentQuestionSelect = $(htmlObj).find(".cls-parent-question select");
                $('.cls-slno').each(function( index ) {
                    if(index < (total_count)){
                        var parentQuestionOptions = "<option id='"+$( this ).text().trim()+"' value='"+$( this ).text().trim()+"'>"+$( this ).text()+"</option>";
                        parentQuestionSelect.append(parentQuestionOptions);
                    }
                });
                $(htmlObj).find(".cls-show-if input[type='radio']").attr('name','show_if['+questionsObj.questionElementCount+']');
                $(htmlObj).find(".cls-multiple-answers").attr('name','multiple_answers['+questionsObj.questionElementCount+']');

                questionsObj.updateParentQuestionSelect();

            }
        }
        questionsObj.startLoading();
        questionsObj.questionRowHtml = questionsObj.getQuestionRow();
        questionsObj.updateParentQuestionSelect();
        questionsObj.endLoading();

        $(document).on("change",".cls-parent-question-select",function(){
            questionsObj.startLoading();

            /*** block to remove the parent "Show if/ Score" if no children present ***/
            var selectedParent = $(this).data("prev");
            var totalChildren = $('select.cls-parent-question-select option[id="'+selectedParent+'"]:selected').length;
            if(totalChildren === 0){
                $(this).parents().find('.cls-slno').each(function( index ) {
                    if($(this).text().trim() == selectedParent){
                        var closestTrShowif = $(this).closest("tr").find(".cls-show-if");
                        var closestTrScore = $(this).closest("tr").find(".cls-score");
                        var closestTr = $(this).closest("tr");
                        closestTrShowif.hide();
                        closestTrShowif.find("input").prop('required',false);
                        closestTrShowif.find('input[type=radio]').prop('checked', false);
                        closestTrShowif.find('input[type=text]').val('');
                        closestTr.find('input[type=checkbox]').prop('checked', false);
                        closestTr.find(".cls-multiple-answers").hide();
                        // Enable all question types when no more children
                        closestTr.find(".parent_question select,.question_type select").removeAttr('readonly');
                        closestTr.find(".question_type select option").show();
                        closestTr.find(".parent_question select option").show();
                    }
                });
                $(this).data("prev",null);
            }
            // Formating other fields
            if($(this).val() !== "NA"){   // child question
                $(this).closest("tr").find(".cls-score").find('input').prop('required',false);
                $(this).parent().closest("tr").find(".cls-score").find('input').prop('required',false);
                $(this).parent().closest("tr").find(".cls-score input").val("");
                $(this).closest("tr").find(".cls-score").hide();
                $(this).data("prev",$(this).val()); // Save the parent
                $(this).parents().closest("tr").find(".child-question-option").show(); //display answer options for child question
                $(this).parents().closest("tr").find(".child-question-option:first").prop('selected','selected'); //make the available option selected
                $(this).parents().closest("tr").find(".parent-question-option").hide(); //hide answer options (yes/no) for parent question

                var selectedParent = $(this).parents().closest("tr").find(".cls-parent-question-select").val();  // Saving the parent question
                var parentQuestionType = 0;
                //Display show if of the parent
                $(this).parents().find('.cls-slno').each(function( index ) {
                    if($(this).text().trim() == selectedParent){
                        var closestTrShowif = $(this).closest("tr").find(".cls-show-if");
                          var closestTrScore = $(this).closest("tr").find(".cls-score");
                        var closestTr = $(this).closest("tr");
                        closestTrShowif.show();
                        closestTrShowif.find("input").prop('required',true);
                        $(this).parents().closest("tr").find(".cls-multiple-answers").show();
                        parentQuestionType = closestTr.find(".question_type select").val();
                        // making question types disabled for parent.. will be enabled when no more children
                        closestTr.find(".question_type select")[0].setAttribute('readonly',true);
                        closestTr.find(".parent_question select")[0].setAttribute('readonly',true);
                        closestTr.find(".question_type select option").hide();
                        closestTr.find(".question_type select option[value="+parentQuestionType+"]").show();
                        closestTr.find(".parent_question select option").hide();
                        closestTr.find(".parent_question select option:first").show()
                    }
                });
                // making question types disabled for children.. will be enabled when not child anymore
                $(this).closest("tr").find(".question_type select")[0].setAttribute('readonly',true);
                $(this).closest("tr").find(".question_type select option").hide();
                $(this).closest("tr").find(".question_type select option[value="+parentQuestionType+"]").show();
                $(this).closest("tr").find(".question_type select option[value="+parentQuestionType+"]").prop("selected","selected");
            } else{// parent question
                 $(this).closest("tr").find(".cls-score").find('input').prop('required',true);
                var closestTrScore = $(this).closest("tr").find(".cls-score");
                closestTrScore.find("input").prop('required',true);
                closestTrScore.find("input").val("");
                $(this).closest("tr").find(".cls-score").show();
                var closestTr = $(this).parents().closest("tr");
                $(closestTr).find(".parent-question-option").show(); //display answer options (yes/no) for parent question
                $(closestTr).find(".parent-question-option:first").prop('selected','selected'); //make the available option selected
                $(closestTr).find(".child-question-option").hide(); //hide answer options for child question
                //$(this).parents().closest("tr").find(".question_type select option").removeAttr('disabled');
                $(this).closest("tr").find(".question_type select").removeAttr('readonly');
                $(closestTr).find(".question_type select option").show();
            }

            questionsObj.updateParentQuestionSelect();
            questionsObj.endLoading();
        });

        //Change the question text on choosing the answer type 'Leave Type'
        $(document).on("change", ".cls-answer-type", function(){
            if($(this).val() == 4){   //Here Answer Type '4' is Leave Type
                $(this).closest("tr").find("textarea").prop('required',false);
                $(this).closest("tr").find("textarea").prop('readonly',true);
                $(this).closest("tr").find("textarea").val('What is their name and employee number?');
            }else{
                $(this).closest("tr").find("textarea").prop('required',true);
                $(this).closest("tr").find("textarea").prop('readonly',false);
                $(this).closest("tr").find("textarea").val('');
            }
        });

        $('#template-add-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('templates.add') }}";
            var formData = new FormData($('#template-add-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        if(data.result == false){
                            result = "Template has been updated successfully";
                        }else{
                            result = "Template has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('templates') }}";
                        });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function () {
            $('#position-form').find('input[name="id"]').val('0');
        });

    });
</script>
<style>
.event{
    z-index:999;
}
</style>
@stop
