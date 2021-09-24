
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Shift Module')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Shift Module</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'customer-shift-module.store','id'=>'shift-module-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
         <div class="form-group row" id="customer_id">
            <input type="hidden" name="module_exists" value="{{$module_exists or ''}}"/>
          <label class="col-form-label col-md-2">  Choose Customer <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <select  name="customer_id" id="customerid" onchange="customerUpdate($(this).val());" class="form-control">
                    <option value=0 selected>Select All</option>
                    @foreach($customer_list as $id=>$data)
                    <option @if($module_id != 0 && $module[0]->customer_id == $data->id) selected @endif value={{$data->id}}>{{$data->project_number}} - {{$data->client_name}}</option>
                    @endforeach
                </select>
                 <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="module_name">
            <input type="hidden" name="id" value="{{$module[0]->id or ''}}"/>
            <label class="col-form-label col-md-2" for="module_name">Module Name <span class="mandatory">*</span></label>
            <div class=" col-md-4">
                <input type="text" class="form-control" placeholder="Maximum character limit is 20" name="module_name" value="{{$module[0]->module_name or ''}}" >
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="enable_timeshift">

            <label class="col-form-label col-md-2" for="enable_timeshift">Enable Time Shift <span class="mandatory">*</span></label>
            <div class=" col-md-4">
            <label> <input type="radio" name="enable_timeshift" @if(isset($module[0]->enable_timeshift) && ($module[0]->enable_timeshift== 1)) checked @endif  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
            <label> <input type="radio" name="enable_timeshift" @if(isset($module[0]->enable_timeshift) && ($module[0]->enable_timeshift== 0)) checked @endif  value="0" >&nbsp;No&nbsp;&nbsp;</label>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row hidden" id="dashboard_view">

            <label class="col-form-label col-md-2" for="dashboard_view">Show on Dashboard<span class="mandatory">*</span></label>
            <div class=" col-md-4">
            <label> <input type="radio" name="dashboard_view" @if(isset($module[0]->dashboard_view) && ($module[0]->dashboard_view== 1)) checked @endif  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
            <label> <input type="radio" name="dashboard_view" @if(isset($module[0]->dashboard_view) && ($module[0]->dashboard_view== 0)) checked @endif  value="0" > &nbsp;No&nbsp;&nbsp;</label>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" >
            <label class="col-form-label col-md-2" >Module Status </label>
            <div class=" col-md-2">
                  <select class="form-control" name="module_status">
                     <option @if(isset($module[0]->is_active) && ($module[0]->is_active==1)) selected @endif  value="1">Active </option>
                     <option @if(isset($module[0]->is_active) && ($module[0]->is_active==0)) selected @endif value="0">Inactive</option>
                 </select>
                <span class="help-block"></span>
            </div>
        </div>


        <h4 class="color-template-title">Module Fields</h4>
        <div class="table-responsive">
            <table style="text-align: center;" class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info" >
                <thead>
                    <tr>
                       {{-- <th class="sorting_disabled">#</th> --}}
                        <th class="sorting_disabled">Field Type</th>
                        <th class="sorting_disabled">Field Name</th>
                        <th class="sorting_disabled">Status</th>
                        <th class="sorting_disabled">Order</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="module-rows">

                @if($module_id)

                   @foreach ($module_fields as $key=>$eachfield)

                      {{-- <tr id="{{$loop->iteration}}"> --}}
                        <tr role="row" class="option-row">
                           <td style="display:none;" aria-controls="position-table" class="cls-slno">1<input type="hidden" name="position[]" value="1"/></td>
                           <input type="hidden" name="shiftmodulefield_id[]" value="{{$eachfield->id}}"/></td>
                      <td id="fields_type_{{isset($key)?($key):"0"}}" class="types"> <div class="form-group field_type" id="field_type_{{isset($key)?($key):"0"}}"><select class="form-control cls-field_type" onchange="typeChanged($(this).val(),$(this));" @if($module_exists==1)readonly="true"  @endif/ name="field_type[]">
                <option  value="0" @if($module_exists==1 ) disabled @endif>Please Select</option>
                @foreach ($field_type as $type)
                <option @if($eachfield->field_type == $type->id) selected  @endif  @if($module_exists==1 && $eachfield->field_type != $type->id) disabled @endif value="{{ $type->id }}">{{$type->type_name}} </option>
                @endforeach
                 </select>
               </div>

@if($eachfield->field_type ==3)
<select style="margin-top:5px;" class="form-control field_dropdown"  name="field_dropdown_{{isset($key)?($key):"0"}}" @if($module_exists==1)readonly="true"  @endif id="field_dropdown_{{isset($key)?($key):"0"}}"> <option @if($module_exists==1 ) disabled @endif value="0">Please Select</option>
 @foreach ($shift_dropdown as $eachdown)
                <option @if($eachfield->dropdown_id == $eachdown->id) selected  @endif @if($module_exists==1 && $eachfield->dropdown_id != $eachdown->id) disabled @endif value="{{ $eachdown->id }}">{{$eachdown->dropdown_name}} </option>
@endforeach
</select>
@elseif($eachfield->field_type ==6)
<select style="margin-top:5px;" class="form-control field_dropdown_info"  name="field_dropdown_info_{{isset($key)?($key):"0"}}" @if($module_exists==1)readonly="true"  @endif id="field_dropdown_info_{{isset($key)?($key):"0"}}"> <option @if($module_exists==1 ) disabled @endif value="0">Please Select</option>
 @foreach ($shift_dropdown_with_info as $eachdown)
                <option @if($eachfield->dropdown_id == $eachdown->id) selected  @endif @if($module_exists==1 && $eachfield->dropdown_id != $eachdown->id) disabled @endif value="{{ $eachdown->id }}">{{$eachdown->dropdown_name}} </option>
@endforeach
</select>
@elseif($eachfield->field_type ==8)
<select style="margin-top:5px;" class="form-control field_post_order"  name="field_post_order_{{isset($key)?($key):"0"}}" @if($module_exists==1)readonly="true"  @endif id="field_post_order_{{isset($key)?($key):"0"}}"> <option @if($module_exists==1 ) disabled @endif value="0">Please Select</option>
 @foreach ($post_orders as $eachdown)
                <option @if($eachfield->dropdown_id == $eachdown->id) selected  @endif @if($module_exists==1 && $eachfield->dropdown_id != $eachdown->id) disabled @endif value="{{ $eachdown->id }}">{{$eachdown->dropdown_name}} </option>
@endforeach
</select>

@elseif($eachfield->field_type ==1)
 <label id="lab_photo_{{isset($key)?($key):"0"}}"  class="labels" style="float:left;padding-top:8px;">Enable Multiple: <input type="checkbox"  @if($eachfield->is_multiple_photo == 1) checked  @endif   id="multiple_photo_{{isset($key)?($key):"0"}}" @if($module_exists==1) onclick="return false;" @endif  name="multiple_photo_{{isset($key)?($key):"0"}}" class="label_photo"></label>
@endif
                    </td>
                      <td><div class="form-group field_name" id="field_name_{{isset($key)?($key):"0"}}"><input class="form-control cls-field_name"   type="text"  name="field_name[]" value="{{$eachfield->field_name}}"><span class="help-block"></span></div>
                      </td>
                     <td><select class="form-control field_status" id="field_status_{{isset($key)?($key):"0"}}" name="field_status[]">
                     <option @if($eachfield->field_status ==1) selected  @endif value="1">Active </option>
                     <option @if($eachfield->field_status ==0) selected  @endif  value="0">Inactive</option>
                 </select></td>
                 <td align="center"><div class="form-group  field_order" id="field_order_{{isset($key)?($key):"0"}}"><input  class="form-control cls-field_order" style="width: 45px;" value="{{$eachfield->order_id}}" type="text" name="field_order[]"><span class="help-block"></span></div></td>
                    <td>
                      <div>
                        <span>
                                            @if($module_exists==1)
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                            <i class="fa fa-minus fa-disabled" aria-hidden="true"  @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                            @else
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"     onclick="questionsObj.removeQuestion(this)">
                                         <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                         @endif
                                     </a>
                                 </span>

                                 <span>
                                     @if($module_exists==1)
                                    <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn">
                                        <i class="fa fa-plus fa-disabled" aria-hidden="true"></i>
                                        @else
                                        <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                          @endif
                                    </a>
                                </span></div>
                    </td>
                      </tr>
                   @endforeach
                    <input type="hidden" name="field_count" id="field_count" value="{{count($module_fields)}}">
                    <input type="hidden" name="row_count" id="row_count" value="{{count($module_fields)}}">

                @else
                  <tr role="row" class="option-row">
                   {{-- <td>1</td>  --}}
                    <td style="display:none;" aria-controls="position-table" class="cls-slno">1<input type="hidden" name="position[]" value="1"/></td>
                    <td id="fields_type_0" class="types"> <div class="form-group field_type" id="field_type_0"><select class="form-control cls-field_type" onchange="typeChanged($(this).val(),$(this));"  name="field_type[]">
                <option  value="0">Please Select</option>
                @foreach ($field_type as $type)
                <option  value="{{ $type->id }}">{{$type->type_name}} </option>
                @endforeach
                 </select>
                 <span class="help-block"></span></div>
               </td>
                    <td><div class="form-group field_name" id="field_name_0"><input  class="form-control cls-field_name"  type="text" name="field_name[]"> <span class="help-block"></span></div></td>
                     <td class="form-group field_status" id="field_status_0"><select class="form-control field_status" name="field_status[]">
                     <option   value="1">Active </option>
                     <option  value="0">Inactive</option>
                 </select> <span class="help-block"></span></td>
                    <td align="center" class="form-group field_order" id="field_order_0"><input  class="form-control cls-field_order" style="width: 45px;" type="text" name="field_order[]"><span class="help-block"></span></td>
                    <td class="sorting_disabled">  <div class="input-group">     <span>
                                            @if($module_exists==1)
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                            <i class="fa fa-minus fa-disabled" aria-hidden="true"  @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                            @else
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"     onclick="questionsObj.removeQuestion(this)">
                                         <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                         @endif
                                     </a>
                                 </span>

                                 <span>
                                     @if($module_exists==1)
                                    <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn">
                                        <i class="fa fa-plus fa-disabled" aria-hidden="true"></i>
                                        @else
                                        <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                          @endif
                                    </a>
                                </span>
                  </div>
                    </td>
                    </tr>
                    <input type="hidden" name="row_count" id="row_count" value="1">
                    <input type="hidden" name="field_count" id="field_count" value="1">
                 @endif
                </tbody>

            </table>

        </div>


        <div class="modal-footer">
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
            <a href="{{ route('customer-shift-module') }}" class="btn btn-primary blue">Cancel</a>
        </div>

    </section>
    {{ Form::close() }}
</div>
@stop


@section('js')
<script>

  $(function () {

 $('#customerid').select2();

});
     $(function () {
         post_order_exist = false;
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
                $('#module-rows').append(htmlStr);
                var newHtmlObj = $('#module-rows tr:last');
                this.prepareNextRow(newHtmlObj);
                questionsObj.endLoading();
                $('#module-rows .cls-field_type').select2({ width: '100%' });
             //   $('#customerid').trigger('change');
             /*   $(".cls-field_type").each(function () {
                 if($(this).val()==8){
                  $(".cls-field_type option[value='8']").remove();
                 }
                });*/
            },
            removeQuestion: function(currObj){
                    // Get the position of row to be removed
                    var removedPos = $(currObj).closest('tr').find('input[name="position[]"]').val();
                    var removedPosition = $(currObj).closest('tr').prevAll('tr').length+1;
                    position_num=removedPosition-1;
                    $(currObj).closest('tr').nextAll().each(function( index,value ) {
                       $(value).find('.field_type').attr("id", 'field_type_'+position_num);
                       $(value).find('.field_name').attr("id", 'field_name_'+position_num);
                       $(value).find('.field_status').attr("id", 'field_status_'+position_num);
                       $(value).find('.field_order').attr("id", 'field_order_'+position_num);
                       $(value).find('.label_photo').attr("id", 'label_photo_'+position_num);
                        $(value).find('.field_dropdown').attr("id", 'field_dropdown_'+position_num);
                        $(value).find('.field_dropdown').attr("name", 'field_dropdown_'+position_num);
                        $(value).find('.field_dropdown_info').attr("id", 'field_dropdown_info_'+position_num);
                        $(value).find('.field_dropdown_info').attr("name", 'field_dropdown_info_'+position_num);
                        $(value).find('.field_post_order').attr("id", 'field_post_order_'+position_num);
                        $(value).find('.field_post_order').attr("name", 'field_post_order_'+position_num);
                        $(value).find('.label_photo').attr("name", 'multiple_photo_'+position_num);

                        

                        $(value).find(".types").attr("id", 'fields_type_'+position_num);
                       position_num++;
                   });
                    $(currObj).closest('tr').remove();

                },
                getQuestionRow:function(){
                    var htmlText = '<tr role="row" class="option-row">'+$('#module-rows tr:first').html()+'</tr>';
                    return htmlText.replace(/checked="checked"/g, "");
                },
                prepareNextRow: function(htmlObj){
                    //reset values
                     var row_count  = parseInt($('#row_count').val()) + 1;
                    $('#row_count').val(row_count);
                    $(htmlObj).find("#row_count").val(row_count);
                    $(htmlObj).find(".field_type").val('');
                    $(htmlObj).find(".field_dropdown").hide();
                    $(htmlObj).find(".field_dropdown_info").hide();
                    $(htmlObj).find(".field_post_order").hide();
                    $(htmlObj).find(".cls-field_order").val('');
                    $(htmlObj).find(".cls-field_name").val('');
                    $(htmlObj).find(".cls-field_type").val(0);
                    $(htmlObj).find(".cls-field_status").val('');
                    $(htmlObj).find(".labels").hide();
                    var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                    $(htmlObj).find(".cls-slno").html(positionHtml);

          // $('#field_count').val(parseInt($('#field_count').val()) + 1);
                    $(htmlObj).find(".fa-minus").show();
                    $(htmlObj).find(".field_type").attr('id','field_type_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".field_name").attr('id','field_name_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".label_photo").attr('id','label_photo_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".field_status").attr('id','field_status_'+(this.questionElementCount*1-1));
                     $(htmlObj).find(".field_order").attr('id','field_order_'+(this.questionElementCount*1-1));
                     $(htmlObj).find(".types").attr('id','fields_type_'+(this.questionElementCount*1-1));
                     //$(htmlObj).find(".field_dropdown").attr('id','field_dropdown_'+(this.questionElementCount*1-1));

                     //Add id value for the newly added textbox to show validation messages
                     position_num=$('.option-row').length-1;

                     $(".field_type:last").attr("id", 'field_type_'+position_num);
                     $(".label_photo:last").attr("id", 'label_photo_'+position_num);
                     $(".field_name:last").attr("id", 'field_name_'+position_num);
                      $(".field_status:last").attr("id", 'field_status_'+position_num);
                     $(".field_order:last").attr("id", 'field_order_'+position_num);
                     //$(".field_dropdown:last").attr("id", 'field_dropdown_'+position_num);
                      $(".types:last").attr("id", 'fields_type_'+position_num);
                      $(htmlObj).find('.cls-field_type').removeAttr('onchange');
                    $(htmlObj).find('.cls-field_type:last').change(function(){ typeChanged($(this).val(),$(this)); });
                     var total_count = $('.cls-slno' ).length-1;

                 }
             }
             questionsObj.startLoading();
             questionsObj.questionRowHtml = questionsObj.getQuestionRow();
             questionsObj.endLoading();

             $('#module-rows .cls-field_type').select2({ width: '100%' });

        $('#shift-module-add-form').submit(function (e) {
            e.preventDefault();
            $('.field_type').prop('disabled', false);
            var $form = $(this);
            var form = $('#shift-module-add-form');
            url = "{{ route('customer-shift-module.store') }}";
            var formData = new FormData($('#shift-module-add-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data)  {
                    if (data.success) {
                        if(data.result == false){
                            result = "Module has been updated successfully";
                        }else{
                            result = "Module has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('customer-shift-module') }}";
                        });
                    }
                     else  {

                     }



                },
                fail: function (response) {
                    console.log(data);
                },
                error: function (xhr, textStatus, thrownError) {
                  $('.field_type').prop('disabled', true);
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });
         });


      function customerUpdate(customerId) {
       // var id = $(this).val();
       console.log(customerId);
        var base_url = "{{route('customer-shift-module.checkpostorder',':customer_id')}}";
        var url = base_url.replace(':customer_id', customerId);
        $.ajax({
          url: url,
          type: 'GET',
          success: function(data) {
            post_order_exist = false;
            if((data > 0) && ($(".cls-field_type option[value='8']").length > 0)){
                $(".cls-field_type option[value='8']").remove();
                post_order_exist = true;
            }else if((data == 0) && ($(".cls-field_type option[value='8']").length == 0)){
                $('.cls-field_type').append(`<option value="8">Post Order</option>`);
                post_order_exist = false;
            }
          }
        });
      }



       function typeChanged(selected,row_count_value){
           console.log(row_count_value.val());
       row_counts=$(row_count_value).closest('td').attr('id').replace(/[^0-9.]/g, "");
        row_count=row_counts.trim();

        if(selected == 3){
           var shift_dropdown = {!! json_encode($shift_dropdown); !!};
           var select_html = '&nbsp;&nbsp;<select style="margin-top:-30px;" class="form-control field_dropdown" id="field_dropdown_'+ row_count +'" name="field_dropdown_'+ row_count +'"> <option  value="0">Please Select</option></select>';
           $('#module-rows td#fields_type_'+row_count).append(select_html);
                      $.each(shift_dropdown, function(key, value) {
                          $('#field_dropdown_'+row_count)
                           .append($("<option></option>")
                           .attr("value",value.id)
                           .text(value.dropdown_name));
                         });
              //$('#multiple_photo_'+row_count).hide();
             $('#lab_photo_'+ row_count).remove();
             $('#field_dropdown_info_'+ row_count).remove();
             $('#field_post_order_'+ row_count).remove();
         }else if(selected == 6){
           var shift_dropdown_with_info = {!! json_encode($shift_dropdown_with_info); !!};
           var select_html = '&nbsp;&nbsp;<select style="margin-top:-30px;" class="form-control field_dropdown_info" id="field_dropdown_info_'+ row_count +'" name="field_dropdown_info_'+ row_count +'"> <option  value="0">Please Select</option></select>';
           $('#module-rows td#fields_type_'+row_count).append(select_html);
                      $.each(shift_dropdown_with_info, function(key, value) {
                          $('#field_dropdown_info_'+row_count)
                           .append($("<option></option>")
                           .attr("value",value.id)
                           .text(value.dropdown_name));
                         });
              //$('#multiple_photo_'+row_count).hide();
             $('#lab_photo_'+ row_count).remove();
             $('#field_dropdown_'+ row_count).remove();
             $('#field_post_order_'+ row_count).remove();

         }else if(selected == 8){
           post_order_field = $(".cls-field_type option[value='8']:selected").length;
           if(post_order_field > 1){
            row_count_value.val(0);
            swal("Warning", "Post order field is already added", "warning");
           }else if(post_order_exist == true){
            row_count_value.val(0);
            swal("Warning", "Post order field is already added in another module", "warning");
           }else{
           var post_orders = {!! json_encode($post_orders); !!};
           var select_html = '&nbsp;&nbsp;<select style="margin-top:-30px;" class="form-control field_dropdown_info" id="field_post_order_'+ row_count +'" name="field_post_order_'+ row_count +'"> <option  value="0">Please Select</option></select>';
           $('#module-rows td#fields_type_'+row_count).append(select_html);
                      $.each(post_orders, function(key, value) {
                          $('#field_post_order_'+row_count)
                           .append($("<option></option>")
                           .attr("value",value.id)
                           .text(value.dropdown_name));
                         });
              //$('#multiple_photo_'+row_count).hide();
              $('#field_dropdown_'+ row_count).remove();
              $('#field_dropdown_info_'+ row_count).remove();
              $('#lab_photo_'+ row_count).remove();
           }
         }else if(selected == 1){
             var check_html= '<label class="labels" id="lab_photo_'+ row_count +'" style="float:left;padding-top:1px;">Enable Multiple: <input  type="checkbox"  id="multiple_photo_'+ row_count +'" name="multiple_photo_'+ row_count +'"  value='+row_count+' class="label_photo""></label>';
             $('#module-rows td#fields_type_'+row_count).append(check_html);
               $('#field_dropdown_'+ row_count).remove();
               $('#field_dropdown_info_'+ row_count).remove();
               $('#field_post_order_'+ row_count).remove();
            //$('.labels').css('display','none');
         }else{
             $('#field_dropdown_'+ row_count).remove();
             $('#field_dropdown_info_'+ row_count).remove();
             $('#multiple_photo_'+ row_count).remove();
            $('#lab_photo_'+ row_count).remove();
            $('#field_post_order_'+ row_count).remove();
         }

       }

</script>
 <style type="text/css">
        .fa-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
    </style>
@stop
