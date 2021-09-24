@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
    <style>
       .customselect>span.select2-container {
            height: 60px;
            overflow-y: auto;
        } 
        
            </style>
        @endsection
        @section('content')
        <style>
            .table_title {
                font-family: 'Montserrat';
                font-size: 16px;
                color: hsl(143, 53, 62);
                color: rgb(51, 63, 80);

            }
        </style>
        <div class="table_title">
            <h4>Incident Updates
                <?php
                $selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
                if (!empty($selected_customer_ids)) {
                    echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary float-right"> Reset Filter</button>';
                }
                ?>
            </h4>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="row mb-2">
                            <label class="col-md-1 pt-1">Status:</label>
                        <div class="col-md-2">
                            <select class="form-control" id="incident-status" multiple="multiple">
                                @foreach($status as $st)
                                <option value="{{$st->id}}">{{$st->status}}</option>
                                @endforeach
                            </select>
                        </div>
                            <label class="col-md-1 pt-1" style="text-align: right !important">Customer:</label>
                        <div class="col-md-4 customselect ">
                    <select id="project" class="" placeholder="Select a Project" multiple>
                        @foreach ($allocatedCustomers as $value)
                        <option value="{{$value["id"]}}"
                       
                        >{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                        @endforeach

                    </select>
                </div>
                    </div>
                    <div class="row mt-1">
                <div class="col-md-1 pt-1" style="">
                    Start Date:
                </div>
                <div class="col-md-2">
                    <input type="text" readonly id="dateFrom" name="dateFrom"    class="form_control datepick" 
                    value="{{$fromDate!=null?$fromDate:date("Y-m-d", strtotime("-3 months"))}}"

                    />
                </div>
                <div class="col-md-1 pt-1" style="text-align: right !important">
                    End Date:
                </div>
                <div class="col-md-2">
                    <input type="text" readonly id="dateTill" name="dateTill"   class="form_control datepick"
                     value="{{$toDate!=null?$toDate:date("Y-m-d")}}"
                      />
                </div>
            
                <div class="col-md-4"></div>
                <div class="col-md-2">
                    <button class="btn btn-primary form-control searchbutton">Search</button>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

</div>
<div>
    <table class="table table-bordered auto-refresh" id="incidents-table">
        <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th class="sorting">Customer</th>
                <th class="sorting">Pay Period</th>
                <th class="sorting">Incident Title</th>
                <th class="sorting">Incident Subject</th>
                <th class="sorting">Incident Report</th>
                <th class="sorting">Incident Priority</th>
                <th></th>
                <th></th>
                <th class="sorting">Reported At</th>
                <th class="sorting">Reported By</th>
                <th class="sorting">Employee No</th>
                <th class="sorting">Last Updated</th>
                <th class="sorting">Status</th>
                <th width="9%" class="sorting">Response Time (HH:MM)</th>
                <th class="">Action</th>
            </tr>
        </thead>
    </table>
</div>
@stop
@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">

<script>

    
    
    
    $(document).ready(function() {
        var selectedCustomers = {!! json_encode($selectedCustomers) !!};
        let args = globalUtils.uraQueryParamToJson(window.location.href);
        let cIds = globalUtils.decodeFromCsv(args.cIds);
       

        $("#project").val(selectedCustomers).select2({
            placeholder: "Select a customer",
            allowClear: true
        });

        // $('.datepick').each(function(){
        //     $(this).datepicker({
        //         dateFormat: 'yy-mm-dd'
        //     }).datepicker("setDate", "0");;
        // });
        $("#dateFrom").datepicker({
            format: "yyyy-mm-dd",
            // defaultDate: new Date(),
            change: function(e) {
            // table.ajax.reload();
            }
        });

        $("#dateTill").datepicker({
            format: "yyyy-mm-dd",
            // defaultDate: new Date(),
            change: function(e) {
            // table.ajax.reload();
            }
        });

        var table = $('#incidents-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: true,
        dom: 'Blfrtip',
        buttons: [{
            extend: 'excelHtml5',
            exportOptions: {
                columns: 'th:not(:last-child)',
            }
        }, ],
        ajax: {
            "url": "{{ route('incident.dashboard.list') }}",
            "data": function(d) {
                //Url arguments
                let projects="";
                if($('#project').val()!=""){
                    projects=$('#project').val();
                }
                
                let args = globalUtils.uraQueryParamToJson(window.location.href);
                d.status = $('#incident-status').val();
                d.customer = projects;
                d.date_from=$("#dateFrom").val();
                d.date_till=$("#dateTill").val();
                return $.extend(d, args);
            },
            'global': true,
            "error": function(xhr, textStatus, thrownError) {
                if (xhr.status === 401) {
                    window.location = "{{ route('login') }}";
                }
            },
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        order: [
            [0, "desc"]
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex) {
            status = (aData['status']).toLowerCase();
            /* Append the grade to the default row class name */
            if (status == "open") {
                $(nRow).addClass('open');
            } else if (status == "in progress") {
                $(nRow).addClass('in_progress');
            } else {
                $(nRow).addClass('closed');
            }
            var info = table.page.info();
            $('td', nRow).eq(0).html(iDisplayIndex + 1 + info.page * info.length);
        },
        lengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        columnDefs: [],
        columns: [{
                data: 'updated_at',
                name: 'updated_at',
                visible: false
            },
            {
                data: 'id',
                name: 'id',
                sortable: false
            },
            {
                data: null,
                name: 'client_name',
                render: function(row) {
                    var view_url = '{{ route("customer.details", [":id",":payperiod_id"]) }}';
                    view_url = view_url.replace(':id', row.customer_id);
                    view_url = view_url.replace(':payperiod_id', row.payperiod_id);
                    return '<a title="View customer details" href="' + view_url + '">' + row.client_name + '</a>';
                },
            },
            {
                data: 'pay_period_name',
                name: 'pay_period_name'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'subject',
                name: 'subject',
            },
            {
                data: null,
                name: 'incident_report',
                sortable: false,
                render: function(o) {
                    if (o.attachment != "") {
                        return '<a href="' + o.attachment + '" target="_blank" title="Incident Report"  class="fa fa-lg fa-list-alt cgl-font-blue"></a>';
                    } else {
                        return '';
                    }
                },
            },
            {data: 'value', name: 'value',defaultContent:'--',sortable:false},
            {data: 'reporter_first_name', name: 'reporter_first_name',visible:false,defaultContent:'--'},
            {data: 'reporter_last_name', name: 'reporter_last_name',visible:false,defaultContent:'--'},
            {
                data: null,
                name: 'created_at',
                render:function(o){
                    return o.created_at
                }
            },
            {data: null, name: 'reporter_first_name',render: function(row){ return row.reporter_first_name!=null?((row.reporter_first_name+" "+((row.reporter_last_name == null)?'':row.reporter_last_name ) )):'--' }},
            {data: 'employee_no', name: 'employee_no'},
            {data: 'updated_at_date', name: 'updated_at_date'},
            {data: 'status', name: 'status'},
            {data: 'closed_time', name: 'closed_time'},
            {
                    data: null,
                    sortable: false,
                    render: function (row) {
                      var view_url = '{{ route("incident.details", [":id"]) }}';
                    view_url = view_url.replace(':id', row.id);
                    return '<a class="fas fa-eye" title="View details" href="' + view_url + '"></a>';
            }
        }
        ]
    });

    $(document).on("click",".searchbutton",function(e){
        e.preventDefault();
        table.ajax.reload();
    })

    });

    $('#incident-status').select2();
    

    $(document).on("change", "#incident-status", function() {
        // table.ajax.reload();
    });

    $(document).on("change", "#project", function() {
        // table.ajax.reload();
    });

    
    
</script>

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"> --}}

@stop