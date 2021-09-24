@extends('adminlte::page')
@section('title', 'IDS Payment Reasons')
@section('content_header')
<h1>IDS Payment Reasons</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2 .select2-container{
        width : 12% !important;
    }
</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Reasons">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="office-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created At</th>
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
                <h4 class="modal-title" id="myModalLabel">Office</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ids-office-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="description" class="form-group">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                       {{ Form::textarea('description',null,array('class'=>'form-control','id'=>'description_val','placeholder' => 'Description','rows'=>'5')) }}
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
        $('#office-ids').select2();//Added Select2 to office-ids listing
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#office-table').DataTable({

                ajax: {
                    "url": "{{ route('payment-reasons.getAll') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var actions = "";


                                @can('edit_masters')
                                    actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $('.add-new').on('click',function(e){
            $('#ids-office-form')[0].reset();
            $('#myModal #description_val').text('');
        })

        /* Office Store - Start*/
        $('#ids-office-form').submit(function (e) {
            e.preventDefault();
            if($('#ids-office-form input[name="id"]').val()){
                var message = 'Data has been updated successfully';
            }else{
                var message = 'Data has been created successfully';
            }
            formSubmit($('#ids-office-form'), "{{ route('payment-reasons.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Office Edit - Start*/
        $("#office-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("payment-reasons.single",":id") }}';
            var url = url.replace(':id', id);
            $('#ids-office-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#ids-office-form')[0].reset();

                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal #description_val').text(data.description);

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Payment Reason: " + data.name)
                    } else {
                        // console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    // console.log(xhr.status);
                    // console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Office Edit - End*/

        /* Office Delete  - Start */
        $('#office-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('payment-reasons.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Data has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Office Delete  - End */


    });
</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

@stop
