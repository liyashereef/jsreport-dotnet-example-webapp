@extends('adminlte::page')
@section('title', 'Competency')
@section('content_header')
<h1>Competency</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Competency">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="competency-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Competency</th>
            <th>Description</th>
            <th>Behaviors</th>
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
            {{ Form::open(array('url'=>'#','id'=>'competency-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('competency') ? 'has-error' : '' }}" id="competency">
                    <label for="competency" class="col-sm-3 control-label">Competency</label>
                    <div class="col-sm-9">
                        {{ Form::text('competency',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('competency_matrix_category_id') ? 'has-error' : '' }}" id="competency_matrix_category_id">
                    <label for="competency_matrix_category_id" class="col-sm-3 control-label">Competency Category</label>
                    <div class="col-sm-9">
                         {{ Form::select('competency_matrix_category_id',(['' => 'Please Select']+$category_lookup),old('competency_matrix_category_id'),array('class' => 'form-control','id'=>'competency_matrix_category_id')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('definition') ? 'has-error' : '' }}" id="definition">
                    <label for="definition" class="col-sm-3 control-label">Definition</label>
                    <div class="col-sm-9">
                         {{ Form::textarea('definition',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('behavior') ? 'has-error' : '' }}" id="behavior">
                    <label for="behavior" class="col-sm-3 control-label">Behavior</label>
                    <div class="col-sm-9">
                         {{ Form::textarea('behavior',null,array('class'=>'form-control')) }}
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
@stop
@section('js')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#competency-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.competency-matrix.list') }}",
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
                    data: 'competency',
                    name: 'competency',
                },
                 {
                    data: 'definition',
                    name: 'definition',
                },
                 {
                    data: 'behavior',
                    name: 'behavior',
                },
                {
                    data: null,
                    orderable:false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
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

        $('select#competency_matrix_category_id').select2({
            tags: true,
            dropdownParent: $("#myModal"),
            placeholder :'Please select',
            width: '100%'
            });


        /* Posting data to Controller - Start*/
        $('#competency-form').submit(function (e) {
            e.preventDefault();
            if($('#competency-form input[name="id"]').val()){
                var message = 'Competency has been updated successfully';
            }else{
                var message = 'Competency has been created successfully';
            }
            formSubmit($('#competency-form'), "{{ route('recruitment.competency-matrix.store') }}", table, e, message);
        });
        /* Posting data to Controller - End*/

        $("#competency-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.competency-matrix.single",":id") }}';
            var url = url.replace(':id', id);
            $('#competency-form')
            .find('.form-group')
            .removeClass('has-error')
            .find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="competency"]').val(data.competency);
                        $('#myModal select[name="competency_matrix_category_id"]').val(data.competency_matrix_category_id);
                        $('#myModal textarea[name="definition"]').html(data.definition);
                        $('#myModal textarea[name="behavior"]').html(data.behavior);
                        $('#myModal input[name="id"]').val(data.id);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Competency: " +data.competency );
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


        $("#competency-table").on("click", ".delete", function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.competency-matrix.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Competency has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });

    $('.add-new').click(function(){
        $('#myModal textarea').empty();
    });

</script>
@stop
