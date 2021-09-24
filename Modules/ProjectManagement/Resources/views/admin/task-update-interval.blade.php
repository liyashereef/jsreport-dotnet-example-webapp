@extends('adminlte::page')
@section('title', 'Task Update Interval')
@section('content_header')
<h1>Task Update Interval</h1>
@stop

@section('content')

<section class="content">
  {{ Form::open(array('url'=>'#','id'=>'interval-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
  @if(empty($interval) )

  <div class="form-group dynamic-fields" id="interval_0">
    <input type="hidden" name="position[]" class="pos"  value="0">
    <label for="interval" class="col-md-5 interval_label" id="label_0">Number of days prior to due date for sending 1st remainder email
      <span class="mandatory">*</span>
    </label>
    <div class="col-md-4">
      {{ Form::number('interval[]', null,array('class'=>'form-control','min'=>'1','id'=>"intervals_0",'onfocusout'=>"setMax(this);" ,'placeholder'=>'Number of Days','required'=>true)) }}
      <small class="help-block"></small>
    </div>

    <div class="col-sm-3 add-hide">

      <a title="Add another experience" href="javascript:;" class="add_button">
        <i class="fa fa-plus" aria-hidden="true"></i>
      </a>

    </div>
  </div>
  @else
  @foreach($interval as $key=>$each_intervals)
  <div class="form-group dynamic-fields" id="interval_{{$key}}">
    <input type="hidden" name="position[]" class="pos"  value="{{$key}}">
    <label for="interval" class="col-md-5 interval_label" id="label_{{$key}}">
    </label>
    <div class="col-md-4">
      {{ Form::number('interval[]', $each_intervals,array('class'=>'form-control','id'=>"intervals_$key",'onfocusout'=>"setMax(this);",  'placeholder'=>'Number of Days','min'=>'1','required'=>true)) }}
      <small class="help-block"></small>
    </div>
    <div class="col-sm-3 add-hide">

      <a title="Remove Option" href="javascript:;"  class="remove_button" @if (count($interval)==$key+1 && $key!=0) style="display:inline;" @else style="display:none;"  @endif>
        <i class="fa fa-minus fa-disabled" aria-hidden="true"></i></a>

        <a title="Add Another Experience" href="javascript:;" class="add_button" style="display: none">
          <i class="fa fa-plus" aria-hidden="true"></i>
        </a>

      </div>
      <div class="form-control-feedback"></div>
    </div>
    @endforeach
    @endif


    <div class="form-group follower-div" id="follower-div" style="padding-left: 15px;padding-top: 20px;">
      <span style="color:black;"><h4>Enable task update for followers :</h4></span>
      <div class="col-md-12" style="padding-top: 20px;">
        <label class="form-label"><input type="radio" name="task_followers_config_value" id="enable_all_followers" value="1"/>&nbsp;Followers can update their task</label><br />
        <label class="form-label" style="padding-top: 13px;"><input type="radio" name="task_followers_config_value" id="enable_followers_task_wise" value="0"/>&nbsp;Followers can update their task (Based on follower settings specified on the task)</label>
      </div>
    </div>


    <div class="modal-footer">
      {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
      <a href="{{ route('interval') }}" class="btn btn-primary blue">Cancel</a>
      {{ Form::close() }}
    </div>
    @stop
    @section('js')

    <script>
      $(function () {
        @if(!empty($followerConfigurationValue) && ($followerConfigurationValue == "1"))
          $('#enable_all_followers').prop("checked", true);
          $('#enable_followers_task_wise').prop("checked", false);
        @else
          $('#enable_all_followers').prop("checked", false);
          $('#enable_followers_task_wise').prop("checked", true);
        @endif

       setLabelAttributes();
       $('.dynamic-fields:last').find('.add_button').show();
       $("#interval-form").on("click",".add_button",function(){
        addRow();
      });
       $("#interval-form").on("click",".remove_button",function(){
         var removedPos = $(this).parents().closest('.dynamic-fields').find('input[name="position[]"]').val();
         position_num=removedPos;

         $(this).parents().closest('.dynamic-fields').remove();

         if(position_num!=1)
          $('.dynamic-fields:last').find('.remove_button').show();
        $('.dynamic-fields:last').find('.add_button').show();
        $('.dynamic-fields:last').find('.add-hide').show();


      });
        $.each( $('#interval-form  [name="interval[]"]'), function( key, value){
         setMax(value)
        });
       $('#interval-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        $('.form-group').removeClass('has-error').find('.help-block').text('');
        url = "{{ route('interval.store') }}";
        var formData = new FormData($('#interval-form')[0]);
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: 'POST',
          data: formData,
          success: function (data) {
            if (data.success) {
              swal({title: "Saved", text: "Task settings has been saved", type: "success"},
               function(){
                 location.reload();
               }
               );
            } else {
              alert(data);
            }
          },
          fail: function (response) {
            alert('here');
          },
          error: function (xhr, textStatus, thrownError) {
            associate_errors(xhr.responseJSON.errors, $form,true);
          },
          contentType: false,
          processData: false,
        });
      });


     });

      function setLabelAttributes()
      {
        $('#interval-form *').filter('.dynamic-fields').each(function(index,value){
          var position=$(value).find('.pos').val();
          var ordinal_suffix=ordinal_suffix_of(parseInt(position)+parseInt('1'))
          $("#label_"+position).html('Number of days prior to due date for sending '+ordinal_suffix+' remainder email<span class="mandatory">*<span>');
        });
      }
      function addRow()
      {
        position_num= $('.pos:last').val()
        prev=parseInt($('#intervals_'+position_num).val())-1;
        new_position_num=parseInt(position_num)+1;
        var ordinal_suffix=ordinal_suffix_of(new_position_num+1)
        var html='<div class="form-group dynamic-fields"  id="interval_'+new_position_num+'"><input type="hidden" name="position[]" class="pos"  value="'+new_position_num+'"><label for="interval" id="label_'+new_position_num+'" class="col-md-5 interval_label">Number of days prior to due date for sending '+ordinal_suffix+' remainder email<span class="mandatory">*</span></label><div class="col-md-4"><input type="number" name="interval[]" class="form-control" placeholder="Number of Days" max='+prev+' min="1" required="true" id="intervals_'+new_position_num+'", onfocusout="setMax(this);"><small class="help-block"></small></div><div class="col-sm-3 add-hide"><a href="javascript:void(0);" class="remove_button" title="Remove field"><i class="fa fa-minus" aria-hidden="true"></i></a>&nbsp;&nbsp;<a title="Add another experience" href="javascript:;" class="add_button"><i class="fa fa-plus" aria-hidden="true"></i></a></div><div class="form-control-feedback"></div></div>';
        $("#interval_"+position_num).after(html);
        $('.dynamic-fields').find('.add-hide').hide();
        if($('.dynamic-option-fields').length<=4){
        $('.dynamic-fields:last').find('.add-hide').show();

        }
      }

      function ordinal_suffix_of(i) {
        var j = i % 10,
        k = i % 100;
        if (j == 1 && k != 11) {
          return i + "st";
        }
        if (j == 2 && k != 12) {
          return i + "nd";
        }
        if (j == 3 && k != 13) {
          return i + "rd";
        }
        return i + "th";
      }
      function setMax(row)
      {
        current_id=($(row).attr('id'))
        var position = current_id.substr(current_id.indexOf("_") + 1)
        new_position_num=parseInt(position)+1;
        new_max_value=parseInt($(row).val())-1;
      $('#intervals_'+new_position_num).attr({"max" : new_max_value});
      }

    </script>
    @stop
