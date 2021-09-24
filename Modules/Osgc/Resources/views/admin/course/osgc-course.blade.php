@extends('adminlte::page')
@section('title', 'OSGC Courses')
@section('content_header')
<h1>OSGC Courses</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Course">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="course-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Price</th>
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
                <h4 class="modal-title" id="myModalLabel">OSGC Course</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'course-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{Form::hidden('course_image_name',null, ['id' => 'course_image_name',])}}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="title">
                    <label for="title" class="col-sm-2 control-label">Course Title<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('title',null,array('class'=>'form-control', 'Placeholder'=>'Course Title', "maxlength"=>"255")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="description">
                    <label for="description" class="col-sm-2 control-label">Description<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class'=>'form-control', 'Placeholder'=>'Description','rows'=>"3")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="price">
                    <label for="price" class="col-sm-2 control-label">Course Price<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('price',null,array('class'=>'form-control', 'Placeholder'=>'Course Price')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                
                <div class="form-group col-sm-12" id="course_headings">
                        <div class=" table-responsive pop-in-table" id="course-heading">
                            <table class="table table-bordered course-heading-table">
                                <thead>
                                <tr>
                                    <th><div class='col-sm-5'>Course Content Headings</div><div class='col-sm-2'>Sort Order</div><div class='col-sm-3'>Status</div><div class='col-sm-2'></div></th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        
                </div>
               
                
                
                {{ Form::close() }}
                
                <!-- image upload -->
                
                <div class="form-group col-sm-12" id="course_img">
                <form action="https://{{$uploadDet['my_bucket']}}.s3-{{$uploadDet['region']}}.amazonaws.com" method="post" id="aws_upload_img_form" class="form-horizontal" enctype="multipart/form-data">
                    
                    
                    <input type="hidden" name="acl" value="">
                    <input type="hidden" name="success_action_status" value="">
                    <input type="hidden" name="policy" value="">
                    <input type="hidden" name="X-amz-credential" value="">
                    <input type="hidden" name="X-amz-algorithm" value="">
                    <input type="hidden" name="X-amz-date" value="">
                    <input type="hidden" name="X-amz-expires" value="">
                    <input type="hidden" name="X-amz-signature" value="">
                    <input type="hidden" name="key" id="img-key" value="">
                    <input type="hidden" name="Content-Type" id="img-type" value="">    
                    
                    <label for="fileUpload" class="col-sm-2 control-label">Course Image</label>
                    <div class="col-sm-7">
                    <input class="form-control" type="file" id="img-file" name="file" />    
                    <small id="img-error" class="help-block err"></small>
                    <div id="myProgress" style="display:none;">      
                        <div id="myBar"></div>    
                    </div>
                    <label id="uploaded_img_name"></label>
                    </div>
                    <div class="col-sm-3">
                    <input type="submit" name="submitbtn"  class="button btn btn-primary blue" value="Upload" />
                    </div>
                    
                </form><br/>
                </div>
                <!-- image upload -->
                

                
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            
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
            /* width: 35%; */

        }
        .dataTable a.view,a.delete, .dataTable .edit-disable {
    padding-right: 8%;
}
.err{
    color: #dd4b39 !important;
}
#myProgress{  width: 100%;  background-color: grey;} 
#myBar {  width: 1%;  height: 30px;  background-color: green;}
</style>
<script>
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
                        columns: [ 0,1, 2,3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2,3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2,3]
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('osgc-course.list') }}",
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
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                        var url_details = '{{ route("osgc-course-contents",'') }}';
                        actions += '<a href="'+url_details+"/"+ o.id +'" class="view fa fa-eye" ></a>'
                        var question_url = '{{ route("osgc.exam-questions-settings",'') }}';
                        actions += '<a href="'+question_url+"/"+ o.id +'" class="fa fa-question-circle view" title="Questions" ></a>'

                        if(o.active =='Active')
                        {
                            actions += '<a href="#" class="inactivate fa fa-check" data-id=' + o.id + ' title="Activated"></a>'
                            
                        }else{
                            actions += '<a href="#" class="activate fa fa-times" data-id=' + o.id + ' title="Deactivated"></a>'
                            
                        }
                        
                        return actions;
                    },
                },
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
        $('#mdl_save_change').on('click', function() {
            $('#course-form').submit();
        });
        $("#aws_upload_img_form").submit(function(e) {
            e.preventDefault();
            the_file = $("#img-file")[0].files[0];
                //validation//
                var allowedExtensions =  
                        /(\.jpg|\.png|\.jpeg)$/i; 
                if(the_file !==undefined)
                {
                    if (!allowedExtensions.exec(the_file.name)) { 
                    $('#img-error').text('Invalid file format');
                    return false; 
                    }else{
                        var contentname = Date.now() + '.' + the_file.name.split('.').pop();
                        var filename = 'osgc/image/'+contentname;
                        $("#img-key").val(filename);
                        $("#course_image_name").val(contentname);
                        $("#img-type").val(the_file.type);
                        var results =  'img-results';
                        var fileid = 'img_file_id';
                        var success = 'img-success';

                        var progressDiv = document.getElementById("myProgress");
                        progressDiv.style.display="block";
                        var progressBar = document.getElementById("myBar");
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
                        }).done(function(response){console.log('hh')
                            var url = $(response).find("Location").text(); //get file location
                            console.log(url)
                            
                            
                        }); 
                    }
                    
                    
                }
                   
                
                
            
        });
        /* Posting data to PositionLookupController - Start*/
        $('#course-form').submit(function (e) {
            e.preventDefault();
            $('#course-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var values = [];
            var flag=0;
            var headerflag=0;
            $('.sorting').each(
                function() {
                
                if (values.indexOf(this.value) >= 0) {
                    
                    if($(this).attr("name"))
                    {
                        var name=$(this).attr("name");
                        $('#course-form').find('.form-group').find('#'+name).addClass('has-error').find('.help-block').text('The Sort order has already been taken.');
                        $('#myModal input[name="'+name+'"]').css("border-color", "red");
                        flag=1;
                    }
                   
                } else {
                    $(this).css("border-color", ""); //clears since last check
                    values.push(this.value);
                }
                }
            );
            $('.header').each(
                function() {
                if (values.indexOf(this.value) >= 0) {
                    
                    if($(this).attr("name"))
                    {
                        var name=$(this).attr("name");
                        $('#course-form').find('.form-group').find('#'+name).addClass('has-error').find('.help-block').text('The Course heading has already been taken.');
                        $('#myModal input[name="'+name+'"]').css("border-color", "red");
                        headerflag=1;
                    }
                   
                } else {
                    $(this).css("border-color", ""); //clears since last check
                    values.push(this.value);
                }
                }
            );
            if(flag==1||headerflag==1)
            {
                return false;
            }
           
            
            if($('#course-form input[name="id"]').val()){
                var message = 'Course has been updated successfully';
            }else{
                var message = 'Course has been created successfully';
            }
            formSubmit($('#course-form'), "{{ route('osgc-course.store') }}", table, e, message);
        });


        $("#course-table").on("click", ".edit", function (e) {
            $("#course_image_src").hide();
            var id = $(this).data('id');
            var url = '{{ route("osgc-course.single",":id") }}';
            var url = url.replace(':id', id);
            $('#course-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $(".course-heading-table tbody tr").remove();
            var progressDiv = document.getElementById("myProgress");
            progressDiv.style.display="none";   
            $('#myModal input[name="file"]').val('') 
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (result) {
                    
                    $("#course-form").trigger('reset');
                    if(result.courseDet !=null){
                        var data=result.courseDet;
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="title"]').val(data.title)
                        $('#myModal textarea[name="description"]').val(data.description);
                        if(data.course_price){
                        $('#myModal input[name="price"]').val(data.course_price.price)
                        }
                        if(data.course_image != null){ 
                            $("#uploaded_img_name").text(data.course_image);
                            $('#course_image_name').val(data.course_image)
                        }
                        $.each(data.course_headers, function(key, value) {

                        var heading_edit_row = '';
                        heading_edit_row =
                        "<tr><td><div class='form-group' id='content_heading_" + key + "'><input type='text' name='row-no[]' class='row-no' value=" + key + "><input type='hidden' class='heading_id' name='heading_id_" + key + "' value='"+value.id+"'><div class='col-sm-5 form-group' id='heading_"+key+"'><input type='text' name='heading_" + key + "' class='form-control header'  value='"+value.name+"'><small class='help-block'></small></div><div class='col-sm-2 form-group' id='sort_order_"+key+"'><input type='number' name='sort_order_" + key + "' value='"+value.sort_order+"' class='form-control sorting'><small class='help-block'></small></div><div class='col-sm-3 form-group' id='status_"+key+"'><select class='form-control' name='status_"+key+"'><option value='' selected>Select</option><option value='1'>Active</option><option value='0'>Inactive</option></select><small class='help-block'></small></div><div class='col-sm-2'><label for='remove-heading'  class='btn btn-primary remove-heading'>-</label>&nbsp<label for='add-heading' class='btn btn-primary add-heading' >+</label></div></div></td></tr>";
                        $(".course-heading-table tbody").append(heading_edit_row);
                        $('select[name="status_' + key + '"] option[value="' + value.active + '"]').prop('selected', true);
                        

                        
                        });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit OSGC Course: "+ data.title)

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
        $('.add-new').click(function(){
           $("#myModal").modal();
           var progressDiv = document.getElementById("myProgress");
           progressDiv.style.display="none";   
           $('#myModal form').trigger('reset');
           $('.remove-heading').show();
           $("#course_image_src").hide();
           $('#course-heading tbody').find('tr').remove();
           $course_first_row = "<tr><td><div class='form-group' id='content_heading_0'><input type='text' name='row-no[]' class='row-no' value='0'><div class='col-sm-5 form-group' id='heading_0'><input type='text' name='heading_0'  class='form-control header'><small class='help-block'></small></div><div class='col-sm-2 form-group' id='sort_order_0'><input type='number' name='sort_order_0' class='form-control sorting'><small class='help-block' ></small></div><div class='col-sm-3 form-group' id='status_0'><select class='form-control' name='status_0'><option value='' selected>Select</option><option value='1'>Active</option><option value='0'>Inactive</option></select><small class='help-block' ></small></div><div class='col-sm-2'><label for='remove-heading'  class='btn btn-primary remove-heading'>-</label>&nbsp<label for='add-heading' class='btn btn-primary add-heading' >+</label></div></div></td></tr>";
           $('#course-heading tbody').append($course_first_row);
           
           $('#myModal #uploaded_img_name').text(''); 

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

        $('#course-table').on('click', '.delete', function (e) {

            var id = $(this).data('id');
            var base_url = "{{ route('osgc-course.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Course has been deleted successfully';
            deleteRecord(url, table, message);
           
        });
        $('#course-table').on('click', '.activate', function (e) {

        var id = $(this).data('id');
        var base_url = "{{ route('osgc-course.activateCourse',':id') }}";
        var url = base_url.replace(':id', id);
        var message = 'Do you want to activate this course?';
        var flag='Activated';
        activateordeactivateRecord(url, table, message,flag);

        });
        $('#course-table').on('click', '.inactivate', function (e) {

        var id = $(this).data('id');
        var base_url = "{{ route('osgc-course.deactivateCourse',':id') }}";
        var url = base_url.replace(':id', id);
        var message = 'Do you want to deactivate this course?';
        var flag='Deactivated';
        activateordeactivateRecord(url, table, message,flag);

        });

        $('.remove-heading').hide();
        $(".course-heading-table").on('click', '.add-heading', function () {
            
            $last_row_no = $(".course-heading-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }
            if($next_row_no <=5){
                if($next_row_no ==5)
                {
                    //$("#add-heading").hide();
                }
                
                var course_new_row =
                "<tr><td><div class='form-group' id='content_heading_" + $next_row_no + "'><input type='text' name='row-no[]' class='row-no'><div class='col-sm-5 form-group' id='heading_"+$next_row_no+"'><input type='text' name='heading_" + $next_row_no + "'  class='form-control header'><small class='help-block'></small></div><div class='col-sm-2 form-group' id='sort_order_"+$next_row_no+"'><input type='number' name='sort_order_" + $next_row_no + "' class='form-control sorting'><small class='help-block'></small></div><div class='col-sm-3 form-group' id='status_"+$next_row_no+"'><select class='form-control' name='status_"+$next_row_no+"'><option value='' selected>Select</option><option value='1'>Active</option><option value='0'>Inactive</option></select><small class='help-block'></small></div><div class='col-sm-2'><label for='remove-heading'  class='btn btn-primary remove-heading'>-</label>&nbsp<label for='add-heading' class='btn btn-primary add-heading' >+</label></div></div></td></tr>";
                $(".course-heading-table tbody").append(course_new_row);
                $(".course-heading-table").find('tr:last').find('.row-no').val($next_row_no);

                if ($last_row_no > 0 || $last_row_no == undefined) {
                    $('.remove-heading').show();
                }
            }
            
        });
        //*
        $(".course-heading-table").on('click', '.remove-heading', function () {
            var t=$(this).closest('tr');
            var id = t.find(".heading_id").val();
            var url = '{{ route("osgc-course.deleteCourseHeader",":id") }}';
            var url1 = url.replace(':id', id);
            $last_row_no = $(".course-heading-table").find('tr:last .row-no').val();
            if ($last_row_no != undefined) {
                $next_row_no = ($last_row_no * 1) + 1;
            } else {
                $next_row_no = 0;
            }
            if($next_row_no <=6)
            {
                $("#add-heading").show();
            }
            
            $.ajax({
                url: url1,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data)
                    if (data.success == true) {console.log('dd')
                        t.remove();
                        
                    } else {
                        var swalHtml = `You cannot delete this Header Beacause It is Used in Course Content.`;
                                swal({
                                    title: "Failed",
                                    text : swalHtml,
                                    html: true,
                                    icon: "warning",
                                    confirmButtonText: "OK",
                                });
                               
                    }
                }
            });
           
          

        });
        /* Form submit - End */

/* activate or deactivate Record - Start */
function activateordeactivateRecord(url, table, message,flag) {
    var url = url;
    var table = table;
    swal({
        title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    },
    function () {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data.success) {
                    swal(flag, "Course has been updated successfully", "success");
                    if (table != null) {
                        table.ajax.reload();
                    }
                } else {
                    
                    swal("Warning", 'Headers/Sections not added for this course', "warning");
                    
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
}
/* Delete Record - End */
       
       
    });
</script>
@stop
