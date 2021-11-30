{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page') @section('title', 'Employee Rating') @section('content_header')
<h1>Employee Ratings</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Employee Rating">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="rating-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Rating Lookup</th>
            <th>Rating Score</th>
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
            {{ Form::open(array('url'=>'#','id'=>'rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group row" id="rating">
                    <label for="rating" class="col-sm-3 control-label">Rating</label>
                    <div class="col-sm-9">
                        {{ Form::text('rating',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="score">
                    <label for="score" class="col-sm-3 control-label">Rating Score</label>
                    <div class="col-sm-9">
                        {{ Form::number('score',null,array('class'=>'form-control','step'=>1)) }}
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
        var table = $('#rating-table').DataTable({
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
                        emailContent(table, 'Employee Rating');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('employee-rating.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [2, "asc"]
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
                    data: 'rating',
                    name: 'rating'
                },
                {
                    data: 'score',
                    name: 'score'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                         actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id +'></a>'
                         @endcan
                          @can('lookup-remove-entries')
                         actions +=  '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id +'></a>';
                          @endcan
                          return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        /* Posting data to AssignmentTypeLookup Controller - Start*/
        $('#rating-form').submit(function (e) {
            e.preventDefault();
             if($('#rating-form input[name="id"]').val()){
                var message = 'Employee rating has been updated successfully';
            }else{
                var message = 'Employee rating has been created successfully';
            }
            formSubmit($('#rating-form'), "{{ route('employee-rating.store') }}", table, e, message);
        });
        /* Posting data to AssignmentTypeLookup Controller - End*/

        /* Editing Assignment Types - Start */
        $("#rating-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("employee-rating.single",":id") }}';
            var url = url.replace(':id', id);
            $('#rating-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="rating"]').val(data.rating)
                        $('#myModal input[name="score"]').val(data.score)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Employee Rating: " + data.rating)
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
        /* Editing Assignment Types - End */

        /* Deleting Assignment Types - Start */
        $('#rating-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('employee-rating.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Employee rating has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Assignment Types - End */

    });
</script>
@stop
