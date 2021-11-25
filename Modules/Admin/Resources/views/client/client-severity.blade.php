@extends('adminlte::page')
@section('title', 'Client Severity')
@section('content_header')
<h1>Severity Levels</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Severity">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="severity-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Severity</th>
            <th>Short Name</th>
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
            {{ Form::open(array('url'=>'#','id'=>'severity-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="severity">
                    <label for="severity" class="col-sm-3 control-label">Severity</label>
                    <div class="col-sm-9">
                        {{ Form::text('severity',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group row" id="value">
                    <label for="value" class="col-sm-3 control-label">Order Sequence</label>
                    <div class="col-sm-9">
                        {{ Form::number('value',null,array('class'=>'form-control','min'=>1)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
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
        var table = $('#severity-table').DataTable({
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
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Experiences Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('severity.list') }}",
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
                    data: 'severity',
                    name: 'severity'
                },
                {data: 'short_name', name: 'short_name'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Experience Save - Start*/
        $('#severity-form').submit(function (e) {
            e.preventDefault();
            if($('#severity-form input[name="id"]').val()){
                var message = 'Severity  has been updated successfully';
            }else{
                var message = 'Severity has been created successfully';
            }
            formSubmit($('#severity-form'), "{{ route('severity.store') }}", table, e, message);
        });
        /* Experience Save - End*/


        /* Experience Edit - Start*/
        $("#severity-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("severity.single",":id") }}';
            var url = url.replace(':id', id);
            $('#severity-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="severity"]').val(data.severity)
                        $('#myModal input[name="short_name"]').val(data.short_name)
                        $('#myModal input[name="value"]').val(data.value)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Feedback: " + data.severity)
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
        /* Experience Edit - End*/


        /* Severity Delete - Start*/
        $('#severity-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{route('severity.destroy',':id')}}"
            var url = base_url.replace(':id',id);
            var message = 'Severity has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Severity Delete - End*/

    });
</script>
@stop
