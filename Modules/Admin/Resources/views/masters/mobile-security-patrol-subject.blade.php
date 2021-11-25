@extends('adminlte::page')
@section('title', 'Mobile Security Patrol Subject')
@section('content_header')
<h1>Mobile Security Patrol Subject</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add Mobile Security Patrol Subject">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="subject-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Subject</th>
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
            {{ Form::open(array('url'=>'#','id'=>'subject-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group row" id="subject">
                    <label for="subject" class="col-sm-3 control-label">Subject</label>
                    <div class="col-sm-9">
                        {{ Form::text('subject',null,array('class'=>'form-control')) }}
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
        var table = $('#subject-table').DataTable({
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
            ajax: "{{ route('mobile-security-patrol-subject.list') }}",
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
                    data: 'subject',
                    name: 'subject'
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
        $('#subject-form').submit(function (e) {
            e.preventDefault();
            if($('#subject-form input[name="id"]').val()){
                var message = 'Subject has been updated successfully';
            }else{
                var message = 'Subject has been created successfully';
            }
            formSubmit($('#subject-form'), "{{ route('mobile-security-patrol-subject.store') }}", table, e, message);
        });
        /* Feedback Save - End*/


        /* Feedback Edit - Start */
        $("#subject-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("mobile-security-patrol-subject.single",":id") }}';
            var url = url.replace(':id', id);
            $('#subject-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="subject"]').val(data.subject)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Subject: " + data.subject)
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
        $('#subject-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('mobile-security-patrol-subject.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Subject has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Feedback Delete - End */


    });
</script>
@stop
