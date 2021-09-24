@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Post Order Topics')
@section('content_header')
<h1>Post Order Topics</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Topic">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th width="15%">#</th>
            <th>Topic</th>
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
                <div class="form-group" id="topic">
                    <label for="topic" class="col-sm-3 control-label">Topic</label>
                    <div class="col-sm-9">
                        {{ Form::text('topic', null,array('class' => 'form-control','required'=>TRUE)) }}
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
                        emailContent(table, 'Topics');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('contracts.post-order-topics.list') }}",
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
                    data: 'topic',
                    name: 'topic'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
  
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';

                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Topic Save - Start */
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'Topic has been updated successfully';
            }else{
                var message = 'Topic has been created successfully';
            }
            formSubmit($('#form'), "{{ route('contracts.post-order-topics.store') }}", table, e, message);
        });
        /* Topic Save- End */


        /* Topic Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("contracts.post-order-topics.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="topic"]').val(data.topic)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Topic: " + data.topic)
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
        /* Topic Edit- End */

        /* Topic Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('contracts.post-order-topics.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Topic has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Topic Delete- End */
    });
</script>
@stop
