@extends('layouts.app') @section('content')
@section('content')
<style>
    /* Create an active/current tablink class */
    .nav-tabs .item-emp-schedule.active {
        color: black !important;
    }

    .schedule-grid-view-modal {
        width: 100%;
    }

    .ul-schedule {
        border-bottom-color: transparent;
    }

    .item-emp-schedule{
        border: 1px solid #ddd;
    }

    .re_scheduled_0 {
        background-color: #f26321  !important;
        color: #fff !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true]{
        background-color: #f26321  !important;
        color: #fff !important;
    }
    a.schedule-actions {
        padding-right: 15%;
    }

    #payperiod_select .select2-selection__rendered{
        color: white !important;
    }

    .select2-results__option .wrap:before{
    font-family:fontAwesome;
    color:#999;
    content:"\f096";
    width:25px;
    height:25px;
    padding-right: 10px;
    
}
.select2-results__option[aria-selected=true] .wrap:before{
    content:"\f14a";
}

/* not required css */

.select2-multiple, .select2-multiple2
{
  width: 50%
}
</style>

<div class="table_title" style="margin-bottom: 35px;">
    <h4>Schedule Listing</h4>
</div>
<div style="width: 99%;">
    <div class="form-group">
        <div class="row">
            <label class="col-md-1">Pay Period :</label>
            <div class="col-md-3" id="payperiod_select">
                <select id="payperiod" multiple="multiple" class="form-control float-right">
                    @foreach ($payperiods as $payperiod)
                    <option 
                    @if (in_array($payperiod->id,$lastFewPayperiods))
                        selected
                    @endif
                    value="{{$payperiod->id}}">{{$payperiod->pay_period_name.' ('.$payperiod->short_name.')'}}</option>
                    @endforeach
                </select>
            </div>
            <label style="display: none" class="col-md-2">Regional Managers :</label>
            <div style="display: none" class="col-md-3" id="rm_select">
                <select id="regional_manager" multiple="multiple" class="form-control float-right">
                    @foreach ($regionalManager as $rm)
                    <option value="{{$rm->id}}">{{$rm->getFullNameAttribute()}}</option>
                    @endforeach

                </select>
            </div>
        </div>
    </div>
    <div id="schedule-tab-list-element" style="padding-top: 1%;">
        <ul class="nav nav-tabs mb-3 ul-schedule" id="employee-schedule-tab" role="tablist">
            <li>
                <a class="nav-link item-emp-schedule active" id="schedule-pending-tab" data-toggle="pill" href="#schedule-pending" role="tab" aria-controls="pills-pending" aria-selected="true">&nbsp;Pending</a>
            </li>
            <li>
                <a class="nav-link item-emp-schedule" id="schedule-approved-tab" data-toggle="pill" href="#schedule-approved" role="tab" aria-controls="pills-approved" aria-selected="false">&nbsp;Approved</a>
            </li>
            <li>
                <a class="nav-link item-emp-schedule" id="schedule-rejected-tab" data-toggle="pill" href="#schedule-rejected" role="tab" aria-controls="pills-rejected" aria-selected="false">&nbsp;Rejected</a>
            </li>
        </ul>
        <div class="tab-content tab-alignment" id="pills-tabContent">
            <div class="tab-pane active" id="schedule-pending" role="tabpanel" aria-labelledby="pills-pending-tab">
                <table class="table table-bordered employee-schedule" id="employee-schedule-pending-tbl">
                    @include('employeescheduling::partials.schedule-approval-page-pending-header')
                </table>
            </div>
            <div class="tab-pane" id="schedule-approved" role="tabpanel" aria-labelledby="pills-approved-tab">
                <table class="table table-bordered employee-schedule" id="employee-schedule-approved-tbl">
                    @include('employeescheduling::partials.schedule-approval-page-header')
                </table>
            </div>
            <div class="tab-pane" id="schedule-rejected" role="tabpanel" aria-labelledby="pills-rejected-tab">
                <table class="table table-bordered employee-schedule" id="employee-schedule-rejected-tbl">
                    @include('employeescheduling::partials.schedule-rejected-page-header')
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    //schedule rejected status code
    var SCHEDULE_REJECT_STATUS_KEY = 2;
    var haveDeletePermission = "{!! json_encode($haveDeletePermission) !!}";


    var active_status = 0;
    $(function () {
        load_table_data('schedule-pending-tab');

        // $('#payperiod').select2({
        //     formatSelectionCssClass: function (data, container) { return "select2-employee-schedule"; },
        // });

        $('#payperiod').on('change', function () {
            load_table_data();
        });

        $('.item-emp-schedule').on('click', function () {
            load_table_data($(this).attr('id'));
        });
    });

    function load_table_data(active_tab_id = '') {
        if (active_tab_id === '') {
            active_tab_id = $("ul.nav-tabs > li > a.active").attr("id");
        }

        var status = '';
        switch (active_tab_id) {
            case 'schedule-pending-tab':
                tbl_id = 'employee-schedule-pending-tbl';
                status = 0;
                break;
            case 'schedule-approved-tab':
                tbl_id = 'employee-schedule-approved-tbl';
                status = 1;
                break;
            case 'schedule-rejected-tab':
                tbl_id = 'employee-schedule-rejected-tbl';
                status = 2;
                break;
            default:
                tbl_id = 'employee-schedule-pending-tbl';
                status = 0;
        }

        active_status = status;
        if(tbl_id == 'employee-schedule-approved-tbl') {
            var table = $('#employee-schedule-approved-tbl').DataTable({
                bProcessing: false,
                processing: true,
                responsive: false,
                serverSide: true,
                scrollX: false,
                pageLength: 10,
                destroy: true,
                dom: 'Blfrtip',
                ajax: {
                    "url": "{{ route('scheduling.approve-status') }}",
                    "data": function (d) {
                        d.payperiods = $("#payperiod").val();
                        d.status = active_status;
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                createdRow: function (row, data) {
                    if (data.is_rescheduled)
                    {
                        $(row).find("td:first").addClass('re_scheduled_' + data.status);
                    }
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: null,
                        name: 'customer', render: function (o) {
                            return o.customer ? o.customer : '';
                        }
                    },
                    {
                        data: null,
                        name: 'number_of_employees', render: function (o) {
                            return (o.number_of_employees) ? o.number_of_employees : '';
                        }
                    },
                    {
                        data: null,
                        name: 'bi_weekly_total_hours1', render: function (o) {
                            return o.bi_weekly_total_hours ? o.bi_weekly_total_hours : '';
                        }
                    },
                    {
                        data: null,
                        name: 'contractual_hours', render: function (o) {
                            var contractual_hours_string = ((o.contractual_hours) ? o.contractual_hours : '0.00');
                            if(contractual_hours_string != "") {
                                contractual_hours_string = contractual_hours_string.replace(".",":");
                            }
                            return contractual_hours_string;
                        }
                    },
                    {
                        data: null,
                        name: 'created_by', render: function (o) {
                            return o.created_by ? o.created_by : '';
                        }
                    },
                    {
                        data: null,
                        name: 'updated_by', render: function (o) {
                            return o.updated_by ? o.updated_by : '';
                        }
                    },
                    {
                        data: null,
                        name: 'updated_date', render: function (o) {
                            return o.updated_date ? o.updated_date : '';
                        }
                    },
                    {
                        data: null,
                        name: 'Action', render: function (o) {
                            var action_html = '<a class="schedule-actions fa fa-eye" href="#" onclick="load_grid_view(\'' + o.id + '\')" title="View More"></a>';
                            if (active_status === SCHEDULE_REJECT_STATUS_KEY) {
                                action_html += '<a class="schedule-actions fa fa-exchange" href="#" onclick="re_schedule_entry(\'' + o.id + '\')" title="Re-schedule"></a>';
                            }

                            if (haveDeletePermission === 'true') {
                                action_html += '<a class="schedule-actions fa fa-trash" href="#" onclick="delete_schedule_entry(\'' + o.id + '\')" title="Delete schedule"></a>';
                            }
                            return action_html;
                        },bSortable: false,


                    }
                ],
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                    },
                    {
                        extend: 'excelHtml5',
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                    },
                ]
            });
        }else if(tbl_id == 'employee-schedule-rejected-tbl') {
            var table = $('#employee-schedule-rejected-tbl').DataTable({
                bProcessing: false,
                processing: true,
                responsive: false,
                scrollX: false,
                serverSide: true,
                pageLength: 10,
                destroy: true,
                dom: 'Blfrtip',
                ajax: {
                    "url": "{{ route('scheduling.approve-status') }}",
                    "data": function (d) {
                        d.payperiods = $("#payperiod").val();
                        d.status = active_status;
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                createdRow: function (row, data) {
                    if (data.is_rescheduled)
                    {
                        $(row).find("td:first").addClass('re_scheduled_' + data.status);
                    }
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: null,
                        name: 'customer', render: function (o) {
                            return o.customer ? o.customer : '';
                        }
                    },
                    {
                        data: null,
                        name: 'number_of_employees', render: function (o) {
                            return (o.number_of_employees) ? o.number_of_employees : '';
                        }
                    },
                    {
                        data: null,
                        name: 'bi_weekly_total_hours1', render: function (o) {
                            return o.bi_weekly_total_hours ? o.bi_weekly_total_hours : '';
                        }
                    },
                    {
                        data: null,
                        name: 'contractual_hours', render: function (o) {
                            var contractual_hours_string = ((o.contractual_hours) ? o.contractual_hours : '0.00');
                            if(contractual_hours_string != "") {
                                contractual_hours_string = contractual_hours_string.replace(".",":");
                            }
                            return contractual_hours_string;
                        }
                    },
                    {
                        data: null,
                        name: 'created_by', render: function (o) {
                            return o.created_by ? o.created_by : '';
                        }
                    },
                    {
                        data: null,
                        name: 'updated_by', render: function (o) {
                            return o.updated_by ? o.updated_by : '';
                        }
                    },
                    {
                        data: null,
                        name: 'updated_date', render: function (o) {
                            return o.updated_date ? o.updated_date : '';
                        }
                    },
                    {
                        data: null,
                        name: 'Action', render: function (o) {
                            var action_html = '<a class="schedule-actions fa fa-eye" href="#" onclick="load_grid_view(\'' + o.id + '\')" title="View More"></a>';
                            if (active_status === SCHEDULE_REJECT_STATUS_KEY) {
                                action_html += '<a class="schedule-actions fa fa-exchange" href="#" onclick="re_schedule_entry(\'' + o.id + '\')" title="Re-schedule"></a>';
                            }

                            if (haveDeletePermission === 'true') {
                                action_html += '<a class="schedule-actions fa fa-trash" href="#" onclick="delete_schedule_entry(\'' + o.id + '\')" title="Delete schedule"></a>';
                            }
                            return action_html;
                        },bSortable: false,


                    }
                ],
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                    },
                    {
                        extend: 'excelHtml5',
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                    },
                ]
            });
        }else{
            var table = $('#employee-schedule-pending-tbl').DataTable({
                bProcessing: false,
                processing: true,
                responsive: false,
                serverSide: true,
                scrollX: false,
                destroy: true,
                pageLength: 10,
                dom: 'Blfrtip',
                ajax: {
                    "url": "{{ route('scheduling.approve-status') }}",
                    "data": function (d) {
                        d.payperiods = $("#payperiod").val();
                        d.status = active_status;
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    },
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                createdRow: function (row, data) {
                    if (data.is_rescheduled)
                    {
                        $(row).find("td:first").addClass('re_scheduled_' + data.status);
                    }
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: null,
                        name: 'customer', render: function (o) {
                            return o.customer ? o.customer : '';
                        }
                    },
                    {
                        data: null,
                        name: 'number_of_employees', render: function (o) {
                            return (o.number_of_employees) ? o.number_of_employees : '';
                        }
                    },
                    {
                        data: null,
                        name: 'bi_weekly_total_hours', render: function (o) {
                            return o.bi_weekly_total_hours ? o.bi_weekly_total_hours : '';
                        }
                    },
                    {
                        data: null,
                        name: 'contractual_hours', render: function (o) {
                            var contractual_hours_string = (o.contractual_hours ? o.contractual_hours : '0.00');
                            if(contractual_hours_string != "") {
                                contractual_hours_string = contractual_hours_string.replace(".",":");
                            }
                            return contractual_hours_string;
                        }
                    },
                    {
                        data: null,
                        name: 'created_by', render: function (o) {
                            return o.created_by ? o.created_by : '';
                        }
                    },
                    {
                        data: null,
                        name: 'Action', render: function (o) {
                            var action_html = '<a class="schedule-actions fa fa-eye" href="#" onclick="load_grid_view(\'' + o.id + '\')" title="View More"></a>';
                            if (active_status === SCHEDULE_REJECT_STATUS_KEY) {
                                action_html += '<a class="schedule-actions fa fa-exchange" href="#" onclick="re_schedule_entry(\'' + o.id + '\')" title="Re-schedule"></a>';
                            }

                            if (haveDeletePermission === 'true') {
                                action_html += '<a class="schedule-actions fa fa-trash" href="#" onclick="delete_schedule_entry(\'' + o.id + '\')" title="Delete schedule"></a>';
                            }
                            return action_html;
                        },bSortable: false,


                    }
                ],
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                    },
                    {
                        extend: 'excelHtml5',
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                    },
                ]
            });
        }
    }

    function load_grid_view(schedule_id) {
        var base_url = "{{route('scheduling.approval-grid-view','id=:id')}}"
        url = base_url.replace(':id', schedule_id);
        window.location = url;
    }

    function re_schedule_entry(schedule_id) {
        swal({
            title: "Do you want to re-schedule?",
            text: "You won't be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        }, function () {
            $.ajax({
                type: "POST",
                url: "{{route('scheduling.re-schedule-rejected-entry')}}",
                data: {'schedule_id': schedule_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        window.location = '{{ route("scheduling.create",["rejected_id" => ""])}}/' + response.rejected_id + '';
                    } else {
                        swal("Rescheduled", response.msg, 'error');
                    }
                }
            });
        });
    }

    function delete_schedule_entry(schedule_id) {
        swal({
            title: "Do you want to delete?",
            text: "You won't be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        }, function () {
            $.ajax({
                type: "POST",
                url: "{{route('scheduling.delete-schedule')}}",
                data: {'schedule_id': schedule_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        load_table_data();
                        swal("Deleted", response.msg, 'success');
                    } else {
                        swal("Deleted", response.msg, 'error');
                    }
                }
            });
        });
    }

    $(function($) {
	$.fn.select2.amd.require([
    'select2/selection/single',
    'select2/selection/placeholder',
    'select2/selection/allowClear',
    'select2/dropdown',
    'select2/dropdown/search',
    'select2/dropdown/attachBody',
    'select2/utils'
  ], function (SingleSelection, Placeholder, AllowClear, Dropdown, DropdownSearch, AttachBody, Utils) {

		var SelectionAdapter = Utils.Decorate(
      SingleSelection,
      Placeholder
    );
    
    SelectionAdapter = Utils.Decorate(
      SelectionAdapter,
      AllowClear
    );
          
    var DropdownAdapter = Utils.Decorate(
      Utils.Decorate(
        Dropdown,
        DropdownSearch
      ),
      AttachBody
    );
    
		var base_element = $('#payperiod')
    $(base_element).select2({
    	placeholder: 'Select multiple items',
      selectionAdapter: SelectionAdapter,
      dropdownAdapter: DropdownAdapter,
      allowClear: true,
      templateResult: function (data) {

        if (!data.id) { return data.text; }

        var $res = $('<div></div>');

        $res.text(data.text);
        $res.addClass('wrap');

        return $res;
      },
      templateSelection: function (data) {
      	if (!data.id) { return data.text; }
        var selected = ($(base_element).val() || []).length;
        var total = $('option', $(base_element)).length;
        return "Selected " + selected + " of " + total;
      }
    })

    var base_element2 = $('#regional_manager')
    $(base_element2).select2({
    	placeholder: 'Select multiple items',
      selectionAdapter: SelectionAdapter,
      dropdownAdapter: DropdownAdapter,
      allowClear: true,
      templateResult: function (data) {

        if (!data.id) { return data.text; }

        var $res = $('<div></div>');

        $res.text(data.text);
        $res.addClass('wrap');

        return $res;
      },
      templateSelection: function (data) {
      	if (!data.id) { return data.text; }
        var selected = ($(base_element2).val() || []).length;
        var total = $('option', $(base_element2)).length;
        return "Selected " + selected + " of " + total;
      }
    })
  
  });
  
});

</script>

@stop
