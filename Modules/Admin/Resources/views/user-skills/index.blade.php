@extends('adminlte::page')
@section('title', 'User Skills')
@section('content_header')
<h1>User Skills</h1>
@stop
@section('content')

<div class="add-new" data-title="Add New Skill">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="user-skills-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Skill Name</th>
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
                <h4 class="modal-title" id="myModalLabel">DS</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'user-skills-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Skill Name</label>
                    <div class="col-sm-6">
                        {{ Form::text('name',null,array('class'=>'form-control col-sm-3')) }}
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
@endsection
@section('js')
<script>

$(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#user-skills-table').DataTable({
              bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Incident Subjects');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('user-skills.list') }}",
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
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }
});

        /* Posting data to UserSkillLookup Controller - Start*/
        $('#user-skills-form').submit(function (e) {
            e.preventDefault();
            var table = $('#user-skills-table').DataTable();
            if($('#user-skills-form input[name="id"]').val()){
                var message = 'User skill has been updated successfully';
            }else{
                var message = 'User skill has been created successfully';
            }
            formSubmit($('#user-skills-form'), "{{ route('user-skills.store') }}", table, e, message);
        });
        /* Posting data to UserSkillLookup Controller - End*/

        /* Editing Skill Types - Start */
        $("#user-skills-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("user-skills.single",":id") }}';
            var url = url.replace(':id', id);
            $('#user-skills-table').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Skills: " + data.name)
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
        /* Editing Skill Types - End */

        $('#user-skills-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var table = $('#user-skills-table').DataTable();
            var base_url = "{{ route('user-skills.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'User skill has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Skills - End */
</script>
@endsection
