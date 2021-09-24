{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'User Skill Option')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>User Skill Option</h3>
@stop
@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'recruitment.customer-uniform-kits.add','id'=>'user-skill-option-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <input type="hidden" name="id" value="{{ $option_id}}"/>
        <div class="form-group row" id="option_name">
          <label class="col-form-label col-md-2">  Option Name <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <input type="text" name="option_name" value= "{{ $skillOptionName}}" class="form-control">
                 <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="skill_id">
          <label class="col-form-label col-md-2">  Choose Skill <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <select   name="skill_id[]" class="form-control select2" multiple>
                  {{--   <option value="Please Select" selected>Please Select</option> --}}
                    @foreach($skill as $projectid => $eachSkill)
                   <option value="{{$eachSkill->id}}" @if(in_array($eachSkill->id, $userSkillArr)) selected='selected' @endif >{{$eachSkill->name}}</option>
                   @endforeach
                </select>
                 <span class="help-block"></span>
            </div>
        </div>


        <br>
        <div class="table-responsive">
            <table class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info"  id="add-table">
                <thead>
                    <tr>
                       {{--  <th class="sorting_disabled" style="white-space: nowrap">Document</th> --}}
                        <th class="sorting_disabled" style="white-space: nowrap">Option Value</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Order</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="option-values">

                @if(isset($userSkillOptionValue) && !$userSkillOptionValue->isEmpty())
                        @foreach($userSkillOptionValue as $key=>$eachUserSkillOptionValue)
                        <tr role="row" class="option-row">
                   
                             <td>
                              <input type="hidden" class="value_id" name="value_id[]" value="{{ $eachUserSkillOptionValue->id }}"/>
                                <div class="form-group option_value" id="option_value_{{isset($key)?($key):"0"}}">
                                    <input  class="form-control cls-option-name"   type="text" name="option_value[]" value="{{$eachUserSkillOptionValue->name}}"/>
                                         <span class="help-block"></span>
                                </div>
                             </td>

                                <td  align="center" >
                                   <div class="form-group order" id="order_{{isset($key)?($key):"0"}}">

                                    <input  class="form-control cls-option-order" style="width: 68px;" type="text" name="order[]" value="{{$eachUserSkillOptionValue->order}}"/>        <span class="help-block"></span>
                                </div>
                               </td>
                                <td  align="center" class="sorting_disabled">
                                 <div class="input-group">
                                     <span>

                                              <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                           <i class="fa fa-minus" aria-hidden="true" onclick="removeOptionValue(this)"
                                            {{-- @if(!isset($key) || (isset($key) && $key == 0) || ($key <= count($doc_list_list)))
                                            style="display: none;"
                                            @endif --}}
                                            ></i>
                                     </a>
                                 </span>

                                 <span>

                                    <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn">
                                        <i class="fa fa-plus" aria-hidden="true" onclick="questionsObj.addQuestion(this)"></i>

                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else

               <tr role="row" class="option-row">

                  {{--   <td>
                        <input type="hidden" class="id" name="id[]" value=""/>
                        <div class="form-group document_id" id="document_id_0">
                        <select style="margin-top:5px;" class="form-control document"  name="document_id[]" onchange="documentName(this)">
                            <option value="0"  >Please Select</option>
                                @if(!empty($onboarding_documents))
                                @foreach ($onboarding_documents as $eachItem)
                                        <option value="{{ $eachItem->id }}">{{$eachItem->document_name}} </option>
                               @endforeach
                               @endif
                        </select>
                         <span class="help-block"></span>
                        </div>
                    </td> --}}
                    <td>
                         <input type="hidden" class="value_id"  name="value_id[]" value=""/>
                        <div class="form-group option_value" id="option_value_0">
                        <input  class="form-control"   type="text" name="option_value[]" value=""/>
                           <span class="help-block"></span>
                        </div>
                     </td>

                     <td align="center">
                        <div class="form-group order" id="order_0">
                         <input  class="form-control cls-option-order" style="width: 68px;"  type="text" name="order[]" value=""/>
                        <span class="help-block"></span>
                     </div>
                    </td>
                    <td  align="center" class="sorting_disabled">
                     <div class="input-group">
                         <span
                            <a title="Remove Option" href="javascript:;"   class="remove_button" disabled="true" onclick="questionsObj.removeQuestion(this)" >
                             <i class="fa fa-minus"  disabled="true" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                         </a>
                     </span
                     <span>
                        <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                     </span>
                        </div>
                    </td>
                </tr>
    @endif
        </tbody>
    </table>
</div>

<div class="modal-footer">
    <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
    <a href="" class="btn btn-primary blue">Cancel</a>
</div>

</section>
{{ Form::close() }}
</div>
@stop


@section('js')
<script>
$(function () {

    $('.select2').select2();

    questionsObj = {
        childArray: [],
        questionElementCount: 1,
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
            var htmlStr = questionsObj.questionRowHtml.replace(/readonly="true"|fa-disabled/g,"");
            $('#option-values').append(htmlStr);
            var newHtmlObj = $('#option-values tr:last');
            this.prepareNextRow(newHtmlObj);
            questionsObj.endLoading();
        },
        removeQuestion: function(currObj){
                var removedPos = $(currObj).closest('tr').find('input[name="position[]"]').val();
                var removedPosition = $(currObj).closest('tr').prevAll().length+1;
                position_num=removedPosition-1;
                $(currObj).closest('tr').nextAll().each(function( index,value ) {
                    $(value).find('.option_value').attr("id", 'option_value_'+position_num);
                    $(value).find('.order').attr("id", 'order_'+position_num);
                   position_num++;
               });
                $(currObj).closest('tr').remove();

            },
            getQuestionRow:function(){
                var htmlText = '<tr role="row" class="option-row">'+$('#option-values tr:first').html()+'</tr>';
                return htmlText.replace(/checked="checked"/g, "");
            },
            prepareNextRow: function(htmlObj){
                    //reset values
                    console.log(htmlObj)
                    $(htmlObj).find(".cls-option-name").val('');
                    $(htmlObj).find(".cls-option-info").val('');
                    $(htmlObj).find(".cls-option-order").val('');
                    var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                    $(htmlObj).find(".cls-slno").html(positionHtml);

                    $(htmlObj).find(".fa-minus").show();
                    $(htmlObj).find(".option_value").attr('id','option_value_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".order").attr('id','order_'+(this.questionElementCount*1-1));
                     //Add id value for the newly added textbox to show validation messages
                     position_num=$('.option-row').length-1;
                      $(".value_id:last").val('');
                     $(".option_value:last").attr("id", 'option_value_'+position_num);
                     $(".order:last").attr("id", 'order_'+position_num);


                     var total_count = $('.cls-slno' ).length-1;

                 }
         }
         questionsObj.startLoading();
         questionsObj.questionRowHtml = questionsObj.getQuestionRow();
         questionsObj.endLoading();

         $('#add-table').dataTable({
                "bPaginate": false
          });



         $('#user-skill-option-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            // $('select[name="info_id"]').prop('disabled',false);
           // for (instance in CKEDITOR.instances)
          //  CKEDITOR.instances[instance].updateElement();
            url = "{{ route('user-skill-option.store') }}";
            var formData = new FormData($('#user-skill-option-form')[0]);
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
                            result = "Skill Option has been  updated successfully";
                        }else{
                            result = "Skill Option has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                      },function(){
                        // $('.form-group').removeClass('has-error').find('.help-block').text('');
                        window.location.href = "{{ route('user-skill-option') }}";
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

    });


   function removeOptionValue(currObj) {
       var id = $(currObj).closest('tr').find('input[name="value_id[]"]').val();
        var base_url = "{{ route('user-skill-option-value.destroy',':id') }}";
        var url = base_url.replace(':id', id);
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this action. Proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, remove",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
        function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        swal("Deleted", "Option Value has been deleted successfully", "success");
                        questionsObj.removeQuestion(currObj);
                        if ($('#option-values tr').length === 0) {
                            questionsObj.addQuestion();
                            
                        }

                    }else{
                        swal("Warning",data.message, "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                },
                contentType: false,
                processData: false,
            });
        });
   }

</script>

@stop
