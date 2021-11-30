@extends('adminlte::page')
@section('title', 'Maintenance Type')
@section('content_header')
<h1>Maintenance Type</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Type">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="maintenance-type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Critical after km</th>
            <th>Critical after days</th>
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
                <h4 class="modal-title" id="myModalLabel">Type Lists</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'maintenance-type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group row" id="category_id">
                    <label for="make" class="col-sm-3 control-label">Category<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('category_id',[null=>'Please Select']+$category_list, old('category_id'),array('class' => 'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                <div class="form-group row" id="name">
                    <label for="make" class="col-sm-3 control-label">Type Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                 <div class="form-group row" id="type">
                    <label for="type" class="col-sm-3 control-label">Value Type<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('type',[null=>'Please Select']+$datavalues, old('type'),array('class' => 'form-control','id'=>'choose_type')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                <div class="form-group row hide-this-block" id="critical_after_km">
                    <label for="critical_after_km" class="col-sm-3 control-label">Critical after km<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('critical_after_km',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> 
                <div class="form-group row hide-this-block" id="critical_after_days">
                    <label for="critical_after_days" class="col-sm-3 control-label">Critical after days<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('critical_after_days',null,array('class'=>'form-control')) }}
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
        var table = $('#maintenance-type-table').DataTable({
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
            ajax: "{{ route('vehicle-maintenance-type.list') }}",
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
                    data: 'category.category_name',
                    name: 'category.category_name'
                },

                {
                    data: 'critical_after_km',
                    name: 'critical_after_km',
                    defaultContent:'--'
                },
                {
                    data: 'critical_after_days',
                    name: 'critical_after_days',
                      defaultContent:'--'
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

        $('#maintenance-type-form').submit(function (e) {
            e.preventDefault();
            if($('#vmaintenance-type-form input[name="id"]').val()){
                var message = 'Vehicle Maintenance Type has been updated successfully';
            }else{
                var message = 'Vehicle Maintenance Type has been created successfully';
            }     
            formSubmit($('#maintenance-type-form'), "{{ route('vehicle-maintenance-type.store') }}", table, e, message);
        });
        


        $("#maintenance-type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("vehicle-maintenance-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#maintenance-type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal select[name="category_id"]').val(data.category_id);
                        $('#myModal select[name="type"]').val(data.type);
                        $('#myModal input[name="id"]').val(data.id); 
                        $('#myModal input[name="name"]').val(data.name);
                        hideOrShowBlock(data.type);
                        $('#myModal input[name="critical_after_days"]').val(data.critical_after_days);
                        $('#myModal input[name="critical_after_km"]').val(data.critical_after_km);     
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

        $('#maintenance-type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('vehicle-maintenance-type.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Maintenance Type  has been deleted successfully';
            deleteRecord(url, table, message);
        });

        $('#choose_type').on('change',function()
        {
       
          hideOrShowBlock($(this).val());
       })

        $('.add-new').click(function(){
        $('#critical_after_days').addClass('hide-this-block');
        $('#critical_after_km').addClass('hide-this-block');
    });

    }); 
function hideOrShowBlock(isKilometer)
{
    //Check whether kilometer field(1) or date field(2)
    if(isKilometer==1)
          {
            $('#critical_after_km').removeClass('hide-this-block');
            $('#critical_after_days').addClass('hide-this-block');
          }
          else
          { 
            $('#critical_after_days').removeClass('hide-this-block');
          $('#critical_after_km').addClass('hide-this-block');
            

          }
}
</script>
@stop
