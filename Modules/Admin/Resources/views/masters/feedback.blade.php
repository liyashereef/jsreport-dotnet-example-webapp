@extends('adminlte::page')
@section('title', 'Feedbacks')
@section('content_header')
<h1>Feedback</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add Candidate Feedback">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="feedback-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Feedback</th>
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
            {{ Form::open(array('url'=>'#','id'=>'feedback-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="feedback">
                    <label for="feedback" class="col-sm-3 control-label">Feedback</label>
                    <div class="col-sm-9">
                        {{ Form::text('feedback',null,array('class'=>'form-control')) }}
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
        var table = $('#feedback-table').DataTable({
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
                        emailContent(table, 'Feedback Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('candidate-feedback-lookup.list') }}",
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
                    data: 'feedback',
                    name: 'feedback'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
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

        /* Feedback Save - Start*/
        $('#feedback-form').submit(function (e) {
            e.preventDefault();
            if($('#feedback-form input[name="id"]').val()){
                var message = 'Feedback has been updated successfully';
            }else{
                var message = 'Feedback has been created successfully';
            }
            formSubmit($('#feedback-form'), "{{ route('candidate-feedback-lookup.store') }}", table, e, message);
        });
        /* Feedback Save - End*/


        /* Feedback Edit - Start */
        $("#feedback-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("candidate-feedback-lookup.single",":id") }}';
            var url = url.replace(':id', id);
            $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="feedback"]').val(data.feedback)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Feedback: " + data.feedback)
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
        /* Feedback Edit - End */


        /* Feedback Delete - Start */
        $('#feedback-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('candidate-feedback-lookup.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Feedback has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Feedback Delete - End */


    });
</script>
@stop
