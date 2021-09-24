@extends('adminlte::page')
@section('title', 'Visitor Screening Templates Questions')
@section('content_header')
<h1>{{$template->name}} : Visitor Screening Template Questions</h1>
@stop @section('content')
<style>
    .view{
        padding-right: 8%;
    }
    .addNewBtn{
        float: right;
        /* width: 200px; */
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
    #answerError{
        color: red;
    }
</style>

<button class="addNewBtn"  onclick="addnew()" data-title="" >
Add <span class="add-new-label">New</span>
</button>

<table class="table table-bordered" id="feedback-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Question</th>
            <th>Answer</th>
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
                <h4 class="modal-title" id="myModalLabel">Add New Visitor Screening Templates Questions</h4>
            </div>

            {{ Form::open(array('url'=>'#','id'=>'feedback-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
             {{ Form::hidden('visitor_log_screening_template_id',$id) }}
            <div class="modal-body">

                <div class="form-group" id="question">
                    <label for="question" class="col-sm-3 control-label">Question</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('question',null,array('class'=>'form-control','rows'=>"3",'placeholder'=>"Question",'required'=>'required')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="answer">
                    <label for="answer" class="col-sm-3 control-label"> Expected answer</label>
                    <div class="col-sm-9">
                        <input type="radio" name="answer" value="1" id="1" style="margin-right: 7px;"> Yes
                        <input type="radio" name="answer" value="0" id="0" style="margin: 0px 7px 0px 25px;"> No
                        <small class="help-block" id="answerError"></small>
                    </div>
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
            var url = '{{ route("visitor-log-screening-templates.questions-list",":id") }}';
            var url = url.replace(':id', {{$id}});
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
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                // [0, "asc"]
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
                    data: 'question',
                    name: 'question'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var answer = '';
                         if(o.answer == 0){
                            answer = 'No';
                         }else{
                            answer = 'Yes';
                         }

                     return answer;
                    },
                    name: 'answer'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
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
            if($("input[name='answer']:checked").val() == undefined){
                $("#answerError").text('Expected answer is required');
            }else{
                if($('#feedback-form input[name="id"]').val()){
                    var message = 'Question has been updated successfully';
                }else{
                    var message = 'Question has been created successfully';
                }
                formSubmit($('#feedback-form'), "{{ route('visitor-log-screening-templates.questions.store') }}", table, e, message);
            }
        });
        /* Experience Save - End*/


        /* Experience Edit - Start*/
        $("#feedback-table").on("click", ".edit", function (e) {
            $("#myModal #customerIds").val('').trigger('change');
            var id = $(this).data('id');
            var url = '{{ route("visitor-log-screening-templates.questions.single",":id") }}';
            var url = url.replace(':id', id);
            $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    // console.log('data', data);
                    if (data) {
                        $("#feedback-form")[0].reset();
                        $('#feedback-form input[name="visitor_log_screening_template_id"]').val({{$id}});
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal textarea[name="question"]').text(data.question);
                        // $('#myModal input[name="question"]').val(data.question)
                        // $('#myModal input[name="answer"]').val(data.answer)

                        $("#"+data.answer).prop("checked", true);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Visitor Screening Templates Questions")
                    } else {
                        // alert(data);

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
             var base_url = "{{ route('visitor-log-screening-templates.questions.destroy',':id') }}";
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
                            swal("Deleted", "Visitor templates and allocation has been deleted successfully", "success");
                            table.ajax.reload();
                        }
                     else{
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

    function addnew(data=null) {

        $("#myModal").modal();
        $("#feedback-form")[0].reset();
        $('#myModal input[name="id"]').val('');
        $('#myModal textarea[name="question"]').text('');
        $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
    //    alert({{$id}});
        $('#feedback-form input[name="visitor_log_screening_template_id"]').val({{$id}});

    }

</script>
@stop
