



@extends('adminlte::page')

@section('title', 'Mileage Reimbursement Rate')

@section('content_header')
<h1>Mileage Reimbursement Rate</h1><br>
@stop




@section('content')
{{ Form::open(array('url'=>'#','id'=>'mileage-reimbursement-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

<fieldset>
    <div id="filter">
        <label><input type="radio" id="flat" name="mileage-reimbursement-type" value="0" >&nbsp;Flat Rate</label>
        <label><input type="radio" id ="slab" name="mileage-reimbursement-type" value="1">&nbsp;Slab Rate</label>
    </div>
</fieldset>
<br>
<div id ="flat-new">
    <div id="flat-add">
<div class="add-new flat" data-title="Add New Flat Rate">Add
    <span class="add-new-label">New</span>
</div></div>
<table class="table table-bordered" id="mileage-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Flat Rate</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Status</th>
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
                <h4 class="modal-title" id="myModalLabel">Mileage Reimbursement Rate</h4>
            </div>
         
            <div class="modal-body">               
                  <div class="form-group" id="flat_rate">
                    {{ Form::hidden('id', null) }}    
                    <label for="name" class="col-sm-3 control-label">Enter Flat Rate  <span class="mandatory">*</span></label>   
                    <div class="col-sm-7">
                        <div  style="display:flex; align-items: center;">
                        <span style="margin-right: .5rem;">$</span>
                        {{ Form::text('flat_rate',null,array('class'=>'form-control','id'=>'flat_id','style'=>'width:130px' )) }}
                       
                        
                        <label for="name" class="control-label" style="margin-left: .5rem; padding-top:0;">/km </label>
                    </div>
                    <small class="help-block" style="margin-left: 2rem; margin-top:1rem;"></small>
                </div>
                   
                </div>
                <div class="form-group" id="is_active">
                    <label for="active" class="col-sm-3 control-label">Activate Now</label>
                    <div class="col-sm-9">
                        <input type="checkbox" id="is_active" class="chk" name="is_active" value="1"  checked="checked">
                     <small class="help-block"></small>
                 </div>
                </div>
               
            </div>

            
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
        </div>
    </div>
</div>
</div>
<div class="table-responsive slab_class " id="slab_rate">
        <table id="mileage_table" class="table table-bordered  w-100 mileage_table_cls" role="grid" aria-describedby="position-table_info">
                

            <thead>
                <tr>
                   
                    <th class="sorting_disabled" style="white-space: nowrap">Starting Kilometer</th>
                    <th class="sorting_disabled" style="white-space: nowrap">Ending Kilometer</th>
                    <th class="sorting_disabled" style="white-space: nowrap">Cost</th>
                    <th class="sorting_disabled">Action</th>
                </tr>
            </thead>
            <tbody >
                    @if(isset($option_list))
                    @forelse($option_list as $key=>$option)
                    <tr role="row" class="template-row">
                        <input   type="hidden" name="option_id" value="{{$option->id}}"/>
                        <td aria-controls="position-table" id="start_name_{{isset($key)?($key):"0"}}" class="cls-slno">
                        <input type="text" name="row-no[]" class="row-no" value="{{$key}}">
                        <input type="text" class="form-control" placeholder="Starting Kilometer" name="starting_kilometer[]" value="{{$option->starting_kilometer}}">
                        <span id="starting_kilometer_{{$key}}" class="starting_kilometer_{{$key}}_err help-block clearclass add-block text-danger align-middle font-12 "></span></td>
                        <td aria-controls="position-table" class="cls-slno" id="end_name_{{isset($key)?($key):"0"}}">
                        <input type="text" id="end_id" class="form-control" placeholder="Ending Kilometer" name="ending_kilometer[]" value="{{$option->ending_kilometer}}">
                        <span id="ending_kilometer_0" class="ending_kilometer_{{$key}}_err help-block clearclass add-block  text-danger align-middle font-12 "></span></td>
                        <td aria-controls="position-table" class="cls-slno" id="cost_name_{{isset($key)?($key):"0"}}">
                        <input type="text" id='cost_id' class="form-control" placeholder="Cost" name="cost[]" value="{{$option->cost}}">
                        <span id="cost_0" class="cost_{{$key}}_err help-block clearclass add-block  text-danger align-middle font-12 "></span></td>
                        <td class="data-list-disc attachment-button">
                            <a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i></a>
                       </td> 
                    </tr>
                    @empty
                    <tr role="row" class="template-row"><td colspan="4" style="text-align: center;"> No records found </td></tr>
                    @endforelse
                    {{-- <input type="button" class="add-attachment" value="Add New"> --}}
                 
                    <div class="add-attachment" onclick="validate()">Add New</div>
   

        @else
            <tr role="row" class="template-row template1">

<td aria-controls="position-table" class="cls-slno">
<input type="text" name="row-no[]" class="row-no" value="0">
<input type="text" class="form-control" placeholder="Starting Kilometer" name="starting_kilometer[]">
<span class="starting_kilometer_0_err help-block clearclass add-block  text-danger align-middle font-12 "></span></td>
<td aria-controls="position-table" class="cls-slno">
<input type="text" id="end_id" class="form-control" placeholder="Ending Kilometer" name="ending_kilometer[]">
<span class="ending_kilometer_0_err help-block clearclass add-block  text-danger align-middle font-12 "></span></td>
<td aria-controls="position-table" class="cls-slno">
<input type="text" id='cost_id' class="form-control" placeholder="Cost" name="cost[]">
<span class="cost_0_err help-block clearclass add-block  text-danger align-middle font-12 "></span></td>
<td class="data-list-disc attachment-button">
    <a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i></a>
</td>
<div class="add-attachment" onclick="validate()">Add New</div>
   
</tr>
@endif
</tbody>
            </table>
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            <a class="btn btn-primary blue" onclick="cancel_mileage()">Cancel</a>
    </div>      
    
    {{ Form::close() }}
@stop
@section('js')
<script>
    function cancel_mileage()
    {   
        $('#mileage-reimbursement-form').get(0).reset();
        $("#slab").prop("checked", true);

    }
    $('#flat-new').show();
    $('#slab_rate').hide();
    $("#flat").prop("checked", true);
    $('#filter').on('change', 'input[name=mileage-reimbursement-type]', function () {
    if($(this).val()==0) 
        { 
         $('#flat-new').show();
         $('#slab_rate').hide();
         }
    else if($(this).val()==1)
       {
        
       $('#slab_rate').show();
       $('#flat-new').hide();

         }
    else
       {

       $('#flat-new').show();
      $('#slab_rate').hide();

}
    
    });
    $('#mileage_table').on('click', '.remove_attachment' ,function() {
      
      $('#mileage_table tr:last').remove();
  });

    $('#flat-add').click(function(){
        $('#flat_id').val('');
            });
   function validate(){

    var last_row_no = $(".mileage_table_cls").find('tr:last .row-no').val();
            if(last_row_no != undefined){
                next_row_no = (last_row_no*1)+1;
            }else{
                next_row_no = 0;
            }


$('#mileage_table tbody').append('<tr role="row" class="template-row template1">'+
    '<td aria-controls="position-table" class="cls-slno">'+
    '<input type="text" name="row-no[]" class="row-no" value='+next_row_no+'>'+

'<input type="text" class="form-control" placeholder="Starting Kilometer" name="starting_kilometer[]">'+
'<span id="starting_kilometer_'+next_row_no+'_err" class="starting_kilometer_'+next_row_no+'_err clearclass help-block add-block  text-danger align-middle font-12 "></span></td>'+
'<td aria-controls="position-table" class="cls-slno">'+
'<input type="text" id="end_id" class="form-control" placeholder="Ending Kilometer" name="ending_kilometer[]">'+
'<span class="ending_kilometer_'+next_row_no+'_err help-block add-block  clearclass text-danger align-middle font-12 "></span></td>'+
'<td aria-controls="position-table" class="cls-slno">'+
'<input type="text" id="cost_id" class="form-control" placeholder="Cost" name="cost[]">'+
'<span class="cost_'+next_row_no+'_err clearclass help-block add-block  text-danger align-middle font-12 "></span></td>'+

  '<td class="data-list-disc attachment-button">'+
   ' <a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i></a>'+
   
'</tr>');
      
}
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#mileage-table').DataTable({
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
            ajax: "{{ route('expense-mileage-type.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [4, "asc"]
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
                    data: 'flat_rate',
                    name: 'flat_rate'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {data: 'created_by', name: 'created_by'},
                {data: 'is_active', name: 'is_active'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                       
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }      
     
         $('#mileage-reimbursement-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('mileage-reimbursement.add') }}";
            var formData = new FormData($('#mileage-reimbursement-form')[0]);
             $('#mileage_table tbody .clearclass').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                       var type= $('input[name=mileage-reimbursement-type]:checked').val();
                       if(type == 1)
                       {
                        swal("Saved", "Slab rate has been saved successfully", "success");
                       }else{
                        swal("Saved", "Flat rate has been saved successfully", "success");
                       }
                        
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        $('.form-control').find('.help-block').text('');
                        $("#myModal").modal('hide');
                            table.ajax.reload();                 
                    } 
                    
                    else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
           
                     $.each(xhr.responseJSON.errors, function(key, value) {     
                       
                        result = key.replace(/\./g,'_'); 
                        $('.'+result+'_err').text(value);
    });
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        
        $("#mileage-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("expense-mileage-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#mileage-reimbursement-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');

            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        var is_active = data.is_active;
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="flat_rate"]').val(data.flat_rate);
                        if(is_active == 0){
                            $("#myModal input[type='checkbox']").prop('checked', false);
                        }else{
                            $("#myModal input[type='checkbox']").prop('checked', true);
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Flat Rate: "+ data.flat_rate)    

                    }
                     else {
                        alert(data);
                    }
                  

                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                    $.each(xhr.responseJSON.errors, function(key, value) {  
                        result = key.replace(/\./g,'_');
                      
    
            });
            associate_errors(xhr.responseJSON.errors, $form);
                    console.log(xhr.responseJSON.errors.starting_kilometer);
          
         
                },
                contentType: false,
                processData: false,
            });
        });
   

       

    });
</script>
<style>

    .add-attachment
    {
     float: right;
     width: 175px;
     background-color: #f26222;
     color: #ffffff;
     font-size: 14px;
     font-weight: 700;
     margin-bottom: 10px;
     text-align: center;
     border-radius: 5px;
     padding: 5px 0px;
     margin-left: 5px;
     cursor: pointer;
    }
    .add-block 
    {
    display: block;
    margin-top: 5px;
    margin-bottom: 10px;
    color: #f20a0a;
}
#mileage-table{
    width: 100% !important;
}
    </style>
@stop