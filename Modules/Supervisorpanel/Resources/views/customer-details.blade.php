@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')

<div class="">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('customers.mapping')}}">Supervisor Panel</a></li>
        <li class="breadcrumb-item active">Customer Details</li>
        @if(auth()->user()->can('create_site_notes'))
        <div class="align-left site-note-tab" >
            <ul class="nav nav-tabs" role="tablist">
                @foreach($note_list as $id => $created_at)
                <li role="presentation" class="note-tab">
                    <a href="#" onclick="siteNoteClick(this,{{$id}})"  aria-controls="userTab" role="tab" data-toggle="tab" id="siteNote-{{$id}}">{{ Carbon\Carbon::parse($created_at)->format($site_note_dateformat)}}</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </ol>
</div>
<div class="customer-details-block">
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-4 col-form-label  col-xs-3"><b>Project No.</b></label>
            <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['project_number'] ?? "--"}}</label>

        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Contact</b></label>
            <label  class="col-md-7 col-form-label col-xs-3">{{$customer['details']['contact_person_name'] ?? "--"}}
            </label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3">{{ isset($customer['supervisor']['full_name']) ? $customer['supervisor']['full_name'] : "--"}} </label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3">
            {{isset($customer['areamanager']['full_name']) ? $customer['areamanager']['full_name'] : "--"}}</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-4 col-form-label  col-xs-3"><b>Client</b></label>
            <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['client_name'] ?? "--"}}
            </label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Phone</b></label>
            <label  class="col-md-7 col-form-label col-xs-3">{{ ( $customer['details']['contact_person_phone_ext']!=null?($customer['details']['contact_person_phone'].' x'.$customer['details']['contact_person_phone_ext']):(null!=$customer['details']['contact_person_phone']?$customer['details']['contact_person_phone']:'--') )}}</label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor Phone</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3">{{ ( isset($customer['supervisor']['phone']) && isset($customer['supervisor']['phone_ext'])?($customer['supervisor']['phone'].' x'.$customer['supervisor']['phone_ext']):(isset($customer['supervisor']['phone'])?$customer['supervisor']['phone']:'--') )}}</label>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager Phone</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3">{{ (isset($customer['areamanager']['phone_ext'])?($customer['areamanager']['phone'].' x'.$customer['areamanager']['phone_ext']):(isset($customer['areamanager']['phone'])?$customer['areamanager']['phone']:'--') )}}</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-4 col-form-label  col-xs-3"><b>Address</b></label>
            <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['address'] ?? ""}}, {{$customer['details']['city'] ?? ""}}, {{$customer['details']['province'] ?? ""}}, {{$customer['details']['postal_code'] ?? ""}}.
            </label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Email</b></label>
            <label  class="col-md-7 col-form-label col-xs-3 email-break">{{$customer['details']['contact_person_email_id'] ?? "--"}}</label>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor Email</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3 email-break">
                {{$customer['supervisor']['email'] ?? "--"}}
            </label>
        </div>
    </div>
     {{--  <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-5 label-adjust col-form-label col-xs-3"><b>Alternate Email</b></label>
            <label  class="col-md-7 label-adjust col-form-label  col-xs-3 email-break">
                {{$customer['supervisor']['alternate_email'] ?? "--"}}
            </label>
        </div>
    </div> --}}
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="row styled-form-readonly">
            <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager Email</b></label>
            <label  class="col-md-6 col-form-label  col-xs-3 email-break">
                {{$customer['areamanager']['email'] ?? "--"}}
            </label>
        </div>
    </div>
</div>
</div>

<div class="scrollmenu">
    <div class="customerstatus-container">
        @include('supervisorpanel::partials.average-score')
    <div class="form-group row">
      <label class="col-sm-5 col-md-4 "><br>
    <div class="row">
        @if(auth()->user()->can('view-trend-report') || auth()->user()->can('create_site_notes'))
        <div class="col-sm-12 col-md-12 col-lg-12 row site-more-action-div">
        @if(auth()->user()->can('view-trend-report'))
        <div id="generateReport" class="add-new col-sm-12 col-lg-5" data-title="Generate Report">
           <span class="add-new-label">Generate Report</span>
        </div>
        @endif
        @if(auth()->user()->can('create_site_notes'))
        <div id="siteNote" class="add-new col-sm-12 col-lg-5 shift_notes" data-title="Site Note">
           <span class="add-new-label">Add Site Note</span>
        </div>
        @endif
        </div>
        @endif
    </div>
         </label>
        <div class="stacked-bar-graph-header col-sm-7 col-md-8">
            <span class="stacked-bar-graph-prev prev-payperiod" @if($payperiods->onFirstPage()) id="prev-next-style" @else id="" @endif><a href="{{$payperiods->previousPageUrl()}}"><i style="font-size:18px" class="fa">&#xf137;</i></a></span>
            @foreach($payperiods as $id=>$payperiod)
            <span class="stacked-bar-graph-header-size payperiod" >
                <a title="Click here to submit a survey/view graph" style="white-space: nowrap;" class="view-add" data-count_id="{{$id}}" data-customer_id="{{ $customer['details']['id'] }}" data-payperiod_id="{{ $payperiod->id }}" href="javascript:;">{{ucfirst($payperiod->short_name!=null?$payperiod->short_name:$payperiod->pay_period_name)}}</a>
                <div style="margin-top: 5%;">
                @if(auth()->user()->can('edit-survey') && $payperiod->start_date < Carbon\Carbon::today())
                <a title="Edit survey" class="edit fa fa-lg fa-edit" data-count_id="{{$id}}" data-view='admin' data-customer_id="{{$customer['details']['id']}}" data-payperiod_id="{{ $payperiod->id }}" href="javascript:;" class=""></a>
                @elseif(auth()->user()->can('view-submitted-survey') || auth()->user()->can('view-area-manager-notes') || auth()->user()->can('edit-area-manager-notes'))
                <a class="edit fa fa-eye" data-view="{{--displayed current role here--}}" data-count_id="{{$id}}" data-customer_id="{{$customer['details']['id']}}" data-payperiod_id="{{ $payperiod->id }}" href="javascript:;" class=""></a>
                @endif
                @if($payperiod->start_date < Carbon\Carbon::today() && (auth()->user()->can('create-incident-report') || auth()->user()->can('view_all_incident_report') || auth()->user()->can('view_allocated_incident_report')))
                <a title="Incident Report" class="incident-report fa fa-lg fa-list-alt" data-count_id="{{$id}}" data-customer_id="{{$customer['details']['id']}}" data-payperiod_id="{{ $payperiod->id }}" href="javascript:;" style="font-weight: normal"></a>
                @endif
                </div>
            </span>
            @endforeach
            <span class="stacked-bar-graph-next next-payperiod" @if($payperiods->hasMorePages()) id="" @else id="prev-next-style" @endif><a href="{{$payperiods->nextPageUrl()}}" id="next"><i style="font-size:18px" class="fa">&#xf138;</i></a></span>
        </div>
    </div>
        <div class="ajax-content">

        </div>
    </div>
</div>
<div style="padding: 0px 12px;" class="trend-content">

</div>
<div class="modal fade"  data-backdrop="static"  id="reportCriteriaModal" role="dialog" >
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Enter Criteria</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
    <div class="modal-body">
       <div class="form-group">
        <div class="col-sm-3">
          <label class="control-label">Start Period:</label>
        </div>
        <div class="col-sm-9">
            <select id="start_payperiod" class="form-control">
              @foreach($all_payperiods as $id=>$eachpayperiod)
              <option  value="{{$eachpayperiod->start_date}}" >{{$eachpayperiod->year.' - '.$eachpayperiod->pay_period_name.' - '.$eachpayperiod->short_name}}</option>
              @endforeach
          </select>
       </div>
      </div>

  <div class="form-group">
        <div class="col-sm-3">
          <label class="control-label" >End Period:</label>
        </div>
      <div class="col-sm-9">

        <select id="end_payperiod" class="form-control">
          @foreach($all_payperiods as $id=>$eachpayperiod)
          <option  value="{{$eachpayperiod->start_date}}" >{{$eachpayperiod->year.' - '.$eachpayperiod->pay_period_name.' - '.$eachpayperiod->short_name}}</option>
          @endforeach
        </select>
      </div>
  </div>
  <br>
   <div class="form-group">
       <div class="col-sm-6">
        <input  type="hidden" id="customer_id" value="{{$customer['details']['id']}}">
        <a id="generate" title="Generate Report" class="btn cancel ico-btn incident_add_button" >
    Generate </a>
       </div>
         </div>
      </div>
   </div>
 </div>
</div>
@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js" charset="utf-8"></script>
<script>

    $(function () {

        if (window.location.href.indexOf("sitenotes") > -1) {
            var urlPath = window.location.pathname;
            var number = urlPath.lastIndexOf('/') + 1;
            console.log(urlPath.substring(number,));
            var siteNotesId = urlPath.substring(number,);
            $('#siteNote-'+ siteNotesId).trigger('click');

        }


        $('.payperiod a.edit').on('click', function () {
            $('.payperiod').removeClass('active');
            $(this).closest('.payperiod').addClass('active');
            customer_id = $(this).data('customer_id');
            payperiod_id = $(this).data('payperiod_id');
            user = $(this).data('view');
            console.log('edit',user,customer_id,payperiod_id);
            var url = "{{ route('customer.reportedit',[':customer_id',':payperiod_id']) }}";
            url = url.replace(':customer_id', customer_id);
            url = url.replace(':payperiod_id', payperiod_id);
            $.ajax({
                url: url,
                type: 'GET',
                //data: {'customer_id': customer_id, 'payperiod_id': payperiod_id},
                success: function (data) {
                    if (data.success) {
                        $('.ajax-content').html(data.content)
                        $(".emplist").html(data.employeeHtml)

                        if(user == 'coo' || user == 'areamanager'){
                            $('.ajax-content').find('input[type=text]').prop('readonly',true);
                            $('.ajax-content').find('input[type=radio]').attr('disabled',true);
                            $('.ajax-content').find('select').attr('disabled',true);
                            $('.ajax-content').find('.report-button-left').remove();
                        }
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
            }).done(function(data){
                if(data.success){
                    $( ".emplist" ).each(function( index ) {
                        let answerEmployee=$(this).attr("attr-answer");
                        if(answerEmployee>0){
                            $(this).val(answerEmployee)
                        }
                    });
                    $(".emplist").select2();
                }
            });
        });

        $('.payperiod a.incident-report').on('click', function () {
            $('.payperiod').removeClass('active');
            $(this).parents('.payperiod').addClass('active');
            customer_id = $(this).data('customer_id');
            payperiod_id = $(this).data('payperiod_id');
            var url = "{{ route('incident.init',[':customer_id',':payperiod_id']) }}";
            url = url.replace(':customer_id', customer_id);
            url = url.replace(':payperiod_id', payperiod_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        $('.ajax-content').html(data.content);
                        $('html, body').animate({
                            scrollTop: $("#incidents-table").offset().top
                        }, 2000);
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
        });
        $('.payperiod a.view-add').on('click', function () {
            $('.payperiod').removeClass('active');
            $(this).parent().addClass('active');
            customer_id = $(this).data('customer_id');
            payperiod_id = $(this).data('payperiod_id');
            var url = "{{ route('customer.report',[':customer_id',':payperiod_id']) }}";
            url = url.replace(':customer_id', customer_id);
            url = url.replace(':payperiod_id', payperiod_id);
            $.ajax({
                url: url,
                type: 'GET',
                //data: {'customer_id': customer_id, 'payperiod_id': payperiod_id},
                success: function (data) {
                    if (data.success) {
                        $('.ajax-content').html(data.content);
                        $(".emplist").html(data.employeeHtml)
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
            }).done(function(data){
                if(data.success){
                    $( ".emplist" ).each(function( index ) {
                        let answerEmployee=$(this).attr("attr-answer");
                        if(answerEmployee>0){
                            $(this).val(answerEmployee)
                        }
                    });
                    $(".emplist").select2();
                }
            });
        });

        $('#generateReport').on('click', function () {

                $('#reportCriteriaModal').modal('show');
         });

        $('#generate').on('click', function () {
           customer_id = $('#customer_id').val();
           var payperiod_start = $('#start_payperiod').val();
           var payperiod_end   = $('#end_payperiod').val();
           startdate  = new Date(payperiod_start);
           enddate    = new Date(payperiod_end);
           if(startdate > enddate){
               swal("Alert", "End pay period must be a later pay period", "warning");
           }else{
    var url = "{{ route('customer.trendreport',[':customer_id',':payperiod_start',':payperiod_end']) }}";
            url = url.replace(':customer_id', customer_id);
            url = url.replace(':payperiod_start', payperiod_start);
            url = url.replace(':payperiod_end', payperiod_end);
            $('#reportCriteriaModal').modal('hide');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        $('.scrollmenu').hide();
                        $('.trend-content').html(data.content);
                       setTimeout(function(){
                       var chart_content = $(".charts-chart>div").highcharts();
                       chart_content.setSize(
                       $('#trend-container').width()-85,
                       400,
                       false
                       );
                       },200);
                    } else {
                        swal("Alert", data.content, "warning");
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
        });

           @if($incident_load && $analytics_load==true)
                $('.payperiod a.view-add[data-payperiod_id="{{ $incident_load }}"]').trigger('click');
           @elseif($incident_load && $analytics_load==false)
                $('.payperiod').find("[data-payperiod_id='{{ $incident_load }}'].incident-report").trigger('click');
           @elseif(null!=$current_payperiod && $analytics_load==false )
                $('.payperiod a.view-add[data-payperiod_id="{{ $current_payperiod->id }}"]').trigger('click');
           @else
                //  $('.payperiod a.view-add[data-payperiod_id="{{ $current_payperiod->id }}"]').trigger('click');
                //$('.ajax-content').text('No Active Payperiod/Template/Report Found!')
           @endif
    });
 $('.shift_notes').on('click', function () {
    $(".site-note-tab li").removeClass('active show');
    $(".site-note-tab ul").prepend('<li role="presentation" class=" note-tab active show"><a onclick="siteNoteClick(this,0)" href="#note0" aria-controls="userTab" role="tab" data-toggle="tab" data-note-id="0">{{ \Carbon\Carbon::now()->format($site_note_dateformat) }}</a></li>');
    $(".site-note-tab ul li:first a").click();
 });

function siteNoteClick(el,note_id){
    $(".site-note-tab li").removeClass('active show');
    $(el).parent('li').addClass('active show');
    customer_id = $('#customer_id').val();
    var url = "{{ route('customer.sitenotes',[':0',':1']) }}";
    var param_arr = [customer_id, note_id];
    fetchContent(url, param_arr);
}

 function fetchContent(url, param_arr) {
    for(var param in param_arr){
        var holder = ":"+param
        url = url.replace(holder, param_arr[param]);
    }
    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            if (data.success) {
                $('.scrollmenu').hide();
                $('.trend-content').html(data.content);
            } else {
                console.log(data);
            }
            if (!(window.location.href.indexOf("sitenotes") > -1)) {
                $("#back").removeClass('hidden');
            }
            $("#site-note-remove-tasks").hide();
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



</script>
<style>
#prev-next-style{
   pointer-events : none;
   cursor : default;
   opacity : 0.6;
}
#footer{
    position: relative;
}
</style>
@include('supervisorpanel::scripts')
@endsection
