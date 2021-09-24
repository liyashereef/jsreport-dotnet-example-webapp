@extends('layouts.app')
@section('title', 'Employee availability - Entry form')
@section('content_header')
<h1 class="ts-approve">Employee availability - Entry form</h1>
<head>
   <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<link rel="stylesheet" href="{{config('app.url')}}/css/custom.css" type="text/css" />
@stop
@section('content')
<div class="table_title">
   <h4>
   Employee Availability - Entry Form
</div>
<div class="container">
   <div class="form-group row" id="emp">
      <div class="col-md-2">Employee Name</div>
      <div class="col-md-3">
         <select id="employee" class="form-control" name="employee_name" required>
            <option value=''>Select</option>
           {{--  @foreach ($employeeslist as $key=>$employees)
            <option value="{{$key}}">{{$employees}}</option>
            @endforeach --}}
         </select>
      </div>
       <div class="form-control-feedback">
    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('to', ':message') !!}
</div>
   </div>
   <div class="row">
<nav class="col-lg-12 col-md-9 col-sm-8">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="col-lg-6 col-md-6 col-sm-6 nav-item nav-link active m-0 b-0"  id="nav-availability-tab"  data-toggle="tab" href="#availability" role="tab" aria-controls="nav-availability" aria-selected="true"><span>Available</span></a>
        <a class="col-lg-6  col-md-6 col-sm-6 nav-item nav-link m-0 b-1" id="nav-unavailability-tab" data-toggle="tab" href="#unavailability" role="tab" aria-controls="nav-unavailability" aria-selected="false"><span>Unavailable</span></a>
    </div>
</nav>
</div>
<div class="tab-content" style="margin-top: 30px;">
     <div class="tab-pane container active" id="availability">
         {{ Form::open(array('id'=>'schedule-form','class'=>'form-horizontal', 'method'=> 'POST' )) }}

   <div class="row">
      <div class="col-md-2 day-item header-day"></div>
      <input type="hidden" name="shiftkeyindex" value="{{count($shiftarray)}}">
      @foreach ($shiftarray as $shiftskey=>$shiftvalue)
      <div class="col-md-1" style="height:40px">{{$shiftvalue}}</div>
      @endforeach
   </div>
     <input type="hidden" name="array_shiftindex" value="{{count($array_shifts)}}">
   @foreach ($array_shifts as $key=>$array_shift)
   <div class="row table shifts">
      <div class="col-md-2">
         {{$array_shift["shift_name"]}}
      </div>
      @for($i=0;$i<$shiftcount;$i++)
      <div class="col-md-1 row_{{$array_shift["shift_name"]}}" style="height:40px;margin-left: 5px;">
      @if($array_shift["shift_name"]."-".$shiftarray[$i]!="All-Any Day" && trim($array_shift["id"])!=1 && $i>0)
      <input style="text-align:center;" class="form-control gridcheckbox shiftiming {{$shiftarray[$i]}} row{{$array_shift["id"]}} col{{$i}} matrix-{{$array_shift["shift_name"]}}-{{$array_shift["id"]}}"
      attr-whichrow="row{{$array_shift["id"]}}" type="checkbox" attr-whichday="{{$shiftarray[$i]}}" attr-col="col{{$i}}"
      attr-all="{{$array_shift["shift_name"]}}" attr_headid="matrix-{{$i}}" id="matrix-{{$array_shift["shift_name"]}}-{{$i}}"
      name="{{strtolower($array_shift["shift_name"])}}-{{strtolower($shiftarray[$i])}}" value="{{$i}}" attr_sideid="matrix-{{$array_shift["id"]}}" attr_id="matrix-{{$shiftarray[$i]}}-{{$i}}"  attr-whichcolumn="column{{$i}}" data-id="matrix-{{$array_shift["shift_name"]}}-{{$array_shift["id"]}}"/>
      @elseif($array_shift["shift_name"]."-".$shiftarray[$i]!="All-Any Day" && trim($array_shift["id"])!=1 && $i==0)
      <input class="form-control gridcheckbox shiftiming {{$shiftarray[$i]}} matrix-{{$array_shift["id"]}}"  attr-whichrow="row{{$array_shift["id"]}}"
      type="checkbox" attr-whichday="{{$shiftarray[$i]}}" attr-col="col{{$i}}" attr-all="{{$array_shift["shift_name"]}}"
      attr_headid="matrix-{{$array_shift["shift_name"]}}-0" name="{{strtolower($array_shift["shift_name"])}}-{{strtolower($shiftarray[$i])}}" id="matrix-{{$array_shift["shift_name"]}}-{{$i}}" data-id="matrix-{{$array_shift["id"]}}"
      value="{{$i}}" attr_sideid="matrix-{{$array_shift["id"]}}" attr_id="matrix-{{$shiftarray[$i]}}-{{$i}}" attr-whichcolumn="column{{$i}}"/>
      @elseif(trim($array_shift["id"])==1  )
      <input class="form-control gridcheckbox shiftiming {{$shiftarray[$i]}} matrix-{{$array_shift["id"]}}" attr-whichrow="row{{$array_shift["id"]}}"
      type="checkbox" attr-whichday="{{$shiftarray[$i]}}" attr-col="col{{$i}}" attr-all="{{$array_shift["shift_name"]}}"
      attr_headid="matrix-{{$i}}" id="matrix-{{$i}}" name="{{strtolower($array_shift["shift_name"])}}-{{strtolower($shiftarray[$i])}}" data-id="matrix-{{$array_shift["shift_name"]}}-{{$array_shift["id"]}}"
      value="{{$i}}" attr_sideid="matrix-{{$array_shift["id"]}}" attr_id="matrix-{{$shiftarray[$i]}}-{{$i}}" attr-whichcolumn="column{{$i}}"/>
      @endif
   </div>
   @endfor
</div>
@endforeach


  @canany(['update_all_employee_availability','update_allocated_employee_availability'])
<div class="row">
   <div class="col-md-2"></div>
    <div class="col-sm-6 text-center">
        {{ Form::reset('Cancel', array('class'=>'btn cancel','onClick'=>"window.location.reload();"))}}
        {{ Form::submit('Save',array('class'=>'btn submit','id'=>'save'))}}
    </div>
</div>
@endcan

{{ Form::close() }}
<div id="last_update_details" class="last_update_details" style="padding-top: 30px;"></div>
</div>
 <div class="tab-pane fade" id="unavailability">
 @canany(['update_delete_all_employee_unavailability','update_delete_allocated_employee_unavailability'])
  <div class="add-new mb-3" data-title="Add New Unavailability">Add
    <span class="add-new-label">New</span>
  </div>
 @endcan
<table class="table table-bordered" id="unavailability-table">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>From</th>
            <th>To</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div id="last_update_details" class="last_update_details" style="padding-top: 30px;"></div>
 </div>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'unallocate-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null,array('id' => 'edited_id')) }}
            <div class="modal-body">
              <div class="form-group" id="from">
                    <label for="from" class="col-sm-3 control-label">From</label>
                    <div class="col-sm-9">
                        {{ Form::text('from',null,array('class' => 'form-control datepicker', 'Placeholder'=>'From', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="to">
                    <label for="to" class="col-sm-3 control-label">To</label>
                    <div class="col-sm-9">
                        {{ Form::text('to',null,array('class' => 'form-control datepicker', 'Placeholder'=>'To', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="comments">
                    <label for="comments" class="col-sm-3 control-label">Comments</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('comments',null,array('class' => 'form-control', 'Placeholder'=>'Comments','id'=>'employee_comments', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
               {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
</div>
<input type="hidden" name="colslength" id="colslength" value="{{$shiftcount}}" />
<input type="hidden" name="rowlength" id="rowlength" value="{{$rowlength}}" />
</div>
@stop
@section('scripts')
<script>


   $(document).ready(function(){
       $("#employee").select2();
       var url = '{{route("employeeList.list","nav-availability-tab")}}';
      populateSelectDropdown(url);
   })

   var colonoperations = function(self,event)
   {
       var flag=false;
       //var cols = $(self).attr("attr_headid");
       var row = $(self).attr("attr-whichcolumn");
       var col = $(self).attr("attr-col");
       $("."+row).each(function(index,currentelement){
           var elemid =currentelement["id"];
           if($(currentelement).prop("checked") == false){
              flag=false;
              return flag;
          }
          else
          {
             flag=true;
             uncheckedelementhead = $(currentelement).attr("attr_sideid");
         }
     });
       if( flag==true)
       {

         $("."+uncheckedelementhead).prop("checked",true);
     }
     $("."+col).each(function(index,currentelement){
    var head=$(currentelement).attr("attr_sideid");
    var each_elem=$('.shifts').find("[attr_sideid='" + head + "']")
    $(each_elem).each(function(index,currentelement){
    var elem_id=$(currentelement).attr('data-id')
    if(elem_id!=head)
    {

      if($(currentelement).prop("checked") == false){
       flag=false;
       return flag;
   }
   else
   {
     flag=true;
     uncheckedelementhead =  $("input[id="+head+"]");
   }

    }

   });
    if( flag==true)
   {
     $("input[data-id="+head+"]").prop("checked",true);
   }
   });
   }
   var coloffoperations = function(self,event)
   {
   var col = $(self).attr("attr_headid");
   var row = $(self).attr("attr-whichrow");
   $("."+row).each(function(index,currentelement){
   var elemid =currentelement["id"];
   if($(currentelement).prop("checked") == false){
      var uncheckedelement = elemid;
      var uncheckedelementhead = $(currentelement).attr("attr_headid");
      $("#"+uncheckedelementhead).prop("checked",false);
   }
   });
   }

   var rowonoperations = function(self,event){
   var flag=false;
   var col = $(self).attr("attr-col");
   //var row = $(self).attr("attr-whichcolumn");
   var row=$(self).attr("attr-whichrow")
   $("."+col).each(function(index,currentelement){
    var elemid =$(currentelement).data('id')
    if($(currentelement).prop("checked") == false){
       flag=false;
       return flag;
   }
   else
   {
     flag=true;
     uncheckedelementhead = $(currentelement).attr("attr_headid");
   }
   });
   if( flag==true)
   {

     $("#"+uncheckedelementhead).prop("checked",true);
   }
   $("."+row).each(function(index,currentelement){
    var head=$(currentelement).attr("attr_headid");
    var each_elem=$('.shifts').find("[attr_headid='" + head + "']")
    $(each_elem).each(function(index,currentelement){
    var elem_id=currentelement['id']
    if(elem_id!=head)
    {

      if($(currentelement).prop("checked") == false){
       flag=false;
       return flag;
   }
   else
   {
     flag=true;
     uncheckedelementhead =  $("input[id="+head+"]");
   }

    }

   });
    if( flag==true)
   {
     $("input[id="+head+"]").prop("checked",true);
   }
   });

   }
   var rowoffoperations = function(self,event){
   var col = $(self).attr("attr-col");
   var row = $(self).attr("attr-whichcolumn");
   $("."+col).each(function(index,currentelement){
    var elemid =$(currentelement).data('id')
    if($(currentelement).prop("checked") == false){
       var uncheckedelement = elemid;
       var uncheckedelementhead = $(currentelement).attr("attr_sideid");
       $("."+uncheckedelementhead).prop("checked",false);
       }
           });

   }
   $(".shiftiming").on("click",function(event){
   var shiftimingchecked = this.checked;
   var allvariable = $(this).attr("attr-all");
   if(allvariable == "All")
   {
       if(shiftimingchecked == true)
       {
           var col = $(this).attr("attr-col");
           var whichday = $(this).attr("attr-whichday");
           $("."+col).prop( "checked", true );
       }
       else
       {
           var whichday = $(this).attr("attr-whichday");
           var col = $(this).attr("attr-col");
           $("."+col).prop( "checked", false );
       }

   }

   var daytype= $(this).attr("attr-whichday");
   var rowclass = $(this).attr("attr-whichrow");
   if(daytype == "Any Day"){
       if(shiftimingchecked == true)
       {
           $("."+rowclass).prop("checked",true);
       }
       else{
           $("."+rowclass).prop("checked",false);
       }
   }
   if(daytype == "Any Day" && allvariable == "All"){

       if(shiftimingchecked == true)
       {
           $(".gridcheckbox").prop("checked",true);
       }
       else{
           $(".gridcheckbox").prop("checked",false);
       }
   }
   var shiftkeyindex=$('input[name=shiftkeyindex]').val();
   var array_shiftindex=$('input[name=array_shiftindex]').val();
   if(shiftimingchecked == true)
   {
       colonoperations(this,event);
       rowonoperations(this,event);
      if ($(".shifts input:checkbox:checked").length == (shiftkeyindex*array_shiftindex)-1)
    {
        $("#matrix-0").prop("checked",true);
     }
   }
   else
   {
       $("#matrix-0").prop("checked",false);
       coloffoperations(this,event);
       rowoffoperations(this,event);
   }


   });
    $('#unallocate-form').submit(function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = new FormData($('#unallocate-form')[0]);
    employee_id=$('#employee').val();
    formData.append('employee_id', employee_id);
     $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
    if($('#employee').val()=="")
    {
    $('#emp').addClass('has-error').find('.help-block').text('Please choose employee');
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return;
    }
      $.ajax({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       url: '{{ route('employee.unAvailabilityStore') }}',
       type: 'POST',
       data: formData,
       success: function (data) {
           if (data.success) {
               $('.last_update_details').html('<div><b>'+(data.last_updated_data.last_updated_date? 'Last Updated : ' + data.last_updated_data.last_updated_date: '')+'' +(data.last_updated_data.last_updated_user? '<br />Updated By : '+data.last_updated_data.last_updated_user: '')+ '</b></div>');
               var editedid=$('#edited_id').val();
               console.log(editedid)
               var text_str='Employee Unavailability has been successfully created';
               if(editedid !='')
               {
                var text_str='Employee Unavailability has been successfully updated';
               }
               swal({
                   title: 'Success',
                   text:   text_str,
                   icon: "success",
                   type: 'success',
                   button: "OK",
             },
            function () {
                 $("#myModal").modal('toggle');
                 $('#unavailability-table').DataTable().ajax.reload();
                        });

             }
            else {
               console.log(data);
           }
       },
       fail: function (response) {
           console.log(response);
       },
       error: function (xhr, textStatus, thrownError) {
           associate_errors(xhr.responseJSON.errors, $form);
       },
               contentType: false,
               processData: false,
           });
 });

    $('.modal').on('hidden.bs.modal', function(){
    $(this).find('form')[0].reset();
    });


   $('#schedule-form').submit(function (e) {
    e.preventDefault();
    var $form = $(this);
    if($('#employee').val()=="")
    {
    $('#emp').addClass('has-error').find('.help-block').text('Please choose employee');
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return;
    }
   var checkbox_arr ={};
   $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
   employee_id=$('#employee').val();
   $.each($form.find('input[type="checkbox"]:checked'), function(){
       checkbox_arr[$(this).attr("name")] = $(this).val();
   });
   var checkbox_values = JSON.stringify(checkbox_arr);
   $.ajax({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       url: '{{ route('employee.scheduleStore') }}',
       type: 'POST',
       data: { checkbox_values: checkbox_values,employee_id: employee_id },
       success: function (data) {
           if (data.success) {
            $('.last_update_details').html('<div><b>'+(data.last_updated_data.last_updated_date? 'Last Updated : ' + data.last_updated_data.last_updated_date: '')+'' +(data.last_updated_data.last_updated_user? '<br />Updated By : '+data.last_updated_data.last_updated_user: '')+ '</b></div>');
               swal({
                   title:'Success',
                   text: 'Employee availability has been successfully updated',
                   icon: "success",
                   type: 'success',
                   button: "OK",
             });
           } else {
               console.log(data);
           }
       },
       fail: function (response) {
           console.log(response);
       },
       error: function (xhr, textStatus, thrownError) {
           associate_errors(xhr.responseJSON.errors, $form);
       },
           });
   });

   $('select').on('change', function() {
   $('#emp').removeClass('has-error').find('.help-block').text('');
   employee_id= $(this).val();
   $('input[type=checkbox]').prop('checked',false)
   scheduleShift(employee_id);
   loadDatatable(employee_id);
   });


   function scheduleShift(employee_id)
   {
      $('.last_update_details').html('');
      $.ajax({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       url: '{{ route('employee.getSchedule') }}',
       type: 'POST',
       data: { employee_id: employee_id },
       success: function (data) {
        console.log(data)
           if (data.success) {
               $.each(data.data, function( index, value ) {
                    $('input:checkbox[name="' + value + '"]').prop('checked',true);
               });
             (data.flag==false)?$('#save').hide():$('#save').show();
             console.log(data.last_updated_data);
             $('.last_update_details').html('<div><b>'+(data.last_updated_data.last_updated_date? 'Last Updated : ' + data.last_updated_data.last_updated_date: '')+'' +(data.last_updated_data.last_updated_user? '<br />Updated By : '+data.last_updated_data.last_updated_user: '')+ '</b></div>');
           } else {
               console.log(data);
           }
       },
       fail: function (response) {
           console.log(response);
       },
       error: function (xhr, textStatus, thrownError) {
           associate_errors(xhr.responseJSON.errors, $form);
       },
           });
   }
$('.add-new').on('click', function () {
  if($('#employee').val()=="")
    {
    $('#emp').addClass('has-error').find('.help-block').text('Please choose employee');
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return;
    }
    var title = $(this).data('title');
    $("#myModal").modal();
    $('#other_category_name_id').hide();
    $('#is_valid').hide();
    $('#myModal form').trigger('reset');
    $('#myModal').find('input[type=hidden]').val('');
    $("#document_category_details option:not(:first)").remove().trigger('change');
    $('#myModal .modal-title').text(title);
    $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
});
function loadDatatable(id)
{
  $('#unavailability-table').DataTable().clear().draw();
        var table = $('#unavailability-table').DataTable({
                destroy:true,
                bProcessing: false,
                processing: true,
                serverSide: false,
                fixedHeader: false,
                deferLoading: 0,
                ajax: {
                    url: "{{ route('employee.unAvailabilityList') }}", // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function (d) {
                        d.id = Number(id);
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                columnDefs: [ {
                                "searchable": false,
                                "targets": 0,
                            } ],
                order: [[ 0, "desc" ]],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                 { data: "id" ,visible:false  },
                 { data: "DT_RowIndex" ,name:''  },
                 { data: "from" },
                 { data: "to" },
                 { data: "comments" },


                          {
                    data: null,
                    sortable: false,
                    render: function (o) {

                        var actions = '';
                        if(o.editable==true){
                        actions += '<a href="#" class="edit fa fa-pencil  " data-id=' + o.id + '></a>  '
                        actions += ' <a href="#" class="delete fa fa-trash-o  " data-id=' + o.id + '></a>';
                      }
                        else{
                        actions += '<a href="#" class="fa fa-pencil   fa-disabled" ></a>  '
                        actions += ' <a href="#" class="fa fa-trash-o  fa-disabled" ></a>';
                      }

                        return actions;
                    },
                }


                ]
            });
}
            $('#unavailability-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('unavailability.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Unavailability has been deleted successfully';
            var url = url;
            var table = $('#unavailability-table').DataTable();
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
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        swal("Deleted", message, "success");
                        if (table != null) {
                            table.ajax.reload();
                        }
                    } else {
                        console.log(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });
        });


  $('#nav-unavailability-tab,#nav-availability-tab').on("click",function(event)
  {
   event.preventDefault();
   var id = $(this).attr('id');
   var url = '{{route("employeeList.list",":type")}}';
   var url = url.replace(':type', id);
   populateSelectDropdown(url);
  });
   function populateSelectDropdown(url) {
    employee_id=$('#employee').val();
    sessionStorage.setItem("employee_id", employee_id);
   $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                  $('#employee').find('option:not(:first)').remove();
                    if (data.success) {
                      var arr=[];
                      $.each(data.employee_id, function(key, value) {
                    //   $('#employee').append($("<option></option>").attr("value",value.id)
                    // .text(value.name));
                    var newOption = new Option(value.name, value.id, false, false);
                     $('#employee').append(newOption);
                     arr.push(value.id)
                    });

                      if(arr.includes(Number(sessionStorage.getItem("employee_id")))){
                        $('#employee').val(sessionStorage.getItem("employee_id"));
                       loadDatatable(sessionStorage.getItem("employee_id"));
                     }
                      else
                      {
                       sessionStorage.setItem("employee_id", '');
                      loadDatatable(sessionStorage.getItem("employee_id"));
                      }

                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
 }
     /* Unavailability Edit - Start */
        $("#unavailability-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("employeeUnavailability.edit",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="from"]').val(data.from);
                        $('#myModal input[name="to"]').val(data.to)
                        $("#myModal textarea#employee_comments").val(data.comments);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Unavailability ")
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Unavailability Edit - End */
</script>
@stop
@section('css')
<style>
input[type=checkbox]{
  width:25px;
  height: 20px;
  margin-left: 10px;
   }
.nav-tabs .nav-link.active {
  background: #f26321 !important;
    }

.nav-tabs .nav-link.active  span {
  color: #ffffff !important;
    }
.nav-link
{
  text-align: center;
}
#nav-tab  a {
  padding: 5px 20px;
  margin-right: 3px;
  border: 1px solid #003A63;
  display: inline-block;
  color: #003A63;
  border-radius: 3px;
    }
.fa-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
@stop
