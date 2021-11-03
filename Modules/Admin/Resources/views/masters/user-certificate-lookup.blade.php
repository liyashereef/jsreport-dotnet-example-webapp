{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'User Certificates')
@section('content_header')
<h1>User Certificates</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Certificate">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="certificate-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Certificate</th>
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
                <h4 class="modal-title" id="myModalLabel">User-Certificates</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'certificate-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="certificate_name">
                    <label for="certificate_name" class="col-sm-3 control-label">Certificate </label>
                    <div class="col-sm-9">
                        {{ Form::text('certificate_name',null,array('class'=>'form-control')) }}
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
        var table = $('#certificate-table').DataTable({
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
                        emailContent(table, 'Positions');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('user-certificate.list') }}",
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
                    sortable:false,
                },
                {
                    data: 'certificate_name',
                    name: 'certificate_name'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var actions = '';
                       if(o.is_deletable ==1){
                        @can('edit_masters')
                         actions = '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        }
                       else{
                        @can('edit_masters')
                         actions = '<a href="#"  title="Unable to Edit" class="edit-disable {{Config::get('globals.editFontIcon')}}"</a>';
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" title="Unable to Delete" class="edit-disable {{Config::get('globals.deleteFontIcon')}}"></a>';
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

        /* Posting data to PositionLookupController - Start*/
        $('#certificate-form').submit(function (e) {
            e.preventDefault();
             if($('#certificate-form input[name="id"]').val()){
                var message = 'Certificate has been updated successfully';
            }else{
                var message = 'Certificate has been created successfully';
            }
            formSubmit($('#certificate-form'), "{{ route('user-certificate.store') }}", table, e, message);
        });


        $("#certificate-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("user-certificate.single",":id") }}';
            var url = url.replace(':id', id);
            $('#certificate-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="certificate_name"]').val(data.certificate_name)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Certificate: "+ data.certificate_name)
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

        $('#certificate-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('user-certificate.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Certificate has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });
</script>
@stop
