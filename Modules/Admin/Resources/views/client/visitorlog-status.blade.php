@extends('adminlte::page')
@section('title', 'Visitor Status')
@section('content_header')
<h1>Visitor Status</h1>
@stop @section('content')
<style>
    .view{
        padding-right: 8%;
    }
</style>

<button class="add-new" data-title="Add New Visitor Status" >
    Add<span class="add-new-label">New</span>
</button>

<table class="table table-bordered" id="feedback-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Authorised</th>
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
            
            {{ Form::open(array('url'=>'#','id'=>'feedback-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
            <div class="modal-body">
              
                <div class="form-group row" id="feedback">
                    <label for="feedback" class="col-sm-3 control-label">Status Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                
                <div class="form-group row" id="active">
                    <label for="active" class="col-sm-3 control-label">Authorised</label>
                        <label class="switch" style="">
                            {{ Form::checkbox('is_authorised',1,null, array('class'=>'form-control')) }}
                        <span class="slider round"></span>
                    </label>
                </div>
                
            </div>

            <div class="modal-footer" style="text-align: right !important;">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
          
        </div>
    </div>
</div>

@stop @section('js')
<script>
     $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#feedback-table').DataTable({
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
                        emailContent(table, 'Experiences Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('visitor-log-status.list') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var authorised = '';
                         if(o.is_authorised == 0){
                            authorised = 'No';
                         }else{
                            authorised = 'Yes';  
                         }

                     return authorised;
                    },
                    name: 'authorised'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var actions = '';
                    
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';

                     return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Experience Save - Start*/
        $('#feedback-form').submit(function (e) {
            e.preventDefault();
            if($('#feedback-form input[name="id"]').val()){
                var message = 'Visitor status has been updated successfully';
            }else{
                var message = 'Visitor status has been created successfully';
            }
            formSubmit($('#feedback-form'), "{{ route('visitor-log-status-list.store') }}", table, e, message);
        });
        /* Experience Save - End*/


        /* Experience Edit - Start*/
        $("#feedback-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("visitor-log-status-list.single",":id") }}';
            var url = url.replace(':id', id);
            $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) { 
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input:checkbox').prop('checked', data.is_authorised)  
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Visitor Status : " + data.name)
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
        /* Experience Edit - End*/



         $('#feedback-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
             var base_url = "{{ route('visitor-log-status-list.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "Visitor status has been deleted successfully", "success");
                            table.ajax.reload();
                        }
                     else {
                              swal("Warning", "Delete failed. Try again", "warning");
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


    });
</script>
@stop
