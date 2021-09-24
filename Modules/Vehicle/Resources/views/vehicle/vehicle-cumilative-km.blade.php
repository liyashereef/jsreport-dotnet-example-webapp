@extends('layouts.app')
@section('css')

@endsection
@section('content')
<br>
<div class="row">
<nav class="col-lg-9 col-md-9 col-sm-8">
    <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
        @can('view_pending_maintenance')<a class="nav-item nav-link expense" href="{{ route('vehicle.pending.maintenance') }}">Pending Maintenance</a>@endcan
        @can('view_completed_maintenance') <a class="nav-item nav-link expense" href="{{ route('vehicle.maintenance') }}">Completed Maintenance</a>@endcan
        @can('view_vehicle_cumilative_km')<a class="nav-item nav-link expense active" href="#">Cumulative KM Driven</a>@endcan
        @can('view_vehicle_analysis')<a class="nav-item nav-link expense" href="{{ route('vehicle.analysis') }}">Analysis</a>@endcan
    </div>
</nav>
</div>



<table class="table table-bordered" id="maintenance-table">
    <thead>
        <tr>
        <th></th>
        <th></th>
        <th width="6%"  class="sorting">Start Date</th>
        <th width="10%"  class="sorting">End Date</th>
        <th width="12%"  class="sorting">Employee Name</th>
        <th width="10%"  class="sorting">Employee Number</th>
        <th width="14%">Project Name</th>
        <th width="8%">Project Number</th>
            <th width="8%"  class="sorting">License Plate Number</th>
            <th width="10%"  class="sorting">Current Odometer</th>
            <th width="10%"  class="sorting">Starting Odometer</th>
            <th width="10%"  class="sorting">Ending Odometer</th>
            <th width="10%"  class="sorting">Total km</th>
            <th width="15%" >View</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Cumulative KM Driven</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" style="font-size: small;">
           <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Start Date</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="start_date"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>End Date</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="end_date"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Employee Name</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="employee_name"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Employee Number</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="employee_number"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Project Name</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="project_name"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Project Number</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="project_number"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Vehicle Number</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="vehicle_number"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Vehicle Model</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="vehicle_model"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Starting Odometer by User</strong></label>
            <div class="col-sm-3">
            <label  class="control-label" id="user_odometer_start"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Starting Odometer by System</strong></label>
            <div class="col-sm-3">
            <label  class="control-label" id="system_odometer_start"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Ending Odometer by User</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="user_odometer_end"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Ending Odometer by System</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="system_odometer_end"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Trip km by User</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="trip_km_user"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Trip km by System</strong> </label>
            <div class="col-sm-3">
            <label  class="control-label" id="trip_km_system"> </label>
            </div>
            </div>
            <div class="form-group row">
            <label  class="col-sm-3 control-label"><strong>Starting Visible Damage</strong></label>
            <div class="col-sm-3">
            <label  class="control-label" id="visible_damage_start"> </label>
            </div>
            <label  class="col-sm-3 control-label"><strong>Ending Visible Damage</strong></label>
            <div class="col-sm-3">
            <label  class="control-label" id="visible_damage_end"> </label>
            </div>
            </div>
            <div class="form-group row" style="margin-bottom:-6px !important;">
            <label  class="col-sm-6 control-label"><strong>Start Notes:</strong> </label>
            <label  class="col-sm-6 control-label"><strong>End Notes:</strong> </label>
            </div>

            <div class="form-group row">
            <div class="col-sm-6">
            <label  class="control-label"  id="start_notes"> </label>
            </div>
            <div class="col-sm-6">
            <label  class="control-label" id="end_notes"> </label>
            </div>
            </div>

        </div>
        <div class="modal-footer">
        </div>
        </div>
    </div>
</div>
@stop
@section('scripts')

<script>

    $(function(){

        var url = "{{ route('vehicle.cumilative.list') }}";

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#maintenance-table').DataTable({
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
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [
             { width: '15%', targets: 2 },
             { width: '15%', targets: 3 }
             ],
            columns: [
             {
                    data: null,
                    name: 'start_datetime',
                    visible:false
             },
             {
                    data: null,
                    name: 'end_datetime',
                    visible:false
             },
             {
                    data: 'start_datetime_formatted',
                    name: 'start_datetime_formatted',
                    defaultContent: "--",
                    orderData : [0]
                },

                {
                    data: 'end_datetime_formatted',
                    name: 'end_datetime_formatted',
                    defaultContent: "--",
                    orderData : [1]
                },
                {
                       //data: 'hr.user.first_name',
                        data: 'created_by',
                        name:'created_by',
                    },
                 {

                    data: 'employee_no',
                    name: 'employee_no',
                    defaultContent: "--",
                },
                 {
                    data: 'client_name',
                    name: 'client_name',
                    defaultContent: "--",
                },
                {
                    data: 'project_number',
                    name: 'project_number',
                    defaultContent: "--",
                },
                {
                    data: 'vehicle_number',
                    name: 'vehicle_number',
                    defaultContent: "--"
                },
                {
                    data: 'vehicle_odometer_reading',
                    name: 'vehicle_odometer_reading',
                    defaultContent: "--"
                },
                {
                    data: 'user_odometer_start',
                    name: 'user_odometer_start',
                    defaultContent: "--"
                },

                {
                    data: 'user_odometer_end',
                    name: 'user_odometer_end',
                    defaultContent: "--"
                },
                {
                    data: 'user_distance_travelled',
                    name: 'user_distance_travelled',
                    defaultContent: "--"
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions = '<a href="#" class="edit fa fa-eye" data-id=' + o.id + '></a>';
                        return actions;
                    },
                },


            ]
        });

         } catch(e){
            console.log(e.stack);
        }

});

       $('#maintenance-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('vehicle.maintenance.store') }}";
            var formData = new FormData($('#maintenance-form')[0]);
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
                        swal("Saved", "Service record has been saved",
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


      $('#maintenance-table').on('click', '.edit', function(e){

        var id = $(this).data('id');
        var url = '{{ route("vehicle.cumilative.single",":id") }}';
        var url = url.replace(':id', id);
        console.log(id,url);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
                $("#myModal").modal();
               if(data){
                $('#start_date').text( moment(data.start_datetime.slice(0, -3)).format('MMMM DD, Y hh:mm A'));
                $('#end_date').text(moment(data.end_datetime.slice(0, -3)).format('MMMM DD, Y hh:mm A'));
                $('#employee_name').text(data.user.full_name);
                $('#employee_number').text(data.user.trashed_employee.employee_no);
                $('#project_name').text(data.customer.client_name);
                $('#project_number').text(data.customer.project_number);
                $('#vehicle_model').text(data.vehicle.model);
                $('#vehicle_number').text(data.vehicle.number);
                $('#user_odometer_start').text(data.user_odometer_start);
                $('#user_odometer_end').text(data.user_odometer_end);
                $('#system_odometer_start').text(data.system_odometer_start);
                var system_odometer_end=(data.system_odometer_end)?data.system_odometer_end:'Shift not submitted';
                $('#system_odometer_end').text(system_odometer_end);
                $('#start_notes').text(data.start_notes);
                $('#end_notes').text(data.end_notes);
                $('#trip_km_user').text(data.user_distance_travelled);
                 var system_distance_travelled=(data.system_distance_travelled!=null || data.system_distance_travelled!=0)?data.system_distance_travelled:'--';
                $('#trip_km_system').text(system_distance_travelled);
                 var start_image = '';
                 var end_image   = '';
                if(data.attachments.length > 0){
                 $.each(data.attachments, function(key,value){
                    var url = '{{route('filedownload', [':attachment_id','vehicle-module'])}}'
                    url = url.replace(':attachment_id', value.attachment_id);
                    if(value.vehicle_damage_time ==1 ){
                     start_image = start_image + '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                     $('#visible_damage_start').html(start_image);
                    }else if(value.vehicle_damage_time == 2){
                        end_image = end_image +  '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                     $('#visible_damage_end').html(end_image);
                    }

                  });
                }else{
                    $('#visible_damage_start').html('--');
                     $('#visible_damage_end').html('--');
                }

               }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false,
            processData: false,
        });
        });


     /*   $("#myModal").on("click",".close",function(){
               $('#start_date').text('');
                $('#end_date').text('');
                $('#employee_name').text('');
                $('#employee_number').text('');
                $('#project_name').text('');
                $('#project_number').text('');
                $('#vehicle_model').text('');
                $('#vehicle_number').text('');
                $('#user_odometer_start').text('');
                $('#user_odometer_end').text('');
                $('#system_odometer_start').text('');
                $('#system_odometer_end').text('');
                $('#start_notes').text('');
                $('#end_notes').text('');
                $('#trip_km_user').text('');
                $('#trip_km_system').text('');
       });*/

             function addNew() {

        $("#myModal").modal();
       $('#myModal .modal-title').text("Add new maintenance record:");
      //  $('#client-employee-rating-form').trigger('reset');
       // $('#client-employee-rating-form textarea').text('');
     //   $('#client-employee-rating-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    }
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
        outline: none;
}
.form-group{
margin-bottom: .5rem !important;
}
.control-label{
    color:#191717ad !important;
}
</style>
@stop

