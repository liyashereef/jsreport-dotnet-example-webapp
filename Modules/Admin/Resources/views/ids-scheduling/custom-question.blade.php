@extends('adminlte::page')
@section('title', 'Custom Question')
@section('content_header')
<h1>Custom Question</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new"  data-title="Add New Question">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="service-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Question</th>
            <th>Options</th>
            <th>Created Date</th>
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
                <h4 class="modal-title" id="myModalLabel">Question</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'custom-question-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
              <!-- Active Toggle button - Start -->
              <div class="form-group col-sm-12" id="active" style="display:block;">
                <label class="switch" style="float:right;">
                  <input name="is_active" type="checkbox" value="1">
                  <span class="slider round"></span>
              </label>
              <label style="float:right;padding-right: 5px;">Active</label>
            </div>
          <!-- Active Toggle button - End -->
            <div class="form-group row" id="test_question">
              <label for="question" class="col-sm-12">Question</label>
               <div class="col-sm-10">
                 <textarea name="question" maxlength="300" rows="3" class="form-control" placeholder="Question" required></textarea>
                 <small class="help-block"></small>
               </div>
            </div>

             <div class="form-group row" id="display_order">
               <label for="display_order" class="col-sm-12">Display Order</label>
                 <div class="col-sm-10">
                  {{ Form::text('display_order',null,array('class' => 'form-control', 'Placeholder'=>'Order')) }}
                  <small class="help-block"></small>
            </div>
          </div>

        <div class="form-group" id="is_mandatory_display">
            <div>
                <label for="is_required" class="col-sm-3">Mandatory Question</label>
                <div class="col-sm-1" style="margin-left: -7%;">
                <input type="checkbox" name="is_required" id="mandatory" value="1">
                <small class="help-block"></small>
            </div>
            <label for="has_other" class="col-sm-3">Other Option Required</label>
            <div class="col-sm-1" style="margin-left: -6%;">
                <input type="checkbox" name="has_other" id="other" value="1">
                <small class="help-block"></small>
            </div>
        </div>
        </div>


        <div  class="form-group row dynamic-option-fields" id="answer_option_0">
            <input type="hidden" name="position[]" class="pos"  id="0">
            <input type="hidden" name="option_id[]" id="optionid_0">
            <label for="feedback_id" class="col-sm-12">Answer Options</label>
            <div class="col-sm-10">
                {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'required'=>TRUE,'id'=>'option_0')) }}
                <small class="help-block"></small>
            </div>
            <div class="col-sm-2">
                <a title="Add another experience" href="javascript:;" class="add_button">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </div>
            <div class="form-control-feedback"></div>
        </div>

        {{-- <div  class="form-group row dynamic-option-fields" id="answer_option_1"> --}}
            {{-- <input type="hidden" name="option_id[]" id="optionid_1">
            <input type="hidden" name="position[]"  class="pos"  id="1">
            <div class="col-sm-10">
                {{ Form::text('answer_option[]',null,array('class' => 'form-control', 'Placeholder'=>'Option', 'id'=>'option_1')) }}
                <small class="help-block"></small>
            </div> --}}

            {{-- <div class="col-sm-2">
                <a title="Add another experience" href="javascript:;" class="add_button">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </div> --}}
            {{-- <div class="form-control-feedback"></div> --}}
        {{-- </div> --}}

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
        try {
            var table = $('#service-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Services');
                    }
                }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('ids-custom-question-option-list.list') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 1, "desc" ]],
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
                    data: 'question',
                    name: 'question'
                },
                {
                data: 'ids_custom_question_allocation.[, ].ids_custom_option.custom_question_option',
                name: 'ids_custom_question_allocation.[0].ids_custom_option.custom_question_option',
                sortable: false

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
                @can('edit_masters')
                actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' +o.id + '></a>';
                @endcan
                 @can('lookup-remove-entries')
                 actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' +o.id + '></a>';
                 @endcan
                 return actions;
             },
         }
         ]
     });
        } catch (e) {
            console.log(e.stack);
        }


        /* Service Store - Start*/


        $('#custom-question-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('ids-custom-question.store') }}";
            var formData = new FormData($('#custom-question-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                         swal("Saved", "Question has been created successfully", "success");
                          $("#myModal").modal('hide');
                         table.ajax.reload();
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form,true);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Store - End*/

        /* Service Edit - Start*/
        $("#service-table").on("click", ".edit", function (e) {
              $('.new-fields').remove();
            id = $(this).data('id');
            var url = '{{ route("ids-custom-question.edit",":id") }}';
            var url = url.replace(':id', id);
             $('.dynamic-option-fields:last').find('.add_button').show();
            $('#custom-question-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="display_order"]').val(data.display_order)
                        $('#myModal textarea[name="question"]').text(data.question);
                         $('#myModal input[name="is_active"]').prop('checked', data.is_active);
                        $('#myModal input[name="is_required"]').prop( "checked", data.is_required );
                           $('#myModal input[name="has_other"]').prop( "checked", data.has_other );
                          $.each(data.ids_custom_question_allocation, function( key, value ) {
                            if(key>=1)
                            {
                                addRow();
                            }
                            $('#myModal #option_'+key).val(value.ids_custom_option.custom_question_option)
                             $('#myModal #optionid_'+key).val(value.ids_custom_option.id)

                         });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Question")
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Edit - End*/

        /* Service Delete  - Start */
        $('#service-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('ids-custom-question.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Question has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Service Delete  - End */
        $("#myModal").on("click",".add_button",function(){
            addRow();
        });
        $("#myModal").on("click",".remove_button",function(){
           var removedPos = $(this).parents().closest('.dynamic-option-fields').find('input[name="position[]"]').attr('id');
           position_num=removedPos;
           $(this).parents().nextAll('.dynamic-option-fields').each(function( index,value ) {
             $(value).find('.pos').attr('id',position_num);
             $(value).attr('id', 'answer_option_'+position_num);
             $(value).find("input[name='answer_option[]']").attr('id', 'option_'+position_num);
             position_num++;
         });
           $(this).parents().closest('.dynamic-option-fields').remove();
           $('.dynamic-option-fields:last').find('.add_button').show();


       });
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
            $('#myModal input[name="is_active"]').prop('checked', true);
            $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        });

    });
function addRow()
{
    position_num= $('.pos:last').attr('id');
    new_position_num=parseInt(position_num)+1;
    var html='<div  class="form-group row new-fields dynamic-option-fields" id="answer_option_'+new_position_num+'"> <input type="hidden" name="option_id[]" id="optionid_'+new_position_num+'"> <input type="hidden" name="position[]" class="pos"  id="'+new_position_num+'"><div class="col-sm-10"><input type="text" name="answer_option[]" class="form-control" Placeholder="Option" required id="option_'+new_position_num+'"><small class="help-block"></small></div><div class="col-sm-2"><a title="Add another experience" href="javascript:;" class="add_button"><i class="fa fa-plus" aria-hidden="true"></i></a>&nbsp;&nbsp;<a href="javascript:void(0);" class="remove_button" title="Remove field"><i class="fa fa-minus" aria-hidden="true"></i></a></div><div class="form-control-feedback"></div></div>';
    $("#answer_option_"+position_num).after(html);
    $('.dynamic-option-fields').find('.add_button').hide();
    $('.dynamic-option-fields:last').find('.add_button').show();

}

</script>
@stop
