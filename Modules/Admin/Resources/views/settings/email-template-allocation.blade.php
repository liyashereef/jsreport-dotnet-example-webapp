  @extends('adminlte::page')
  @section('title', 'email-template-allocation')
  @section('content_header')
  <h1>Email Allocation</h1>
  @stop
  @section('content')
  <div id="message"></div>
  {{ Form::open(array('url'=>'#','id'=>'email-template-allocation-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
  {{ Form::hidden('id', null) }}
  {{Form::hidden('show_customer_block',null)}}
  <div  class="box-body col-md-12">
   <div  class="form-group row" id="type">
    <label class="col-form-label col-md-3"> Type </label>
    <div class="col-md-6" style="padding-left: 30px;">
      <select name="type" onchange="showHideCustomerBlock($(this).children('option:selected').val())"  class="form-control" id="types">
       <option value=0 disabled="disabled" selected>Select</option>
       @foreach($type as $id=>$data)
       <option value={{$id}}>{{$data}}</option>
       @endforeach
     </select>
     <span class="help-block"></span>
   </div>
 </div>
 <div class="block-for-customers">
  <div  class="form-group row">
    <label class="col-form-label col-md-3">  Select Customers </label>
    <div class="col-md-6" style="padding-left: 30px;">
      <select name="customer"  class="form-control" id="customers">
        <option value=""  selected>Selected All Customers</option>
        @foreach($customer as $id=>$data)
        <option value={{$id}}>{{$data}}</option>
        @endforeach
      </select>
      <span class="help-block"></span>

    </div>
  </div>
  <div  class="form-group row">
    <label class="col-form-label col-md-3">  Select Allocation </label>
    <div class="col-md-3" style="padding-left: 30px;">
      <input type="checkbox"  name="areamanagers" value="1" id="area_manager">
      <label for="areamanagers">Select allocated Area Managers</label>
      <span class="help-block"></span>
    </div>
    <div class="col-md-3" style="padding-left: 30px;">
      <input type="checkbox"  name="supervisors" value="1" id="supervisor">
      <label for="supervisors">Select allocated Supervisors</label>
      <span class="help-block"></span>
    </div>
  </div>
</div>
<div  class="form-group row">
  <label class="col-form-label col-md-3">  Select Users </label>
  <div class="col-md-6" style="padding-left: 30px;">
    <select name="user[]"  class="form-control" id="users" multiple>
     @foreach($users as $id=>$data)
     <option value={{$id}}>{{$data}}</option>
     @endforeach
   </select>
   <span class="help-block"></span>
 </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-md-3">Role Based</label>
    <div class="col-md-3" style="padding-left: 30px;">
      <input type="checkbox"  name="role_based" value="1" id="role_based">
      <span class="help-block"></span>
    </div>
    </div><br>

    <div class="row" id="role_list" style="display: none;">
      <label for="role_list" class="col-md-3" style="margin-left:0px;">Select Roles</label>
      <div class="col-md-6" style="padding-left:30px;">
        <select name="role[]"  class="form-control select2 selected_role" id="role_list" multiple>
        @foreach($roles as $key=>$value)
        <option value={{$value->id}}>{{$value->name}}</option>
        @endforeach
        </select>
      <span class="help-block"></span>
     </div>
     </div>

</div>
<div class="modal-footer">
  <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
  </div>

<div>
</div>

{{ Form::close() }}

@stop
@section('js')
<script>
  $(function () {

    $(".select2").select2({
      width:'100%'
    });
    $("#role_based").on("click", function(event) {
        var isChecked = $('#role_based').is(':checked');
        if ($("#role_based").is(':checked')) {
            $("#role_list").show();
        } else {
            $('.selected_role').val('').change();
            $("#role_list").hide();
            $('#role_based').prop('checked', false);
        }
    });

    var areamanagers=[];
    var supervisor=[];
    $('#customers').select2();
    $('#users').select2();
    $(".selected_role").change(function() {
      var selected=[];
      jQuery.each($(this).val(), function(index,value){
        selected.push(parseInt(value));
    });
    });
    // $("#users").change(function() {
    //   var selected=[];
    //   jQuery.each($(this).val(), function(index,value){
    //     selected.push(parseInt(value));
    //   });
    //   let checker = (arr, target) => target.every(v => arr.includes(v));
    //   $boolean_areamanager=checker(selected,areamanagers)?true:false;
    //   $boolean_supervisor=checker(selected,supervisor)?true:false;
    //   if(areamanagers.length>0){
    //     $('#area_manager').prop('checked', $boolean_areamanager);
    //   }
    //   if(supervisor.length>0){
    //     $('#supervisor').prop('checked', $boolean_supervisor);
    //   }
    // });
    $("select[name='customer'],select[name='type']").change(function() {
      var arrs=[];
      var selected_role_ids=[];
      var customer_id=$('select[name="customer"]').val();
      var template_id=$('select[name="type"]').val();
      var base_url = "{{route('allocation.userslist',[':template_id',':customer_id'])}}";
      var url1 = base_url.replace(':customer_id', customer_id);
      var url = url1.replace(':template_id', template_id);
      $.ajax({
       url: url,
       type: 'GET',
       success: function(data) {
         areamanagers=data.area_manager;
         supervisor=data.supervisor;
         if(data.role_list.length != 0){
            roles = data.role_list[0].role_id_mapping;
         }else{
           roles=null;
         }
          console.log(data);
         if(data.success && data.data){
          $('input[name=id]').val(data.data.id);
          $('#area_manager').prop('checked', data.data.send_to_areamanagers);
          $('#supervisor').prop('checked', data.data.send_to_supervisors);

        }
        else
        {
          $('input[name=id]').val("");
          $('#supervisor').prop('checked', false);
          $('#area_manager').prop('checked', false);

        }
        if(roles){
          $.each(roles, function(key, value) {
            selected_role_ids.push(value.role_id);
          });
          if (selected_role_ids === undefined || selected_role_ids.length == 0) {
            $('#role_based').prop('checked', false);
            $('#role_list').val("");
            $("#role_list").hide();
          }else{
            $('#role_based').prop('checked', true);
            $("#role_list").show();
          }
          $(".selected_role").val(selected_role_ids).trigger('change');
        }
        $.each(data.result, function(key, value) {
          arrs.push(key);
        });
        $("#users").val(arrs).trigger('change');
      }
    });

    });


   //  $('input[type="checkbox"]').change(function() {
   //   var checked_arr=[];
   //   var customerid=$('#customers').val();
   //   var remove_allocated_arr=[];
   //   var checkbox=$(this);
   //   $('input[type=checkbox]').each(function () {
   //     if (this.checked) {
   //       checked_arr.push($(this).attr('id'));
   //     }
   //   });
   //   $.ajax({
   //    url: "{{route('email-template-allocation.allocated-users')}}",
   //    type: 'GET',
   //    data: {userid:checked_arr,customer:customerid},
   //    success: function(data) {
   //      $.each(data.allocated_list_remove, function(key, value) {
   //        remove_allocated_arr.push(key);
   //      })
   //      ;
   //      $.each(data.full_data, function(key, value) {
   //        $('#users  option[value="'+key+'"]').prop("selected", true).change();
   //      })
   //      ;
   //      $('#users > option:selected').each(function() {
   //        if(jQuery.inArray($(this).val(),remove_allocated_arr) != -1)
   //        {
   //          $('#users  option[value="'+$(this).val()+'"]').prop("selected", false).change();
   //        }
   //      });
   //    }
   //  });

   // });



    $('#email-template-allocation-form').submit(function (e) {
      e.preventDefault();
      var customerid=$('#customers').val();
      var $form = $(this);
      url = "{{ route('email-template-allocation.store') }}";
      var formData = new FormData($('#email-template-allocation-form')[0]);
      if(customerid==0 && $('input[name=show_customer_block]')==1){
        swal({
          title: "You have chosen all customers",
          text: "This will reset all previous allocations! Are you sure you want to continue?",
          type:'warning',
          showCancelButton: true,
          confirmButtonText: "Confirm",
          cancelButtonText: "Cancel",
          closeOnConfirm: true,
          closeOnCancel: false
        },
        function(inputValue){
  //Use the "Strict Equality Comparison" to accept the user's input "false" as string)
  if (inputValue===false) {
   swal.close();
 } else {
   saveFormData(url,formData,$form);
 }
});
      }
      else
      {
       saveFormData(url,formData,$form);
     }
   });


    var type_id = JSON.parse('{!! json_encode($type_id) !!}');
    var customer_id = JSON.parse('{!! json_encode($customer_id) !!}');
    if(type_id!=null || customer_id!=null){
     $("select[name='type']").val(type_id);
     showHideCustomerBlock(type_id);
     $("select[name='customer']").val(customer_id).trigger('change');
   }
 });

  function saveFormData(url,formData,$form)
  {
   $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: url,
    type: 'POST',
    data: formData,
    success: function (data) {
      if (data.success) {
        if(data.result == true){
          result = "Users has been allocated successfully";
        }else{
          result = "Users has been allocated successfully";
        }
        swal({
          title: "Saved",
          text: result,
          type: "success",
          confirmButtonText: "OK",
        },function(){
          $('.form-group').removeClass('has-error').find('.help-block').text('');
          window.location.href = "{{ route('allocation.emaillist') }}";
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
 }
 function showHideCustomerBlock(type_id){
  var customer_based = JSON.parse('{!! json_encode($customer_based) !!}');
  if(customer_based[(type_id)]==1)
  {
    $('.block-for-customers').show();
    $('input[name=show_customer_block]').val(1);
    $("select[name='customer']").val(null).trigger('change');

  }
  else{
   $('.block-for-customers').hide();
   $('select[name="customer"]').val('0')
   $('input[name=show_customer_block]').val(0);
 }

}
</script>
<style>
.modal-footer{
  margin-left: -0.7em;
}
</style>
@stop
