@extends('layouts.app')
@section('css')
<!-- Start facility management dashboard css js  -->=
<!-- <link href="{{ asset('faclitymanagementdashboard/custom.css') }}" rel="stylesheet"> -->

<link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet">


<!-- End facility management dashboard css js  -->

@endsection
@section('content')
<div class="content-component px-3 pt-3 content2 ">
    <!-- top card area -->
    <div class="row mainlink-component card-view-section mb-2  position-relative">
        <div class="position-absolute icons-top-left d-flex flex-column">
            <!-- <button type="button"
                    style="z-index:1500"
                    data-toggle="modal"
                    data-target="#customizeWidgetModal">
                    Customize Widgets
                    </button> -->


            <!-- <span class=""><img src="{{asset('images/pie.png') }}" alt="settings"></span> -->

        </div>
        <div class="col-xl-2  col-lg-3 col-md-4 col-sm-6 col-12 pl-1 pr-1 pb-2">
            <div data-id="0" class="card card-expense">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex ">
                        <span class="d-flex flex-column b-right-color pr-2">

                            <input id="startdate"  width="100%" value="{{$startdate}}" class="custom-datepicker" />
                            <label for="" class="mb-0 text-white label-name"><strong>Start Date</strong></label>

                        </span>
                        <span class="d-flex flex-column pl-2">

                            <input type="text" id="enddate" width="100%"  value="{{$enddate}}" class="custom-datepicker" />
                            <label for="" class="mb-0 text-white label-name"><strong>End Date</strong></label>
                        </span>
                    </div>
                    <!-- <img src="{{asset('images/date.png') }}" alt="date" class="position-absolute logos-top-card"> -->
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div  data-id="1"  class="card card-mileage">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s">Pending Approval</label>
                            <label id="pending_count" for=""
                                class="mb-0 text-white label-name">Receipts: 0</label>
                            <label for="" id="pending_amount"
                                class="mb-0 text-white label-day">$0</label>
                            </label>

                        </span>
                    </div>
                  {{--  <img src="{{asset('images/visitors.png') }}" alt="date" class="position-absolute logos-top-card">--}}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div data-id="3" class="card card-mileage">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="d-flex pr-4">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" class="mb-0 text-white label-s">Approved</label>
                            <label for="" id="aprroved_count"
                                class="mb-0 text-white label-name">Receipts: 0</label>
                            <label for="" id="aprroved_amount"
                                class="mb-0 text-white label-day">$0</label>

                        </span>
                    </div>
                    {{--   <img src="{{asset('images/Incidents.png') }}" alt="date" class="position-absolute logos-top-card">--}}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div data-id="4" class="card card-mileage">
                <div class="card-body d-flex align-items-center py-2">
                    <span class="d-flex flex-column  pr-2">
                        <label for="" class="mb-0 text-white label-s">Pending Reimbursement</label>
                        <label for="" id="pending_reimbursed_count"
                            class="mb-0 text-white label-name">Receipts: 0</label>
                        <label for="" id="pending_reimbursed_amount"
                            class="mb-0 text-white label-day">$0</label>

                    </span>
                    {{--  <img src="{{asset('images/tickets.png') }}" alt="date" class="position-absolute logos-top-card">--}}
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-1 pb-2">
            <div data-id="5" class="card card-mileage">
                <div class="card-body d-flex align-items-center py-2">
                    <span class="d-flex flex-column  pr-2">
                        <label for="" class="mb-0 text-white label-s">Reimbursed</label>
                        <label for="" id="reimbursed_count"
                            class="mb-0 text-white label-name">Receipts: 0</label>
                        <label for="" id="reimbursed_amount"
                            class="mb-0 text-white label-day">$0</label>

                    </span>
                    {{--  <img src="{{asset('images/hours.png') }}" alt="date" class="position-absolute logos-top-card">--}}
                </div>
            </div>
        </div>
    </div>
    <!-- card area -->



</div>

<div class="row">
    <div class="col-md-6">
        <nav>
            <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
            @if(auth()->user()->can('view_all_expense_claim') || auth()->user()->can('view_allocated_expense_claim'))
                   <a class="nav-item nav-link expense" id="expenseClaim">Expense Claim</a>
            @endif
            @if(auth()->user()->can('view_all_mileage_claim') || auth()->user()->can('view_allocated_mileage_claim'))
                <a class="nav-item nav-link expense active" href="#">Mileage Claim</a>
            @endif
            </div>
        </nav>
    </div>
    <div class="col-md-6 pull-right">
        <div class="col-md-12 row border p-2 m-0">
            <label for="employee-filter" class="col-md-2 align-self-end p-0">Employee</label>
            <div class="col-md-6">
                <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-filter">
                    <option value="">Select Employee</option>
                        @foreach($user_list as $each_userlist)
                        <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                        </option>
                        @endforeach
                </select>
                <span class="help-block"></span>
            </div>
            <div class="col-md-4 text-align-right align-self-end p-0">
                @if(auth()->user()->can('view_all_mileage_claim') || auth()->user()->can('view_allocated_mileage_claim'))
                    <input type="checkbox" name="viewmyexpense" id="viewmyexpense">
                    <label for="viewmyexpense">View my mileage claim</label>
                @endif
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered" id="mileage-table">
    <thead>
        <tr>
            <th width="10%"  class="sorting">Transaction Date</th>
            <th width="15%"  class="sorting">Name</th>
            <th width="20%" class="sorting">Starting Location</th>
            <th width="20%" class="sorting">Destination</th>
            <th width="12%" class="sorting">Total km</th>
            <th width="10%" class="sorting">Total Value</th>
            <th class="sorting">Billable</th>
            <th class="sorting">Status</th>
            <th width="10%"  class="sorting">Submitted Date</th>
            <th width="10%"  class="sorting">Created Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

@stop
@section('scripts')

<script>
    var viewMyExpense = {{ $viewMyExpense }};

    $(function(){

         $(".select2").select2({ width: '100%' });
         $(".employee-filter").change(function(){
            var viewmyexpense = $('#viewmyexpense').is(":checked") ? 1 : 0;
             var startdate = $('#startdate').val();
             var enddate = $('#enddate').val();
             var status = 0;
             var employee = $('#employee-filter').val() ?  $('#employee-filter').val() : 0;
             var url = "{{ route('mileage-claims.list',[':viewmyexpense',':startdate',':enddate',':status',':employee']) }}";
             url = url.replace(':viewmyexpense', viewmyexpense);
             url = url.replace(':startdate', startdate);
             url = url.replace(':enddate', enddate);
             url = url.replace(':status', status);
             url = url.replace(':employee', employee);
             table.ajax.url(url).load();
             updateCount(startdate,enddate,employee);
        });
            sessionStorage.setItem("mileage_start_date", $('#startdate').val());
            sessionStorage.setItem("mileage_end_date", $('#enddate').val());
            $('#startdate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                 if(sessionStorage.getItem("mileage_start_date") != $('#startdate').val()){
                  changeDateFilter();
                 }
                },
            });
             $('#enddate').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                change: function(e) {
                 if(sessionStorage.getItem("mileage_end_date") != $('#enddate').val()){
                    changeDateFilter();
                 }
                },
            });

            $('#viewmyexpense').on('click', function() {
                if ($(this).is(":checked")) {
                    viewMyExpense =  1;
                    $('#employee-filter').val("").trigger('change').prop('disabled', true);
                } else {
                    viewMyExpense = 0;
                    $('#employee-filter').prop('disabled', false);
                }
                changeDateFilter();
            });

            if (viewMyExpense == 1) {
                $('#viewmyexpense').prop('checked', true);
                $('#employee-filter').prop('disabled', true);
            }

            $('#expenseClaim').on('click', function() {
                $(this).attr("href", "{{ route('expense-dashboard.index') }}/"+viewMyExpense);
            });


        var url = "{{ route('mileage-claims.list',[':viewmyexpense',':startdate',':enddate']) }}";
        var viewmyexpense = $('#viewmyexpense').is(":checked") ? 1 : 0;
        var startdate = $('#startdate').val();
        var enddate = $('#enddate').val();
        url = url.replace(':viewmyexpense', viewmyexpense);
        var employee = $('#employee-filter').val() ?  $('#employee-filter').val() : 0;
        url = url.replace(':startdate', startdate);
        url = url.replace(':enddate', enddate);

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#mileage-table').DataTable({
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
                }],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [9, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            createdRow: function (row, data, dataIndex) {
                    if( data.status_id=='Approved' || data.status_id=='Reimbursed'){
                        $(row).addClass('approved');
                    }else if(data.status_id=='Pending Reimbursement'){
                        $(row).css("background-color", "#dcd989");
                    }

             },
            columns: [

                {
                    data: 'transaction_date',
                    name: 'transaction_date',
                    defaultContent: "--"
                },
                {
                    data: 'name',
                    name: 'name',
                    defaultContent: "--"
                },
                {
                    data: 'starting_location',
                    name: 'starting_location',
                    defaultContent: "--"

                },
                {
                    data: 'destination',
                    name: 'destination',
                    defaultContent: "--"

                },

                {
                    data: 'total_km',
                    name: 'total_km',
                    defaultContent: "0"

                },
                {
                    data: 'amount',
                    name: 'amount',
                    defaultContent: "0"
                },


                {
                    data: 'billable',
                    name: 'billable',

                },

                {
                    data: 'status_id',
                    name: 'status_id',
                },
                {
                   data: 'created_at',
                   name: 'created_at',
                },
                {data: 'ordering_created_at', name: 'ordering_created_at',visible:false},
                {

                    data: null,
                    sortable: false,
                    render: function (row) {
                        var url = '{{ route("mileage-claims-single",'') }}';
                        actions = '';
                        actions = '<a href="'+url+"/"+ row.id +'" class="edit fas  fa-edit"></a>';


                      return actions;

                           }

                }


            ]
        });
          updateCount(startdate,enddate);
         } catch(e){
            console.log(e.stack);
        }
});

function changeDateFilter(){
        sessionStorage.setItem("mileage_start_date", $('#startdate').val());
        sessionStorage.setItem("mileage_end_date", $('#enddate').val());
        var viewmyexpense = $('#viewmyexpense').is(':checked') ? 1 : 0;
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        var status=0;
        var employee = $('#employee-filter').val() ?  $('#employee-filter').val() : 0;
        var url = "{{ route('mileage-claims.list',[':viewmyexpense',':startdate',':enddate',':status',':employee']) }}";
        url = url.replace(':viewmyexpense', viewmyexpense);
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        url = url.replace(':status', status);
        url = url.replace(':employee', employee);
        table.ajax.url( url ).load();
        updateCount(sdate,edate,employee);
}


    function updateCount(startdate,enddate, employee){
        var viewmyexpense = $('#viewmyexpense').is(':checked') ? 1 : 0;
        var employee = employee ? employee:0;
        var url = "{{ route('mileage-claims.getCounts',[':viewmyexpense',':startdate',':enddate', ':employee']) }}";
        url = url.replace(':viewmyexpense', viewmyexpense);
        url = url.replace(':startdate', startdate);
        url = url.replace(':enddate', enddate);
        url = url.replace(':employee', employee);
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                  $('#pending_amount, #aprroved_amount, #pending_reimbursed_amount, #reimbursed_amount').text('$0');
                  $('#pending_count, #aprroved_count, #pending_reimbursed_count, #reimbursed_count').text('Receipts: 0');
                   $.each(data, function(key, value){
                  //  $('#document_category_details').append("<option value="+value.id+">"+value.document_category+"</option>");
                    if(value.status_id==1){
                      $('#pending_count').text('Receipts: '+value.total_count);
                      $('#pending_amount').text('$'+(value.total_amount != null ? value.total_amount : '0' ))
                     }else if(value.status_id==3){
                      $('#aprroved_count').text('Receipts: '+value.total_count);
                      $('#aprroved_amount').text('$' +(value.total_amount != null ? value.total_amount : '0' ))
                     }else if(value.status_id==4){
                        $('#pending_reimbursed_count').text('Receipts: '+value.total_count);
                        $('#pending_reimbursed_amount').text('$'+(value.total_amount != null ? value.total_amount : '0' ))
                     }else if(value.status_id==5){
                        $('#reimbursed_count').text('Receipts: '+value.total_count);
                        $('#reimbursed_amount').text('$'+(value.total_amount != null ? value.total_amount : '0' ))
                     }
                  });

                    } else {
                        console.log('error in else',data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
                contentType: false,
                processData: false,
            });

    }

    $('.card-mileage').on('click', function(e){
        if($(this).attr('data-id') > 0){
        var viewmyexpense = $('#viewmyexpense').is(':checked') ? 1 : 0;
        var sdate = $('#startdate').val();
        var edate = $('#enddate').val();
        var status = $(this).attr('data-id');
        var employee = $('#employee-filter').val() ? $('#employee-filter').val() : 0;
        var url = "{{ route('mileage-claims.list',[':viewmyexpense',':startdate',':enddate', ':status', ':employee']) }}";
        url = url.replace(':viewmyexpense', viewmyexpense);
        url = url.replace(':startdate', sdate);
        url = url.replace(':enddate', edate);
        url = url.replace(':status', status);
        url = url.replace(':employee', employee);
        table.ajax.url( url ).load();
        }
    });

</script>
<style>
    div.modal-footer {
        text-align: center;
        display: block !important;
    }

    .approve-button {
        background: #003a63;
        color: #ffffff;
    }
    .dataTable tbody td {
        padding: 17px 17px !important;
    }
    .employees-filter-row
    {
       padding-top:15px;
    }
    .select2-container
    {
        width: 300px !important;
    }
</style>
@stop
