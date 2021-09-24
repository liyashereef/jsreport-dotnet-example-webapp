@extends('adminlte::page')
@section('title', 'Shift Timings')
@section('content_header')
<h1>Competency Category</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Competency Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="competency-category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Category</th>
            <th>Short Name </th>
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
            {{ Form::open(array('url'=>'#','id'=>'competency-category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('category_name') ? 'has-error' : '' }}" id="category_name">
                    <label for="category_name" class="col-sm-3 control-label">Category</label>
                    <div class="col-sm-9">
                        {{ Form::text('category_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group {{ $errors->has('short_name') ? 'has-error' : '' }}" id="short_name">
                    <label for="from" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control')) }}
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
        var table = $('#competency-category-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('competency-matrix-category.list') }}",
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
                    data: 'category_name',
                    name: 'category_name',
                },
                 {
                    data: 'short_name',
                    name: 'short_name',
                },
                {
                    data: null,
                    orderable:false,
                    render: function (o) {
                        var id=o.id;
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + id + '></a>';
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

        /* Posting data to Controller - Start*/
        $('#competency-category-form').submit(function (e) {
            e.preventDefault();
            if($('#competency-category-form input[name="id"]').val()){
                var message = 'Competency category has been updated successfully';
            }else{
                var message = 'Competency category has been created successfully';
            }
            formSubmit($('#competency-category-form'), "{{ route('competency-matrix-category.store') }}", table, e, message);
        });
        /* Posting data to Controller - End*/

        $("#competency-category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("competency-matrix-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#competency-category-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="category_name"]').val(data.category_name);
                        $('#myModal input[name="short_name"]').val(data.short_name);
                        $('#myModal input[name="id"]').val(data.id);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Competency Category");
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

        $("#competency-category-table").on("click", ".delete", function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('competency-matrix-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Competency category has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });
</script>
@stop
