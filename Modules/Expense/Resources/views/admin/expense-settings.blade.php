@extends('adminlte::page')

@section('title', 'expense settings')

@section('content_header')
<h1>Expense Settings</h1>
@stop
@section('content')
<div id="message"></div>


{{ Form::open(array('route'=>'expense-settings.add','id'=>'expense-settings-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
{{ Form::hidden('id', null) }}
 {{--  <div id="dynamic-section-email">
</div> --}}
    <div  class="box-body col-md-12">
      <div  class="form-group row">
          <label class="col-form-label col-md-3"> Send Email to approver if the invoice is not approved (in days)   </label>
            <div class="col-md-6" style="padding-left: 30px;">
           {{ Form::number('email_reminder', isset($reminderEmailInterval)?$reminderEmailInterval->interval:'',
                    array(
                    'class'=>'form-control financial email-reminder-days',
                    'min'=>'1',
                    'placeholder'=>'Number of Days','required'=>true,'style'=>'width:420px;')) }}
            <span class="help-block"></span>

            </div>
        </div>
        <div  class="form-group row">
          <label class="col-form-label col-md-3">  Send Statement Attachment ? </label>
            <div class="col-md-6" style="padding-left: 30px;">
            <label><input type="radio" name="sent_statement_attachment" @if(@$expense_settings->sent_statement_attachment== 1) checked @endif  value="1" >&nbsp;Yes</label>&nbsp;&nbsp;&nbsp;
            <label><input type="radio" name="sent_statement_attachment" @if(@$expense_settings->sent_statement_attachment== 0) checked @endif  value="0">&nbsp;No</label>
            <span class="help-block"></span>
            </div>
        </div>


            <div  class="form-group row" >
             <label class="col-form-label col-md-3"> Finance Controller </label>
             <div class="col-md-4">
             <div class="table-responsive">
                <table  class="table dataTable " role="grid" aria-describedby="position-table_info"  id="users_table">
 <tbody id="module-rows">
 @if($finance_controllers) 
  @foreach ($finance_controllers as $key=>$eachcontroller)
     <tr>
       <td>
           <select class="form-control financial" style="width: 420px;" id="financial_controller"   name="financial_controller[]">
                <option  value="0">Please Select</option>
                @foreach ($userslist as $key=>$eachuser)
                  <option @if($eachcontroller == $key) selected @endif value="{{$key}}">{{$eachuser}} </option>
                @endforeach
            </select>
            <span class="help-block"></span>
       </td>
        <td>
        <a title="Add another option"  class="add_attachment">
             <i class="fa fa-plus fa-disabled" aria-hidden="true"></i>
             <a title="Remove" href="javascript:;" class="remove_attachment">
            <i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i>
        </a>
        </td>
    </tr>
    @endforeach  

   @else
   <tr>
       <td>
           <select class="form-control financial" style="width: 420px;" id="financial_controller"   name="financial_controller[]">
                <option  value="0">Please Select</option>
                @foreach ($userslist as $key=>$eachuser)
                  <option value="{{$key}}">{{$eachuser}}</option>
                @endforeach
            </select>
            <span class="help-block"></span>
       </td>
        <td>
        <a title="Add another option"  class="add_attachment">
             <i class="fa fa-plus fa-disabled" aria-hidden="true"></i>
        </a>
        </td>
    </tr>
   @endif
</tbody>
</table>
<input type="hidden" name="row_count" id="row_count" value="0">
   

 </div>
             </div>
            </div>
            <div  class="col-md-2"></div>
          <div  class="modal-footer col-md-4">
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">   
            <button class="button btn btn-primary blue" onClick="window.location.reload();">Cancel</button>
          </div>
        
    {{ Form::close() }}
 
@stop
@section('js')
<script src="{{ asset('js/moreel.js') }}"></script>
<script>

$('#financial_controller').select2();
$('#users_table').on('click', '.add_attachment' ,function() {
    var row_count  = parseInt($('#row_count').val()) + 1;
    $('#row_count').val(row_count);
    var userslist = {!! json_encode($userslist); !!};
    $('#module-rows').append('<tr><td><select style="width: 420px;" class="form-control financial" id="financial_controller_'+ row_count +'" name="financial_controller[]"> <option  value="0">Please Select</option></select></td><td><a title="Add" href="javascript:;" class="add_attachment"><i class="fa fa-plus size-adjust-icon" aria-hidden="true"></i> </a> <a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i></a></td></tr>');
    $.each(userslist, function(key, value) {
                          $('#financial_controller_'+row_count)
                           .append($("<option></option>")
                           .attr("value",key)
                            .text(value));
                         });
   $('#financial_controller_'+row_count).select2();
});

    $('#users_table').on('click', '.remove_attachment' ,function() {
      $(this).parents().closest('tr').remove();
      //  $('#users_table tr:last').remove();
    });


             $('#expense-settings-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('expense-settings.store') }}";
                var formData = new FormData($('#expense-settings-form')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            if(data.result == true){
                                result = "Expense settings has been updated successfully";
                            }else{
                                result = "Expense settings has been updated successfully";
                            }
                            swal({
                              title: "Saved",
                              text: result,
                              type: "success",
                              confirmButtonText: "OK",
                          },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            window.location.href = "{{ route('expense-settings') }}";
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

            
</script>
<style>
    .modal-footer{
        text-align:left !important;
    }
</style>
@stop