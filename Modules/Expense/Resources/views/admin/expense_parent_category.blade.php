{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Expense Category')
@section('content_header')
<h1>Expense Parent Category</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="expense-category-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Category Name</th>
            <th>Short Name</th>
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
                <h4 class="modal-title" id="myModalLabel">Expense Parent Category</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'expense-category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="parent_category_name">
                    <label for="parent_category_name" class="col-sm-3 control-label">Category Name <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('parent_category_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control')) }}
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
        var table = $('#expense-category-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Expense_Parent_Category');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('expense-parent-category.list') }}",
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
                    data: 'parent_category_name',
                    name: 'parent_category_name'
                },
                {
                    data: 'short_name',
                    name: 'short_name'
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
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

        /* Posting data to ExpenseParentCategoryController - Start*/
        $('#expense-category-form').submit(function (e) {
            e.preventDefault();
            if($('#expense-category-form input[name="id"]').val()){
                var message = 'Expense parent category has been updated successfully';
            }else{
                var message = 'Expense parent category  has been created successfully';
            }     
            formSubmit($('#expense-category-form'), "{{ route('expense-parent-category.store') }}", table, e, message);
        });
        /* Posting data to ExpenseParentCategoryController - End*/


        $("#expense-category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("expense-parent-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#expense-category-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="parent_category_name"]').val(data.parent_category_name);
                        $('#myModal input[name="short_name"]').val(data.short_name);

                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Category Name: "+ data.parent_category_name)
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
        $('#expense-category-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('expense-parent-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Expense Parent Category  has been deleted successfully';
            deleteRecord(url, table, message);
        });
       
    });
</script>
@stop