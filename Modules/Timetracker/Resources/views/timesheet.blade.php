@extends('layouts.app')
@section('content')
<!-- Style for status -->
<style>
    .timesheet-filters {
        padding: 10px;
        padding-bottom: 15px;
    }
    .div-text{
        text-align: right
    }
</style>
<div class="table_title">
    <h4>Timesheet By Employee
    <?php
$selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
if (!empty($selected_customer_ids)) {
    echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary pull-right"> Reset Filter</button>';
}
?>
    </h4>


</div>
@include('timetracker::employee-payperiod-filter')

<table class="table table-bordered" id="timesheet-table">
    <thead>
        <tr>
           <th class="sl-no" >#</th>
            <th class="sl-no" >#</th>
            <th class="ts-gen">Employee Id</th>
            <th class="ts-gen">Employee Name</th>
            <th class="ts-gen">Role</th>
            <th class="ts-gen">Project Number</th>
            <th class="ts-gen">Client</th>
            <th class="start-end-date">Start Date & Time<br>(YYYY-MM-DD HH:MM)</th>
            <th class="start-end-date">End Date & Time<br>(YYYY-MM-DD HH:MM)</th>
            <th class="ts-gen">Total Hours</th>
            <th class="note-header" width="15%">Notes</th>

        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('.select2').select2();
            table = $('#timesheet-table').DataTable({
                 processing: true,
                serverSide: true,
             //   fixedHeader: true,
                 responsive: false,
                 scrollX: true,
                 bProcessing: false,
               // fixedColumns: true,
                dom: 'Blfrtip',
                "pageLength": 10,
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o'
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-print'
                    },
                ],
                lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                "columnDefs": [
                   { className: "nowrap", "targets": [ 7,8]},
                ],

                ajax: {
                    "url":'{{ route('timetracker.getTimesheetReport') }}',
                    "data": function ( d ) {
                            d.payperiod = $("#payperiod-filter").val();
                            d.customer = $("#customer-filter").val();
                            d.employee = $("#employee-filter").val();
                            d.from_date = $("#from_date").val();
                            d.to_date = $("#to_date").val();

                        },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[0, 'desc']],
                columns: [
                {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible:false
                    },
                    {
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'employee_no',
                        name: 'employee_no'
                    },
                    {
                        data: 'full_name',

                        name:'full_name'
                    },
                    {data: null, name: 'role',
                    render: function(data, type, row, meta) {
                        return uppercase(data.role.replace('_', ' '));
                    },
                },
                    {
                        data: 'project_number',
                        name: 'project_number'
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: null,
                        render:function (data, type, row) {
                            var datetime_str = "";
                            console.log('data.shifts',data.shifts)
                            for(var shift in data.shifts){
                                var start_date = data.shifts[shift].start.replace(/-/g,'/');
                                var date_obj = new Date(start_date);
                                datetime_str = datetime_str+ datetimeformat(date_obj)+"<br/>\r\n";
                            }


                            return datetime_str;
                        },
                        name:'shifts.0.start'
                    },
                    {
                        defaultContent: "--",
                        data: null,
                        render:function (data, type, row) {
                            var datetime_str = "";
                            for(var shift in data.shifts){
                                if(data.shifts[shift].end){
                                var end_date = data.shifts[shift].end.replace(/-/g,'/');
                                var date_obj = new Date(end_date);
                                datetime_str = datetime_str+ datetimeformat(date_obj)+"<br/>\r\n";
                                }else{
                                    datetime_str +='<br/>\r\n';
                                }
                            }
                             return datetime_str;
                        },
                        name:'shifts.0.end'
                    },
                    {
                        data: null,
                        render:function (data, type, row) {
                            var datetime_str = "";
                            for(var shift in data.shifts){
                                datetime_str = datetime_str+ datetimeformat(data.shifts[shift].work_hours,true)+"<br/>\r\n";
                            }
                            return datetime_str;
                        },
                        name:'shifts.0.work_hours'
                    },
                    {
                        data: null,
                        render:function (data, type, row) {
                            var notes_str = notes_str1=new_notes_str= "";
                            for(var shift in data.shifts){
                                //notes_str = notes_str+ data.shifts[shift].notes+"<br/>\r\n";
                                if(data.shifts[shift].notes == "")
                                {
                                   // notes_str = notes_str+"<br/>\r\n";
                                    notes_str =notes_str+'<span style="display:none"></span><br/>\r\n';
                                }else{
                                    //notes_str = '<span class="nowrap">'+notes_str+ data.shifts[shift].notes+"</span><br/>\r\n";
                                    notes_str1 = data.shifts[shift].notes;
                                    if(notes_str1.length > 35){
                                          notes_str = notes_str+'<span class="show-btn nowrap" onclick="$(this).hide();$(this).next().show();">'+ notes_str1.substr(0,40)+'..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a></span><span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">'+ notes_str1+'&nbsp;&nbsp;<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a></span><br/>\r\n';
                                    }else{
                                        notes_str = notes_str+'<span>'+notes_str1+'</span><br/>\r\n';
                                    }
                                }



                                // notes_str = notes_str+ data.shifts[shift].notes;
                                // if(notes_str.length > 35){
                                //     new_notes_str = '<span class="show-btn nowrap" onclick="$(this).hide();$(this).next().show();">'+ notes_str.substr(0,40)+'..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a></span><span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">'+ notes_str+'&nbsp;&nbsp;<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a></span><br/><br/>\r\n';
                                // }else{
                                //     new_notes_str = '<span class="nowrap">'+notes_str+'</span><br/><br/>\r\n';
                                // }
                            }
                            return notes_str;
                        },
                        name: 'shifts.0.notes'
                    },
                ]
            });
        } catch(e){
            console.log(e.stack);
        }

        $("#table-id_wrapper").addClass("no-datatoolbar datatoolbar");

        /*Payperiod dropdown change event - Start*/
        $("#payperiod-filter").change(function(){
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });
        $("#employee-filter").change(function(){
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });
        $("#customer-filter").change(function(){
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });

        $("#filterbutton").click(function(){
            if($("#from_date").val()!="" && $("#to_date").val()==""){
                swal("Warning", "End date cannot be null", "warning");
            }
            else if($("#from_date").val()=="" && $("#to_date").val()!=""){
                swal("Warning", "Start date cannot be null", "warning");
            }
            else if($("#from_date").val()>$("#to_date").val()!=""){
                swal("Warning", "End date cannot be less than Start date", "warning");
            }
            else{
                //$("#payperiod-filter").val('');
                table.ajax.reload();
            }

        });

        $("#from_date, #to_date").change(function(){
            $("#payperiod-filter").val('');
            $("#customer-filter").val('');
            $("#employee-filter").val('');
        });

        $("#resetbutton").click(function(){
            $("#payperiod-filter").val('');
            $("#customer-filter").val('');
            $("#employee-filter").val('');
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });
        /*Payperiod dropdown change event - End*/
        table.on('click', function () {
                refreshSideMenu();
    });
        /*Payperiod dropdown change event - End*/

    });

    /*Function to format datetime - Start*/
    function datetimeformat(date_obj,onlytime){
        if(onlytime){
            var hr_split_arr = date_obj.split(":");
            datetime_str = hr_split_arr[0]+':'+hr_split_arr[1];
            return datetime_str;
        }
        var date_str = date_obj.getDate();
        if(date_str<10) date_str = '0'+date_str;
        var month_str = (date_obj.getMonth())+1;
        if(month_str<10) month_str = '0'+month_str;
        var year_str = date_obj.getFullYear();
        var hour_str = date_obj.getHours();
        if(hour_str<10) hour_str = '0'+hour_str;
        var minute_str = date_obj.getMinutes();
        if(minute_str<10) minute_str = '0'+minute_str;
        var datetime_str = year_str+'-'+month_str+'-'+date_str+' '+hour_str+':'+minute_str;
        return datetime_str;
    }
    /*Function to format datetime - End*/

</script>
@stop
