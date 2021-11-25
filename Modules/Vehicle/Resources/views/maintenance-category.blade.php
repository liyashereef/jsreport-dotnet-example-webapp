@extends('adminlte::page')
@section('title', 'Maintenance Category')
@section('content_header')
<h1>Maintenance Category</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="maintenance-category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Category Name</th>
             <th>Tax</th>
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
                <h4 class="modal-title" id="myModalLabel">Category Lists</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'maintenance-category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group row" id="category_name">
                    <label for="make" class="col-sm-3 control-label">Category Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('category_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                 <div class="form-group row" id="tax">
                    <label for="tax" class="col-sm-3 control-label">Tax Rate<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('tax',null,array('class'=>'form-control','placeholder'=>'Tax Percentage','min'=>'0','step'=>'0.01')) }}
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
        var table = $('#maintenance-category-table').DataTable({
    
                bprocessing: false,
                dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Users');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('vehicle-maintenance-category.list') }}",
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
                    sortable:false,
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                  {
                    data: 'tax',
                    name: 'tax',
                    defaultContent:'--',
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

        $('#maintenance-category-form').submit(function (e) {
            e.preventDefault();
            if($('#vmaintenance-category-form input[name="id"]').val()){
                var message = 'Vehicle maintenance category has been updated successfully';
            }else{
                var message = 'Vehicle maintenance category has been created successfully';
            }     
            formSubmit($('#maintenance-category-form'), "{{ route('vehicle-maintenance-category.store') }}", table, e, message);
        });
        


        $("#maintenance-category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("vehicle-maintenance-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#maintenance-category-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="category_name"]').val(data.category_name);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="tax"]').val(data.tax);
                        $("#myModal").modal();
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
        
              $('#maintenance-category-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('vehicle-maintenance-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
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
                                swal("Deleted", "Maintenance category  has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "This category has one or more type", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
                });

    }); 
</script>
@stop
