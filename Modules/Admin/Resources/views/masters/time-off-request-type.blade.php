
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Time Off Request Type')

@section('content_header')
<h1>Time Off Request Type</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Request Type">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="request-type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Request Type</th>
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
                    <h4 class="modal-title" id="myModalLabel">Time Off Request Type</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'request-type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}

                <div class="modal-body">
                    <div class="form-group row" id="request_type">
                        <label for="name" class="col-sm-3 control-label">Request Type</label>
                        <div class="col-sm-9">
                            {{ Form::text('request_type',null,array('class'=>'form-control')) }}

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
        var table = $('#request-type-table').DataTable({
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
                        emailContent(table, 'Time Off Request Type');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('time-off-request-type.list') }}",
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
                {data: 'request_type', name: 'request_type'},
                {data: 'updated_at', name: 'updated_at'},
                {data: null,
                    orderable: false,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        if(o.is_editable==1)
                        {
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        }
                        else
                        {
                            @can('edit_masters')
                            actions += '<a class="{{Config::get('globals.editFontIcon')}} edit-disable" title="Unable to edit" ></a>'
                            @endcan

                        }    
                        
                        if(o.is_deletable != 0)
                        {
                            @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                        }

                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Save Request Type - Start*/
        $('#request-type-form').submit(function (e) {
            e.preventDefault();
            if($('#request-type-form input[name="id"]').val()){
                var message = 'Request type has been updated successfully';
            }else{
                var message = 'Request type has been created successfully';
            }
            formSubmit($('#request-type-form'), "{{ route('time-off-request-type.store') }}", table, e, message);
        });
        /* Save Request Type - End*/

         /* Editing Request Type - Start */
        $("#request-type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("time-off-request-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#request-type-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="request_type"]').val(data.request_type);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Request Type: " + data.request_type)
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
        $('#request-type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('time-off-request-type.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Request type has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Request Type delete - End */

    });
</script>
@stop
