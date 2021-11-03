@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Permission Mapping')
@section('content_header')

<h1>Permission Mapping</h1>
@stop
@section('content')
<div class="add-new" onclick="addnew()" data-title="Add New Permission Mapping">Add
    <span class="add-new-label">New</span>
</div>

<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Role</th>
            <th width="15%">Permission</th>
            <th width="5%">Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Permission Mapping</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'permission-mapping-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}

                <div class="modal-body">
                    <div class="form-group {{ $errors->has('role_id') ? 'has-error' : '' }}" id="role_id">
                        <label for="role_id" class="col-sm-3 control-label">Role</label>
                        <div class="col-sm-9">
                            {{ Form::select('role_id',[''=>'Please select']+$roles,null,array('class'=>'form-control','id'=>'roleid','required'=>'required')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="permission_id">
                        <label for="permission_id" class="col-sm-3 control-label">Permission</label>
                        <div class="col-sm-9">
                            {{ Form::select('permission_id[]',$permissions,null,array('class'=>'form-control','id'=>'permissions','multiple'=>'multiple','style'=>'width: 591px;')) }}
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
         $('#permissions').select2();//Added Select2 to office-ids listing
         $('select#roleid').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Choose the roles',
            width: '100%'
            });
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
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
                            emailContent(table, 'Policy');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('permission-mapping.list') }}",
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
                        sortable: false
                    },
                    {
                        data: 'role_name',
                        name: 'role_name',

                    },
                    {
                        data: 'permission_name',
                        name: 'permission_name',

                    },
                    {
                        data: null,
                        sortable: false,
                        render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.role_id + '></a>'
                        @endcan
                        return actions;
                    },
                }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

            $('#permission-mapping-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('permission-mapping.store') }}";
                var formData = new FormData($('#permission-mapping-form')[0]);
                $.ajax({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            swal("Saved", "Permission mappings been updated successfully", "success");
                             $("#myModal").modal('hide');
                            table.ajax.reload();
                        } else {
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                            console.log(data);
                        }
                    },
                    fail: function (response) {
                        console.log(response);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                    },
                    contentType: false,
                    processData: false,
                });
            });

            $("#table").on("click", ".edit", function (e) {
                $('#permissions').val('');
                var id = $(this).data('id');
                var url = '{{ route("permission-mapping.single",":id") }}';
                var url = url.replace(':id', id);
                $('#permission-mapping-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
                $.ajax({
                   url: url,
                   type: 'GET',
                   data: "id=" + id,
                   success: function (data) {
                       if (data) {
                        $.each(data, function( index, value ) {
                        $('#myModal select[name="role_id"]').val(value.role_id);
                        $('#myModal select[name="permission_id[]"] option[value="'+value.permission_id+'"]').prop('selected',true).change();
                        });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Mapping:")
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


            $('#permission-mapping-form').on('change', '#roleid', function() {
               $('#permissions').val('');
               $('#permissions').select2();
               var id = $(this).val();
               var url = '{{ route("permission-mapping.single",":id") }}';
               var url = url.replace(':id', id);
               $.ajax({
                    url: url,
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                    if (data) {
                      $.each(data, function( index, value ) {
                      $('#myModal select[name="permission_id[]"] option[value="'+value.permission_id+'"]').prop('selected',true).change();
                      });

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

    });

    function addnew()
    {
    $("#myModal").modal();
    $("#permissions").val('').trigger('change') ;
    }
</script>
@stop
