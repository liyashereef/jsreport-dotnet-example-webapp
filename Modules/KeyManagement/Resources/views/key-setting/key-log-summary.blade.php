@extends('layouts.app')
@section('content')
<style>
.gj-datepicker {
    width: 95%;
}
</style>
<div class="table_title">
    <h4> Key Log Summary </h4>
</div>
{{ Form::open(array('url'=>'#','method'=> 'POST')) }}
{{csrf_field()}}
<div id="filter" style="padding-bottom:20px;">
    <div class="col-md-4"></div><br>
        <div class="form-group row mx-0">
            <div class="col-md-0" style="margin-top:7px;margin-left: 0px;">From Date</div>
                <div class="col-sm-2" id="from_date" style="margin-left:15px;">
                    <input type="text" id="fr_date" name="fr_date" class="form-control datepicker" max="2900-12-31" value="{{date('Y-m-d', strtotime("-2 days"))}}" />
                    <small class="help-block"></small>
                </div>
        <div class="col-md-0" style="padding-left:10px;padding-right:20px;margin-top:7px;margin-left: 0px;"> To Date</div>
            <div class="col-sm-2" id="to_date">
                <input type="text" id="t_date" name="to_date" class="form-control datepicker" max="2900-12-31" value="{{date('Y-m-d')}}" />
                <small class="help-block"></small>
            </div>
        <div class="col-md-0" style="padding-left:10px;padding-right:20px;margin-top:7px;margin-left: 0px;"> Key Name</div>
            <div class="col-sm-2" id="emp_name">
                {{ Form::select('key_id',[''=>'Please Select']+$each_row, null,array('id'=>'key_id','class' => 'form-control select2')) }}
                <small class="help-block"></small>
            </div>
            <div class="col-md-0" style="padding-left:10px;margin-top:7px;margin-left: 0px;"> Customer</div>
        <div class="col-sm-2" >
            <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                <option value="">Select Customer</option>
                @foreach($customer_list as $each_customername)
                <option value="{{ $each_customername->id}}">{{ $each_customername->client_name .' ('.$each_customername->project_number.')' }}
                </option>
                @endforeach
            </select>
            <small class="help-block"></small>
        </div>
        <div class="col-sm-1">
                <input class="button btn btn-primary blue" id="search" type="button" value="Filter" onclick="filter()">
        </div>
    </div>
</div>
{{ Form::close() }}
<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
             <th>#</th>
             <th>Project Number</th>
             <th>Client Name</th>
             <th>Key Details</th>
             <th>Check Out Date</th>
             <th>Check Out Time</th>
             <th>Check In Time</th>
             <th>Check Out Note</th>
             <th>Status</th>
             <th>Action</th>
         </tr>
    </thead>
</table>
@include('keymanagement::key-setting.partials.modal')
@stop
@section('scripts')
<script>

function filter(){
    var table = $('#table-id').DataTable();
    table.ajax.reload();
}

    $(function () {

        $('#key_id').select2();
        $('#clientname-filter').select2();
        function collectFilterData() {
            return {
                keyid: $('#key_id').val(),
                frdate: $('#fr_date').val(),
                tdate: $('#t_date').val(),
                client_id: $('#clientname-filter').val(),
            }
        }

        /* Datatable- Start */


        var table = $('#table-id').DataTable({
            ajax: {
                "url":'{{ route('keysetting.keylog.list') }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());

                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
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
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {
                    data: 'project_number',
                    name: 'project_number',
                    defaultContent: "--",
                },
                {
                    data: 'client_name',
                    name: 'client_name',
                    defaultContent: "--",
                },
                {
                    data: 'key_details',
                    name: 'key_details',
                    defaultContent: "--",
                },
                {
                    data: 'keycheckedout_date',
                    name: 'keycheckedout_date',
                    defaultContent: "--",
                },
                {
                    data: 'keycheckedout_time',
                    name: 'keycheckedout_time',
                    defaultContent: "--",
                },
                {
                    data: 'keycheckedin_time',
                    name: 'keycheckedin_time',
                    defaultContent: "--",
                },
                {
                    data: 'key_checked_out_note',
                    name: 'key_checked_out_note',
                    defaultContent: "--",

                },
                {
                    data: 'key_checked_status',
                    name: 'key_checked_status',
                    defaultContent: "--",

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
         /* Datatable- End */

         $('#table-id').on('click', '.edit', function(e){
        var id = $(this).data('id');
        var url = '{{ route("keysetting.keylog.single",":id") }}';
        var url = url.replace(':id', id);
        console.log(id,url);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
                console.log(data);
                $('#check_out_date').text('');
                $('#check_in_date').text('');
                $('#project_name').text('');
                $('#project_number').text('');
                $('#key_name').text('');
                $('#key_number').text('');
                $('#checkout_to').text('');
                $('#checkin_from').text('');
                $('#checkin_by').text('');
                $('#checkout_by').text('');
                $('#checkout_note').text('');
                $('#checkin_note').text('');
                $("#myModal").modal();

               if(data){
                if(data.checked_out_date_time){
                    $('#check_out_date').text(moment(data.checked_out_date_time.slice(0, -3)).format('MMMM DD, Y hh:mm A'));
                }else{
                    $('#check_out_date').text('--');
                }
                if(data.checked_in_date_time){
                    $('#check_in_date').text(moment(data.checked_in_date_time.slice(0, -3)).format('MMMM DD, Y hh:mm A'));
                }else{
                    $('#check_in_date').text('--');
                }
                $('#project_name').text(data.keyinfo.customer.client_name);
                $('#project_number').text(data.keyinfo.customer.project_number);
                $('#key_name').text(data.keyinfo.room_name);
                $('#key_number').text(data.keyinfo.key_id);
                $('#checkout_to').text(data.checked_out_to);

                $('#checkin_from').text(data.checked_in_from);
                // var system_odometer_end=(data.system_odometer_end)?data.system_odometer_end:'Shift not submitted';
                if(data.checkedinuser != null){
                    $('#checkin_by').text(data.checkedinuser.name_with_emp_no);
                }
                if(data.checkedoutuser != null){
                    $('#checkout_by').text(data.checkedoutuser.name_with_emp_no);
                }
                $('#checkout_note').text(data.notes);
                $('#checkin_note').text(data.check_in_notes);
                var identification_attchment = '';
                var signature_attachment = '';
                var checkin_signature_attchment = '';
                if(data.identifications.length > 0){
                 $.each(data.identifications, function(key,value){
                    var url = '{{route('filedownload', [':attachment_id','keymanagement-identification'])}}'
                    url = url.replace(':attachment_id', value.	identification_attachment_id);
                    identification_attchment = identification_attchment + '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                     $('#id_attachment').html(identification_attchment);

                  });
                }else{
                    $('#id_attachment').html('--');
                }
                if(data.signature_attachment_id){
                    var url = '{{route('filedownload', [':attachment_id','keymanagement-signature'])}}'
                    url = url.replace(':attachment_id',data.signature_attachment_id);
                    signature_attachment = signature_attachment + '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                     $('#checkout_signature').html(signature_attachment);

                }else{
                    $('#checkout_signature').html('--');
                }

                if(data.check_in_signature_attachment_id){
                    var url = '{{route('filedownload', [':attachment_id','keymanagement-signature'])}}'
                    url = url.replace(':attachment_id',data.check_in_signature_attachment_id);
                    checkin_signature_attchment = checkin_signature_attchment + '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                     $('#checkin_signature').html(checkin_signature_attchment);

                }else{
                    $('#checkin_signature').html('--');
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


     });

</script>
@stop


