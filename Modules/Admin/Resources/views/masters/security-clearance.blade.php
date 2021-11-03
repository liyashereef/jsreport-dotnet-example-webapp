@extends('adminlte::page')
@section('title', 'Security Clearance')
@section('content_header')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>Security Clearance</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Security Clearance">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="security-clearance-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Security Clearance</th>
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
            {{ Form::open(array('url'=>'#','id'=>'security-clearance-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="security_clearance">
                    <label for="security_clearance" class="col-sm-3 control-label">Security Clearance
                        <span class="mandatory">*</span>
                    </label>
                    <div class="col-sm-9">
                        {{ Form::text('security_clearance',null,array('class'=>'form-control')) }}
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
        var table = $('#security-clearance-table').DataTable({
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
                        emailContent(table, 'Security Clearance');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('security-clearance.list') }}",
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
                    data: 'security_clearance',
                    name: 'security_clearance'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions = '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
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

        /* Security clearance save - Start*/
        $('#security-clearance-form').submit(function (e) {
            e.preventDefault();
            if($('#security-clearance-form input[name="id"]').val()){
                var message = 'Security clearance has been updated successfully';
            }else{
                var message = 'Security clearance has been created successfully';
            }
            formSubmit($('#security-clearance-form'), "{{ route('security-clearance.store') }}", table, e, message);
        });
        /* Security clearance save - End*/



        /* Security clearance delete - Start */
        $('#security-clearance-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('security-clearance.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Security clearance has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Security clearance delete - End */

        /* Security clearance Edit - Start */
         $("#security-clearance-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("security-clearance.single",":id") }}';
            var url = url.replace(':id', id);
            $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="security_clearance"]').val(data.security_clearance)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Security Clearance: " + data.security_clearance)
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
         /* Security clearance Edit - End */

        /* Security clearance Edit - Start */
         $("#security-clearance-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("security-clearance.single",":id") }}';
            var url = url.replace(':id', id);
            $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="security_clearance"]').val(data.security_clearance)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Security Clearance: " + data.security_clearance)
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
         /* Security clearance Edit - End */

    });
</script>
@stop
