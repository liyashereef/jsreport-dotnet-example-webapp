@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Post Order Groups')
@section('content_header')
<h1>Post Order Groups</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Group">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th width="15%">#</th>
            <th>Group</th>
            <th  width="20%">Actions</th>
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
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id', null)}}
            <div class="modal-body">
                <div class="form-group" id="group">
                    <label for="group" class="col-sm-3 control-label">Group</label>
                    <div class="col-sm-9">
                        {{ Form::text('group', null,array('class' => 'form-control','required'=>TRUE)) }}
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
                columnDefs: [
                             { "targets": [0,2], 
      "className": "text-center", }
                             ],
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Group');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('contracts.post-order-groups.list') }}",
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
                    data: 'group',
                    name: 'group'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
  
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';

                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Group Save - Start */
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'Group has been updated successfully';
            }else{
                var message = 'Group has been created successfully';
            }
            formSubmit($('#form'), "{{ route('contracts.post-order-groups.store') }}", table, e, message);
        });
        /* Group Save- End */


        /* Group Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("contracts.post-order-groups.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="group"]').val(data.group)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Group: " + data.group)
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
        /* Group Edit- End */

        /* Group Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('contracts.post-order-groups.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Group has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Group Delete- End */
    });
</script>
@stop
