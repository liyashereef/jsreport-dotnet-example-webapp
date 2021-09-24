@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Schedule Summary </h4>
</div>
<div class="row">
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr_short_term,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
            <span class="help-block"></span>
        </div>
    </div>
</div>
<fieldset class="col-sm-6">
    <div id="filter">
        <label class="col-md-4">
            <input type="radio" name="customer-contract-type" value="1" checked>&nbsp;Short Term Contract</label>
        <label  class="col-md-4">
            <input type="radio" name="customer-contract-type" value="0">&nbsp;Permanent Site</label>
    </div>
</fieldset>
</div>
<br>
<div class="">
    <table class="table table-bordered" id="stc-table">
        <thead>
            <tr>
                <th class="sorting" width="10%">Created Date</th>
                <th class="sorting" width="10%">Project Number</th>
                <th class="sorting" width="8%">Client Name</th>
                <th class="sorting" width="15%">Site Address</th>
                <th class="sorting" width="5%">City</th>
                <th class="sorting" width="5%">Postal Code</th>
                <th class="sorting" width="5%">No of Positions</th>
                <th class="sorting" width="5%">No of Positions Assigned</th>
                <th width="10%">Type</th>
                <th width="10%">Notes</th>
                <th class="sorting" width="10%">Start Date</th>
                <th class="sorting" width="10%">End Date</th>
                <th class="sorting" width="1%">Days Required</th>
                <th class="sorting" width="1%">Site Rate</th>
                <th width="10%">Position Opened</th>
                <th width="10%">Position Closed</th>
                <th class="sorting" width="1%">Duration (Days)</th>
                <th class="sorting" width="1%">Status</th>
            </tr>
        </thead>
    </table>
</div>

@stop @section('scripts')
<script>
    let settings = null;
  
    var customer_details_arr_short_term= <?php echo json_encode($customer_details_arr_short_term ); ?>;
    var customer_details_arr_permanent= <?php echo json_encode($customer_details_arr_permanent ); ?>;
    $(function () {
        $(".select2").select2();
        $.ajax({
            url: '{{ route("stc_threshold.settings") }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.data) {
                    settings = data.data;
                }
            }
        });

        url = '{{ route("scheduleRequirement.list",":type") }}';
        type = $('input[name=customer-contract-type]:checked').val();
        url = url.replace(':type', type);
        var table = $('#stc-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: false,
            responsive: false,
            ajax: url,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: ['1,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17']
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ['1,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17']
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: ['1,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17']
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [
                {
                    className: "dt-center", targets: [6,7,8,10,11,12,14,15,16,17]
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if(settings != null) {
                    $(row).find('td:eq(16)').css('color','white');
                    let d = new Date();
                    let dateString = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
                    let days =  (dateString < data.start_date) ? daysCounter(dateString, data.start_date): 0;

                    if(days <= settings.no_of_days_critical) {
                        $(row).find('td:eq(16)').css('background-color', settings.critical_days_color).css('color', settings.critical_days_font_color);
                    }else if(days <= settings.no_of_days_major) {
                        $(row).find('td:eq(16)').css('background-color', settings.major_days_color).css('color', settings.major_days_font_color);
                    }else if(days > settings.no_of_days_minor) {
                        $(row).find('td:eq(16)').css('background-color', settings.minor_days_color).css('color', settings.minor_days_font_color);
                    }
                }
            },
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                },
                {
                    data: null,
                    name: 'customer.project_number',
                    render: function (o) {
                        actions = '';
                        var view_url = '{{ route("stc.details", [":id",":requirement_id"]) }}';
                        view_url = view_url.replace(':id', o.customer.id);
                        view_url = view_url.replace(':requirement_id', o.id);
                        actions += '<a title="View event logs" href="' + view_url + '">' + o.customer
                            .project_number + '</a>';
                        return actions;
                    }
                },
                {
                    data: 'customer.client_name',
                    name: 'customer.client_name',
                    defaultContent: "--",
                },
                {
                    data: 'customer.address',
                    name: 'customer.address',
                    defaultContent: "--",
                },
                {
                    data: 'customer.city',
                    name: 'customer.city',
                    defaultContent: "--",
                },
                {
                    data: 'customer.postal_code',
                    name: 'customer.postal_code'
                },
                {
                    data: null,
                    name: null,
                    render: function (o) {
                        return o.multifill.length;
                    }
                },
                {
                    data: null,
                    name: null,
                    render: function (o) {
                        let assigned_shifts = 0;
                        $.each(o.multifill, function(i, item) {
                           if(item.assigned_employee_id != null) {
                            assigned_shifts++;
                           }
                        });
                        return assigned_shifts;
                    }
                },
                {
                    data: null,
                    name: null,
                    render: function (o) {
                        let fill_type = "--";
                        if(o.trashed_fill_type) {
                            fill_type =  o.trashed_fill_type.type;
                        }
                        return fill_type;
                    }
                },
                {
                    data: null,
                    name: null,
                    render: function (o) {
                        let notes = o.notes;
                        if(notes == "" || notes == null)  {
                            return '--';
                        }else{
                            if(notes.length > 15) {
                            return '<span class="show-btn nowrap" style="cursor:pointer;" onclick="$(this).hide();$(this).next().show();">' + notes.substr(0, 15) +
                                        '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                        '</span><span style="cursor:pointer;display:none;" class="notes big-notes" onclick="$(this).hide();$(this).prev().show();">' +
                                        notes + '&nbsp;&nbsp;' +
                                        '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                        '</span><br/>\r\n';
                            }else{
                                return notes;
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: true,
                    type: 'date',
                    render: function (o) {
                        let start_date = o.start_date;
                        return start_date? moment(start_date).format('MMM DD, Y'):'--';
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: true,
                    type: 'date',
                    render: function (o) {
                        let end_date = o.end_date;
                        return end_date? moment(end_date).format('MMM DD, Y'):'--';
                    }
                },
                {
                    data: null,
                    name: 'days_required',
                    orderable: true,
                    defaultContent: '--',
                    render: function (o) {
                        var start_date = o.start_date ? o.start_date : '--';
                        var end_date = o.end_date ? o.end_date : '--';
                        if (start_date != '--' && end_date != '--') {
                            return daysCounter(start_date, end_date);
                        } else {
                            return '--';
                        }
                    }
                },
                {
                    data: 'site_rate',
                    name: 'site_rate',
                    defaultContent: '--',
                    render: function (site_rate) {
                        return '$' + parseFloat(site_rate).toFixed(2)
                    }
                },
                {
                    data: null,
                    name: 'created_at',
                    defaultContent: '--',
                    render: function (row) {
                        var d = new Date(row.created_at);
                        var datestring = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(
                            -2) + "-" + ("0" + d.getDate()).slice(-2);
                        return moment(datestring).format('MMM DD, Y');
                    }
                },
                {
                    data: null,
                    name: 'created_at',
                    orderable:false,
                    render: function (row) {
                        let assigned_shifts = 0;
                        let url = '--';
                        var count=0;
                        $.each(row.multifill, function(i, item) {
                           if(item.assigned_employee_id != null) {
                            assigned_shifts++;
                           }
                        });

                         $.each(row.event_logs, function(key,value){
                         if(value.status==1)
                         count++;
                         });
                        if(row.schedule_customer_all_shifts.length>0 && row.schedule_customer_all_shifts.length == count)
                         {
                            url = '{{ route("candidate.schedule",[":customer_id",":requirement_id",":customer_contract_type",":security_clearence_id"]) }}'
                            url = url.replace(':customer_id', row.customer.id);
                            url = url.replace(':requirement_id', row.id);
                            url = url.replace(':customer_contract_type', row.customer.stc);
                            var security_clearance=(row.security_clearance_level!=null)? (row.security_clearance_level):null;
                            url = url.replace(':security_clearence_id', security_clearance);
                        }
                        if (row.event_log_entry_latest == null || row.event_log_entry_latest.status !=
                            1 || (row.schedule_customer_all_shifts.length>0 && row.schedule_customer_all_shifts.length != count)) {
                            @can('candidate-schedule')
                            url = '{{ route("candidate.schedule",[":customer_id",":requirement_id",":customer_contract_type",":security_clearence_id"]) }}'
                            url = url.replace(':customer_id', row.customer.id);
                            url = url.replace(':requirement_id', row.id);
                            url = url.replace(':customer_contract_type', row.customer.stc);
                             var security_clearance=(row.security_clearance_level!=null)? (row.security_clearance_level):null;
                            url = url.replace(':security_clearence_id', security_clearance);
                            @endcan
                        }

                        if((row.event_log_entry_latest != null && row.schedule_customer_all_shifts.length>0 && row.schedule_customer_all_shifts.length == count)||(row.event_log_entry_latest != null && row.event_log_entry_latest.status ==1 && row.schedule_customer_all_shifts.length==0)) {
                            return (url != "--") ? '<a href="'+url+'">' + moment(row.event_log_entry_latest.project_closed_date).format('MMM DD, Y') +'</a>' : '<a>' + moment(row.event_log_entry_latest.project_closed_date).format('MMM DD, Y') +'</a>';
                        }else if((row.multifill.length > 0) && (assigned_shifts > 0)){
                            return (url != "--") ? '<a href="'+url+'"><i>On going</i></a>': '<a><i>On going</i></a>';
                        }else if((row.multifill.length > 0)){
                            return (url != "--") ? '<a href="'+url+'"><i>Not yet started</i></a>': '<a><i>Not yet started</i></a>';
                        }else{
                            return '<a><i>Positions Removed</i></a>';
                        }

                    }
                },
                {
                    data: null,
                    name: 'duration',
                    sortable: true,
                    defaultContent: '--',
                    render: function (o) {
                         var count=0;
                         $.each(o.event_logs, function(key,value){
                         if(value.status==1)
                         count++;
                         });
                        var d = new Date(o.created_at);
                        var datestring = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(
                            -2) + "-" + ("0" + d.getDate()).slice(-2);
                        if ((datestring != null && o.event_log_entry_latest != null &&
                            o.event_log_entry_latest.status == 1 && o.schedule_customer_all_shifts.length==0) ||(o.event_log_entry_latest != null && o.schedule_customer_all_shifts.length>0 && o.schedule_customer_all_shifts.length == count)){
                            return daysCounter(datestring, o.event_log_entry_latest.project_closed_date);
                        }
                    }
                },
                {
                    data: null,
                    name: 'status',
                    sortable: true,
                    defaultContent: '--',
                    render: function (o) {
                        var d = new Date();
                        var dateString = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(
                            -2) + "-" + ("0" + d.getDate()).slice(-2);
                            return (dateString < o.start_date) ? daysCounter(dateString, o.start_date): 0;
                    }
                }
            ]
        });

        var table = $('#stc-table').DataTable();
        /*Filters for Permanent and STC customer - Start*/
        $('#filter').on('change', 'input[name=customer-contract-type]', function () {
           //var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
           var client_id =  0;
            type = $('input[name=customer-contract-type]:checked').val();
             url = "{{ route('scheduleRequirement.list',[':type',':client_id']) }}";
            let customer_details;
            if(type==1)
            {
            customer_details=customer_details_arr_short_term;
            }
            else
            {
            customer_details=customer_details_arr_permanent; 
            }
            $('#clientname-filter').empty().append($("<option></option>").attr("value", '').text('Select customer')); 
            $.each(customer_details, function(key, value) {   
            $('#clientname-filter').append($("<option></option>").attr("value", key).text(value)); 
            });  
            url = url.replace(':type', type);
            url = url.replace(':client_id', client_id);
            table.ajax.url(url).load();
        });

        $('#clientname-filter').on('change', function(e){
            type = $('input[name=customer-contract-type]:checked').val();
            var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
            url = "{{ route('scheduleRequirement.list',[':type',':client_id']) }}";
            url = url.replace(':type', type);
            url = url.replace(':client_id', client_id);
            table.ajax.url(url).load();
        });

        /*Filters for Permanent and STC customer - End*/

        /** To count days between 2 dates */
        function daysCounter(day1, day2) {
            let start_dt = new Date(day1);
            let start_date = new Date(start_dt.getUTCFullYear(), start_dt.getUTCMonth(), start_dt.getUTCDate(),  start_dt.getUTCHours(), start_dt.getUTCMinutes(), start_dt.getUTCSeconds());
            let end_dt = new Date(day2);
            let end_date = new Date(end_dt.getUTCFullYear(), end_dt.getUTCMonth(), end_dt.getUTCDate(),  end_dt.getUTCHours(), end_dt.getUTCMinutes(), end_dt.getUTCSeconds());
            let timeDiff = Math.abs(end_date.getTime() - start_date.getTime());
            let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            return diffDays + 1;
        }
    });
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
