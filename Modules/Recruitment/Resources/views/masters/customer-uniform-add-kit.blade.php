{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Uniform Kits')
@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Uniform kits</h3>
@stop
@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'recruitment.customer-uniform-kits.add','id'=>'uniform-kit-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <div class="form-group row" id="customer_id">
            <input type="hidden" name="id" value="{{$mappingArr['id'] or ''}}"/>
          <label class="col-form-label col-md-2">  Choose Customer <span class="mandatory">*</span></label>
            <div class="col-md-4">
                <select  name="customer_id" id="customerid" class="form-control select2">
                    <option value=0 selected>Select</option>

                    @foreach($customer_list as $id=>$data)

                    <option @if(isset($mappingArr['customer_id']) &&$mappingArr['customer_id']  == $data->id) selected @endif value={{$data->id}}>{{$data->project_number}} - {{$data->client_name}}</option>
                    @endforeach
                </select>
                 <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group row" id="kit_name">
            {{-- <input type="hidden" name="id"  value="{{$dropdown_arr['id'] or ''}}"/> --}}
            <label class="col-form-label col-md-2" for="dropdown_name">Kit Name </label>
            <div class=" col-md-4">
                <input type="text" class="form-control" placeholder="Name" name="kit_name" value="{{$mappingArr['kit_name'] or ''}}">
                <span class="help-block"></span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info"  id="add-table">
                <thead>
                    <tr>
                        <th class="sorting_disabled" style="white-space: nowrap">Item Quantity</th>
                        <th class="sorting_disabled" style="white-space: nowrap">Quantity</th>
                        <th class="sorting_disabled">Action</th>
                    </tr>
                </thead>
                <tbody id="option-values">

                    @if(isset($mappingArryItems) && !empty($mappingArryItems))
                    @foreach($mappingArryItems as $key=>$items)
                    <tr role="row" class="option-row">
                        <td>
                            <input   type="hidden" class="uniform_kit_id" name="uniform_kit_id[]" value="{{$items->id}}"/>
                            <div class="form-group item_id" id="item_id_{{isset($key)?($key):"0"}}">
                                <select style="margin-top:5px;" class="form-control cls-item_id"  name="item_id[]">
                                    <option value="null">Please Select</option>
                                        @if(!empty($uniformItemList))
                                        @foreach ($uniformItemList as $eachItem)
                                                <option @if($items->item_id == $eachItem->id) selected @endif  value="{{ $eachItem->id }}">{{$eachItem->item_name}} </option>
                                       @endforeach
                                       @endif
                                </select>
                                 <span class="help-block"></span>
                            </div>
                        </td>

                            <td align="center" >
                               <div class="form-group quantity" id="quantity_{{isset($key)?($key):"0"}}">
                               <input  class="form-control cls-option-order" style="width: 68px;" type="text" name="quantity[]" value="{{$items->quantity}}"/>        <span class="help-block"></span>
                            </div></td>
                            <td  align="center" class="sorting_disabled">
                             <div class="input-group">
                                 <span>
                                        @if($mappingArryItems==true)
                                          <a title="Remove Option" href="javascript:;"  class="remove_button"  >
                                        <i class="fa fa-minus" aria-hidden="true" onclick="questionsObj.removeQuestion(this)"  @if(!isset($key) || (isset($key) && $key == 0) || ($key <= count($mappingArryItems))) style="display: none;" @endif></i>
                                        @else
                                          <a title="Remove Option" href="javascript:;"  class="remove_button"     onclick="questionsObj.removeQuestion(this)">
                                     <i class="fa fa-minus" aria-hidden="true" @if(!isset($key) || (isset($key) && $key == 0)) style="display: none;" @endif></i>
                                     @endif
                                 </a>
                             </span>

                             <span>
                                 @if($mappingArryItems==true)
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
                    <td id="item_id_0" class="items col-sm-6">
                        <input   type="hidden" class="uniform_kit_id" name="uniform_kit_id[]" value=""/>
                        <div class="form-group item_id" id="item_ide_0">
                            <select class="form-control cls-item_id" name="item_id[]">
                <option  value="0">Please Select</option>
                @if(!empty($uniformItemList))
                                @foreach ($uniformItemList as $eachItem)
                                        <option value="{{ $eachItem->id }}">{{$eachItem->item_name}} </option>
                               @endforeach
                               @endif
                 </select>
                 <span class="help-block"></span></div>
               </td>
                        <td align="center">
                           <div class="form-group quantity" id="quantity_0">
                            <input  class="form-control cls-option-order" style="width: 68px;"  type="text" name="quantity[]" value=""/>        <span class="help-block"></span>
                        </div></td>
                        <td align="center" class="sorting_disabled">
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
    <a href="{{ route('recruitment.customer-uniform-kits') }}" class="btn btn-primary blue">Cancel</a>
</div>

</section>
{{ Form::close() }}
</div>
@stop


@section('js')
<script>
$(function () {
    $('.select2').select2();
    // $('.cls-item_id').select2({ width: '100%' });

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
                   $(value).find('.item_id').attr("id", 'item_id_'+position_num);
                   $(value).find('.quantity').attr("id", 'quantity_'+position_num);
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
                $(htmlObj).find(".cls-item_id").val('');
                // $('.cls-item_id').remove();
                $(htmlObj).find(".cls-option-info").val('');
                if($("#infomn_id").val()==1){
                $(htmlObj).find(".info_value_default").attr('style','');
                }
                $(htmlObj).find(".cls-option-order").val('');
                var positionHtml = this.questionElementCount+'<input type="hidden" name="position[]" value="'+this.questionElementCount+'"/>';
                $(htmlObj).find(".cls-slno").html(positionHtml);

                $(htmlObj).find(".fa-minus").show();
                $(htmlObj).find(".item_id").attr('id','item_id_'+(this.questionElementCount*1-1));
                $(htmlObj).find(".quantity").attr('id','quantity_'+(this.questionElementCount*1-1));
                 //Add id value for the newly added textbox to show validation messages
                 position_num=$('.option-row').length-1;
                 $(".uniform_kit_id:last").val('');
                  $(".item_id:last").val('');
                 $(".item_id:last").attr("id", 'item_id_'+position_num);
                 $(".quantity:last").attr("id", 'quantity_'+position_num);


                 var total_count = $('.cls-slno' ).length-1;

             }
         }
         questionsObj.startLoading();
         questionsObj.questionRowHtml = questionsObj.getQuestionRow();
         questionsObj.endLoading();

         $('#add-table').dataTable({
                "bPaginate": false
          });



         $('#uniform-kit-add-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            // $('select[name="info_id"]').prop('disabled',false);
           // for (instance in CKEDITOR.instances)
          //  CKEDITOR.instances[instance].updateElement();
            url = "{{ route('recruitment.customer-uniform-kits.add') }}";
            var formData = new FormData($('#uniform-kit-add-form')[0]);
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
                            result = "Uniform kit item has been updated successfully";
                        }else{
                            result = "Uniform kit item has been created successfully";
                        }
                        swal({
                          title: "Saved",
                          text: result,
                          type: "success",
                          confirmButtonText: "OK",
                      },function(){
                        // $('.form-group').removeClass('has-error').find('.help-block').text('');
                        window.location.href = "{{ route('recruitment.customer-uniform-kits') }}";
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
