
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Operation Centre Email')

@section('content_header')
<h1>Operations Centre Email</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Email">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="op-centre-email-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Email</th>
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
                    <h4 class="modal-title" id="myModalLabel">Operations Centre Email</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'operation-centre-email-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}

                <div class="modal-body">
                    <div class="form-group" id="email">
                        <label for="name" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            {{ Form::text('email',null,array('class'=>'form-control')) }}


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
        var table = $('#op-centre-email-table').DataTable({
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
                        emailContent(table, 'Operation Centre Email');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('operation-centre-email.list') }}",
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
                {data: 'email', name: 'email'},
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
        $('#operation-centre-email-form').submit(function (e) {
            e.preventDefault();
            if($('#operation-centre-email-form input[name="id"]').val()){
                var message = 'Email has been updated successfully';
            }else{
                var message = 'Email has been created successfully';
            }
            formSubmit($('#operation-centre-email-form'), "{{ route('operation-centre-email.store') }}", table, e, message);
        });
        /* Save Request Type - End*/

         /* Editing Request Type - Start */
        $("#op-centre-email-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("operation-centre-email.single",":id") }}';
            var url = url.replace(':id', id);
            $('#operation-centre-email-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="email"]').val(data.email);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Email: " + data.email)
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
        $('#op-centre-email-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('operation-centre-email.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Email has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Request Type delete - End */

    });
</script>
@stop
