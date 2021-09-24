@extends('layouts.app')
@section('css')

@endsection
@section('content')
<br>
<div class="row">
<nav class="col-lg-9 col-md-9 col-sm-8">
    <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
         @can('view_pending_maintenance')<a class="nav-item nav-link expense active" href="#">Pending Maintenance</a>@endcan
         @can('view_completed_maintenance')<a class="nav-item nav-link expense" href="{{ route('vehicle.maintenance') }}">Completed Maintenance</a>@endcan
         @can('view_vehicle_cumilative_km')<a class="nav-item nav-link expense" href="{{ route('vehicle.cumilative_km') }}">Cumulative KM Driven</a>@endcan
         @can('view_vehicle_analysis')<a class="nav-item nav-link expense" href="{{ route('vehicle.analysis') }}">Analysis</a>@endcan
        </div>

</nav>


 <div class="checkboxs col-md-3 text-right">
 <input type="checkbox" id="activeCheck" class="chk" name="status_check" value="0" >
 <label for="activeCheck">
 List All Pending Maintenance
 </label>
</div>

</div>



<table class="table table-bordered" id="maintenance-table">
    <thead>
        <tr>
            <th width="10%"  class="sorting">License Plate Number</th>
            <th width="10%"  class="sorting">Model</th>
            <th width="10%"  class="sorting">Make</th>
            <th width="10%"  class="sorting">Current Odometer</th>
            <th width="15%"  class="sorting">Service Type</th>
            <th width="10%"  class="sorting">Service due on date</th>
            <th width="10%">Service due on km</th>
            <th width="7%"  class="sorting">Service Due</th>
            <th width="7%"  class="sorting">Service Critical</th>
            <th width="15%" >Actions</th>
        </tr>
    </thead>
</table>


<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

            <h5 class="modal-title" id="myModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'maintenance-form','class'=>'form-horizontal', 'method'=> 'POST', 'enctype'=>'multipart/form-data')) }}
             {{ Form::hidden('pending_id', null) }}
             {{ Form::hidden('vehicle_odometer', null) }}



            <div class="modal-body">
                <div class="form-group row" id="vehicle_id">
                    <label for="vehicle_id" class="col-sm-3 control-label">Select Vehicle</label>
                    <div class="col-sm-9">
                    {{ Form::select('vehicle_id_bk',[''=>'Please Select']+$vehicles,null,array('class'=>'form-control','id'=>'vehicle_id_bk','disabled'=>true)) }}
                    {{ Form::hidden('vehicle_id', null) }}
                    <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="type_id">
                    <label for="type_id" class="col-sm-3 control-label">Service Type</label>
                    <div class="col-sm-9">
                    {{ Form::select('type_id_bk',[''=>'Please Select']+$vehicle_type,null,array('class'=>'form-control','id'=>'type_id_bk','disabled'=>true)) }}
                    {{ Form::hidden('type_id', null) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="vendor_id">
                    <label for="vendor_id" class="col-sm-3 control-label">Select Vendor</label>
                    <div class="col-sm-9">
                    {{ Form::select('vendor_id',[''=>'Please Select']+$vendorList,null,array('class'=>'form-control','id'=>'vendor_id_bk','required'=>true)) }}
                    <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="service_kilometre">
                    <label for="service_kilometre" class="col-sm-3 control-label">Odometer during service</label>

                    <div class="col-sm-6">
                    {{ Form::text('service_kilometre', null, array('class'=>'form-control','required'=>true , 'style'=>'margin-top: 7px;')) }}

                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="service_date">
                    <label for="service_date" class="col-sm-3 control-label">Service Date</label>
                    <div class="col-sm-6">
                    {{ Form::text('service_date', null, array('class'=>'form-control datepicker', 'placeholder'=>'Service Date','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="total_amount">
                    <label for="total_amount" class="col-sm-3 control-label">Total Charges <span class="mandatory">*</span></label>
                    <label class="control-label" style="margin-left:20px;">$</label>

                    <div class="col-sm-4">
                    {{ Form::text('total_amount', null, array('class'=>'form-control','id'=>'total_amount', 'style'=>'margin-top: 7px;')) }}

                        <small class="help-block"></small>
                    </div>

                </div>
                <div class="form-group row" id="subtotal">
                    <label for="subtotal" class="col-sm-3 control-label">Subtotal</label>
                    <label  class="control-label" style="margin-left:20px;">$</label>

                    <div class="col-sm-4" >
                    {{ Form::text('subtotal', null, array('class'=>'form-control' ,'readonly'=>true, 'style'=>'margin-top: 7px;')) }}

                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="tax">
                    <label for="tax" class="col-sm-3 control-label">Tax <span id="tax_percentage">(@ 0.00%)</span></label>

                    <label class="control-label" style="margin-left:20px;">$</label><div class="col-sm-4">
                    {{ Form::text('tax_amount', null, array('class'=>'form-control','readonly'=>true, 'style'=>'margin-top: 3px;')) }}
                    {{ Form::hidden('tax', null) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="invoice">
                        <div class="col-sm-3">Upload Invoice   <small>(doc,docx,pdf,xls,xlsx)</small></div>
                        <div class="col-sm-6">
                                <input  type="file" name="invoice" id="invoice" class="form-control" />
                                <span id="fname"></span>
                                <small class="help-block"></small>
                        </div>
                </div>
                <div class="form-group row" id="notes">
                    <label for="notes" class="col-sm-3 control-label">Notes</label>
                    <div class="col-sm-9">
                    {{ Form::textarea('notes', null, array('class'=>'form-control','rows'=>4)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@section('scripts')

<script>

    $(function(){
        var base_url = "{{route('vehicle.pending.maintenance.list',':all')}}";
        var url = base_url.replace(':all',  $('#activeCheck').val());

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
                [8, "desc"],[7, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [
             { width: '10%', targets: 0 }
             ],
             createdRow: function (row, data, dataIndex) {
                    if( data.service_critical=='Yes'){
                            $(row).css('background-color', 'red').addClass('font-color-red');
                            $(row).children().addClass('font-color-red');
                    }

                    if(( data.service_due=='Yes') && (data.service_critical=='No')){
                            $(row).css('background-color', 'yellow').addClass('font-color-yellow');
                            $(row).children().addClass('font-color-yellow');
                    }

             },
            columns: [


                {
                    data: 'vehicle_number',
                    name: 'vehicle_number',
                    defaultContent: "--"
                },
                {
                    data: 'vehicle_model',
                    name: 'vehicle_model',
                    defaultContent: "--"
                },
                {
                    data: 'vehicle_make',
                    name: 'vehicle_make',
                    defaultContent: "--"
                },
                {
                    data: 'vehicle_odometer_reading',
                    name: 'vehicle_odometer_reading',
                    defaultContent: "--"
                },
                {
                    data: 'maintenance_type_name',
                    name: 'maintenance_type_name',
                    defaultContent: "--"
                },

                {
                    data: 'service_date',
                    name: 'service_date',
                    defaultContent: "--"
                },

                {
                    data: 'service_kilometre',
                    name: 'service_kilometre',
                    defaultContent: "--"
                },
                {
                        data: 'service_due',
                        name: 'service_due',

                },
                {
                        data: 'service_critical',
                        name: 'service_critical',
                        defaultContent: "--",

                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('add_maintanence_vehicle')
                         actions = '<a href="#" title="Add service" class="edit fa fa-plus-square" data-id=' + o.id +'></a>'
                         @endcan
                         return actions;
                    },
                }

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
            formData.append("upload_file","invoice");
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

        $('#activeCheck').on('change', function (e) {
            if(this.checked)
            {
              $('#activeCheck').val(1);
            }
            else
            {
              $('#activeCheck').val(0);
            }

 table.ajax.url('maintenance/list/'+$('#activeCheck').val()).load();

});
        $("#maintenance-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("vehicle.pending.maintenance.single",":id") }}';
            var url = url.replace(':id', id);
            $('#maintenance-table').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#maintenance-form').trigger('reset');
                        $('#myModal input[name="pending_id"]').val(data.id)
                        $('#myModal select[name="vehicle_id_bk"]').val(data.vehicle_id);
                        $('#myModal input[name="vehicle_id"]').val(data.vehicle_id);
                        $('#myModal select[name="type_id_bk"]').val(data.type_id);
                        $('#myModal input[name="type_id"]').val(data.type_id);
                        $('#myModal input[name="vehicle_odometer"]').val(data.vehicle.odometer_reading);

                        if(data.maintenance_type.category.tax != null){
                            tax_percentage = '(@'+data.maintenance_type.category.tax+'%)';
                            tax = data.maintenance_type.category.tax;
                        }else{
                            tax_percentage = '(@ 0.00%)';
                            tax = 0;
                        }
                        $('#myModal input[name="tax"]').val(tax);
                        $('#myModal span[id="tax_percentage"]').text(tax_percentage);
                        $('#myModal .modal-title').text("Enter maintenance details");
                        $("#myModal").modal();

                    } else {
                        console.log(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

     function addNew() {

        $("#myModal").modal();
       $('#myModal .modal-title').text("Add new maintenance record:");
      //  $('#client-employee-rating-form').trigger('reset');
       // $('#client-employee-rating-form textarea').text('');
       $('#maintenance-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    }

    $("#myModal").on("input", "#total_amount", function (e) {
        var total = $('#myModal input[name="total_amount"]').val();
        var tax = $('#myModal input[name="tax"]').val();
        if (isNaN(total)) {
            swal("Warning", "Please enter valid amount", "warning");
        }else if(total != ''){

            var num = Number(total) * 100;
            var deno = 100 + Number(tax);
            subtotal = (num / deno).toFixed(2);
            tax_amount = (Number(total) - Number(subtotal)).toFixed(2);
          $('#myModal input[name="subtotal"]').val(Number(subtotal).toFixed(2));
          $('#myModal input[name="tax_amount"]').val(Number(tax_amount).toFixed(2));
        /*var url = '{{ route("vehicle.pending.maintenance.getsubtotal",[':total',':tax'])}}';
        var url = url.replace(':total', total);
        var url = url.replace(':tax', tax);
          $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="subtotal"]').val(Number(data.subtotal).toFixed(2));
                        $('#myModal input[name="tax_amount"]').val(Number(data.taxamount).toFixed(2);
                    } else {
                        console.log(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });*/
        }else{
            $('#myModal input[name="subtotal"]').val('0.00');
            $('#myModal input[name="tax_amount"]').val('0.00');
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
        outline: none;
}
.form-group{
    margin-bottom:.7rem !important;
}
.control-label{
    margin-bottom:.1rem !important;
    margin-top:.7rem !important;
}
</style>
@stop

