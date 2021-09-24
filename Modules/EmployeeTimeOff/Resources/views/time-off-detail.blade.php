@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Time Off Detail View</h4>
</div>
{{-- @include('timetracker::payperiod-filter') --}}
<table class="table table-bordered timesheet" id="table-id">
    <thead>
        <tr>
            <th class="sl-no">#</th>
            <th class="sl-no">#</th>
           
           <th class="ts-gen">Employee Id</th>
           <th class="ts-gen">Employee Name</th>
           <th class="ts-gen">Description</th>
           <th class="ts-gen">ESA Type</th>
           <th class="ts-gen">Attachments</th>
           <th class="ts-gen">Days Claimed</th>
           <th class="ts-gen">Days Permitted</th>
           <th class="ts-gen">Days Approved</th>
           <th class="ts-gen">Days Rejected</th>
           <th class="start-end-date">Request Date</th>
           <th class="start-end-date">Request Time</th>
           <th class="ts-gen">HR Associate</th>
           <th class="ts-gen">Review Date</th>
           <th class="ts-gen">Review Time</th>
           <th class="ts-gen">Reviewed By</th>
           <th class="note-header">Status</th>
           <th class="ts-gen">Action</th>
       </tr>
   </thead>
</table>
@can('approve_timeoff')
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        
        <div class="modal-content">
               
            {{ Form::open(array('url'=>'#','id'=>'timeoff-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                    <div class="table_title">
                            <h4> Approve/Reject Employee Time Off Request </h4>
                        </div>
                <div class="form-group {{ $errors->has('approved') ? 'has-error' : '' }}" id="approved">
                    <label for="approved" class="col-sm-12 control-label">Approve/Reject</label>
                    <div class="col-sm-12">
                        {{ Form::select('approved', [null=>'Please Select','1'=>'Approve','0'=>'Reject'], null,array('class' =>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}" id="notes">
                    <label for="notes" class="col-sm-12 control-label">Notes</label>
                    <div class="col-sm-12">
                        {{ Form::textarea('notes',null,array('class'=>'form-control','rows' => 2, 'cols' => 2)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}" id="start_date">
                    <label for="start_date" class="col-sm-12 control-label">Start Date</label>
                    <div class="col-sm-12">
                        {{ Form::text('start_date', null, array('readonly' => true, 'class'=>'form-control', 'placeholder'=>'Start Date', 'id'=>'requested_start_date', 'required')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}" id="end_date">
                    <label for="end_date" class="col-sm-12 control-label">Return Date</label>
                    <div class="col-sm-12">
                        {{ Form::text('end_date', null, array('readonly' => true,'class'=>'form-control', 'placeholder'=>'Start Date', 'id'=>'requested_start_date', 'required')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('days_requested') ? 'has-error' : '' }}" id="days_requested">
                    <label for="days_requested" class="col-sm-12 control-label">Days Requested</label>
                    <div class="col-sm-12">
                        {{ Form::text('days_requested',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                
                <div class="form-group {{ $errors->has('days_approved') ? 'has-error' : '' }}" id="days_approved">
                    <label for="days_approved" class="col-sm-12 control-label">Days Approved</label>
                    <div class="col-sm-12">
                        {{ Form::text('days_approved',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('days_rejected') ? 'has-error' : '' }}" id="days_rejected">
                    <label for="days_rejected" class="col-sm-12 control-label">Days Rejected</label>
                    <div class="col-sm-12">
                        {{ Form::text('days_rejected',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>  
                <div class="form-group {{ $errors->has('days_remaining') ? 'has-error' : '' }}" id="days_remaining">
                    <label for="days_remaining" class="col-sm-12 control-label">Remaining Balance</label>
                    <div class="col-sm-12">
                        {{ Form::text('days_remaining',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>                  
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endcan
@stop
@section('scripts')
<script>
    $(function () {
        var table = '';
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
                //bProcessing: false,
                processing: false,
                //serverSide: true,
                //fixedHeader: true,
                responsive: true,
                pageLength: 10,
                //dom: 'Blfrtip',
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
                    "columnDefs": [
                    {
                        className: "nowrap",
                       // "targets": [ 6,7,8 ]
                    },
                    {
                        "width": "20%",
                        "targets": 16
                    }

                    ],
                    ajax: {
                        //"url":"{{ route('timetracker.getTimesheetReportDetail') }}",
                        "url":"{{ route('time-off.list') }}",
                        // "data": function ( d ) {
                        //     d.payperiod = $("#payperiod-filter").val();
                        // },
                        // "dataSrc": function (d) {
                        //     return d
                        // },
                        "error": function (xhr, textStatus, thrownError) {
                            if(xhr.status === 401){
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [
                   // [7, 'desc']
                    ],
                    lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    columns: [
                    {
                        data: 'updated_at',
                        name:'updated_at',
                        visible:false
                    },
                    {
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    // {data: 'DT_RowIndex', name: ''},
                    {
                        data: 'employee.employee_no',
                        name: 'employee_id'
                    },
                    {
                        //data: 'employee.user.first_name',
                        data:null,render:function(o){
                            if(o.employee.trashed_user.last_name)
                            {
                                return o.employee.trashed_user.first_name+' '+o.employee.trashed_user.last_name
                            }else{
                                return o.employee.trashed_user.first_name
                            }
                        },
                        name:'employee_name'
                    },
                    {
                        data: 'nature_of_request',
                        name: 'description'
                    },
                    {
                        //data: 'leave_reason.reason',
                        data:null,render:function(o){
                            if(o.category)
                            {
                                return o.category.type;
                            }else{
                                return '';
                            }
                        },
                        name: 'type'
                    },

                  
                    {
                        data: null, name: 'attachments',
                        sortable: false,
                        render: function (o) {
                         if(o.attachments != ""){
                           var link ='';
                             for (var i = 0; i < o.attachments.length; i++) {
                       //     link += '<a title="Download" href="' + o.attachments[i].at_details2 + '">'+o.attachments[i].attachment.original_name+'</a>';
                         var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                         view_url = view_url.replace(':id', o.attachments[i].attachment_id);
                         view_url = view_url.replace(':module', 'employeeTimeOff');
                         link += '<a title="Download" target="_blank" href="' + view_url + '">'+o.attachments[i].attachment.original_name+'</a><br>';
                           }
                         return link;

                         } else{
                            return '';
                         }
                           return '';
                        },
                    },

                    {
                        data: 'days_requested',
                        name:'days_requested'
                    },
                    {
                        //data: 'days_remaining',
                        data:null, render:function(o){
                            if(o.category)
                            {
                                return o.category.allowed_days;
                            }else{
                                return '';
                            }
                        },
                        name:'days_permitted'
                    },
                    {
                        data: 'days_approved',
                        name:'days_approved'
                    },
                    {
                        data: 'days_rejected',
                        name:'days_rejected'
                    },
                    {
                        data: null,render:function (data, type, row) {
                            var datetime_str = "";
                            datetime_str = datetimeformat(data.created_at,2);
                            return datetime_str;
                        },
                    },
                    {
                        data: null,render:function (data, type, row) {
                            var datetime_str = "";
                            datetime_str = datetimeformat(data.created_at,3);
                            return datetime_str;
                        },
                    },
                    {
                       //data: 'hr.user.first_name',
                        data: null,render:function(data){
                            if(data.hr.trashed_user)
                            {
                                if(data.hr.trashed_user.last_name)
                                {
                                    return data.hr.trashed_user.first_name+' '+data.hr.trashed_user.last_name;
                                }else{
                                    return data.hr.trashed_user.first_name;
                                }
                            }
                        },
                        name:'hr'
                    },
                    {
                        data: null,render:function (data, type, row) {
                           if(data.approved == 0 || data.approved == 1)
                           {
                                var datetime_str = "";
                                datetime_str = datetimeformat(data.latest_log.created_at,2);
                                return datetime_str;
                           }else{
                               return '';
                           }
                        },
                        name : 'review_date'
                    },
                   
                    {
                        data: null,render:function (data, type, row) {
                            if(data.approved == 0 || data.approved == 1)
                           {
                                var datetime_str = "";
                                datetime_str = datetimeformat(data.latest_log.created_at,3);
                                return datetime_str;
                           }else{
                               return '';
                           }
                        },
                        name: 'review_time'
                    },
                    {
                        data: null,render:function (data, type, row) {
                        if(data.approved == 0 || data.approved == 1)
                           {
                               if(data.latest_log.created_by.trashed_user.last_name)
                               {
                                return data.latest_log.created_by.trashed_user.first_name+' '+data.latest_log.created_by.trashed_user.last_name;
                               }else{
                                return data.latest_log.created_by.trashed_user.first_name;
                               }
                           }else{
                               return '';
                           }
                        },
                        name: 'reviewed_by',
                    },
                    {
                        data: null,
                        orderable: false,
                        sortable: false,
                        render: function (o) {
                            var actions = '';
                            if(o.approved == 0 )
                            {
                                return 'Rejected'

                            }else if(o.approved == 1)
                            {
                                return 'Approved'
                            }else{
                                return 'Pending'
                            }

                            //actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                            @can('lookup-remove-entries') @endcan
                            //return actions;
                        },
                    },
                    {
                        data: null,
                        orderable: false,
                        sortable: false,
                        render: function (o) {
                            var actions = '';
                            @can('approve_timeoff')
                            
                                actions += '<a href="#" onclick="openModal(' + o.id + ')" class="fa fa-podcast fa-lg link-ico" data-id=' + o.id + '></a>';
                            
                            @endcan
                            actions += '<a href="edit/' + o.id + '"  class="fa fa-edit fa-lg link-ico" data-id=' + o.id + '></a>';
                            //actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                           
                            return actions;
                        },
                    }
                    ]
                });
                table.on('draw', function () {
                refreshSideMenu();
            } );
        } catch(e){
            console.log(e.stack);
        }

        $("#table-id_wrapper").addClass("no-datatoolbar datatoolbar");

        /*Payperiod dropdown change event - Start*/
        $("#payperiod-filter").change(function(){
            table.ajax.reload();
        });
        /*Payperiod dropdown change event - End*/

    table.on('click', function () {
                refreshSideMenu();
    });
    @can('approve_timeoff')
        $('#timeoff-action-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('time-off.process') }}";
            var formData = new FormData($('#timeoff-action-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "Status of this leave application has been updated",
                            "success");
                        $("#myModal").modal('hide');
                        table.ajax.reload();
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        @endcan



    });

/*Function to format datetime - Start*/
function datetimeformat(date_obj,onlytime){
    if(onlytime == 1){
        var hr_split_arr = date_obj.split(":");
        datetime_str = hr_split_arr[0]+':'+hr_split_arr[1];
        return datetime_str;
    }
    if(onlytime == 2)
    {
        var hr_split_arr = date_obj.split(" ");
        datetime_str = hr_split_arr[0];//+':'+hr_split_arr[1];
        return datetime_str;
    }
    if(onlytime == 3)
    {
        var hr_split_arr = date_obj.split(" ");
        datetime_str = hr_split_arr[1];
        return datetime_str;
    }
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
/**
 * Open a modal popup
 * 
 * @param {*} id 
 */
 function openModal(id) {
    $('#myModal form')[0].reset();
    $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    $('#myModal').find('input[name="id"]').val(id);
    url = 'getSingle/'+id;
     $.ajax({
                url: url,
                type: 'GET',
                //data: id,
                success: function (data) {
                    //alert(JSON.stringify(data));
                    $('#myModal').find('input[name="days_requested"]').val(data.days_approved);
                    $('#myModal').find('input[name="days_approved"]').val(data.days_approved);
                    $('#myModal').find('input[name="days_rejected"]').val(data.days_rejected);
                    $('#myModal').find('input[name="days_remaining"]').val(data.days_remaining);
                    $('#myModal').find('input[name="start_date"]').val(data.start_date);
                    $('#myModal').find('input[name="end_date"]').val(data.end_date);
                    
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    //alert(JSON.stringify(thrownError));
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });

    $('#myModal').modal();
}



</script>
@stop
