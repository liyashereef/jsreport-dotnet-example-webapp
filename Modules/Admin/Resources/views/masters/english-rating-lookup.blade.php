@extends('adminlte::page')
@section('title', 'English Rating')
@section('content_header')
<h1>English Rating</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New English Rating">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered  responsive nowrap" id="english-rating-table"  cellspacing="0" width="100%">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>English Rating</th>
            <th>Order Sequence</th>
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
            {{ Form::open(array('url'=>'#','id'=>'english-rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('english_ratings') ? 'has-error' : '' }}" id="english_ratings">
                    <label for="rating" class="col-sm-3 control-label">Rating</label>
                    <div class="col-sm-9">
                        {{ Form::text('english_ratings',null,array('class'=>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                  <div class="form-group" id="order_sequence">
                        <label for="order_sequence" class="col-sm-3 control-label">Order Sequence Number</label>
                        <div class="col-sm-9">
                            {{ Form::number('order_sequence',null,array('class'=>'form-control','min'=>1)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('score') ? 'has-error' : '' }}" id="score">
                        <label for="score" class="col-sm-3 control-label">Score</label>
                        <div class="col-sm-9">
                            {{ Form::number('score',null,array('class'=>'form-control','min'=>1,'required'=>true)) }}
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
        var table = $('#english-rating-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('english-rating.list') }}",
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
            "columnDefs": [
                { "width": "5%", "targets": 1 },
                { "width": "10%", "targets": 2 },
                { "width": "5%", "targets": 3 },
                { "width": "5%", "targets": 4 },
            ],
            columns: [{
                   
                    data: 'id',
                    name: 'id',
                    visible:false
                },{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'english_ratings',
                    name: 'english_ratings',
                },
                {
                    data: 'order_sequence',
                    name: 'order_sequence',
                },
                {
                    data: null,
                    orderable:false,
                    sortable:false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id='+o.id+'></a>';
                        @endcan
                        @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id='+o.id+'></a>';
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
        $('#english-rating-form').submit(function (e) {
            e.preventDefault();
            if($('#english-rating-form input[name="id"]').val()){
                var message = 'English rating has been updated successfully';
            }else{
                var message = 'English rating has been created successfully';
            }
            formSubmit($('#english-rating-form'), "{{ route('english-rating.store') }}", table, e, message);
        });
        /* Posting data to Controller - End*/

        $("#english-rating-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("english-rating.single",":id") }}';
            var url = url.replace(':id', id);
            $('#english-rating-form')
            .find('.form-group')
            .removeClass('has-error')
            .find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('#myModal input[name="english_ratings"]').val(data.english_ratings);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="order_sequence"]').val(data.order_sequence);
                        $('#myModal input[name="score"]').val(data.score);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit English Rating: ");
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


        $("#english-rating-table").on("click", ".delete", function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('english-rating.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'English rating has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });

</script>
@stop
