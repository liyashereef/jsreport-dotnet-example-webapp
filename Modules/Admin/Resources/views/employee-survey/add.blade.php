{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Templates')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Employee Survey Template</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'templates.add','id'=>'template-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <div class="form-group row" id="template_name">
            <input type="hidden" name="id" value="{{$template_arr['id'] or ''}}"/>
            <label class="col-form-label col-md-2" for="template_name">Template Name </label>
            <div class="col-md-10">
                <input type="text" class="form-control" placeholder="Name" name="template_name" value="{{$template_arr['survey_name'] or ''}}" required  @if(isset($is_view)) readonly @endif>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="customer_id">
            <label class="col-form-label col-md-2" for="template_description">Customers</label>
            <div class="col-md-10">
               <select name="customer_id[]" @if(isset($is_view)) disabled="disabled" @endif id="customerid" class="form-control select2" multiple='multiple'>
                   <option value="0" @if(isset($template_arr) && $template_arr['customer_based']==0) selected @endif>All Customers</option>
                    @foreach($customer_list as $id=>$data)    
                    <option value="{{$data->id}}" @if(isset($customer_arr) &&  in_array($data->id,$customer_arr))selected @endif>{{$data->project_number}}-{{$data->client_name}}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="role_id">
            <label class="col-form-label col-md-2" for="template_description">Roles</label>
            <div class=" col-md-10">
                 <select name="role_id[]" @if(isset($is_view)) disabled="disabled" @endif id="roleid" class="form-control select2" multiple='multiple'>
                    <option value="0" @if(isset($template_arr) && $template_arr['role_based']==0) selected @endif>All Roles</option>
                    @foreach($roles as $id=>$data)
                    <option value="{{$data->id}}" @if(isset($role_arr) && in_array($data->id,$role_arr))selected @endif> {{ ucwords(str_replace("_", " ", $data->name))}}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-form-label col-md-2 col-xs-12 col-sm-12">Effective Date </label>
            <label class="col-form-label control-label col-xs-12 col-md-1 col-sm-6">From <span class="mandatory">*</span></label>
            <div class="col-md-3 col-xs-12 col-sm-6">
                <div class="form-group input-group date" data-provide="datepicker" id="start_date">
                    <input type="text"   name="start_date" value="{{$template_arr['start_date'] or ''}}" required class="form-control @if(!isset($is_view)) datepicker" @else " readonly @endif>
                    <span class="help-block"></span>
                </div>
            </div>
            <label class=" col-form-label col-md-1 col-sm-12 col-xs-12"></label>
            <label class=" col-form-label control-label col-xs-12 col-md-1 col-sm-6">To <span class="mandatory">*</span></label>
            <div class="col-md-3 col-xs-12 col-sm-6">
                <div class="form-group input-group date" data-provide="datepicker" id="end_date">
                    <input type="text"  name="end_date" value="{{$template_arr['expiry_date'] or ''}}" required  class="form-control @if(!isset($is_view)) datepicker" @else " readonly @endif>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>

        <h4 class="color-template-title">Template Questions</h4>
        <div class="table-responsive">
            <table class="table table-bordered " role="grid" aria-describedby="position-table_info">
                <thead>
                    <tr>
                        <th class="sorting_disabled" style="white-space: nowrap">#</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Question Text</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Answer Type</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Sequence</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="template-questions">
                    @if(isset($template_form_arr))
                        @foreach($template_form_arr as $key=>$template_form)
                        {{-- tr replaced from js --}}
                        <tr role="row" class="template-row">
                            @include('admin::employee-survey.question-row')
                        </tr>
                        {{-- tr replaced from js --}}
                        @endforeach
                    @else
                        <tr role="row"  class="template-row">
                            @include('admin::employee-survey.question-row')
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            @if(!isset($is_view))
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
            <a href="{{ route('employee-survey-template') }}" class="btn btn-primary blue">Cancel</a>
            @endif
        </div>

    </section>
    {{ Form::close() }}
</div>
@stop


@section('js')
<script>

    $(function () {
          $('#customerid').select2();//Added Select2 to project listing
           $('#roleid').select2();
        questionsObj = {
            childArray: [],
            questionElementCount: {{$last_template_position or 1}},
            questionRowHtml: "",
          
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
                 $(value).find('.sequence').attr("id", 'sequence_'+position_num);
                 $(value).find('.question_text').attr("id", 'question_text_'+position_num);
                 $(value).find('.answer_type').attr("id", 'answer_type_'+position_num);
                 $(value).find('.cls-slno').html(position_num+1);
                 position_num++;
              });
               
               
                $(currObj).closest('tr').remove();
            },
            getQuestionRow:function(){
                var htmlText = '<tr role="row" class="template-row">'+$('#template-questions tr:first').html()+'</tr>';
                return htmlText.replace(/checked="checked"/g, "");
            },
            prepareNextRow: function(htmlObj){
                //reset values
                $(htmlObj).find(".cls-element-id").val('');
                $(htmlObj).find(".cls-answer-type option:first").attr('selected','selected').change();
                $(htmlObj).find(".cls-question-text textarea").html('');
                $(htmlObj).find(".fa-minus").show();
                $(htmlObj).find(".order").val('');
                $(htmlObj).find(".question_text").attr('id','question_text_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".answer_type").attr('id','answer_type_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".sequence").attr('id','sequence_'+(this.questionElementCount*1-1));
                var positionHtml = (this.questionElementCount)+'<input type="hidden" name="position[]" value="'+(this.questionElementCount)+'"/>';
                $(htmlObj).find(".cls-slno").html(positionHtml);

                $(".cls-length-id").val($('.template-row').length);

                 //Append id value for the newly added yes and no textbox to show validation messages
                position_num=$('.template-row').length-1;
                $(".quest_id:last").val('');
                $(".sequence:last").attr("id", 'sequence_'+position_num);
                $(".answer_type:last").attr("id", 'answer_type_'+position_num);
                $(".question_text:last").attr("id", 'question_text_'+position_num);
                $('.cls-slno:last').html(position_num+1);
                var total_count = $('.cls-slno' ).length-1; 

            }
        }
        questionsObj.startLoading();
        questionsObj.questionRowHtml = questionsObj.getQuestionRow();
        questionsObj.endLoading();

      

        $('#template-add-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('employee-survey-template.store') }}";
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
                            window.location.href = "{{ route('employee-survey-template') }}";
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
