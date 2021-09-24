@extends('adminlte::page')

@section('title', 'Vehicle Vendor')

@section('content_header')
<h1>Vendor Name</h1>
@stop

@section('content')
<div class="add-new" data-title="Add New Vendor">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="vehicle-vendor-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor Name</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'vehicle-vendor-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="vehicle_vendor">
                    <label for="vehicle_vendor" class="col-sm-3 control-label">Vendor Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('vehicle_vendor',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop


@section('js')
<script>

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#vehicle-vendor-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('vehicle-vendor-lookup.list') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'vehicle_vendor', name: 'vehicle_vendor',sortable:true},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

         /* Posting data to CriteriaLookupController - Start*/
         $('#vehicle-vendor-form').submit(function (e) {
            e.preventDefault();
            if($('#vehicle-vendor-form input[name="id"]').val()){
                var message = 'Vendor name has been updated successfully';
            }else{
                var message = 'Vendor name has been created successfully';
            }
            formSubmit($('#vehicle-vendor-form'), "{{ route('vehicle-vendor-lookup.store') }}", table, e, message);
        });
        /* Posting data to CriteriaLookupController - End*/

         /* Editing Criterias - Start */
        $("#vehicle-vendor-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("vehicle-vendor-lookup.single",":id") }}';
            var url = url.replace(':id', id);
            $('#vehicle-vendor-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="vehicle_vendor"]').val(data.vehicle_vendor)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Vendor Name: "+data.vehicle_vendor)
                    } else {
                        alert(data);

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
        /* Editing Criterias - End */

        /* Deleting Criterias - Start */
        $('#vehicle-vendor-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('vehicle-vendor-lookup.destroy',':id') }}";
             var url = base_url.replace(':id', id);
            var message = 'Vendor Name has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Criterias - End */

    });

</script>
@stop
