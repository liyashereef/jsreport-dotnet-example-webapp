@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
   </head>
@section('content')
<div class="table_title">
    <h4>QR Patrol Summary</h4>
</div>


<div  class="col-md-12 filter-wrapper">
    <div class="row">
     <div><label class="label-name">Customer Name</label></div>
     <div style="width: 300px;">
     {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
     </div>
     <div><label class="label-name">Employee Name</label></div>
     <div style="width: 300px;">
        <select class="form-control option-adjust employee-filter select2"  name="employee-filter" id="employee-name-filter">
             <option value="0">Select Employee</option>
            @foreach($user_list as $each_userlist)
             <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
             </option>
             @endforeach
         </select>
     </div>
     <div ><label class="label-name">Start Date</label></div>
     <div ><input id="startdate"  width="100%" value="{{$startdate}}" class="form-control custom-datepicker" /></div>
     <div ><label class="label-name">End Date</label></div>
     <div><input type="text" id="enddate" width="100%"  value="{{$enddate}}" class="form-control custom-datepicker" /></div>
    </div>
</div>


    <!-- top card area -->
  <!--  <div class="row mainlink-component card-view-section mb-4  position-relative">
    <div class="position-absolute icons-top-left d-flex flex-column">
    </div>



    <div class="col-sm-12" >
    <div class="row">
    <div class="col-sm-6">

    <div class="Date_filter">
            <div data-id="0" >

                    <div class="d-flex ">
                        <span class="col-sm-6 d-flex flex-column b-right-color pr-2 ">
                        <div class="col-sm-5">
                            <label for="" class="mb-0  label-name">Start Date</label>
                            </div>
                            <div class="col-sm-7">
                            <input id="startdate"  width="100%" value="{{$startdate}}" class="form-control custom-datepicker" />
                        </div>

                        </span>
                        <span class="col-sm-6 d-flex flex-column pl-2 ">
                        <div class="col-sm-5">
                             <label for="" class="mb-0  label-name end-date">End Date</label>
                             </div>
                            <div class="col-sm-7">
                            <input type="text" id="enddate" width="100%"  value="{{$enddate}}" class="form-control custom-datepicker" />
                            </div>
                        </span>
                    </div>

            </div>
        </div>
   </div>
   <div class="col-sm-6">
   <div class="timesheet-filters mb-2 filter-div">
    <div class="row">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4"><label class="filter-text">Employee Name</label></div>
                <div class="col-lg-8 filter">
                    <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                        <option value="0">Select Employee</option>
                       @foreach($user_list as $each_userlist)
                        <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
   </div>
   </diV>
        </div>
        </div>
       </div>
       </div>
        -->

<div id="message"></div>
{{-- @include('timetracker::payperiod-filter') --}}
<table class="table table-bordered" id="customer_qrcode_shifts_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Checkpoint</th>
            <th>Emp Details</th>
            <th>Project Details</th>
            <th>Actual Count</th>
            <th>Expected Count</th>
            <th>Percentage (%)</th>
            <th></th>
            <th></th>

        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>



    $(function(){



        $('#customerid').select2();

        function collectFilterData() {
            return {
                employeename: $("#employee-name-filter").val(),
                employeeno: $('#employee-no-filter').val(),
                client_id:$("#clientname-filter").val(),
            }
        }

        sessionStorage.setItem("customer_start_date", $('#startdate').val());
        sessionStorage.setItem("customer_end_date", $('#enddate').val());
            count = 0 ;
            $('#startdate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                 if(sessionStorage.getItem("customer_start_date") != $('#startdate').val()){
                  changeDateFilter();
                 }
                },
            });
             $('#enddate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                  if(sessionStorage.getItem("customer_end_date") != $('#enddate').val()){
                    changeDateFilter();
                  }
                },
            });

        var url = "{{ route('customerqrcodeshift.list',[':startdate',':enddate']) }}";
        var startdate = $('#startdate').val();
        var enddate = $('#enddate').val();
        url = url.replace(':startdate', startdate);
        url = url.replace(':enddate', enddate);

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('.select2').select2();
            table = $('#customer_qrcode_shifts_table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,


            ajax: url,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            createdRow: function (row, data, dataIndex) {
                    if( data.total_count >= data.expected_attempts){
                            $(row).addClass('approved');
                   }else{
                        $(row).addClass('rejected');
                        $(row).find('td','a').css('color','white');
                   }

             },
            columnDefs: [
             { width: '10%', targets: 0 }
             ],
            columns: [

                {  data: 'id',
                    name: 'id',
                    visible:false
                },
                {
                    data: 'created_date',
                    name: 'created_at',
                    defaultContent: "--"
                },
                {
                    data: 'checkpoint',
                    name: 'qrcodeWithTrashed.location',
                    defaultContent: "--"
                },
                {
                    data: "employee_details",
                    name: "shifts.shift_payperiod.trashed_user.first_name",
                    defaultContent: "--"
                },
                {
                    data: 'customer_details',
                    name: 'shifts.shift_payperiod.trashed_customer.client_name',

                },
                {
                    data: 'total_count',
                    name: 'total_count',
                },
                {
                    data: 'expected_attempts',
                    name: 'expected_attempts',
                },
                {
                    data: 'missed_count_percentage',
                    name: 'missed_count_percentage',
                },
                {
                    data: "employee_lastname",
                    name: "shifts.shift_payperiod.trashed_user.last_name",
                    visible: false,
                },
                {
                    data: "employee_no",
                    name: "shifts.shift_payperiod.trashed_user.trashedEmployee.employee_no",
                    visible: false,
                },
            ]
        });

          } catch(e){
            console.log(e.stack);
        }

});

function changeDateFilter(){
    sessionStorage.setItem("customer_start_date", $('#startdate').val());
        sessionStorage.setItem("customer_end_date", $('#enddate').val());
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        console.log(sdate,edate);
        var url = "{{ route('customerqrcodeshift.list',[':startdate',':enddate']) }}";
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        table.ajax.url( url ).load();

}

$('#employee-name-filter').on('change', function(e){

       var sdate = $('#startdate').val();
       var edate = $('#enddate').val();
       var emp_id = $("#employee-name-filter").val() ?  $("#employee-name-filter").val() : 0;
       var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
       var url = "{{ route('customerqrcodeshift.list',[':startdate',':enddate',':emp_id',':client_id']) }}";
       url = url.replace(':startdate', sdate);
       url = url.replace(':enddate', edate);
       url = url.replace(':emp_id', emp_id);
       url = url.replace(':client_id', client_id);
       table.ajax.url( url ).load();

   });

   $('#clientname-filter').on('change', function(e){
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        var emp_id = $("#employee-name-filter").val() ?  $("#employee-name-filter").val() : 0;
        var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
        var url = "{{ route('customerqrcodeshift.list',[':startdate',':enddate',':emp_id',':client_id']) }}";
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        url = url.replace(':emp_id', emp_id);
        url = url.replace(':client_id', client_id);
        table.ajax.url( url ).load();
    });

</script>
<style>

.filter-div{
    margin-top: 20px;
}
.label-name{
    margin: 7px 12px 0px 36px;
}

.filter-wrapper{
   padding: 14px 0px 38px 0px;
}

.custom-datepicker{
    margin-bottom: -40px;
}
.Date_filter{
    margin-left:80px;
}
.filter{
    margin-left:-80px;
}
.end-date{
    margin-right:20px;
}

</style>

@stop
