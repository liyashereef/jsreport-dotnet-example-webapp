@extends('adminlte::page')
@section('title', 'Skill Type')
@section('content_header')
<h1>Skill Type</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Skill Type">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="skill-type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Skill Type</th>
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
                <h4 class="modal-title" id="myModalLabel">Skill Type</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'skill-type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="value">
                    <label for="value" class="col-sm-3 control-label">Skill Type</label>
                    <div class="col-sm-9">
                        {{ Form::text('value',null,array('class'=>'form-control')) }}
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
        var table = $('#skill-type-table').DataTable({
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
            ajax: "{{ route('skill-type.list') }}",
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
                    data: 'value',
                    name: 'value'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
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

        /* Posting data to PositionLookupController - Start*/
        $('#skill-type-form').submit(function (e) {
            e.preventDefault();
            if($('#skill-type-form input[name="id"]').val()){
                var message = 'Skill type has been updated successfully';
            }else{
                var message = 'Skill type has been created successfully';
            }
            formSubmit($('#skill-type-form'), "{{ route('skill-type.store') }}", table, e, message);
        });


        $("#skill-type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("skill-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#skill-type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="value"]').val(data.value)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Skill Type: "+ data.value)
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

        $('#skill-type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('skill-type.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Skill type has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });
</script>
@stop