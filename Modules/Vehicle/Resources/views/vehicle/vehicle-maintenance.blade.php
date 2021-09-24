@extends('layouts.app')
@section('css')

@endsection
@section('content')
<br>
<div class="row">
<nav class="col-lg-9 col-md-9 col-sm-8">
    <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
        @can('view_pending_maintenance')<a class="nav-item nav-link expense" href="{{ route('vehicle.pending.maintenance') }}">Pending Maintenance</a>@endcan
        @can('view_completed_maintenance')<a class="nav-item nav-link expense active" href="#">Completed Maintenance</a>@endcan
        @can('view_vehicle_cumilative_km')<a class="nav-item nav-link expense" href="{{ route('vehicle.cumilative_km') }}">Cumulative KM Driven</a>@endcan
        @can('view_vehicle_analysis')<a class="nav-item nav-link expense" href="{{ route('vehicle.analysis') }}">Analysis</a>@endcan
    </div>
</nav>
{{--<div class="col-lg-3 col-md-3 text-right" data-title="Add New Vehicle"><span onclick="addNew();" class="add-new">Add New Record</span></div>--}}
</div>



<table class="table table-bordered" id="maintenance-table">
    <thead>
        <tr>

            <th width="8%"  class="sorting">License Plate Number</th>
            <th width="8%"  class="sorting">Model</th>
            <th width="8%"  class="sorting">Make</th>
            <th width="8%"  class="sorting">Region</th>
            <th width="10%"  class="sorting">Current Odometer</th>
            <th width="10%">Service Type</th>
            <th width="10%"  class="sorting">Service Date</th>
            <th width="7%"  class="sorting">Service Odometer</th>
            <th width="7%"  class="sorting">Total Charges</th>
            <th width="7%"  class="sorting">Subtotal</th>
            <th width="7%"  class="sorting">Tax</th>
            <th width="7%"  class="sorting">Vendor</th>
            <th width="4%" >Invoice</th>
            <th  class="sorting">Notes</th>
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
            {{ Form::open(array('url'=>'#','id'=>'maintenance-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
             <br>
            <div class="modal-body">
                <div class="form-group row" id="vehicle_id">
                    <label for="vehicle_id" class="col-sm-3">Select Vehicle</label>
                    <div class="col-sm-9">
                    {{ Form::select('vehicle_id',[''=>'Please Select']+$vehicles,null,array('class'=>'form-control','id'=>'vehicleid','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="type_id">
                    <label for="type_id" class="col-sm-3 control-label">Service Type</label>
                    <div class="col-sm-9">
                    {{ Form::select('type_id',[''=>'Please Select'],null,array('class'=>'form-control','id'=>'typeid','required'=>true)) }}

                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="service_kilometre">
                    <label for="service_kilometre" class="col-sm-3 control-label">Odometer</label>
                    <div class="col-sm-6">
                    {{ Form::text('service_kilometre', null, array('class'=>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="service_date">
                    <label for="service_date" class="col-sm-3 control-label">Service Date</label>
                    <div class="col-sm-6">
                    {{ Form::text('service_date', null, array('class'=>'form-control datepicker', 'placeholder'=>'Service Date (Y-m-d)','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="notes">
                    <label for="notes" class="col-sm-3 control-label">Notes</label>
                    <div class="col-sm-9">
                    {{ Form::textarea('notes', null, array('class'=>'form-control','rows'=>5)) }}
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

        var url = "{{ route('vehicle.maintenance.list') }}";

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
                    pageSize: 'A2'
                },
                {
                    extend: 'excelHtml5'
                },
                {
                    extend: 'print',
                    pageSize: 'A2'
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [6, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [
             { width: '10%', targets: 0 }
             ],
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
                    data: 'region_name',
                    name: 'region_name',
                    defaultContent: "--"
                },
                {
                    data: null,
                    name:'odometer_reading',
                    defaultContent: "--",
                    render: function (o) {
                        return Number.parseFloat(o.odometer_reading).toFixed(0);
                    },
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
                },
                 {
                    data: 'total_charges',
                    name: 'total_charges',
                    defaultContent:'--'
                },
     {
                    data: 'subtotal',
                    name: 'subtotal',
                    defaultContent:'--'
                },
     {
                    data: 'tax_amount',
                    name: 'tax_amount',
                    defaultContent:'--',
                  /*  render:function(o)
                    {
                        if(o.tax_amount && o.tax)
                        {
                        var tax_percentage=(o.tax==0.00)?0:o.tax;
                        return  o.tax_amount+' ('+ tax_percentage+'%)';
                        }
                        else
                        return '--';
                    } */
                },
                {
                    data: 'vehicle_vendor',
                    name: 'vehicle_vendor',
                    defaultContent:'--'
                },

                {
                    data: null,
                        name: 'attachment_id',
                        defaultContent: "--",
                        sortable: false,
                        render: function (o) {
                         if(o.attachments != ""){
                            var link ='';
                             for (var i = 0; i < o.attachments.length; i++) {
                       //     link += '<a title="Download" href="' + o.attachments[i].at_details2 + '">'+o.attachments[i].attachment.original_name+'</a>';
                         var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                         view_url = view_url.replace(':id', o.attachments[i].attachment_id);
                         view_url = view_url.replace(':module', 'vehicle-maintenance');
                         link += '<a title="Download" target="_blank" href="' + view_url + '"><i class="fa fa-download fa-lg" aria-hidden="true"></i></a><br>';
                           }
                         return link;

                         } else{
                            return '';
                         }
                        },
                },
                {
                    data: 'notes',
                    name: 'notes',
                }

             //   {
            //        data: 'created_at',
            //        name: 'created_at',
            //    }


            ]
        });

         } catch(e){
            console.log(e.stack);
        }

});
         $('#vehicleid').on('change', function() {
          var id = $(this).val();
          var base_url = "{{route('vehicle.getInitiatedServiceType',':id')}}";
          var url = base_url.replace(':id', id);
          $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
            $('#typeid').find('option:not(:first)').remove();
             $.each(data.initaited_type, function(key, value) {
     $('#typeid')
         .append($("<option></option>")
                    .attr("value",key)
                    .text(value));
});

            }
        })
      })
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
</style>
@stop

