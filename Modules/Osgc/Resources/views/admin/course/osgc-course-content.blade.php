@extends('adminlte::page')
@section('title', 'OSGC Course Contents')
@section('content_header')
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<h1>Course Section : {{ $result->title ?? ''}} </h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Course Content">Add
    <span class="add-new-label">New</span>
</div>

<input type="hidden" name="title" id="title" value="{{ $result->title ?? ''}}">
<table class="table table-bordered" id="course-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Section Name</th>
            <th>Heading Name</th>
            <th>Content Type</th>
            <th>Content Status</th>
            <th>Sort Order</th>
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
                <h4 class="modal-title" id="myModalLabel">OSGC Course Contents</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'course-form','class'=>'form-horizontal', 'method'=> 'POST','enctype'=>'multipart/form-data')) }}
            {{ Form::hidden('id', null) }}
            {{Form::hidden('course_file_name',null, ['id' => 'course_file_name',])}}
            
            <div class="modal-body">
                </ul>
                <input type="hidden" name="course_id" id="course_id" value="{{$courseId}}">
               
                
                <div class="form-group" id="header_id">
                    <label for="header_id" class="col-sm-3 control-label">Course Heading<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    {{Form::select('header_id',[null=>'Please Select']+$headings,null, ['class' => 'form-control select2','id' => 'header_id', 'style'=>"width: 100%;"])}}
                   
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 control-label">Course Section Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control', 'Placeholder'=>'Course Section Name', "maxlength"=>"33")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                
                <div class="form-group" id="sort_order">
                    <label for="sort_order" class="col-sm-3 control-label">Sort Order<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('sort_order',null,array('class'=>'form-control', 'Placeholder'=>'Sort Order', "maxlength"=>"255")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="content_type_id">
                    <label for="content_type" class="col-sm-3 control-label">Content Type<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    <select  name="content_type_id"  id="content_type_id" class="form-control" >
                        <option value="">Select</option>
                        @foreach($contentTypes as $row)
                        <option value="{{$row->id}}">{{$row->type}}</option>
                        @endforeach
                    </select>
                    
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="content_status">
                    <label for="content_type" class="col-sm-3 control-label">Content Status<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                    <select  name="content_status"   class="form-control" >
                        <option value="">Select</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                        
                    </select>
                    
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="completion_mandatory">
                    <label for="completion_mandatory" class="col-sm-3 control-label">Is Course Completion Mandatory?</label>
                    <div class="col-sm-9">
                    
                    {{ Form::checkbox('completion_mandatory', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="fast_forward">
                    <label for="fast_forward" class="col-sm-3 control-label">Disable Fast Forward</label>
                    <div class="col-sm-9">
                    
                    {{ Form::checkbox('fast_forward', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                {{ Form::close() }}
                <form action="https://{{$uploadDet['my_bucket']}}.s3-{{$uploadDet['region']}}.amazonaws.com" method="post" id="aws_upload_video_form" class="form-horizontal" enctype="multipart/form-data">
                
                <input type="hidden" name="acl" value="">
                <input type="hidden" name="success_action_status" value="">
                <input type="hidden" name="policy" value="">
                <input type="hidden" name="X-amz-credential" value="">
                <input type="hidden" name="X-amz-algorithm" value="">
                <input type="hidden" name="X-amz-date" value="">
                <input type="hidden" name="X-amz-expires" value="">
                <input type="hidden" name="X-amz-signature" value="">
                <input type="hidden" name="key" id="video-key" value="">
                <input type="hidden" name="Content-Type" id="video-type" value="">    
                <div class="form-group" >
                    <label for="fileUpload" class="col-sm-3 control-label">Course Content</label>
                    <div class="col-sm-9">
                    <input class="form-control" type="file" id="video-file" name="file" onchange="changeFileFlag();" />    
                    <small id="file-error" class="help-block err"></small>
                    <div id="myProgress" style="display:none;">      
                        <div id="myBar"></div>    
                    </div>

                    <label id="uploaded_file_name"></label>
                    </div>
                </div>
               
                
               
                
            </div>
            <div class="modal-footer">
            <input type="submit" name="submit"  class="button btn btn-primary blue" value="Save" />
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            </form>
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
<div class="modal fade" id="myModalDoc" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalDocLabel">OSGC Course Study Guide</h4>
            </div>
            {{Form::hidden('course_doc_name',null, ['id' => 'course_doc_name',])}}
            {{Form::hidden('course_section_id',null, ['id' => 'course_section_id',])}}
            <form action="https://{{$uploadDet['my_bucket']}}.s3-{{$uploadDet['region']}}.amazonaws.com" method="post" id="aws_upload_doc_form" class="form-horizontal" enctype="multipart/form-data">
                
                
                <input type="hidden" name="acl" value="">
                <input type="hidden" name="success_action_status" value="">
                <input type="hidden" name="policy" value="">
                <input type="hidden" name="X-amz-credential" value="">
                <input type="hidden" name="X-amz-algorithm" value="">
                <input type="hidden" name="X-amz-date" value="">
                <input type="hidden" name="X-amz-expires" value="">
                <input type="hidden" name="X-amz-signature" value="">
                <input type="hidden" name="key" id="doc-key" value="">
                <input type="hidden" name="Content-Type" id="doc-type" value="">    
                <div class="form-group" >
                    <label for="fileUpload" class="col-sm-3 control-label">Study Guide</label>
                    <div class="col-sm-8">
                    <input class="form-control" type="file" id="doc-file" name="file" />    
                    <small id="doc-error" class="help-block err"></small>
                    <div id="myProgressDoc" style="display:none;">      
                        <div id="myBarDoc"></div>    
                    </div>
                    <label id="uploaded_doc_name"></label>
                    </div>
                </div>
               
                
               
                
                  
            <div class="modal-footer">
            <input type="submit" name="submit"  class="button btn btn-primary blue" value="Save" />
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            </form>
        </div>
    </div>
</div>
@stop @section('js')
<style>       
.dataTable a.view,.dataTable a.add, .dataTable .edit-disable {
    padding-right: 8%;
}
#myProgress,#myProgressDoc {  width: 100%;  background-color: grey;} 
#myBar,#myBarDoc {  width: 1%;  height: 30px;  background-color: green;}
.err{
    color: #dd4b39 !important;
}
</style>


<script type="text/javascript">

function changeFileFlag()
{
    $('#course_file_name').val('')
}

   

    $(function () {

            $.fn.dataTable.ext.errMode = 'throw';
        try{
            
        var table = $('#course-table').DataTable({
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
            ajax: {
                    "url":'{{ route("osgc-course-contents.list") }}',
                    "data": function ( d ) {
                        d.course_id = $("#course_id").val();
                        
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           
            // order: [
            //     [10, "desc"]
            // ],
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
                    data: 'heading',
                    name: 'heading'
                },
                
                
                {
                    data: 'content_type',
                    name: 'content_type'
                },
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: 'sort_order',
                    name: 'sort_order'
                },

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                        actions += '<a href="#" class="doc-new fa fa-file view"  data-id=' + o.id + ' title="Study guide"></a>'
                        
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
        var bar = $('.bar');
        var percent = $('.percent');
        /* Posting data to PositionLookupController - Start*/
        $('#course-form').submit(function(e) {
            e.preventDefault();
            var $form = $('#course-form');
            $('#aws_upload_video_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var formData = new FormData($('#course-form')[0]); 
            $('#myModal input[name="submit"]').prop('disabled', true); 
            url = '{{ route("osgc-course-contents.store") }}';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        $('#myModal input[name="submit"]').prop('disabled', false);
                        if($('#course-form input[name="id"]').val()){
                            swal("Saved", "Course content has been updated successfully", "success");
                        }else{
                            swal("Saved", "Course content has been created successfully", "success");
                        }
                        $('#myModal form').trigger('reset');
                        $("#myModal").modal('hide');
                         table.ajax.reload();
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(response);

                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.responseJSON.errors);
                    associate_errors(xhr.responseJSON.errors, $form,true);
                },
                contentType: false,
                processData: false,
            });
        });
        $("#aws_upload_video_form,#aws_upload_doc_form").submit(function(e) {
            e.preventDefault();
            var allowflag=0;
            var formId=$(this).closest("form").attr("id");
            if(formId == 'aws_upload_video_form'){
                var test=$("#course_file_name").val();
                if(test ==''){
                    var allowflag=1;console.log('testing')
                
                $('#aws_upload_video_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $('#file-error').text('');
                the_file = $("#video-file")[0].files[0];
                //validation//
                var allowedExtensions =  
                        /(\.mp4|\.mkv)$/i; 
                if(the_file ===undefined)
                {
                    $('#file-error').text('File is required');
                    return false; 
                    
                }else{
                    if (!allowedExtensions.exec(the_file.name)) { 
                    $('#file-error').text('Invalid file format');
                    return false; 
                    }
                }
                 
                
                //validation//
                var contentname = Date.now() + '.' + the_file.name.split('.').pop();
                var filename = 'osgc/video/'+contentname;
	            $("#video-key").val(filename);
	            //$("#course_file_name").val(contentname);
	            $("#video-type").val(the_file.type);
                var results =  'video-results';
                var fileid = 'video_file_id';
                var success = 'video-success';

                var progressDiv = document.getElementById("myProgress");
                progressDiv.style.display="block";
                var progressBar = document.getElementById("myBar");
                }

            }else{
                var allowflag=1;
                $('#aws_upload_doc_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                
                the_file = $("#doc-file")[0].files[0];
                 //validation//
                var allowedExtensions =  
                        /(\.pdf)$/i; 
                
                if (!allowedExtensions.exec(the_file.name)) {
                    $('#doc-error').text('Invalid file format');
                    return false; 
                } 
                
                //validation//
                var contentname = Date.now() + '.' + the_file.name.split('.').pop();
                var filename = 'osgc/pdf/'+contentname;
	            $("#doc-key").val(filename);
	            //$("#course_doc_name").val(contentname);
	            $("#doc-type").val(the_file.type);
                var results =  'doc-results';
                var fileid = 'doc_file_id';
                var success = 'doc-success';
                var progressDiv = document.getElementById("myProgressDoc");
                progressDiv.style.display="block";
                var progressBar = document.getElementById("myBarDoc");
            }
                
         
	
if(allowflag==1){
    var post_url = $(this).attr("action"); //get form action url
    var form_data = new FormData(this); //Creates new FormData object
    
    $.ajax({
        url : post_url,
        type: 'post',
		datatype: 'xml',
        data : form_data,
		contentType: false,
        processData:false,
		xhr: function(){
			var xhr = $.ajaxSettings.xhr();
			if (xhr.upload){
				var progressbar = $("<div>", { style: "background:#607D8B;height:10px;margin:10px 0;" }).appendTo("#"+results); //create progressbar
				xhr.upload.addEventListener('progress', function(event){
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
							console.log(percent);
                            progressBar.style.width = percent + "%";
						}
				}, true);
			}
			return xhr;
		}
    }).done(function(response){
		var url = $(response).find("Location").text(); //get file location
        console.log(url)
        if(url){
        if(formId == 'aws_upload_video_form'){
            $("#course_file_name").val(contentname);
            $('#course-form').submit();
        }else{
            url = '{{ route("osgc-course-contents.storeStudyGuide") }}';
            $("#course_doc_name").val(contentname);
            var course_section_id=$('#course_section_id').val();
            var course_doc_name=$('#course_doc_name').val();
            form_data.append('course_section_id',course_section_id);
            form_data.append('course_doc_name',course_doc_name);
            if(course_doc_name == '' || course_section_id == '')
            {
                return false;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                url: url,
                type: 'POST',
                data:  form_data,
                success: function (data) {
                    if (data.success) {
                        
                        swal("Saved", "Study Guide has been added successfully", "success");
                        $('#myModalDoc form').trigger('reset');
                        $("#myModalDoc").modal('hide');
                         table.ajax.reload();
                    } else {
                        console.log(data);
                    }
                },
                fail: function (response) {
                    console.log(response);

                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.responseJSON.errors);
                    associate_errors(xhr.responseJSON.errors, $form,true);
                },
                contentType: false,
                processData: false,
            });
        }
    }else{
        var progressDiv = document.getElementById("myProgress");
        progressDiv.style.display="block";
        var progressBar = document.getElementById("myBar");
        alert('error')
    }
          
    }); 
}else{
    $('#course-form').submit();
}
});

        /* Clear Uploaded File label - Start */
        $('.add-new').click(function(){
           
           $("#myModal").modal();
           var progressDiv = document.getElementById("myProgress");
           progressDiv.style.display="none";   
           $('#myModal form').trigger('reset');
           $('#myModal input[name="course_id"]').val({{$courseId}});
           $('#myModal #uploaded_file_name').text(''); 
           $.ajax({
            url: "{{ route('osgc-course.fetchS3Details') }}",
            type: 'GET',
            success: function (data) {
                if (data) {
                    $('#myModal input[name="acl"]').val('private');
                    $('#myModal input[name="success_action_status"]').val('201');
                    $('#myModal input[name="policy"]').val(data.policybase64);
                    $('#myModal input[name="X-amz-credential"]').val(data.amz_credentials);
                    $('#myModal input[name="X-amz-algorithm"]').val('AWS4-HMAC-SHA256');
                    $('#myModal input[name="X-amz-date"]').val(data.iso_date);
                    $('#myModal input[name="X-amz-expires"]').val(data.presigned_url_expiry);
                    $('#myModal input[name="X-amz-signature"]').val(data.signature);
                } else {
                    
                    swal("Warning", 'Failed', "warning");
                    
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
        /* Clear Uploaded File label - End */
        $("#course-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("osgc-course-contents.single",":id") }}';
            var url = url.replace(':id', id);
            $('#course-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            var progressDiv = document.getElementById("myProgress");
            progressDiv.style.display="none";   
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (result) {
                    
                    $("#course-form").trigger('reset');
                    if(result.courseDet !=null){
                        var data=result.courseDet;
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal input[name="sort_order"]').val(data.sort_order)
                        $('#myModal select[name="header_id"] option[value="'+data.header_id+'"]').prop('selected',true);
                        $('#myModal select[name="content_status"] option[value="'+data.active+'"]').prop('selected',true);
                        if(data.course_content)
                        {
                            $('#myModal select[name="content_type_id"] option[value="'+data.course_content.content_type_id+'"]').prop('selected',true);
                        
                        }
                        if(data.course_content){
                            $('#myModal #uploaded_file_name').text(data.course_content.content)
                            $('#myModal #course_file_name').val(data.course_content.content)
                            $('#myModal #uploaded_file_name').css('font-weight',500)
                        }
                        if(data.completion_mandatory ==1)
                        {
                            $('#myModal input[name="completion_mandatory"]').prop('checked',true);
                        }
                        if(data.course_content.fast_forward ==1)
                        {
                            $('#myModal input[name="fast_forward"]').prop('checked',true);
                        }
                        
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit OSGC Course: "+ data.name)

                        $('#myModal input[name="acl"]').val('private');
                        $('#myModal input[name="success_action_status"]').val('201');
                        $('#myModal input[name="policy"]').val(result.uploadDet.policybase64);
                        $('#myModal input[name="X-amz-credential"]').val(result.uploadDet.amz_credentials);
                        $('#myModal input[name="X-amz-algorithm"]').val('AWS4-HMAC-SHA256');
                        $('#myModal input[name="X-amz-date"]').val(result.uploadDet.iso_date);
                        $('#myModal input[name="X-amz-expires"]').val(result.uploadDet.presigned_url_expiry);
                        $('#myModal input[name="X-amz-signature"]').val(result.uploadDet.signature);

                    } else {
                        alert(result);
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

        

       
        
        $('#course-table').on('click', '.doc-new', function (e) {
            var id = $(this).data('id');
            $('#myModalDoc input[name="course_doc_name"]').val('');
            $('#myModalDoc #uploaded_doc_name').text(''); 
            $('#doc-error').text('');
            $.ajax({
                headers: {
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                url:  "{{ route('osgc-course-contents.checkStudyGuideExist') }}",
                type: 'POST',
                data:  {
                'course_section_id':id,
                
                },
                success: function (data) {
                    $("#myModalDoc").modal();
                    $('#myModalDoc form').trigger('reset');
                    var progressDiv = document.getElementById("myProgressDoc");
                    progressDiv.style.display="none";   
                    if(data.studyGuideDet !=null){
                        if(data.studyGuideDet.file_name !='')
                        {
                            $('#myModalDoc input[name="course_doc_name"]').val(data.studyGuideDet.file_name);
                            $('#myModalDoc #uploaded_doc_name').text(data.studyGuideDet.file_name);  
                        }
                    }
                    $('#myModalDoc input[name="course_section_id"]').val(id);
                    $('#myModalDoc input[name="acl"]').val('private');
                    $('#myModalDoc input[name="success_action_status"]').val('201');
                    $('#myModalDoc input[name="policy"]').val(data.uploadDet.policybase64);
                    $('#myModalDoc input[name="X-amz-credential"]').val(data.uploadDet.amz_credentials);
                    $('#myModalDoc input[name="X-amz-algorithm"]').val('AWS4-HMAC-SHA256');
                    $('#myModalDoc input[name="X-amz-date"]').val(data.uploadDet.iso_date);
                    $('#myModalDoc input[name="X-amz-expires"]').val(data.uploadDet.presigned_url_expiry);
                    $('#myModalDoc input[name="X-amz-signature"]').val(data.uploadDet.signature);
                    
                }

                });
          
           
           
        });
       
    });
</script>
@stop
