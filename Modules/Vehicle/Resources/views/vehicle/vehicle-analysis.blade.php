@extends('layouts.app')
@section('css')
@endsection
@section('content')
<br>
<div class="row">
<nav class="col-lg-9 col-md-9 col-sm-8">
    <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
        @can('view_pending_maintenance')<a class="nav-item nav-link expense" href="{{ route('vehicle.pending.maintenance') }}">Pending Maintenance</a>@endcan
        @can('view_completed_maintenance')<a class="nav-item nav-link expense" href="#">Completed Maintenance</a>@endcan
        @can('view_vehicle_cumilative_km')<a class="nav-item nav-link expense" href="{{ route('vehicle.cumilative_km') }}">Cumulative KM Driven</a>@endcan
        @can('view_vehicle_analysis')<a class="nav-item nav-link expense active" href="{{ route('vehicle.analysis') }}">Analysis</a>@endcan
    </div>
</nav>
{{--<div class="col-lg-3 col-md-3 text-right" data-title="Add New Vehicle"><span onclick="addNew();" class="add-new">Add New Record</span></div>--}}
</div>
<div class="table_title">
    <h4>Spend Report</h4>
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
        <div class="col-md-0" style="padding-left:10px;padding-right:20px;margin-top:7px;margin-left: 0px;"> Vendor Name</div>
            <div class="col-sm-2" id="emp_name">
                {{ Form::select('vendor_id',[''=>'Please Select']+$vendorsLookups, null,array('id'=>'vendor_id','class' => 'form-control select2')) }}
                <small class="help-block"></small>
            </div>
        <div class="col-sm-1">
                <input class="button btn btn-primary blue" id="search" type="button" value="Filter" onclick="collectFilterData()">
        </div>
    </div>
</div>
{{ Form::close() }}

<div id="tableDiv">

</div>

@stop
@section('scripts')

<script>

    function collectFilterData () {
            var  filerValue = {
                vendorId: $('#vendor_id').val(),
                frdate: $('#fr_date').val(),
                tdate: $('#t_date').val(),
            }
            listPage(filerValue);
    }
$(function () {
    $('#vendor_id').select2();
    listPage();
});

    function listPage(collectFilterData) {


        var url = "{{ route('vehicle.analysis.list') }}";
          $.ajax({
            url: url,
            type: 'GET',
            data: {data : collectFilterData},
            success: function(data) {
                var table = '';
                var total = '';
                table += '<table class="table table-bordered"><thead><tr>';
                $.each(data.regionDetails, function(key, value) {
                        table += '<th width="1%" class="sorting">'+ value.regions +'</th>';
                });
                table += '</tr></thead>';
                $.each(data.vendors, function(key, value) {
                    table += '<tr class="table_row_'+ key +'"><td id="vendor_name" class="table_col table_cell_'+ key +'"><strong>'+ value.vendor +'</strong></td>';
                        $.each(value.regions, function(keys, values) {
                            if (values.cost != null)
                            {
                                total = "$"+values.cost;
                                } else{
                                    total = "";
                                    }
                        table += '<td id="costRow" class="'+values.region+'">'+ total +'</td>';
                    });
                });

                table += '</tr></table>';
                $('#tableDiv').html(table);
                // $.each(data.regions, function(key, value) {
                // $('#typeid').append($("<option></option>").attr("value",key).text(value));
                // });
            }
        })

    }
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
    .table-bordered{
        background-color: rgb(255, 255, 255);
        color: #c00000 !important;
    }
    .table-bordered td, .table-bordered th{
        border: solid 1px #1d1b1b !important;
}

    }
    .table-bordered th td{
        border: 10px solid black;
    }
    .table-bordered .table_row_0 td{
    background:#c00000 !important;
        color: #f8f5f5 !important;
}
.table_cell_0{
    background:#c00000 !important;
}


    .table-bordered th{
        background: #333f4f;
        color: #ffffff;
        border:5px solid black;
    }
    .table-bordered td{
        /* background: #f36905; */
        color: #000000;
    }

    .dataTable tbody td {
        padding: 17px 17px;
        outline: none;
}

.table_col  {
    background-color: #f36905;
        color: #ffffff;
}
#vendor_name{
    color: #ffffff;
}
.Total {
    background: #333f4f;
    font-weight: bold;
    color: #ffffff !important;
}

</style>
@stop

