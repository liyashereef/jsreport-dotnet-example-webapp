@extends('adminlte::page')
@section('title', 'Competency Rating')
@section('content_header')
<h1>Competency Rating</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Competency Rating">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="competency-rating-table">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>Competency Rating</th>
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
            {{ Form::open(array('url'=>'#','id'=>'competency-rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('rating') ? 'has-error' : '' }}" id="rating">
                    <label for="rating" class="col-sm-3 control-label">Rating</label>
                    <div class="col-sm-9">
                        {{ Form::text('rating',null,array('class'=>'form-control')) }}
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
        var table = $('#competency-rating-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('competency-matrix-rating.list') }}",
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
                    data: 'id',
                    name: 'id',
                    visible:false
                },{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'rating',
                    name: 'rating',
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

        /* Posting data to Controller - Start*/
        $('#competency-rating-form').submit(function (e) {
            e.preventDefault();
            if($('#competency-rating-form input[name="id"]').val()){
                var message = 'Competency rating has been updated successfully';
            }else{
                var message = 'Competency rating has been created successfully';
            }
            formSubmit($('#competency-rating-form'), "{{ route('competency-matrix-rating.store') }}", table, e, message);
        });
        /* Posting data to Controller - End*/

        $("#competency-rating-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("competency-matrix-rating.single",":id") }}';
            var url = url.replace(':id', id);
            $('#competency-rating-form')
            .find('.form-group')
            .removeClass('has-error')
            .find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="rating"]').val(data.rating);
                        $('#myModal input[name="id"]').val(data.id);
                         $('#myModal input[name="order_sequence"]').val(data.order_sequence);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Competency Rating: " +data.rating );
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


        $("#competency-rating-table").on("click", ".delete", function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('competency-matrix-rating.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Competency rating has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });

</script>
@stop
