{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Expense Category')
@section('content_header')
<h1>Expense Category</h1>
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
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Expense Category</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'expense-category-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-3 control-label">Category Name <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="is_taxable">
                    <label for="is_category_taxable " class="col-sm-3 control-label">Does this Category have Tax ?</label>
                    <div class="col-sm-9 radiobuttons">
                        <label><input type="radio" id="cat1" class="taxable_cls"  name="is_category_taxable" value="1"> Yes</label>&nbsp;&nbsp;
                        <label><input type="radio" id="cat0" class="taxable_cls" name="is_category_taxable" value="0" checked="checked"> No</label>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group  row taxable hide-this-block">
                    <label for="tax_id" class="col-sm-3 control-label">Tax</label>
                    <div class="col-sm-9">
                        {{Form::select('tax_id',$taxes,null,['class' => 'form-control select2 tax','placeholder' => 'Choose Tax', 'style'=>"width: 100%;"])}}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class' => 'form-control', 'Placeholder'=>'Description','rows' => 3, 'cols' => 40)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="is_tip_enabled">
                    <label for="description" class="col-sm-3 control-label">Tip Enabled</label>
                    <div class="col-sm-9 radiobuttons">
                       <label><input type="radio" id="tipenabled" name="is_tip_enabled" value="1"> Yes</label>&nbsp;&nbsp;
                       <label><input type="radio" id="tipdisabled" name="is_tip_enabled" value="0" checked="checked"> No</label>
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
        $('.select2').select2();
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
                        emailContent(table, 'Expense_Category_Lookup');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('expense-category.list') }}",
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
                    data: 'name',
                    name: 'name'
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
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
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

         /*Show and Hide dependent block of tax on choosing yes or no - Start*/
          $('.taxable_cls').on('change', function () {
            if (this.value == '1')
            {
                $(".taxable").removeClass('hide-this-block');

            } else
            {
                $(".taxable").addClass('hide-this-block');
                $(".taxable").find('input').val('');
            }
        });
            /*Show and Hide dependent block of tax on choosing yes or no - End*/


        /* Posting data to PositionLookupController - Start*/
        $('#expense-category-form').submit(function (e) {
            e.preventDefault();
            if($('#expense-category-form input[name="id"]').val()){
                var message = 'Category has been updated successfully';
            }else{
                var message = 'Category has been created successfully';
            }
            var checked_value = $("input[name='is_category_taxable']:checked").val();
            if(checked_value == 1){
                var t=$('select[name="tax_id"]').val()
                if(t == "")
                {
                    $('.taxable').addClass('has-error').find('.help-block').text('Tax Field is Required');
                    return false;
                }   
            }
             
              
            formSubmit($('#expense-category-form'), "{{ route('expense-category.store') }}", table, e, message);
        });


        $("#expense-category-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("expense-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#expense-category-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    $("#expense-category-form").trigger('reset');
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input[name="short_name"]').val(data.short_name)
                        $('#myModal textarea[name="description"]').val(data.description)
                        if(data.is_tip_enabled==1)
                        {
                         $('#myModal input[id="tipenabled"]').prop('checked', true);   
                        } 
                        else
                        {
                        $('#myModal input[id="tipdisabled"]').prop('checked', true);  
                        } 
                        
                        if(data.is_category_taxable ==1)
                        {  
                           $('#myModal input[id="cat1"]').prop('checked', true);
                           $(".taxable").removeClass('hide-this-block');
                           $('#myModal select[name="tax_id"] option[value="'+data.tax_id+'"]').prop('selected',true) 
                           
                        }else if(data.is_category_taxable ==0)
                        {
                            $('#myModal input[id="cat0"]').prop('checked', true);
                            $(".taxable").addClass('hide-this-block');
                        }else{
                            $('#myModal input[class="taxable_cls"]').prop('checked', false);
                            $(".taxable").addClass('hide-this-block');
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Category: "+ data.name);
                        $('.select2').select2();
                    } else {
                        //alert(data);
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
            var base_url = "{{ route('expense-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Category has been deleted successfully';
            deleteRecord(url, table, message);
        });
        $('.add-new').click(function(){
       
                $(".taxable").addClass('hide-this-block');
        
        });
    });
</script>
<style type="text/css">
    .radiobuttons
    {
        margin-top: 6px;
    }
</style>
@stop
