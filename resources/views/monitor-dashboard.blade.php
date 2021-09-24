@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .container {
            max-width: 100% !important;
        }
        .popover-body {
        max-height: 500px;
        overflow-y: auto;
       }
       .popover {
           max-width: 500px !important;

       }
       .badge-primary {
            color: #fff;
            background-color: #333f50 !important;
       }
    </style>
</head>

@section('content')

<div class="row">

    <div class="col-md-9 table_title" >
        <h4>Monitor Dashboard</h4>
    </div>

    <div class="col-md-3" style="margin: 15px 0px 15px 0px;">
        <div class="row">
            <div class="col-md-2"><label class="filter-text">Filter</label></div>
            <div class="col-md-8 filter">
                {{ Form::select('region_lookup_id',[0=>'Select All']+$lookups['regionLookup'], isset($region_lookup_id) ? old('region_lookup_id',$region_lookup_id) : null, array('class' => 'form-control edge-validation','id'=>'region_lookup_id')) }}

                <span class="help-block"></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 ">
        <ul class="breadcrumb nav nav-tabs width-100" style="padding:0px;" role="tablist">
            <li class="nav-item success">
            <span id="incident-badge" class="badge badge-pill badge-primary" style="float:right;margin-bottom:-10px;"></span>
                <a class="nav-link nav-item expense" data-toggle="tab" href="#incident" id="incident-tab">
                    <span>Incident
                    </span>
                </a>
            </li>
            <li class="nav-item success">
                <a class="nav-link nav-item expense" data-toggle="tab" href="#compilance" id="compilance-tab">
                    <span>Schedule Compilance
                    </span>
                </a>
            </li>
            <li class="nav-item success">
                <a class="nav-link nav-item expense" data-toggle="tab" href="#breakrequest" id="breakrequest-tab">
                    <span>Break Request
                    </span>
                </a>
            </li>
            <li class="nav-item success">
            <span id="noshow-badge" class="badge badge-pill badge-primary" style="float:right;margin-bottom:-10px;"></span>
                <a class="nav-link nav-item expense" data-toggle="tab" href="#noshow" id="noshow-tab">
                    <span>No Show
                    </span>
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <div id="incident" class="tab-pane">
                <section class="candidate full-width">
                    @include('partials.monitor-dashboard.incident')
                </section>
            </div>
            <div id="compilance" class="container-fluid tab-pane fade">
                <div class="row">
                    <section class="candidate full-width">Schedule Compilance
                    </section>
                </div>
            </div>
            <div id="breakrequest" class="container-fluid tab-pane fade">
                <div class="row">
                    <section class="candidate full-width">
                        Break Request
                    </section>
                </div>
            </div>
            <div id="noshow" class="tab-pane">
                    <section class="candidate full-width">
                        @include('partials.monitor-dashboard.noshow')
                    </section>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    $(function(e) {
       // let activeTab = 'incident-tab';
        if((localStorage.getItem('active-tab') !== null) && (localStorage.getItem('active-tab') == 'noshow-tab')) {
        //  activeTab = localStorage.getItem('active-tab');
          $('#noshow-tab').addClass('active');
          $('#noshow').addClass('active');

        }else{
          $('#incident-tab').addClass('active');
          $('#incident').addClass('active');

        }



       // let noshow_timeout = null;
        $('.box').popover({
            trigger: "click",
            html: true,
            content: function() {
                return get_incident_details($(this).attr('id'));
            }
        });

        $('body').on('click', function(e) {
            //did not click a popover toggle or popover
            if ($(e.target).data('toggle') !== 'popover' &&
                $(e.target).parents('.popover.in').length === 0) {
                $('[data-toggle="popover"]').popover('hide');
            }
        });

        $('.box').on('click', function(e) {
            $('.box').not(this).popover('hide');
        });

        get_incident();
        get_noshow();

        $('[data-toggle="tab"]').click(function(e) {
            $(".box").css('background-color', '#333f50');

          /*  if (typeof qqq !== 'undefined') {
                clearInterval(qqq);
            }
            if (typeof noshow_timeout !== 'undefined') {
                clearInterval(noshow_timeout);
            }*/
            var $this = $(this),
                loadpath = $this.attr('href');
            if (loadpath == '#incident') {
                localStorage.setItem('active-tab','incident-tab');

            }else if(loadpath == '#noshow'){
                localStorage.setItem('active-tab','noshow-tab');
            }

        });

        $('.noshow-box').popover({
            trigger: "click",
            html: true,
            content: function() {
                return get_noshow_details($(this).attr('id'));
            }
        });

        $('.noshow-box').on('click', function(e) {
            $('.noshow-box').not(this).popover('hide');
        });

    });

    function get_incident() {
        let region_lookup_id =  $('#region_lookup_id').val();
        url = "{{ route('all-incidents',':id') }}";
        var url = url.replace(':id', region_lookup_id);
        $.ajax({
            url: url,
            type: 'GET',
            global: false, 
            success: function(data) {
                $("#incident-badge").text(data.length);
                if(localStorage.getItem('active-tab') == 'incident-tab'){
                $(".box").css('background-color', '#333f50');
                  data.forEach(function(val) {
                      console.log(val);
                      $('#' + val).css('background-color', 'red');
                  });
                } 
               qqq =  setTimeout(function(){get_incident();}, 5000);
            },
            fail: function(response) {
                console.log('here');
            },
            contentType: false,
        });

    }


    function get_incident_details(customer_id) {
       
        url = "{{ route('incident-details',':customer_id') }}";
        var url = url.replace(':customer_id', customer_id);
        $.ajax({
            url: url,
            type: 'GET',
            global: false, 
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data) {
           if(data.result.length != 0){
             let  default_text ='--';
              htmlText ='<div class="row">';
                data.result.forEach(function(val) {
                htmlText +='<div class="col-sm-4" style="padding-top:12px;" >Customer </div><div style="padding-top:12px;" class="col-sm-8">'+val.customer.client_name+'</div>';
                htmlText +='<div class="col-sm-4">Project </div><div class="col-sm-8">'+val.customer.project_number+'</div>';
                htmlText +='<div class="col-sm-4">Severity </div><div class="col-sm-8">'+val.priority.value+'</div>';

             // htmlText +='<div class="col-sm-4">Title </div><div class="col-sm-8">'+val.customer.title ? val.customer.title : +'--'+ +'</div>'
             // htmlText +='<div class="col-sm-4">Subject </div><div class="col-sm-8">'+val.customer.incident_report_subject ? val.customer.incident_report_subject : val.description +'</div>'
             // htmlText +='<div class="col-sm-4">Customer Id </div><div class="col-sm-8">'+val.priority.value ? val.priority.value : default_text +'</div>';
                htmlText +='<div class="col-sm-4">Reported By </div><div class="col-sm-8">'+val.reporter.first_name +' '+val.reporter.last_name+'</div><br>';
                });
               htmlText +='<div>';
             }else{
                htmlText ='<div class="col-sm-12">No records </div>';
             }
            },
            fail: function(response) {
                return true;
            },
            contentType: false,
        });
        return htmlText;

    }

    function get_noshow() {
        let region_lookup_id =  $('#region_lookup_id').val();
        url = "{{ route('all-noshow',':id') }}";
        var url = url.replace(':id', region_lookup_id);
        $.ajax({
            url: url,
            type: 'GET',
            global: false, 
            success: function(data) {
                $("#noshow-badge").text(data.records.length);
                if(localStorage.getItem('active-tab') == 'noshow-tab'){
                $(".noshow-box").css('background-color', '#333f50');
                  data.records.forEach(function(val) {
                    console.log(val.site_no);
                    $("[data-id="+val.site_no+"]").css('background-color', 'red');
                  });
                }
               noshow_timeout =  setTimeout(function(){get_noshow();}, 5000);

            },
            fail: function(response) {
                console.log('here');
            },
            contentType: false,
        });

    }

    function get_noshow_details(customer_id) {
       
       url = "{{ route('noshow-details',':customer_id') }}";
       var url = url.replace(':customer_id', customer_id);
       $.ajax({
           url: url,
           type: 'GET',
           global: false, 
           async: false,
           cache: false,
           timeout: 30000,
           success: function(data) {

           if(data.result.records.length != 0){
            let  default_text ='--';
             htmlText ='<div class="row">';
               data.result.records.forEach(function(val) {
               htmlText +='<div class="col-sm-4" style="padding-top:12px;" >Customer </div><div style="padding-top:12px;" class="col-sm-8">'+val.site_name+'</div>';
               htmlText +='<div class="col-sm-4">Project </div><div class="col-sm-8">'+val.site_no+'</div>';
               htmlText +='<div class="col-sm-4">Date </div><div class="col-sm-8">'+val.date+'</div>';
               htmlText +='<div class="col-sm-4">Employee </div><div class="col-sm-8">'+val.employee+'</div><br>';
               });
              htmlText +='<div>';
            }else{
               htmlText ='<div class="col-sm-12">No records </div>';
            }
           },
           fail: function(response) {
               return true;
           },
           contentType: false,
       });
       return htmlText;

   }


   $('#region_lookup_id').on('change', function(){
       let region_lookup_id = $('#region_lookup_id').val();
       if(region_lookup_id == 0){
        window.location.href = "{{route('monitor-dashboard') }}";
       }else{
       window.location.href = "{{route("monitor-dashboard-reg",'') }}/"+region_lookup_id;
       }
    });

</script>
@endsection