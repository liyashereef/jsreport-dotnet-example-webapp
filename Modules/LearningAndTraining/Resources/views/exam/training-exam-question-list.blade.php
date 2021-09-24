@extends('layouts.app')
@section('content')

 <div class="table_title">
        <h4>Test Questions</h4> <br>
    </div>
<div class="add-new" data-title="Add New Question">Add
    <span class="add-new-label">New</span>
</div>
<input type="hidden" name="test_course_master_id" id="test_course_master_id" value={{$id}}>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
             <th>Id</th>
            <th>#</th>
            <th>Question</th>
              <th>Mandatory Display</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            {{ Form::hidden('id', null) }}
             {{ Form::hidden('test_course_master_id', $id) }}
            
            <div class="modal-body">
                <div class="form-group row {{ $errors->has('test_question') ? 'has-error' : '' }}" id="test_question">
                    <label for="test_question" class="col-sm-12 control-label">Question (Select the checkbox to make display mandatory)</label>
                    <div class="col-sm-10">
                       <textarea name="test_question" class="form-control" placeholder="Question" required></textarea>
                        <small class="help-block"></small>
                    </div>
                    <div class="col-sm-2">
                        <input type="checkbox" name="is_mandatory_display" id="mandatory" value="1">
                    </div>
                </div>


            


                    <div  class="form-group row dynamic-option-fields" id="answer_option_0">
                         <input type="hidden" name="position[]" class="pos"  value="0">
                         <label for="feedback_id" class="col-sm-12 control-label">Answer (Select radio button for the right answer)</label>
<div class="col-sm-1 radio">
   <input type="radio" name="is_correct_answer" required value="0">
</div>
    <div class="col-sm-9">
         {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'required'=>TRUE,'id'=>'option_0')) }}
        <small class="help-block"></small>
    </div>
    

    </div>
       <div  class="form-group row dynamic-option-fields" id="answer_option_1">
         <input type="hidden" name="position[]" class="pos"  value="1">
                         
<div class="col-sm-1 radio">
   <input type="radio" name="is_correct_answer" value="1">
</div>
    <div class="col-sm-9">
         {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'required'=>TRUE,'id'=>'option_1')) }}
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
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
            </div>

@stop  @section('scripts')
<script>
    $(function () {
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var url = '{{ route("learningandtraining.questions-list",":id") }}';
        var url = url.replace(':id', $('#test_course_master_id').val());
        var table = $('#table').DataTable({
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
                        emailContent(table, 'Course Category');
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
                [0, "asc"]
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
                        var url = '{{ route("learningandtraining.exam-questions",'') }}';
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @can('lookup-remove-entries')
                        actions += '  <a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
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
            formSubmit($('#form'), "{{ route('learningandtraining.exam-questions.store') }}", table, e, message);
        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            $('.new-fields').remove();
            var id = $(this).data('id');
            var url = '{{ route("learningandtraining.exam-questions.single",":id") }}';
            var url = url.replace(':id', id);
               $('.dynamic-option-fields:last').find('.add_button').show();
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal textarea[name="test_question"]').val(data.test_question)
                        $('#myModal #mandatory').prop('checked',data.is_mandatory_display);
                         $.each( data.test_question_options, function( key, value ) {
                            if(key>=2)
                            {
                                addRow();
                            }
                            $('#myModal #option_'+key).val(value.answer_option) 
                             if(value.is_correct_answer==1){
 
                           $("input[name=is_correct_answer][value=" + key + "]").prop('checked', true);
                       }
                         });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Question ")
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
            var base_url = "{{ route('learningandtraining.exam-questions.destroy',':id') }}";
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
        $('.add-new').click(function(){
           $("#myModal").modal();
            var title = $(this).data('title');
            $("#myModal").modal();
            $('#myModal form').trigger('reset');
            $('#myModal').find('input[name=id]').val('');
             $('#myModal').find('textarea').val('');
            $('#myModal .modal-title').text(title);
            $('.new-fields').remove();
            $('.dynamic-option-fields:last').find('.add_button').show();
            $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        });  
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
                        $("#myModal").modal('hide');
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


         $("#myModal").on("click",".add_button",function(){ 
            addRow(); 
         });
           $("#myModal").on("click",".remove_button",function(){ 
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
            position_num= $('.pos:last').val()
            new_position_num=parseInt(position_num)+1;
            var html='<div  class="form-group row new-fields dynamic-option-fields" id="answer_option_'+new_position_num+'"> <input type="hidden" name="position[]" class="pos"  value="'+new_position_num+'"><div class="col-sm-1 radio"><input type="radio" name="is_correct_answer" value="'+new_position_num+'"></div><div class="col-sm-9"><input type="text" name="answer_option[]" class="form-control" Placeholder="Option" required id="option_'+new_position_num+'"><small class="help-block"></small></div><div class="col-sm-2"><a title="Add another experience" href="javascript:;" class="add_button"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0);" class="remove_button" title="Remove field"><i class="fa fa-minus" aria-hidden="true"></i></a></div><div class="form-control-feedback"></div></div>'; 
                 $("#answer_option_"+position_num).after(html);
                $('.dynamic-option-fields').find('.add_button').hide();
                if($('.dynamic-option-fields').length<=4){
                $('.dynamic-option-fields:last').find('.add_button').show();
                }
        } 
</script>
<style type="text/css">
    .radio{
        text-align: center;
    }
</style>
@stop
