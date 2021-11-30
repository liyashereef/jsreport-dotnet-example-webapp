@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-GL Codes')
@section('content_header')

<h1>GL Codes</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New GL Code">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>GL Code</th>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="gl_code">
                    <label for="gl_code" class="col-sm-3 control-label">GL Code</label>
                    <div class="col-sm-9">
                        {{ Form::text('gl_code',null,array('class' => 'form-control', 'Placeholder'=>'GL Code', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class' => 'form-control', 'Placeholder'=>'Short Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="description">
                    <label for="course_description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class' => 'form-control', 'Placeholder'=>'Description', 'rows' => 5, 'cols' => 40, 'required'=>FALSE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="grouping">
                    <label for="grouping" class="col-sm-3 control-label">Grouping</label>
                    <div class="col-sm-9">
                        {{ Form::text('grouping',null,array('class' => 'form-control', 'Placeholder'=>'Grouping', 'required'=>FALSE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group row" id="pnl_subcode">
                    <label for="pnl_subcode" class="col-sm-3 control-label">PNL Subcode</label>
                    <div class="col-sm-9">
                        {{ Form::text('pnl_subcode',null,array('class' => 'form-control', 'Placeholder'=>'PNL Subcode', 'required'=>FALSE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="pnl_item">
                    <label for="pnl_item" class="col-sm-3 control-label">PNL Item</label>
                    <div class="col-sm-9">
                        {{ Form::text('pnl_item',null,array('class' => 'form-control', 'Placeholder'=>'PNL Item', 'required'=>FALSE)) }}
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
        var table = $('#table').DataTable({
               dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Course Category');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('expense-gl.list') }}",
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
                    data: 'gl_code',
                    name: 'gl_code'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                       
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                       
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

        /* Course Category Save - Start */
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'GL Code has been updated successfully';
            }else{
                var message = 'GL Code has been created successfully';
            }
            formSubmit($('#form'), "{{ route('expense-gl.store') }}", table, e, message);
        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("expense-gl.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="gl_code"]').val(data.gl_code)
                        $('#myModal input[name="short_name"]').val(data.short_name)
                        $('#myModal textarea[name="description"]').val(data.description)
                        $('#myModal input[name="grouping"]').val(data.grouping)
                        $('#myModal input[name="pnl_subcode"]').val(data.pnl_subcode)
                        $('#myModal input[name="pnl_item"]').val(data.pnl_item)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit GL Code: "+ data.gl_code)
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
        /* Course Category Edit - End */

        /* Course Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('expense-gl.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "GL code has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "This category has one or more course", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
                });
        /* Course Category Delete- End */
    });
</script>
@stop
