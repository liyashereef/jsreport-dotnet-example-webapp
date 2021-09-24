@extends('adminlte::page')
@section('title', 'Commissionaires Understanding')
 @section('content_header')
<h1>Commissionaires Understanding</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Comment">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="commissionaires-understanding-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Commissionaires Understanding</th>
            <th>Short Name</th>
            {{-- <th>Created Date</th>
            <th>Last Modified Date</th> --}}
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
            {{ Form::open(array('url'=>'#','id'=>'commissionaires-understanding-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="commissionaires_understandings">
                    <label for="commissionaires_understandings" class="col-sm-3 control-label">Comments</label>
                    <div class="col-sm-9">
                        {{ Form::text('commissionaires_understandings',null,array('class'=>'form-control', 'required')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="order_sequence">
                    <label for="order_sequence" class="col-sm-3 control-label">Order Sequence Number</label>
                    <div class="col-sm-2">
                        {{ Form::number('order_sequence',null,array('class'=>'form-control','min'=>1, 'required')) }}
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
        var table = $('#commissionaires-understanding-table').DataTable({
             bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Commissionaires Understandings');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.commissionaires-understanding.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
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
                    data: 'commissionaires_understandings',
                    name: 'commissionaires_understandings'
                },
                {
                    data: 'short_name',
                    name: 'short_name'
                },
                // {data: 'created_at', name: 'created_at'},
                // {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
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

        /* Submit Commissionaires Understanding - Start*/
        $('#commissionaires-understanding-form').submit(function (e) {
            e.preventDefault();
            if($('#commissionaires-understanding-form input[name="id"]').val()){
                var message = 'Comments has been updated successfully';
            }else{
                var message = 'Comments has been created successfully';
            }
            formSubmit($('#commissionaires-understanding-form'), "{{ route('recruitment.commissionaires-understanding.store') }}", table, e, message);
        });
        /* Submit Commissionaires Understanding - End*/

        /* Edit Commissionaires Understanding- Start*/
        $("#commissionaires-understanding-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.commissionaires-understanding.single",":id") }}';
            var url = url.replace(':id', id);
            $('#commissionaires-understanding-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="commissionaires_understandings"]').val(data.commissionaires_understandings);
                        $('#myModal input[name="short_name"]').val(data.short_name);
                        $('#myModal input[name="order_sequence"]').val(data.order_sequence);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Comment: ");
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
        /* Edit Commissionaires Understanding- End*/

        /* Delete Commissionaires Understanding- Start*/
        $('#commissionaires-understanding-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.commissionaires-understanding.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Comments has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Delete Commissionaires Understanding- End*/

    });
</script>
@stop
