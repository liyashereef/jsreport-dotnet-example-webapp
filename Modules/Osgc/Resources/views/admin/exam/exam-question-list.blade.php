@extends('adminlte::page')
@section('title', 'OSGC Course Exam Settings')
@section('content_header')
<h1>OSGC Courses Questions</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Question">Add
    <span class="add-new-label">New</span>
</div>
<input type="hidden" name="question_master_id" id="question_master_id" value={{$id}}>
<table class="table table-bordered" id="table">
    <thead>
    <th>Id</th>
            <th>#</th>
            <th>Question</th>
            <th>Mandatory Display</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
    </thead>
</table>
<input type="hidden" name="question_master_id" id="question_master_id" value={{$id}}>
<div class="modal fade" id="myModal1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">OSGC Course Exam Settings</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
             {{ Form::hidden('question_master_id', $id) }}
            
            <div class="modal-body">
                <div class="form-group row {{ $errors->has('test_question') ? 'has-error' : '' }}" id="test_question">
                    <label for="test_question" class="col-sm-12 control-label left-align">Question (Select the checkbox to make display mandatory)</label>
                    
                    <div class="col-sm-10"><br>
                       <textarea  name="test_question" class="form-control" placeholder="Question" required></textarea>
                        <small class="help-block"></small>
                    </div>
                    <div class="col-sm-2">
                        <input type="checkbox" name="is_mandatory_display" id="mandatory" value="1">
                    </div>
                </div>


                <div  class="form-group row">         

                <label for="feedback_id" class="col-sm-12 control-label left-align">Answer (Select radio button for the right answer)</label>
</div>
                    <div  class="form-group row dynamic-option-fields" id="answer_option_0">
                         <input type="hidden" name="position[]"      class="pos"  value="0">
                         
                         <div class="col-sm-1 radio">

   <input type="radio" name="is_correct_answer" required value="0">
</div>
    <div class="col-sm-9">
         {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'required'=>TRUE,'id'=>'option_0', "maxlength"=>"200")) }}
        <small class="help-block"></small>
    </div>
    

    </div>
       <div  class="form-group row dynamic-option-fields" id="answer_option_1">
         <input type="hidden" name="position[]" class="pos"  value="1">
                         
<div class="col-sm-1 radio">
   <input type="radio" name="is_correct_answer" value="1">
</div>
    <div class="col-sm-9">
         {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'required'=>TRUE,'id'=>'option_1',"maxlength"=>"200")) }}
        <small class="help-block"></small>
    </div>
   
    <div class="col-sm-2">

        <a title="Add another experience" href="javascript:;" class="add_button">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </a>

        </div>
        <div class="form-control-feedback"></div>
    </div>
            
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel" aria-hidden="true">
<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Warning</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete your account? This action cannot be undone and you will be unable to recover any data.</p>
			</div>
			<div class="modal-footer">
                <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>

			</div>
		</div>
	</div>
</div>
@stop @section('js')
<style>
.left-align
    {
        text-align: left !important;
    }
    .radio{
        text-align: center;
    }
    .fa-plus
    {
        padding-right: 15px;
    }
</style>
<script>
    $(function () {

            $.fn.dataTable.ext.errMode = 'throw';
            try{
        var url = '{{ route("osgc.questions-list",":id") }}';
        var url = url.replace(':id', $('#question_master_id').val());
        var table = $('#table').DataTable({
               dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5]
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax:url,
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
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    visible:false
                },{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {
                    data: 'test_question',
                    name: 'test_question'
                },
                {
                    data: 'is_mandatory_display',
                    name: 'is_mandatory_display',
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                       var actions = '';
                        actions += ' <a href="#" title="Edit" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += ' <a href="#" title="Delete" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
        /* Course Category Save - Start */
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'Question has been updated successfully';
            }else{
                var message = 'Question has been created successfully';
            }
            formSubmit($('#form'), "{{ route('osgc.exam-questions.store') }}", table, e, message);
        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            $('.new-fields').remove();
            var id = $(this).data('id');
            var url = '{{ route("osgc.exam-questions.single",":id") }}';
            var url = url.replace(':id', id);
               $('.dynamic-option-fields:last').find('.add_button').show();
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal1 input[name="id"]').val(data.id)
                        $('#myModal1 textarea[name="test_question"]').val(data.test_question)
                        $('#myModal1 #mandatory').prop('checked',data.is_mandatory_display);
                         $.each( data.test_question_options, function( key, value ) {
                            if(key>=2)
                            {
                                addRow();
                            }
                            $('#myModal1 #option_'+key).val(value.answer_option) 
                             if(value.is_correct_answer==1){
 
                           $("input[name=is_correct_answer][value=" + key + "]").prop('checked', true);
                       }
                         });
                        $("#myModal1").modal();
                        $('#myModal1 .modal-title').text("Edit Question ")
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                   associate_errors(xhr.responseJSON.errors, $form, true);
                },

                contentType: false,
                processData: false,
            });
        });
        /* Course Category Edit - End */

        /* Course Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('osgc.exam-questions.destroy',':id') }}";
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
                                swal("Deleted", "Question has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "This Question has one or more dependencies", "warning");
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

        /* Course Category Delete- End */
         
        function formSubmit($form, url, table, e, message) {
            var $form = $form;
            var url = url;
            var e = e;
            var table = table;
            var formData = new FormData($form[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", message, "success");
                        $("#myModal1").modal('hide');
                        if (table != null) {
                            table.ajax.reload();
                        }
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log('Unknown error');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form,true);
                },
                contentType: false,
                processData: false,
            });
        }


        $('.add-new').click(function(){
           $("#myModal1").modal();
            var title = $(this).data('title');
            $("#myModal1").modal();
            $('#myModal1 form').trigger('reset');
            $('#myModal1').find('input[name=id]').val('');
            var masterId=$('#question_master_id').val();
            $('#myModal1').find('input[name=question_master_id]').val(masterId);
           
            $('#myModal1 .modal-title').text(title);
            $('#myModal1 form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        }); 
        $("#myModal1").on("click",".add_button",function(){ 
            addRow(); 
         });
         $("#myModal1").on("click",".remove_button",function(){ 
             var removedPos = $(this).parents().closest('.dynamic-option-fields').find('input[name="position[]"]').val();
             position_num=removedPos; 
            $(this).parents().nextAll('.dynamic-option-fields').each(function( index,value ) {     
               $(value).find('.pos').val(position_num);
               $(value).attr('id', 'answer_option_'+position_num);
               $(value).find("input[name='is_correct_answer']").val(position_num);
               $(value).find("input[name='answer_option[]']").attr('id', 'option_'+position_num);
               position_num++;
           });
             $(this).parents().closest('.dynamic-option-fields').remove();            
            $('.dynamic-option-fields:last').find('.add_button').show();
           

        });
           
    });
    function addRow()
        {
            position_num= $('.pos:last').val();
            new_position_num=parseInt(position_num)+1;//alert(new_position_num);
            var html='<div  class="form-group row new-fields dynamic-option-fields" id="answer_option_'+new_position_num+'"> <input type="hidden" name="position[]" class="pos"  value="'+new_position_num+'"><div class="col-sm-1 radio"><input type="radio" name="is_correct_answer" value="'+new_position_num+'"></div><div class="col-sm-9"><input type="text" name="answer_option[]" class="form-control" Placeholder="Option" required id="option_'+new_position_num+'" maxlength="200"><small class="help-block"></small></div><div class="col-sm-2"><a title="Add another experience" href="javascript:;" class="add_button"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0);" class="remove_button" title="Remove field"><i class="fa fa-minus" aria-hidden="true"></i></a></div><div class="form-control-feedback"></div></div>'; 
                 $("#answer_option_"+position_num).after(html);
                $('.dynamic-option-fields').find('.add_button').hide();
                if($('.dynamic-option-fields').length<=4){
                $('.dynamic-option-fields:last').find('.add_button').show();
                }
        } 
</script>
@stop
