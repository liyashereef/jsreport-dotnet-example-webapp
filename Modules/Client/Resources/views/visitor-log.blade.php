@extends('layouts.cgl360_visitor_log_layout')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
    .table_title h4 {
    /* margin: 15px 0px; */
    font-family: 'Montserrat', sans-serif !important;
    font-weight: bold;
    font-size: 16pt;
    color:rgb(51,63,80);
    margin-left:-24px;
}
input:focus{
    outline: none;
}
    </style>
</head>

@section('content')
<div class="row  col-sm-12 col-md-12 col-lg-12" style="padding-bottom: 6px;">
<div class="table_title col-sm-12 col-md-12 col-lg-4 icon">
    <h4>Visitor Log

<span class="template-filter-btn"  style="display: none;"  id="icon-fw">
<i class="fas fa-caret-square-down"></i></span>

</h4>
</div>

<div id="button_div" class="col-sm-12 col-md-12 col-lg-8" style="float:right;padding-right: 0px;">

 @can('exit_visitorlog')
  <div class="exit" data-title="Exit"  onclick="if(confirm('Are you sure to exit?')){ event.preventDefault();exitSession(); }">
    <span class="exit-label">Exit</span>
  </div>
  @endcan
   @can('create_visitorlog')
  <div class="add-new" onclick="showTemplateList()" data-title="Add-new">Add
    <span class="add-new-label">New</span>
  </div>
  @endcan

  <button id="filter-btn"  class="filter-btn"><i class="fas fa-filter" ></i></button>
  <span style="color: #003b63; float: right; margin-top: 4px; margin-right:20px; font-family: 'Montserrat', sans-serif !important;">
    @if(null !==Session::get('default_client_name'))
            {{ Session::get('default_client_name') }}
    @endif
    </span>
</div>
</div>

<div id="dashboard_div">
<div class="row" id="counts-div" style="display: none;padding-bottom: 20px;">

    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding" >

        <div class="visit-log-card" data-id="0">
           Total
           <div class="visit-log-count-text">
             {{$total_visitors_count}}
           </div>
        </div>
    </div>

@foreach($type_list as  $key => $name )


     @if(array_key_exists($key, $recently_checkin_type_count))
     <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">

        <div class="visit-log-card" data-id="{{$key}}">

            {{$name}}s

            <div class="visit-log-count-text">
             {{$recently_checkin_type_count[$key]}}
            </div>
         </div>

    </div>
    @else
     <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 visit-log-padding">
       <div class="visit-log-card" data-id="{{$key}}">
            {{$name}}s

            <div class="visit-log-count-text">
             0
            </div>
        </div>
       </div>
     @endif

      @endforeach

 </div>

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 card-padding">

        <div class="card-table">

            <div class="card-header">
                <i class="fas fa-users"></i><span class="pl-2">Recently Checked-In</span>
            </div>

            <div class="card-body table-responsive">
                <table id="recently-checkedin-table" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10%">Created Time</th>
                            <th width="20%">Name</th>
                            <th width="10%">Check-in Time</th>
                            <th width="10%">Type</th>
                            <th width="10%">Company</th>
                            <th width="10%">Visiting Person</th>
                            <th width="10%">QR Added</th>
                            <th width="10%">Type</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
 </div>

<br>
<div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" id="checkout_row">

        <div class="card-table">

            <div class="card-header">
                <i class="fas fa-users"></i><span class="pl-2">Recently Checked-Out</span>
            </div>

            <div class="card-body table-responsive">
                <table id="recently-checkedout-table" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10%">Created Time</th>
                            <th width="30%">Name</th>
                            <th width="25%">Check-in Time</th>
                            <th width="25%">Check-out Time</th>
                            <th width="25%">Time Spent</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 card-padding overstay">

        <div class="card-table">

            <div class="card-header">
                <i class="fas fa-users"></i><span class="pl-2">Overstayed</span>
            </div>

            <div class="card-body table-responsive" >
                <table id="overtime-visitors" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10%">Id</th>
                            <th width="30%">Name</th>
                            <th width="25%">Check-in Time</th>
                            <th width="25%">Check-out Time</th>
                            <th width="25%">Overstay Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

 </div>
</div>

<div id="addnew_div" style="display: none;">
  <div id="show_template">
     <div class="form-group row" id="template">

       <label for="note" class="col-sm-4 col-form-label">Choose Template<span class="mandatory">*</span></label>
        <div class="col-sm-6">
           {{ Form::select('template',[''=>'Please Select'],null,array('class'=>'form-control','id'=>'template_select')) }}
          <small class="help-block"></small>
        </div>
      </div>
   </div>
   <div id="template_form">

   </div>
</div>

<div id="message"></div>


 <div class="modal fade" id="myModal" data-backdrop="static"  role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Choose Customer</h4>
                   {{--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> --}}
                </div>
                {{ Form::open(array('url'=>'#','id'=>'visitor-log-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="form-group row" id="customer">
                       {{-- <label for="customer" class="col-sm-3 control-label">Choose Customer</label> --}}
                        <div class="col-sm-9">
                            {{ Form::select('customer',[''=>'Please  Select  Customer']+$project_list,null,array('class'=>'form-control select2','id'=>'customer_select','required'=>true)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                     </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn submit btn-edit','id'=>'mdl_save_change'))}}
                   {{--  <button class="btn btn-edit" data-dismiss="modal" aria-hidden="true" onclick="$('#shift-journal').trigger('reset');">Cancel</button> --}}
                </div>
                {{ Form::close() }}

           </div>
        </div>
    </div>
    <div class="modal fade" id="checkoutModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">Check-out</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                   {{--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> --}}
                </div>
                {{ Form::open(array('url'=>'#','id'=>'checkout-form','class'=>'form-horizontal')) }}
                {{csrf_field()}}
                <div class="modal-body">
                    <input type="hidden" name="visitor_id" value="">
                    <div style="color:#717980;" class="col-sm-12">Visitor Signature:</div>
                     <div id="signature-pad" class="signature-pad">
                       <div class="signature-pad--body">
                       <canvas style="max-height: 180px;" id="signature-content"></canvas>
                         </div>
                          <div class="signature-pad--footer">
                          <div class="signature-pad--actions">
                           <div style="" class="col-md-12">

                             <button type="button" class="button-clear" data-action="clear">Clear</button>
                           </div>
                          </div>
                          </div>
                         </div>
                      <br> <br>
                    <div class="form-group row" id="notes">
                        <div class="col-sm-12">
                            {{ Form::textarea('notes',null,array('class'=>'form-control','id'=>'checkout_note','placeholder'=>'Please Enter Notes','style'=>"height: 100px;width:670px;",'maxlength'=>700)) }}
                            <small id="checkout_message" class="help-block"></small>
                        </div>
                    </div>
                     </div>
                <div style="text-align: center;" >
                    {{ Form::submit('Checkout', array('class'=>'checkout-btn','id'=>'mdl_save_change'))}}
                   {{--  <button class="btn btn-edit" data-dismiss="modal" aria-hidden="true" onclick="$('#shift-journal').trigger('reset');">Cancel</button> --}}
                </div>
                {{ Form::close() }}

           </div>
        </div>
    </div>
    <div id="mydiv"></div>
@stop
@section('scripts')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ asset('js/webcam.js') }}"></script>
    <script src="{{ asset('js/signature_pad.umd.js') }}"></script>
<script src="{{ asset('js/signature_app.js') }}"></script>
    <link href="{{ asset('css/ie9.css') }}">
    <link href="{{ asset('css/signature-pad.css') }}">
<script>

$('#checkoutModal').on('shown.bs.modal', function (e) {
resizeCanvas()
})

$(document).ready(function() {
if(localStorage.getItem("visitor-log-filter")=="true")
  {
    $('#counts-div').show();
  }
     var customer_id = {!! json_encode(Session::get('default_customer')); !!};
     var overstay = {!! json_encode($overstay); !!};
    if(overstay==0)
    {
     $('.overstay').hide();
     $('#checkout_row').toggleClass('col-xs-12 col-sm-12 col-md-12 col-lg-12 card-padding');
    }
    if (customer_id==undefined)
    {
      $("#myModal").modal('show');
    }

      $("#select2").select2({
              dropdownParent: $("#myModal")
      });
        $('.select2').select2();

        $.fn.dataTable.ext.errMode = 'throw';
        /* to get team profile - start */
var customer_id = {!! json_encode(Session::get('default_customer')); !!};
            var table = $('#recently-checkedin-table').DataTable({
                bProcessing: false,
               destroy: true,
                //dom: 'lfrtBip',
                buttons: [],
                //processing: false,
                //serverSide: true,
                bFilter: true,
                responsive: true,
                ajax: {
                    url: "{{ route('getCurrentLog') }}",
                    type: 'GET',
                    data:{'customer_id':customer_id},
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                order: [
                    [0, "desc"]
                ],
                "rowCallback": function (row, data, index) {
                    //$(row).find('td').css('white-space', 'nowrap');
                },
                dom: "l<'input-group' f <'input-group-append'>>rtip",
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                columns: [
                 {data: 'created_at', name: 'created_at',visible:false},
                 {data: null,
                    render:function(o)
                    {
                        if(o.picture_file_name=='' || o.picture_file_name==null)
                        {
                         var picture_file_name = 'no_avatar.jpg';
                        actions="<img class='details_load' data-id=" + o.id + "  style='margin: 0px 10px' height='40px' width='40px' src='{{asset("images") }}/"+ picture_file_name +"'>";
                         }
                         else
                         {

                        actions="<img class='details_load' data-id=" + o.id + "  style='margin: 0px 10px' height='40px' width='40px' src='{{asset("visitor_log")}}" + '/'  + o.id + '/' + o.picture_file_name + "'>";
                         }
                         actions+="<span class='details_load' data-id='" + o.id +"' >"+o.full_name+"</span>";
                        return actions;


                    }},
                {data: 'checkin', name: 'checkin'},
                {data: 'type', name: 'type'},
                {data: 'name_of_company', name: 'name_of_company'},
                {data: 'whom_to_visit', name: 'whom_to_visit'},
                {data: 'qr_added', name: 'qr_added'},
                {
                    data:null,
                    name: 'check_in_option',
                    render: function (o) {
                      if(o.check_in_option == null){
                          return '--';
                      }
                      return o.check_in_option;
                    },
                },
                 {
                    data: null,
                    orderable:false,
                    render: function (o) {
                            actions = '<a href="javascript:void(0);" class="checkout" data-id=' + o.id + '><i class="fas fa-sign-out-alt fa-2x"></a>';

                        return actions;
                    }
                    },
                ],
            });


            var table = $('#recently-checkedout-table').DataTable({
                bProcessing: false,
                destroy: true,
                //dom: 'lfrtBip',
                buttons: [],
                //processing: false,
                //serverSide: true,
                bFilter: true,
                responsive: true,
                ajax: {
                    url: "{{ route('getCheckoutLog') }}",
                    type: 'GET',
                    data:{'customer_id':customer_id},
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                order: [
                      [0, "asc"]
                ],
                "rowCallback": function (row, data, index) {
                    //$(row).find('td').css('white-space', 'nowrap');
                },
                dom: "l<'input-group' f <'input-group-append'>>rtip",
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                columns: [
                {data: 'updated_at', name: 'updated_at',visible:false},
                {data: null,
                    render:function(o)
                    {
                        if(o.picture_file_name=='' || o.picture_file_name==null)
                        {
                         var picture_file_name = 'no_avatar.jpg';
                        actions="<img class='details_load' data-id=" + o.id + "  style='margin: 0px 10px' height='40px' width='40px' src='{{asset("images") }}/"+ picture_file_name +"'>";
                         }
                         else
                         {

                        actions="<img class='details_load' data-id=" + o.id + "  style='margin: 0px 10px' height='40px' width='40px' src='{{asset("visitor_log")}}" + '/'  + o.id + '/' + o.picture_file_name + "'>";
                         }
                         actions+="<span class='details_load' data-id='" + o.id +"' >"+o.full_name+"</span>";
                        return actions;


                    }},

                {data: 'checkin', name: 'checkin'},
                {data: 'checkout', name: 'checkout'},
                { data:null,render:function(o)
                 {
                        if(o.checkout=='--')
                        {
                        return '--';
                       }
                        else
                        {
                         var d1 = new Date(o.checkout_datetime.replace(/-/g, "/"));
                         var d2 = new Date(o.checkin_datetime.replace(/-/g, "/"));
                         var milli = d1.getTime()-d2.getTime();
                         var minutes = Math.floor(milli / 60000);
                         return timeConvert(minutes);
                      }
                    }
                }
                ],
            });


             var table = $('#overtime-visitors').DataTable({
                bProcessing: false,
                destroy: true,
                //dom: 'lfrtBip',
                buttons: [],
                //processing: false,
                //serverSide: true,
                bFilter: true,
                responsive: true,
                ajax: {
                    url: "{{ route('getOvertimeLog') }}",
                    type: 'GET',
                    data:{'customer_id':customer_id},
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                order: [
                      [0, "desc"]
                ],
                "rowCallback": function (row, data, index) {
                    //$(row).find('td').css('white-space', 'nowrap');
                },
                dom: "l<'input-group' f <'input-group-append'>>rtip",
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
                columns: [
                 {data: 'id', name: 'id',visible:false},
                {data: 'full_name', name: 'full_name'},
                 {data: 'checkin', name: 'checkin'},
                {data: 'checkout', name: 'checkout'},
                {data:null,render:function(o)
                    {
                        if(o.checkout=='--' || o.overstay =='--')
                        {
                        return '--';
                       }
                        else
                        {
                       var today = new Date();
                      const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                      var month = '' + (today.getMonth() + 1);
                       // const month = today.toLocaleString('en-us', { month: 'long' });
                       var dd = String(today.getDate()).padStart(2, '0');
                        var yyyy = today.getFullYear();
                        var dates= monthNames[month]+' '+dd+','+' '+yyyy+' ';
                        start = o.overstay, //eg "09:20 PM"
                        end = o.checkout, //eg "10:00 PM"
                        diff_in_min = ( Date.parse(dates + end) - Date.parse(dates + start) ) / 1000 / 60;
                        return timeConvert(diff_in_min)
                      }
                    }

                    }

                ],
            });

});




function timeConvert(n) {

var num = n;
var hours = (num / 60);
var rhours = Math.floor(hours);
var minutes = (hours - rhours) * 60;
var rminutes = Math.round(minutes);
return  rhours + " hour(s)  " + rminutes + " minute(s).";
}
$('#recently-checkedin-table').on('click', '.checkout', function(e){
      $('input[name=visitor_id]').val($(this).data('id'));
      $("#checkout_note").val('');
      $("#checkoutModal").modal('show');
});

$('#recently-checkedin-table, #recently-checkedout-table').on('click', '.details_load', function(e){
        var id=$(this).data('id');
        var url= '{{route("visitor-log.view",":id")}}';
        var url = url.replace(":id", id);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:url,
                type: 'GET',
                success: function (data) {
                    console.log(data)
                    if (data.success) {
                        $("#viewModal").html('');
                        $("#mydiv").html(data.content);
                        $("#viewModal").modal('show');
                        // swal("Saved", "The visitor has been checkout", "success");
                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
});



$('#checkout-form').submit(function (e) {
     e.preventDefault();
     var canvas = document.getElementById("signature-content");
     var image = canvas.toDataURL();
    if($('#checkout_note').val().length >300){
        $('#checkout_message').text('Check-out note should not exceed 300 characters');
        return false;
    }else{
     var $form = $(this);
     var data = {
              notes : $('#checkout_note').val(),
              visitorid : $('input[name=visitor_id]').val(),
              imageBase64: image,
              imagetype: 'checkout_signature'
               };
     $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('visitor-log.checkout')}}",
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                        location.reload();
                        // swal("Saved", "The visitor has been checkout", "success");
                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
       }
    });

   $('#visitor-log-form').submit(function (e) {
            e.preventDefault();
              var $form = $(this);
              var data = {
              customer_id : $('#customer_select').val(),
               };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('visitor-log.store')}}",
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                         $("#myModal").modal('hide');
                         location.reload();
                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
    });
    function exitSession() {
      $.ajax({
           type: "GET",
          url: "{{route('visitor-log.exit')}}",
           success:function(data) {
              location.reload();
           }

      });
 }


    function showTemplateList() {
       $('#icon-fw').css('display', '')

     var customer_id = {!! json_encode(Session::get('default_customer')); !!};
      var template = {!! json_encode(Session::get('template_id')); !!};
   $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('getCustomerTemplate.list')}}",
                type: 'GET',
                 data: "customer_id=" + customer_id,
                success: function (data) {
                    if (data.success) {

                       $("#button_div").hide();
                       $("#dashboard_div").hide();
                       $("#addnew_div").show();
                       $('#template_select').find('option:not(:first)').remove();
                       if(data.data.length == 1){
                         $('#template_select')
                           .append($("<option selected></option>")
                           .attr("value",data.data[0].template_id)
                           .text(data.data[0].template.template_name));
                           loadCustomerTemplateForm(data.data[0].template_id);
                            $('#show_template').hide();
                       }
                       else if(template !=null)
                        {
                          $.each(data.data, function(key, value) {
                          $('#template_select')
                           .append($("<option></option>")
                           .attr("value",value.template_id)
                           .text(value.template.template_name));
                         });

                        $("#template_select").val(template).find("option[value=" + template +"]").attr('selected', true);
                         $('#show_template').hide();
                         loadCustomerTemplateForm(template);


                        }
                        else{
                         $.each(data.data, function(key, value) {
                          $('#template_select')
                           .append($("<option></option>")
                           .attr("value",value.template_id)
                           .text(value.template.template_name));
                         });
                       }




                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
  }

  $('#template_select').on('change', function (){
    if($('#template_select').find(":selected").val() != 0){
    loadCustomerTemplateForm($('#template_select').find(":selected").val());
    }else{
         $('#template_form').html('');
    }
  });


  function loadCustomerTemplateForm(template_id){

            var url = "{{ route('visitor-log-form.load',[':template_id']) }}";
            url = url.replace(':template_id', template_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        $('#template_form').html(data.content);
                      //  $('html, body').animate({
                      //      scrollTop: $("#incidents-table").offset().top
                      //  }, 2000);
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
  }

$('.visit-log-card').on('click', function(e){
     var visitor_type =  $(this).attr('data-id');
     var customer_id = {!! json_encode(Session::get('default_customer')); !!};
     var url = "{{ route('visitor-log.details',[':type',':customer']) }}";
     url = url.replace(':type', visitor_type);
     url = url.replace(':customer', customer_id);

     window.location = url;

});

$(".template-filter-btn").on('click', function(e){
   if($("#show_template").is(":hidden") )
   {
    $('#show_template').show();

   }
    else
    {
       $('#show_template').hide();

    }
   $(this).toggleClass("down");
})

$('#filter-btn').on('click', function(e){
   $('#counts-div').toggle();
   if($("#counts-div:visible").length > 0)
   {
     localStorage.setItem("visitor-log-filter", "true");
  }
   else
   {
     localStorage.setItem("visitor-log-filter", "false");
   }
  // $(this).toggleClass('filter-btn-clicked');
});



</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
