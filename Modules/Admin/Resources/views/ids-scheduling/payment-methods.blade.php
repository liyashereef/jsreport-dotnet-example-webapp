@extends('adminlte::page')
@section('title', 'IDS Payment Methods')
@section('content_header')
<h1>IDS Payment Methods</h1>
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
<div class="add-new" data-title="Add New Payment Methods">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="office-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Short Name</th>
            <th>Full Name</th>
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
                <div class="form-group" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control','placeholder' => 'Short Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="full_name" class="form-group">
                    <label for="full_name" class="col-sm-3 control-label">Full Name</label>
                    <div class="col-sm-9">
                       {{ Form::text('full_name',null,array('class'=>'form-control','placeholder' => 'Full Name')) }}
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
                    "url": "{{ route('payment-methods.getAll') }}",
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
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
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
                           if(o.not_removable ==0){
                                @can('edit_masters')
                                    actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' +o.id + '></a>';
                                @endcan
                           }
                           return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        /* Office Store - Start*/
        $('#ids-office-form').submit(function (e) {
            e.preventDefault();
            if($('#ids-office-form input[name="id"]').val()){
                var message = 'Data has been updated successfully';
            }else{
                var message = 'Data has been created successfully';
            }
            formSubmit($('#ids-office-form'), "{{ route('payment-methods.store') }}", table, e, message);
        });
        /* Office Store - End*/

        /* Office Edit - Start*/
        $("#office-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("payment-methods.single",":id") }}';
            var url = url.replace(':id', id);
            $('#ids-office-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#ids-office-form')[0].reset();

                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="full_name"]').val(data.full_name)
                        $('#myModal input[name="short_name"]').val(data.short_name)

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Payment Method: " + data.full_name)
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
            var base_url = "{{ route('payment-methods.destroy',':id') }}";
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
