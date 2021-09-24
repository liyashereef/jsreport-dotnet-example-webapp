@extends('layouts.app') @section('content')
@section('css')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f26321;
        color: #fff !important;
    }

    td.compliance-green {
        background-color: green;
        color: black;
    }

    td.compliance-yellow {
        background-color: yellow;
        color: black;
    }

    td.compliance-red {
        background-color: red;
        color: white;
    }

    .greenbg {
        background: #343F4E;
        color: white !important;
    }

    .greenbg a {
        color: white !important;
    }

    .yellowbg {
        background: yellow;
        color: white !important;
    }

    .yellowbg a {
        color: #000 !important;
    }

    .redbg {
        background: red;
        color: white !important;
    }

    #non-compliance-report td {
        text-overflow: ellipsis;
        word-break: break-all;
    }

    #non-compliance-report {
        width: 100%;
    }

    body {
        overflow-x: hidden;
    }
</style>
@endsection

@section('content')
<div class="row  table_title col-sm-12 col-md-12 col-lg-12" style="padding-bottom: 0px;padding-right: 0px;width: 100%;">
    <div class="col-sm-11 col-md-11 col-lg-11">
        <h4>Schedule Non-Compliance Report</h4>
    </div>
    <div style="padding-right: 0px;margin: 15px 0px;" class="col-sm-1 col-md-1 col-lg-1">
        <button id="non-compliance-filter-toggle" class="filter-btn" title="Click to view filter options"><i title="Click to view filter options" class="fas fa-filter"></i></button>
    </div>
</div>

<div class="col-sm-12 col-md-12 col-lg-12 non-compliance-filter-div" style="display: none;">
    <div class="row">

        <div class="col-md-1 text-right">
            <label for="start_date">Start Date<span class="mandatory">*</span></label>
        </div>
        <div class="col-md-2">
            <input type="text" class="datepicker form-control" id="start_date" />
        </div>

        <div class="col-md-1 text-right">
            <label for="start_date">End Date<span class="mandatory">*</span></label>
        </div>
        <div class="col-md-2">
            <input type="text" class="datepicker form-control" id="end_date" />
        </div>

        <div class="col-md-1 text-right">
            <label for="type_element">Payperiod</label>
        </div>
        <div class="col-md-2">
            <select id="payperiod_element" class="select2" placeholder="Select any Payperiod" multiple>
                <option value="">Select any Payperiod</option>
                @foreach ($payperiods as $value)
                <option value="{{$value["id"]}}">{{$value["pay_period_name"]}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 text-right">
            <label for="type_element">Type</label>
        </div>
        <div class="col-md-2">
            <select id="type_element" class="select2" placeholder="Select any Type" multiple>
                @foreach ($scheduleNonComplianceTypes as $key => $scheduleNonComplianceType)
                <option value="{{$key}}">{{$scheduleNonComplianceType}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row" style="padding-top: 10px;padding-bottom:30px;">
        <div class="col-md-1 text-right">
            <label for="customer_element">Customer</label>
        </div>
        <div class="col-md-2">
            <select id="customer_element" class="select2" placeholder="Select any Customer" multiple></select>
        </div>


        <div class="col-md-1 text-right">
            <label for="manager_element">Manager</label>
        </div>
        <div class="col-md-2">
            <select id="manager_element" class="select2" placeholder="Select any Regional Manager" multiple></select>
        </div>


        <div class="col-md-1 text-right">
            <label for="employee_element">Employee</label>
        </div>
        <div class="col-md-2">
            <select id="employee_element" class="select2" placeholder="Select any Employee" multiple></select>
        </div>

        <div class="col-md-1 text-right" style="float: right;">
            <button class="btn btn-primary" id="generate_view">Search</button>
        </div>
    </div>
</div>
<div class="schedule-non-compliance-report-content" style="width:100% !important;">
</div>
<div class="col-sm-12 col-md-12 col-lg-12 text-center load_more_div" style="padding-top:25px;display: none;">
    <button class="btn btn-primary" id="load_more">Load More ...</button>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    let start = 0;
    let limit = 1000;

    $("#generate_view").on("click", function(e) {
        e.preventDefault();

        //element values
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let pay_period = $('#payperiod_element').val();
        let customer = $("#customer_element").val();
        let employee = $('#employee_element').val();
        let manager = $('#manager_element').val();
        let type = $('#type_element').val();

        //check for mandatory filter elements
        if (pay_period == "" && start_date == "" && end_date == "") {
            swal("Alert", "Please fill either date or payperiod fields", "warning");
        } else {
            $(".schedule-non-compliance-report-content-tbody").html('');
            ajaxCallLoadTbl(0, limit, customer, start_date, end_date, pay_period, employee, manager, type);
        }
    });

    $('.datepicker, .select2').on('change', function() {
        start = 0;
        $('.load_more_div').hide();
    });

    $('#load_more').on('click', function() {
        start = (start + limit);
        $('#load_more').prop('disabled', true);

        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let pay_period = $('#payperiod_element').val();
        let customer = $("#customer_element").val();
        let employee = $('#employee_element').val();
        let manager = $('#manager_element').val();
        let type = $('#type_element').val();
        ajaxCallLoadTbl(start, limit, customer, start_date, end_date, pay_period, employee, manager, type)
    });

    function ajaxCallLoadTbl(start, limit, customer, start_date, end_date, pay_period, employee, manager, type) {
        startLoading();
        $.ajax({
            type: "post",
            global:true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('scheduling.report-non-compliance-apply-filter')}}",
            data: {
                'customer_id': customer,
                'start_date': start_date,
                'end_date': end_date,
                'pay_period_id': pay_period,
                'employee_id': employee,
                'manager_id': manager,
                'type': type,
                'start': start,
                'limit': limit
            },
            beforeSend: function() {
                $(".non-compliance-filter-div").hide();
            },
            success: function(response) {
                if (response.data) {
                    // $("#non-compliance-report").removeClass('dataTable');
                    generateHtmlContent(response.data, start);
                    start = parseInt(response.new_start);
                    if (start != 0 && (response.total_rows != start)) {
                        $('.load_more_div').show();
                    } else {
                        $('.load_more_div').hide();
                    }
                    endLoading();
                }
            },
        });
    }

    //generate table from upcoming input array
    function generateHtmlContent(data, start) {
        let htmlBodyElement = ``;
        if (data.length > 0) {
            let i = 0;
            $.each(data, function(key, value) {
                if (value.late_in_color == 2) {
                    i++;
                }

                if (value.early_out_color == 2) {
                    i++;
                }

                htmlBodyElement += `<tr data-user="${value.user_id}" data-pay_period="${value.payperiod_id}"><td style="display: none;">${value.date_hidden}</td>
                    <td>${value.date}</td><td class="text-center">${value.in_time}</td><td class="text-center">${value.out_time}</td>
                    <td>${value.site_no}</td><td>${value.site_name}</td><td>${value.employee_no}</td><td>${value.employee}</td>
                    <td class="text-center ${((value.late_in_color == 0)? 'compliance-green':((value.late_in_color == 1)? 'compliance-yellow':((value.late_in_color == 2)? 'compliance-red':'')))}">${((value.late_in_color == 3)? '-' : value.late_in_minutes)}</td>
                    <td class="text-center ${((value.early_out_color == 0)? 'compliance-green':((value.early_out_color == 1)? 'compliance-yellow':((value.early_out_color == 2)? 'compliance-red':'')))}">

                    ${((value.early_out_color == 3)? '-' : value.early_out_minutes)}

                    </td>
                    <td>${value.email}</td><td>${value.phone}</td><td>${value.area_manager}</td></tr>`;
            });
        }

        if (start == 0) {
            let htmlHeadElement = `<thead class="schedule-non-compliance-report-content-thead"><tr><th style="display: none;"></th><th>Date</th><th>In</th><th>Out</th>
                <th>Site No</th><th>Site Name</th><th>Emp#</th><th>Employee Name</th><th>Late In (Minutes)</th><th>Early Out (Minutes)</th>
                <th>Email</th><th>Phone</th><th>Regional Manager</th></tr></thead>`;

            generateDataTable(`<table id="non-compliance-report" class="table table-bordered" style="width:100%;">${htmlHeadElement}<tbody class="schedule-non-compliance-report-content-tbody">${htmlBodyElement}</tbody></table>`);
        } else {
            let tbl_body_content = $(".schedule-non-compliance-report-content-tbody").html();
            let tbl_head_content = $(".schedule-non-compliance-report-content-thead").html();
            $('#non-compliance-report').DataTable().clear().destroy();
            generateDataTable(`<table id="non-compliance-report" class="table table-bordered" style="width:100%;"><thead class="schedule-non-compliance-report-content-thead">` + tbl_head_content + `</thead><tbody class="schedule-non-compliance-report-content-tbody">` + tbl_body_content + htmlBodyElement + `</tbody></table>`);
            $('#load_more').prop('disabled', false);
        }
    }

    //convert rendered table to data table
    function generateDataTable(html) {
        $(".schedule-non-compliance-report-content").html(html)
            .after(function(e) {
                $("#non-compliance-report").dataTable({
                    "bPaginate": false,
                    "bInfo": false,
                    autoWidth: false,
                    dom: 'Blfrtip',
                    scrollCollapse: true,
                    buttons: [{
                        extend: 'excelHtml5',
                        title: 'Schedule Non-Compliance Report'
                    }],
                    order: [
                        [0, "asc"]
                    ],
                    columnDefs: [{
                        className: "dt-center",
                        targets: [1, 2, 7, 8]
                    }],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    "columns": [{
                            // "width": "5%",
                            className: "text-nowrap"
                        },
                        {
                            // "width": "8%",
                            className: "text-nowrap"
                        },
                        {
                            // "width": "6%",
                            className: "text-nowrap"
                        },
                        {
                            // "width": "6%",
                            className: "text-nowrap"
                        },
                        {
                            "width": "9%"
                        },
                        {
                            "width": "10%"
                        },
                        {
                            "width": "7%"
                        },
                        {
                            "width": "11%"
                        },
                        {
                            "width": "5%"
                        },
                        {
                            "width": "5%"
                        },
                        {
                            "width": "10%"
                        },
                        {
                            "width": "14%"
                        },
                        {
                            "width": "8%"
                        },
                    ]
                });
            });
    }

    $('#customer_element').on('change', function() {
        let customer_id = $(this).val();
        $.ajax({
            type: "get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('scheduling.regional-managers-by-customer')}}",
            data: {
                'customer_id': customer_id
            },
            success: function(response) {
                if (response.success) {
                    let options = '';
                    $.each(response.data, function(key, value) {
                        options += `<option value="${value.id}">${value.name}</option>`;
                    });
                    $('#manager_element').html(options);

                    $.ajax({
                        type: "get",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{route('scheduling.allocated-employees-by-customer')}}",
                        data: {
                            'customer_id': customer_id
                        },
                        success: function(response) {
                            if (response.success) {
                                let options = '';
                                $.each(response.data, function(key, value) {
                                    options += `<option value="${value.id}">${value.name}</option>`;
                                });
                                $('#employee_element').html(options);
                            }
                        }
                    });
                }
            }
        });
    });

    $('#manager_element').on('change', function() {
        let customerIds = $('#customer_element').val();

        if (customerIds == "") {
            let area_manager_id = $(this).val();
            $.ajax({
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('scheduling.customers-by-manager')}}",
                data: {
                    'area_manager_id': area_manager_id
                },
                success: function(response) {
                    if (response.success) {
                        let options = '';
                        $.each(response.data, function(key, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });
                        $('#customer_element').html(options);
                        setFilterArgs();
                        $('#generate_view').trigger('click');
                    }
                }
            });
        }
    });

    function startLoading(){
        $('body').loading({
            stoppable: false,
            message: 'Please wait...'
        });
    }

    function endLoading(){
        $('body').loading('stop');
    }

    function findDateByParam(dateObject, substartDays = 0) {
        var today = new Date();
        today.setDate(today.getDate() - substartDays);
        return moment(today).format('Y-MM-DD');
    }

    $("#non-compliance-filter-toggle").click(function() {
        $(".non-compliance-filter-div").toggle('slow');
    });

    $('#payperiod_element').on('change', function() {
        if ($(this).val() != "") {
            $('#start_date').val('');
            $('#end_date').val('');
        } else {
            $("#start_date").val(findDateByParam(new Date(), 2));
            $("#end_date").val(findDateByParam(new Date()));
        }
    });


    function setFilterArgs() {
        let args = globalUtils.uraQueryParamToJson(window.location.href);
        //Url query filter
        if (Object.keys(args).length > 0) {
            //Auto select date
            if (args.from && args.to) {
                $("#start_date").val(moment(args.from).format('Y-MM-DD'));
                $("#end_date").val(moment(args.to).format('Y-MM-DD'));
            }

            let cIds = globalUtils.decodeFromCsv(args.cIds);
            $("#customer_element").val(cIds);
        }
    }

    $(function(e) {
        $("#start_date").val(findDateByParam(new Date(), 2));
        $("#end_date").val(findDateByParam(new Date()));
        $("#type_element").val([1, 6]);
        setFilterArgs();
        $('#generate_view').trigger('click');
        $(".select2").select2();

        // $('#generate_view').trigger('click');
    });
</script>
@endsection
