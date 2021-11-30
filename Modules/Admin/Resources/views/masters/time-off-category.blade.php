
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Time Off Request Type')

@section('content_header')
<h1>Time Off Category</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Time-off Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>ESA Guidance</th>
            <th>ESA Reference</th>
            <th>Allowed Days</th>
            <th>Allowed Weeks</th>
            <th>Allowed Hours</th>
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
                    <h4 class="modal-title" id="myModalLabel">Time Off Category</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                <div class="modal-body">
                    <div class="form-group row" id="type">
                        <label for="name" class="col-sm-3 control-label">Category Type</label>
                        <div class="col-sm-9">
                            {{ Form::text('type',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="description">
                        <label for="name" class="col-sm-3 control-label">ESA Guidance</label>
                        <div class="col-sm-9">
                            {{ Form::textarea('description',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="reference">
                        <label for="name" class="col-sm-3 control-label">ESA Reference</label>
                        <div class="col-sm-9">
                            {{ Form::text('reference',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="allowed_days">
                        <label for="name" class="col-sm-3 control-label">Allowed Days</label>
                        <div class="col-sm-9">
                            {{ Form::text('allowed_days',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="allowed_weeks">
                        <label for="name" class="col-sm-3 control-label">Allowed Weeks</label>
                        <div class="col-sm-9">
                            {{ Form::text('allowed_weeks',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="allowed_hours">
                        <label for="name" class="col-sm-3 control-label">Allowed Hours</label>
                        <div class="col-sm-9">
                            {{ Form::text('allowed_hours',null,array('class'=>'form-control')) }}
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
                        columns: [0, 1, 2,3,4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2,3,4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2,3,4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Time Off Category');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('time-off-category.list') }}",
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
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'type', name: 'type'},
                {data: 'description', name: 'description'},
                {data: 'reference', name: 'reference'},
                {data: 'allowed_days', name: 'allowed_days'},
                {data: 'allowed_weeks', name: 'allowed_weeks'},
                {data: 'allowed_hours', name: 'allowed_hours'},
                {data: 'updated_at', name: 'updated_at'},
                {data: null,
                    orderable: false,
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

        /* Save Request Type - Start*/
        $('#category-form').submit(function (e) {
            e.preventDefault();
            if($('#category-form input[name="id"]').val()){
                var message = 'Time-off category has been updated successfully';
            }else{
                var message = 'Time-off category has been created successfully';
            }
            formSubmit($('#category-form'), "{{ route('time-off-category.store') }}", table, e, message);
        });
        /* Save Request Type - End*/

         /* Editing Request Type - Start */
        $("#category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');

            var url = '{{ route("time-off-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#category-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="type"]').val(data.type);
                        $('#myModal textarea[name="description"]').html(data.description);
                        $('#myModal input[name="reference"]').val(data.reference);
                        $('#myModal input[name="allowed_days"]').val(data.allowed_days);
                        $('#myModal input[name="allowed_hours"]').val(data.allowed_hours);
                        $('#myModal input[name="allowed_weeks"]').val(data.allowed_weeks);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Category Type: " + data.type)
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
        /* Editing Request Type - End */

        /* Request Type delete - Start */
        $('#category-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('time-off-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Time-off category has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Request Type delete - End */

    });
</script>
@stop
