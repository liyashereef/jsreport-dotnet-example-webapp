@extends('adminlte::page')
@section('title', 'Incident Categories')
@section('content_header')
<h1>Incident Categories</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Category</th>
            <th>Short Name</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade"
    id="myModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Category</label>
                    <div class="col-sm-6">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-3">
                        {{ Form::text('category_short_name',null,array('class'=>'form-control')) }}
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
        var table = $('#category-table').DataTable({
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
                        //emailContent(table, 'Assignement Types');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('incident_categories.list') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category_short_name',
                    name: 'category_short_name'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
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


        /* Posting data to AssignmentTypeLookup Controller - Start*/
        $('#category-form').submit(function (e) {
            e.preventDefault();
            if($('#category-form input[name="id"]').val()){
                var message = 'Category has been updated successfully';
            }else{
                var message = 'Category has been created successfully';
            }
            formSubmit($('#category-form'), "{{ route('incident_categories.store') }}", table, e, message);
        });
        /* Posting data to AssignmentTypeLookup Controller - End*/

        /* Editing Assignment Types - Start */
        $("#category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("incident_categories.show",":id") }}';
            var url = url.replace(':id', id);
            $('#category-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal input[name="category_short_name"]').val(data.category_short_name);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Category: " + data.name)
                    } else {

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
        /* Editing Assignment Types - End */

        /* Deleting Assignment Types - Start */
        $('#category-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('incident_categories.destroy',':id') }}";
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
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted",data.message, "success");
                            if (table != null) {
                                table.ajax.reload();
                            }
                        }else{
                            swal("Warning",data.message, "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });
        /* Deleting Assignment Types - End */

    });
</script>
@stop
