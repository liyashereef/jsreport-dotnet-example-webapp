@extends('adminlte::page')

@section('title', 'Criterias')

@section('content_header')
<h1>Criteria</h1>
@stop

@section('content')
<div class="add-new" data-title="Add New Criteria">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="criteria-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Criteria</th>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'criteria-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="criteria">
                    <label for="criteria" class="col-sm-3 control-label">Criteria</label>
                    <div class="col-sm-9">
                        {{ Form::text('criteria',null,array('class'=>'form-control')) }}
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
        var table = $('#criteria-table').DataTable({
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
                        emailContent(table, 'Criterias Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('criteria.list') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'criteria', name: 'criteria',sortable:true},
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

         /* Posting data to CriteriaLookupController - Start*/
         $('#criteria-form').submit(function (e) {
            e.preventDefault();
            if($('#criteria-form input[name="id"]').val()){
                var message = 'Criteria has been updated successfully';
            }else{
                var message = 'Criteria has been created successfully';
            }
            formSubmit($('#criteria-form'), "{{ route('criteria.store') }}", table, e, message);
        });
        /* Posting data to CriteriaLookupController - End*/

         /* Editing Criterias - Start */
        $("#criteria-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("criteria.single",":id") }}';
            var url = url.replace(':id', id);
            $('#criteria-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="criteria"]').val(data.criteria)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Criteria: "+data.criteria)
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
        /* Editing Criterias - End */

        /* Deleting Criterias - Start */
        $('#criteria-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('criteria.destroy',':id') }}";
             var url = base_url.replace(':id', id);
            var message = 'Criteria has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Criterias - End */

    });

</script>
@stop
