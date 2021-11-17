@extends('adminlte::page')
@section('title', 'OSGC Course Test Settings')
@section('content_header')
<h1>{{ $result->title ?? ''}} - Test Settings</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Test Settings">Add
    <span class="add-new-label">New</span>
</div>
<input type="hidden" name="course_id" id="course_id" value={{$id}}>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
        <th>#</th>
        <th>Heading Name</th>
        <th>Section Name</th>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">OSGC Course Test Settings</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('course_id', null) }}
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
                <div class="form-group" id="header_id">
                    <label for="header_id" class="col-sm-3 control-label">Course Headings<span class="mandatory">*</span></label>
                    <div class="col-sm-8">
                    {{Form::select('osgc_course_header_id',[null=>'Please Select']+$courseHeadings,null, ['class' => 'form-control select2','id' => 'osgc_course_header_id','onchange'=>'getSectionList()', 'style'=>"width: 100%;",'required'=>TRUE])}}
                   
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="sections">
                    <label for="section_id" class="col-sm-3 control-label">Course Sections<span class="mandatory">*</span></label>
                    <div class="col-sm-8">
                    {{Form::select('osgc_course_section_id',[null=>'Please Select'],null, ['class' => 'form-control select2','id' => 'osgc_course_section_id', 'style'=>"width: 100%;",'required'=>TRUE])}}
                   
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="exam_name">
                    <label for="exam_name" class="col-sm-3 control-label">Exam Name</label>
                    <div class="col-sm-8">
                        {{ Form::text('exam_name',null,array('class' => 'form-control', 'Placeholder'=>'Exam Name', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group" id="number_of_question">
                    <label for="number_of_question" class="col-sm-3 control-label">Number Of Questions To Be Displayed</label>
                    <div class="col-sm-8">
                        {{ Form::number('number_of_question',null,array('class' => 'form-control','min'=>'1', 'Placeholder'=>'Skip this field if all questions is to be displayed')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="random_question">
                    <label for="random_question" class="col-sm-3 control-label">Random Question Display</label>
                    <div class="col-sm-8">
                        <label> <input type="radio" name="random_question"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                        <label> <input type="radio" name="random_question"  checked value="0" >&nbsp;No&nbsp;&nbsp;</label> 
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group" id="pass_percentage">
                    <label for="pass_percentage" class="col-sm-3 control-label">Pass Percentage</label>
                    <div class="col-sm-8">
                        {{ Form::number('pass_percentage',null,array('class' => 'form-control', 'Placeholder'=>'Pass Percentage')) }}
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
.heading-div   {
            display: block;
            margin-left:0px;
            padding-left:0px;

        }
        .heading-div label {
            width: 60px;
            margin-left: 0px;
            margin-right: 1%;

        }
        #course-heading{
            margin-left:0px;
            padding-left:0px;
        }
        #course-heading select {
            width: 35%;

        }
        .dataTable a.view, .dataTable .edit-disable {
    padding-right: 8%;
}
</style>
<script>
    $(function () {

            $.fn.dataTable.ext.errMode = 'throw';
            try{
        var url = '{{ route("osgc.settings-list",":id") }}';
        var url = url.replace(':id', $('#course_id').val());
        var table = $('#table').DataTable({
               dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5,6]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5,6]
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
                    data: 'header_name',
                    name: 'header_name'
                },
                {
                    data: 'section_name',
                    name: 'section_name'
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
                    name: 'updated_at',
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                       var url = '{{ route("osgc.exam-questions",'') }}';
                        var actions = '';
                        actions += ' <a href="#" title="Edit" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        actions += '<a href="'+url+"/"+ o.id +'" class="fa fa-question-circle view" title="Questions" data-id=' + o.id + '></a>'
                         
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

        /* Posting data to PositionLookupController - Start*/
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'Test settings updated successfully';
            }else{
                var message = 'Test settings saved successfully';
            }
            formSubmit($('#form'), "{{ route('osgc.exam-questions-settings.store') }}", table, e, message);
        });


        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("osgc.exam-questions-settings.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="exam_name"]').val(data.exam_name)
                        $('#myModal input[name="course_id"]').val(data.course_id);console.log(data.osgc_course_section_id)
                        if(data.course_section){
                        $('#myModal select[name="osgc_course_header_id"] option[value="'+data.course_section.header_id+'"]').prop('selected',true);
                        getSectionList(data.course_section.header_id,data.id,data.osgc_course_section_id)
                        }
                       
            
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
            var base_url = "{{ route('osgc.exam-questions-settings.destroy',':id') }}";
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
            var courseId=$('#course_id').val();
            $('#myModal').find('input[name=course_id]').val(courseId);
            $('#myModal .modal-title').text(title);
            $("#customers").val('').trigger('change') ;
            $('#myModal select[name="osgc_course_section_id"]').find('option').remove();
            $('#myModal select[name="osgc_course_header_id"]').val('');
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
    function getSectionList(headerId,id=0,sectionId=0){
            $('#myModal select[name="osgc_course_section_id"]').find('option').remove();
            let header_id = $("#osgc_course_header_id").val();
            let course_id =$('#course_id').val()
            if(header_id > 0) {
                let url = '{{ route("osgc-course-contents.sectionList") }}';
           // url = url.replace(':id', header_id);
            $.ajax({
                url: url,
                type: 'GET',
                data:  {
                'header_id':header_id,
                'course_id':course_id,
                'id':id,
                
                },
                success: function (data) {
                    if (data) {
                        var options = '';
                        $.each(data, function (key, value) {
                            $('#osgc_course_section_id').append("<option value="+value.id+">"+value.name+"</option>");
                            $('#myModal select[name="osgc_course_section_id"] option[value="'+sectionId+'"]').prop('selected',true);
                            
                        });
                    } else {
                        swal("Oops", "Could not retrive data.", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false
            });
            } else {
                return false;
            }
        }
</script>
@stop
