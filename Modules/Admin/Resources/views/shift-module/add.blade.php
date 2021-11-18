    {{-- resources/views/admin/dashboard.blade.php --}}

    @extends('adminlte::page')

    @section('title', 'Shift Module Dropdown')

    @section('content_header')
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <h3>Shift Module Dropdown</h3>
    @stop
    @section('content')
    <div class="container-fluid container-wrap">
        {{ Form::open(array('route'=> 'shift-module-dropdown.add','id'=>'dropdown-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        <!-- Main content -->
        <section class="content">
            <div class="form-group row" id="dropdown_name">
                <input type="hidden" name="drop_down_exists" value="{{$dropdown_exists or ''}}"/>
                <input type="hidden" name="id"  value="{{@$dropdown_arr['id']}}"/>
                <label class="col-form-label col-md-2" for="dropdown_name">Dropdown Name </label>
                <div class=" col-md-4">
                    <input type="text" class="form-control" placeholder="Name" name="dropdown_name" value="{{@$dropdown_arr['dropdown_name']}}" >
                    <span class="help-block"></span>
                </div>
            </div>

            <div class="form-group row" id="post_order">
             
             <label class="col-form-label col-md-2" for="post_order">Post Order</label>
             <div class=" col-md-4">
             <label> <input type="radio" onchange="updateInfo(1);" name="post_order" @if(isset($dropdown_arr['post_order']) && ($dropdown_arr['post_order']== 1)) checked @endif  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
             <label> <input type="radio" onchange="updateInfo(0);" name="post_order" @if(isset($dropdown_arr['post_order']) )  @if(($dropdown_arr['post_order']== 0))  checked @endif @else checked @endif  value="0" >&nbsp;No&nbsp;&nbsp;</label> 
                 <span class="help-block"></span>
             </div>
           </div>

            <div class="form-group row" id="info_id">
                <label class="col-form-label col-md-2">  Info </label>  
                  <div class="col-md-4">

                  @if(isset($dropdown_arr) && !empty($dropdown_arr))
                    {{ Form::select('info_id', [0=>'No',1=>'Yes'], isset($dropdown_arr['info'])?$dropdown_arr['info']:'',array('class' =>'form-control','id'=>'infomn_id','disabled' => true)) }}
                  @else 
                  {{ Form::select('info_id', [0=>'No',1=>'Yes'], isset($dropdown_arr['info'])?$dropdown_arr['info']:'',array('class' =>'form-control','id'=>'infomn_id')) }}
                  @endif    

                  
                       <span class="help-block"></span>
                  </div>
              </div>

            {{-- <div class="form-group row {{ @$dropdown_arr['info']!=1? 'hide-this-block':'' }} " id="detail">
                <label class="col-form-label col-md-2"> Details</label>
                <div class="col-md-4">
                      <!--<textarea class="form-control" placeholder="Explanation" maxlength="2000"  name="detail" cols="50" rows="10" >{{$dropdown_arr['detail'] or ''}}</textarea>-->
                      {{Form::textarea('detail',old('detail',@$dropdown_arr['detail']),array('class'=>'form-control editor','placeholder'=>"Explanation",'maxlength'=>2000,'id'=>'details'))}}
                         <small class="help-block"></small>
                  </div>
              </div> --}} 

            <h4 class="color-template-title">Dropdown Options</h4>
            <div class="table-responsive">
                <table class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info"  id="add-table">
                    <thead>
                        <tr>
                            <th class="sorting_disabled" style="white-space: nowrap">Dropdown Option Text</th>
                            @if(isset($dropdown_arr) && !empty($dropdown_arr))
                            <th id="info_column"  class="sorting_disabled"   @if($dropdown_arr['info']==0) style="display:none;" @endif  >Information</th>
                            @else
                            <th id="info_column"  class="sorting_disabled"   style="display:none;"  >Information</th>
                            @endif
                            <th class="sorting_disabled" style="white-space: nowrap">Order Sequence</th>
                            <th class="sorting_disabled">Action</th>
                        </tr>
                    </thead>
                    <tbody id="option-values">
                        @if(isset($option_list))
                        @foreach($option_list as $key=>$option)
                        <tr role="row" class="option-row">
                            <td>
                                 <input   type="hidden" class="option_id" name="option_id[]" value="{{$option->id}}"/>
                                <div class="form-group option_name" id="option_name_{{isset($key)?($key):"0"}}"><input  class="form-control cls-option-name"   type="text" name="option_name[]" value="{{$option->option_name}}"/>       <span class="help-block"></span>
                                </div>   </td>
                                <td id="info_value" class="info_value" @if(isset($dropdown_arr)) @if($dropdown_arr['info']==0) style="display:none;" @endif @endif>
                                <div class="form-group option_info" id="option_info_{{isset($key)?($key):"0"}}"><textarea   class="form-control cls-option-info" rows="2" cols="5"   name="option_info[]"> {{$option->option_info ?? ''}} </textarea>     <span class="help-block"></span>
                                </div>   </td>

                                <td  align="center" >
                                   <div class="form-group order_sequence" id="order_sequence_{{isset($key)?($key):"0"}}">

                                    <input  class="form-control cls-option-order" style="width: 68px;" type="text" name="order_sequence[]" value="{{$option->order_sequence}}"/>        <span class="help-block"></span>
                                </div></td>
                                <td  align="center" class="sorting_disabled">
                                 <div class="input-group">
                                     <span>
                                            @if($dropdown_exists==1)
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                            <i class="fa fa-minus" aria-hidden="true" onclick="questionsObj.removeQuestion(this)"  @if(!isset($key) || (isset($key) && $key == 0) || ($key <= count($option_list))) style="display: none;" @endif></i>
                                            @else
                                              <a title="Remove Option" href="javascript:;"  class="remove_button"     onclick="questionsObj.removeQuestion(this)">
                                         <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                         @endif
                                     </a>
                                 </span>

                                 <span>
                                     @if($dropdown_exists==1)
                                    <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn">
                                        <i class="fa fa-plus" aria-hidden="true" onclick="questionsObj.addQuestion(this)"></i>
                                        @else
                                        <a title="Add another option" href="javascript:;" class="add_button margin-left-table-btn" onclick="questionsObj.addQuestion(this)">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                          @endif
                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr role="row" class="option-row">
                        <td>
                              <input   type="hidden" class="option_id" name="option_id[]" value=""/>
                            <div class="form-group option_name" id="option_name_0"><input  class="form-control cls-option-name"   type="text" name="option_name[]" value=""/>       <span class="help-block"></span>
                            </div>   </td>
                            <td class="info_value_default" style="display:none;">
                            <div class="form-group option_info" id="option_info_0"><textarea style="width: 250px; height: 60px;" class="form-control cls-option-info" rows="2" cols="5"  name="option_info[]"></textarea>       <span class="help-block"></span>
                            </div>   </td>
                            <td align="center">
                               <div class="form-group order_sequence" id="order_sequence_0">

                                <input  class="form-control cls-option-order" style="width: 68px;"  type="text" name="order_sequence[]" value=""/>        <span class="help-block"></span>
                            </div></td>
                            <td  align="center" class="sorting_disabled">
                             <div class="input-group">
                                 <span>

                                    <a title="Remove Option" href="javascript:;"   class="remove_button" disabled="true" onclick="questionsObj.removeQuestion(this)" >
                                     <i class="fa fa-minus"  disabled="true" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                 </a>
                             </span>

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
        <a href="{{ route('customer-shift-module-dropdown') }}" class="btn btn-primary blue">Cancel</a>
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
                    // Get the position of row to be removed
                    var removedPos = $(currObj).closest('tr').find('input[name="position[]"]').val();
                    var removedPosition = $(currObj).closest('tr').prevAll().length+1;
                    position_num=removedPosition-1;
                    $(currObj).closest('tr').nextAll().each(function( index,value ) {
                       $(value).find('.option_name').attr("id", 'option_name_'+position_num);
                       $(value).find('.option_info').attr("id", 'option_info_'+position_num);
                       $(value).find('.order_sequence').attr("id", 'order_sequence_'+position_num);
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
                    if($("#infomn_id").val()==1){
                    $(htmlObj).find(".info_value_default").attr('style','');
                    }
                    $(htmlObj).find(".cls-option-order").val('');
                    var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                    $(htmlObj).find(".cls-slno").html(positionHtml);

                    $(htmlObj).find(".fa-minus").show();
                    $(htmlObj).find(".option_name").attr('id','option_name_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".option_info").attr('id','option_info_'+(this.questionElementCount*1-1));
                    $(htmlObj).find(".order_sequence").attr('id','order_sequence_'+(this.questionElementCount*1-1));
                     //Add id value for the newly added textbox to show validation messages
                     position_num=$('.option-row').length-1;
                      $(".option_id:last").val('');
                     $(".option_name:last").attr("id", 'option_name_'+position_num);
                     $(".option_info:last").attr("id", 'option_info_'+position_num);
                     $(".order_sequence:last").attr("id", 'order_sequence_'+position_num);


                     var total_count = $('.cls-slno' ).length-1;

                 }
             }
             questionsObj.startLoading();
             questionsObj.questionRowHtml = questionsObj.getQuestionRow();
             questionsObj.endLoading();

             $('#add-table').dataTable({
                    "bPaginate": false
              });

             $('#dropdown-add-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                $('select[name="info_id"]').prop('disabled',false);
               // for (instance in CKEDITOR.instances)
              //  CKEDITOR.instances[instance].updateElement();
                url = "{{ route('shift-module-dropdown.add') }}";
                var formData = new FormData($('#dropdown-add-form')[0]);
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
                                result = "Dropdown options has been updated successfully";
                            }else{
                                result = "Dropdown options has been created successfully";
                            }
                            swal({
                              title: "Saved",
                              text: result,
                              type: "success",
                              confirmButtonText: "OK",
                          },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('customer-shift-module-dropdown') }}";
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

            $( "#info_id" ).change(function() {
                if( $("#infomn_id").val()==1){
                    $("#info_value").show();
                    $("#info_column").show();
                    $(".info_value_default").show();
                    $(".info_value").show();
                }

                else {
                    $("#info_value").hide();
                    $("#info_column").hide();
                    $(".info_value_default").hide();
                    $(".info_value").hide();
                    $(".cls-option-info").val('')
                    }
             });

             
             //1 = yes 0 = No
            //  if($('#infomn_id').val() == 1){
            //     $('#infomn_id').prop('disabled', true);
            //  }else{
            //     $('#infomn_id').prop('disabled', false);
            //  }


        });

function updateInfo(post_order){
    if(post_order==1){
    $("#info_id option[value='1']").prop('selected', true);
    $("#info_id").trigger('change');
    $('select[name="info_id"]').prop('disabled',true);

    }else{
    $("#info_id option[value='0']").prop('selected', true);
    $("#info_id").trigger('change');   
    $('select[name="info_id"]').prop('disabled',false);
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
