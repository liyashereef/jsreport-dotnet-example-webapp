@extends('adminlte::page')
@section('title', 'Site Note Status')
@section('content_header')
<h1>Site Note Status</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Site Status">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="status-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'status-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="status">
                    <label for="status" class="col-sm-3 control-label">Status</label>
                    <div class="col-sm-9">
                        {{ Form::text('status',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group" id="order_sequence">
                        <label for="order_sequence" class="col-sm-3 control-label">Order Sequence Number</label>
                        <div class="col-sm-9">
                            {{ Form::number('order_sequence',null,array('class'=>'form-control','min'=>1)) }}
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
@stop @section('js')
<script>
    $(function () {
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#status-table').DataTable({
             bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Site Status');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('sitestatus.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'status',
                    name: 'status'
                },

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
          } catch(e){
            console.log(e.stack);
        }

        /* Posting data to Training Controller - Start*/
        $('#status-form').submit(function (e) {
            e.preventDefault();
            if($('#status-form input[name="id"]').val()){
                var message = 'Status has been updated successfully';
            }else{
                var message = 'Status has been created successfully';
            }
            formSubmit($('#status-form'), "{{ route('sitestatus.store') }}", table, e, message);
        });
        /* Posting data to Training Controller - End*/

        /* Edit Training type- Start*/
        $("#status-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("sitestatus.single",":id") }}';
            var url = url.replace(':id', id);
            $('#status-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="status"]').val(data.status)
                         $('#myModal input[name="order_sequence"]').val(data.order_sequence)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Status: "+data.status)
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
        /* Edit Training type- End*/

        /* Delete Training type- Start*/
        $('#status-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('sitestatus.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Status has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Delete Training type- End*/

    });
</script>
@stop
