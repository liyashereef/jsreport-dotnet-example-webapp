@extends('layouts.app')
@section('content')
 <div class="table_title">
        <h4>Test Settings</h4> <br>
    </div>
<div class="add-new" data-title="Add New Test Settings">Add
    <span class="add-new-label">New</span>
</div>
<input type="hidden" name="course_id" id="course" value={{$id}}>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Exam Name</th>
            <th>Number of Question Displayed</th>
            <th>Pass Percentage</th>
            <th>Status</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('training_course_id', $id) }}
            <div class="modal-body">
                   <!-- Active Toggle button - Start -->
                <div class="form-group col-sm-12" id="active">
                    <label class="switch" style="float:right;">
                        {{ Form::checkbox('active',1,null, array('class'=>'form-control')) }}
                      <span class="slider round"></span>
                    </label>
                    <label style="float:right;padding-right: 5px;">Active</label>
                </div>
                <!-- Active Toggle button - End -->
                <div class="form-group" id="exam_name">
                    <label for="exam_name" class="col-sm-5 control-label">Exam Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('exam_name',null,array('class' => 'form-control', 'Placeholder'=>'Exam Name', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group" id="number_of_question">
                    <label for="number_of_question" class="col-sm-7 control-label">Number Of Questions To Be Displayed</label>
                    <div class="col-sm-9">
                        {{ Form::number('number_of_question',null,array('class' => 'form-control','min'=>'1', 'Placeholder'=>'Skip this field if all questions is to be displayed')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="random_question">
                    <label for="random_question" class="col-sm-5 control-label">Random Question Display</label>
                    <div class="col-sm-9">
                        <label> <input type="radio" name="random_question"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                        <label> <input type="radio" name="random_question"  checked value="0" >&nbsp;No&nbsp;&nbsp;</label> 
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group" id="pass_percentage">
                    <label for="pass_percentage" class="col-sm-5 control-label">Pass Percentage</label>
                    <div class="col-sm-9">
                        {{ Form::text('pass_percentage',null,array('class' => 'form-control', 'Placeholder'=>'Pass Percentage')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop  @section('scripts')
<script>
    $(function () {
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var url = '{{ route("learningandtraining.settings-list",":id") }}';
        var url = url.replace(':id', $('#course').val());
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
                    data: 'exam_name',
                    name: 'exam_name'
                },
                 {
                    data: 'number_of_question',
                    name: 'number_of_question'
                },
                {
                    data: null,
                    name: 'pass_percentage',
                    render:function(o)
                    {
                        return o.pass_percentage + '%';
                    }
                },
                 {
                    data: 'active',
                    name:'active',
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
                        actions += '<a href="'+url+"/"+ o.id +'" class="fa fa-question-circle" title="Questions" data-id=' + o.id + '></a>'
                         actions += ' <a href="#" title="Edit" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
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
                var message = 'Exam settings has been updated successfully';
            }else{
                var message = 'Exam settings has been created successfully';
            }
            formSubmit($('#form'), "{{ route('learningandtraining.exam-questions-settings.store') }}", table, e, message);
        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("learningandtraining.exam-questions-settings.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="exam_name"]').val(data.exam_name)
                        if(data.number_of_question==0){
                           $('#myModal input[name="number_of_question"]').val(data.test_questions_count)  
                        }
                        else
                        {
                         $('#myModal input[name="number_of_question"]').val(data.number_of_question)
                         }
                         $('#myModal input[name="pass_percentage"]').val(Math.round(data.pass_percentage))
                         $('#myModal input:radio[name="random_question"][value=' + data.random_question + ']').prop('checked', true)
                         $('#myModal input:checkbox').prop('checked', data.active)     
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Test Settings: "+ data.exam_name)
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
        /* Course Category Edit - End */

        /* Course Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('learningandtraining.exam-questions-settings.destroy',':id') }}";
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
                                swal("Deleted", "Exam settings has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "This settings has one or more questions", "warning");
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
            $('#myModal .modal-title').text(title);
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
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        }
    });
</script>

@stop
