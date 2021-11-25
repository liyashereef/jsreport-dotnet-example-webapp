@extends('adminlte::page')
<style>
    .add-newlink {
        float: right;
        width: 200px;
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
</style>
@section('title', 'Add Content')
@section('content_header')

<h1>Manage Content</h1>
@stop
@section('content')

{{-- <div class="add-new" data-title="Add New Content">Add
    <span class="add-new-label">New content</span>
    <input type="hidden" name="blockId" id="blockId" value="1">

</div> --}}
<div class="add-newlink" data-title="Add New Content">
    <a href="{{route("content-manager.s3view")}}" style="text-decoration: none;color: #ffffff">Add New Content</a>
</div>

<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="23%">Key</th>
            <th width="12%">Expiry Date</th>
            <th width="13%">Days Remaining</th>

            <th width="22%">Video Header</th>
            <th width="20%">Attachments</th>
            <th width="5%">Actions</th>
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
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'content_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <!-- Active Toggle button - Start -->
                <div class="form-group col-sm-11" id="status">
                    {{-- <label class="switch" style="float:right;margin-right: -20px;">
                        {{ Form::checkbox('enabled',1,null, array('class'=>'form-control')) }}
                        <span class="slider round"></span>
                    </label>
                    <label style="float:right;padding-right: 5px;">Active</label> --}}
                </div>
                <!-- Active Toggle button - End -->
                {{-- <div class="form-group" id="key">
                        <label for="key" class="col-sm-3 control-label">Key</label>
                        <div class="col-sm-8">
                            {{ Form::text('key',null,array('class' => 'form-control', 'Placeholder'=>'', 'required'=>TRUE)) }}
                <small class="help-block"></small>
            </div>
        </div> --}}
        
        <div class="form-group" id="title">
            <label for="title" class="col-sm-3 control-label">Video Title</label>
            <div class="col-sm-8">
                {{ Form::text('title',null,array('class' => 'form-control', 'Placeholder'=>'Title')) }}
                <small class="help-block"></small>
            </div>
        </div>

        <div class="form-group" id="pan_mac">
            <label for="pan_mac" class="col-sm-3 control-label">Video file[MP4]</label>
            <div class="col-sm-5">
                <input type="file" accept="video/mp4,video/x-m4v,video/*"
                class="form-control filecontrol" name="video-file"  id="video-file">
                <small class="help-block"></small>
                    <div class="col-sm-12"   style="background-color: aliceblue;"
 id="video-results"><!-- server response here --></div>
            </div>
            <div id="success" class="col-sm-2">
                <input class="btn btn-primary blue video-button" 
                type="button" attr-doctype="video" onclick="fileUpload(this);" 
                value="Upload" />
                
            </div>
            <input type="hidden" name="uploadedS3VideoFileName" id="uploadedS3VideoFileName" value="" />
            
        </div>

        <div id="upload-attachment" class="form-group" id="pan_mac">
            <div class="row blockrow1" >
                <label for="pan_mac" class="col-sm-3 control-label">Title</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="title_off_attachment1">
                    <small class="help-block"></small>
                </div>
                
                
            </div>
            <div class="row blockrow1">
                <label for="pan_mac" class="col-sm-3 control-label">Attachments</label>
                <div class="col-sm-5">
                    <input type="file" class="form-control filecontrol" id="time_off_attachment1" name="time_off_attachment1">
                    <small class="help-block"></small>
                    <input type="hidden" class="uploadedS3AttachedFileName"
                    name="uploadedS3AttachedFileName1" 
                    id="uploadedS3AttachedFileName1" value="" />

                </div>
                <div class="col-sm-1" id="upload-block1">
                    <input  attr-doctype="attachment" attr-block="1"  attr-doctype="attachment" onclick="fileUpload(this);" class="btn btn-primary blue video-button" type="button" value="Upload" />
                </div>
                <div class="col-sm-1 test">
                    <a title="Add" href="javascript:;" attr-id="1" class="add_attachment">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="row blockrow1">
                <div class="col-md-3"></div>
                <div class="col-md-5" id="attachment-progressbar1">

                </div>
            </div>
        </div>
        <div class="form-group" id="title">
            <label for="title" class="col-sm-3 control-label">Expiry Date</label>
            <div class="col-sm-5">
                <input type="text" readonly name="expiry_date" id="expiry_date" class="form-control datepicker" />
                <small class="help-block"></small>
            </div>
        </div>
        <div style="text-align:center !important;" class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>
</div>
<input type="hidden" name="modal-body-hidden" id="modal-body-hidden" class="modal-body-hidden" />
@stop
@section('js')
<script>
    $(function() {
        $.fn.dataTable.ext.errMode = 'throw';
        try {



            var table = $('#type-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
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
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Positions');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('content-manager.list') }}",
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
                        sortable: false,
                    },
                    {
                        data: null,
                        name: 'key',
                        render:function(o){
                            return '<span id="icon-'+o.id+'">'+o.key+'</span>&nbsp;&nbsp;<i  attr-id="'+o.id+'" class="fa fa-refresh" ></i>&nbsp;&nbsp;&nbsp;&nbsp;<i  attr-id="'+o.key+'" class="fa fa-copy"  ></i>'
                        }
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date',
                    },
                    {
                        data: 'days_remaining',
                        name: 'days_remaining',
                    },
                    {
                        data: null,
                        name: 'video_attachment',
                        render:function(o){
                            let link="";
                            let i=1;
                            let contentId=o.id
                            let createdAt=o.created_at
                            if(o.video_link.length>0){
                                (o.video_link).forEach(element => {
                                    // link+=' <a target="_blank" href="'+element+'">'+(o.video_attachment)[i-1]+'</a>'; 
                                    link+=' <a  href="#">'+(o.video_attachment)[i-1]+'</a>'; 
                                    link+='&nbsp&nbsp<a href="#" class="removeattachment fa fa-trash-o" data-id='+contentId+' data-created='+createdAt+' data-file="' + element + '"></a>'
                                    i++;
                                });
                                return link
                            }else{
                                return "--"
                            }
                                                       
                        }
                    },
                    {
                        data: null,
                        name: 'normal_attachment',
                        render:function(o){
                            let link="";
                            let i=1;
                            let contentId=o.id
                            let createdAt=o.created_at

                            if(o.attachment_link.length>0){
                                (o.attachment_link).forEach(element => {
                                    // link+=' <a target="_blank" href="'+element+'">'+(o.normal_attachment)[i-1]+'</a>&nbsp;<a href="#" class="removeattachment fa fa-trash-o" data-id='+contentId+' data-created='+createdAt+' data-file="' + element + '"></a><br/>';
                                    link+=' <a  href="#">'+(o.normal_attachment)[i-1]+'</a>&nbsp;<a href="#" class="removeattachment fa fa-trash-o" data-id='+contentId+' data-created='+createdAt+' data-file="' + element + '"></a><br/>'; 
                                    i++;
                                });
                                return link
                            }else{
                                return "--"
                            }
                                                       
                        }
                    },
                 /*   {
                        data: 'online',
                        name: 'online',
                        defaultContent: "--"
                    },
                    {
                        data: 'low_battery',
                        name: 'low_battery',
                        defaultContent: "--"
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            var authorised = '';
                            if (o.enabled == 0) {
                                authorised = 'Disabled';
                            } else {
                                authorised = 'Enabled';
                            }

                            return authorised;
                        },
                        name: 'authorised'
                    },*/
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            // actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            var routeUrl = '{{ route("content-manager.s3view", ":id") }}';
                            routeUrl = routeUrl.replace(':id', o.id);

                            actions += '<a href="'+routeUrl+'" class="editbtn {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    } 
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }


        validateAttachment=function(){
            let valid=0;
            let nodata=0;
            if($("#video-file").val()!="" && $("#uploadedS3VideoFileName").val()==""){
                valid++;
            }

            if($("#uploadedS3VideoFileName").val()!="" && $("input[name=title]").val()==""){
                valid++;
            }
            if($("#video-file").val()!=""){
                nodata++;
            }
            $('.uploadedS3AttachedFileName').each(function(i, obj) {
                let controlName=(obj.id).replace("uploadedS3AttachedFileName","time_off_attachment");
                let textControlName=(obj.id).replace("uploadedS3AttachedFileName","title_off_attachment");
                let attachmentControl=$("#"+controlName).val()
                let textControl=$("input[name="+textControlName+"]").val()
                if(attachmentControl!="" && obj.value==""){
                    valid++;
                }
                if(textControl=="" && obj.value!=""){
                    valid++;
                }
                if(attachmentControl!=""){
                    nodata++;
                }
                
            });
            
            if(parseInt(valid)>0){
                return false;
            }else{
                if(nodata>0){
                    return true
                }else{
                    return false;
                }
                
            }
            
        }

        $('#content_form').submit(function(e) {
            e.preventDefault();
            $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
            $("<input />").attr("type", "hidden")
                .attr("name", "blockNo")
                .attr("value", $("#blockId").val())
                .appendTo("#content_form");
            if ($('#content_form input[name="id"]').val()) {
                var message = 'Content updated successfully';
            } else {
                var message = 'Content has been created successfully';
            }
            let editId=$("#myModal input[name=id]").val();
            let valid = validateAttachment();
            if(valid==true && editId==""){
                $('.filecontrol').each(function(i, obj) {
                    $("#"+obj.id).val("")
                });
                formSubmit($('#content_form'), "{{ route('content-manager.store') }}", table, e, message);
                $('body').loading('stop');
            }else if(editId>0 && valid==true){
                formSubmit($('#content_form'), "{{ route('content-manager.store') }}", table, e, message);
                $('body').loading('stop');
            }else{
                swal("Warning","Please validate inputs/Click upload","warning")
                $('body').loading('stop');
            }
        });



    });
    $(document).on("click",".remove_attachment",function(e){
        e.preventDefault();
        let blockId=$(this).attr("attr-blockid");
        $(".blockrow"+blockId).remove()
    })
    $(document).on("click",".add_attachment",function(e){

        //  $('#upload-attachment').append('<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"><td class="data-list-disc attachment"><input type="file" class="form-control filecontrol" name="time_off_attachment[]" required></td><td class="data-list-disc attachment-button"><a title="Remove" href="javascript:;" class="remove_attachment"><i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> Remove Attachment</a></td></div>');
        let blockId=parseInt($("#blockId").val())+1;
        $('#upload-attachment').append(` <div class="row blockrow${blockId}">
                <label for="pan_mac" class="col-sm-3 control-label">Title</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="title_off_attachment${blockId}">
                    <small class="help-block"></small>
                </div>
                
                
            </div><div class="row blockrow${blockId}"><label for="pan_mac" class="col-sm-3 control-label"></label>
            <div class="col-sm-5"><input type="file" class="form-control filecontrol"  id="time_off_attachment${blockId}" name="time_off_attachment${blockId}">
                <input type="hidden" name="uploadedS3AttachedFileName${blockId}" 
                    id="uploadedS3AttachedFileName${blockId}" value="" /><small class="help-block"></small></div>
                    <div class="col-sm-1"  id="upload-block${blockId}">
            <input class="btn btn-primary blue video-button"  attr-doctype="attachment" attr-block="${blockId}"   
            type="button" value="Upload"   onclick="fileUpload(this);" />
            </div><div class="col-sm-1 test"> <a title="Remove" href="javascript:;" attr-blockid="${blockId}" class="remove_attachment">
            <i class="fa fa-minus size-adjust-icon" aria-hidden="true"></i> </a> </div></div>
            <div class="row blockrow${blockId}">
                <div class="col-md-3"></div>
                <div class="col-md-5" id="attachment-progressbar${blockId}">

                </div>
            </div>`);
            $("#blockId").val(blockId)



    })

    $('#upload-attachment').on('click', '.remove_attachment', function() {
        $(this).closest('tr').remove();
    });

    function fileUpload(val) {
        let docType=($(val).attr("attr-doctype"))
        $(val).prop("disabled",true)
        var url = {!!json_encode($url) !!};
        let formData = new FormData();
        var signature = {!!json_encode($signature) !!};
        var policy = {!!json_encode($policybase64) !!};
        var iso_date = {!!json_encode($iso_date) !!};
        var today = {!!json_encode($today) !!};

        var presigned_url_expiry = {!!json_encode($presigned_url_expiry) !!};
        var credential = {!!json_encode($amz_credentials) !!};
        var blockCount = $("#blockId").val();
        let filename=$("#video-file").val();
        let fileExtension="";
        let allowedExtension=["mp4","mkv"]
        let valid=0;
        if(docType=="attachment"){
            
            filename=$("#time_off_attachment"+$(val).attr("attr-block")).val();
            filename= filename.split('\\').pop().split('/').pop();
            valid=1;
        }else{
            filename=$("#video-file").val();
            filename= filename.split('\\').pop().split('/').pop();
            fileExtension=filename.split('.').pop();
            if(allowedExtension.includes(fileExtension)){
                valid=1;
            }

        }

        if(valid==1){
            formData.append('X-amz-signature', signature);
        formData.append('acl', "private");
        formData.append('success_action_status', "201");
        formData.append('Content-Type', "");
        formData.append('policy', policy);
        formData.append('X-amz-algorithm', "AWS4-HMAC-SHA256");
        formData.append('X-amz-date', iso_date);
        formData.append('X-amz-expires', presigned_url_expiry);
        formData.append('X-amz-credential', credential);
        formData.append('key', `temp/contentmanager/${today+"/"+filename}`);
        if(docType=="attachment"){
            var inputName="time_off_attachment"+$(val).attr("attr-block");
            formData.append('file', $("#"+inputName)[0].files[0]);
        }else{
            var inputName="video-file";
            formData.append('file', $('input[type=file]')[0].files[0]);
        }
        if(filename!=""){
            $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    docType=$(val).attr("attr-doctype");
                    if(docType=="video"){
                        var progressbar = $("<div>", {style: "background:#607D8B;height:10px;margin:10px 0;"}).appendTo("#video-results"); //create progressbar
                    }else{
                        let attr_id=$(val).attr("attr-block");
                        var progressbar = $("<div>", {style: "background:#607D8B;height:10px;margin:10px 0;"}).appendTo("#attachment-progressbar"+attr_id); //create progressbar
                    }
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                            progressbar.css("width", +percent + "%");
                        }
                    }, true);
                }
                return xhr;
            },
            success: function (data) {
                if (data) {
                    var url = ($(data).find("Key").text()); //get file locatio
                    var the_file_name = $(data).find("Key").text(); //get uploaded file name
                    if(the_file_name != ''){
                        let docType=$(val).attr("attr-doctype");
                        if(docType=="video"){
                            // $('#myAwsModal #' + success).show();
                            $('#myModal #video-results').hide();
                            $('#myModal #success').html('');
                            $('#myModal #success').append('<p style="color:green;">File Uploaded</p>')
                            $("#uploadedS3VideoFileName").val(url)
                        }else{
                            //alert("attachment Uploaded")
                            let attr_id=$(val).attr("attr-block")

                            $('#upload-block'+attr_id).html("")
                            $("#uploadedS3AttachedFileName"+attr_id).val(url)
                            $('#attachment-progressbar'+attr_id).html('<p style="color:green;">File Uploaded</p>');
                        }
                       
                    }
            //  $("#results").html("<span>File has been uploaded, Here's your file <a href=" + url + ">" + the_file_name + "</a></span>"); //response
         //   $('#myAwsModal input[name="' + fileid + '"]').val(the_file_name);
            
                }
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                $(val).prop("disabled",false)

                swal("Oops", "Something went wrong", "error");
            },

        });

        }else{
            $(val).prop("disabled",false)
            swal("Warning","File Name cannot be empty","warning")
        }
        }else{
            swal("Warning","Please upload a valid File Type","warning")
            $(val).prop("disabled",false)

        }

        

        

    }
$(document).ready(function () {
    let modelContent=$(".modal-body").html();
    $("#modal-body-hidden").val(modelContent);
    $("#myModal .datepicker").datepicker()
});

    $(document).on("click",".add-new",function(e){
        //alert("here")
        let modelContent=$(".modal-body-hidden").val();
        $("#myModal .modal-body").html(modelContent)
        $("#myModal #content_form")[0].reset()
        $("#myModal #myModalLabel").html("Add New Content")
        $("#myModal #id").val("")
        $("#expiry_date").datepicker({
            format: 'yyyy-mm-dd' 
        })
    })
    $(document).on("click",".edit",function(e){
        var self=this;
        // $("#expiry_date").datepicker({
        //     format: 'yyyy-mm-dd' 
        // })
        $.ajax({
            type: "get",
            url: '{{route("content-manager.videoOperations")}}',
            data: {"id":$(this).attr("data-id"),"operation":"getVideoDetail"},
            success: function (response) {
                var data=jQuery.parseJSON(response);
                let modelContent=$(".modal-body-hidden").val();
                $("#myModal .modal-body").html(modelContent)
                $("#myModal #content_form")[0].reset()
                $("#myModal #myModalLabel").html("Edit Content")
                $("#myModal").modal();
                $("#myModal input[name=id]").val($(self).attr("data-id"))
                if(data.data.attachment_title!=""){
                    $("#myModal input[name=title]").val(data.data.attachment_title)
                }
                setTimeout(() => {
                    if(data.data.expiry_date!="" || data.data.expiry_date!="undefined"){
                        $("#expiry_date").val(data.data.expiry_date)
                    }
                    

                }, 500);

            }
        });
        
        
        
    })
    $(document).on("click",".fa-copy",function(e){
        var self=this;
        var id=$(self).attr("attr-id")
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(id).select();
        document.execCommand("copy");
        $temp.remove();
        swal("Success","Copied the text","success")
    });
    $(document).on("click",".fa-refresh",function(e){
        var self=this;
        var id=$(self).attr("attr-id")
        swal({
                title: "Are you sure?",
                text: "A new key will be generated!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){

            if (isConfirm){
                
                $.ajax({
                    type: "get",
                    url: "{{route("content-manager.videoOperations")}}",
                    data: {"id":id,"operation":"refreshToken"},
                    success: function (response) {
                        var data=jQuery.parseJSON(response);
                        if(data.code==200){
                            let newKey=data.data;
                            $("#icon-"+id).html(newKey)
                            swal("Success","Key refreshed successfully","success")
                        }
                        
                    }
                });
                } else {
                    swal.close()
                }
            });
    })
    $(document).on("click",".removeattachment",function(e){
        var self=this;
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this process!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, cancel it!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){

        if (isConfirm){
            $.ajax({
            type: "get",
            url: "{{route("content-manager.videoOperations")}}",
            data: {"id":$(self).attr("data-id"),"created":$(self).attr("data-created"),
            "file":$(self).attr("data-file"),"operation":"removeAttachment"},
            success: function (response) {
                var data=jQuery.parseJSON(response);
                if(data.code==200){
                    location.reload()
                }
                
            }
        });
            } else {
            swal.close()
            }
        });
        
        

    })
    $(document).on("click",".delete",function(e){
        var self=this;

        swal({
    title: "Are you sure?",
    text: "You will not be able to undo this process",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    confirmButtonText: 'Yes, I am sure!',
    cancelButtonText: "No, cancel it!",
    closeOnConfirm: false,
    closeOnCancel: false
 },
 function(isConfirm){

   if (isConfirm){
        $.ajax({
            type: "get",
            url: "{{route("content-manager.videoOperations")}}",
            data: {"id":$(self).attr("data-id"),"operation":"removeAll"},
            success: function (response) {
                var data=jQuery.parseJSON(response);
                if(data.code==200){
                    swal("Success", "Removed successfully!", "success");
                    location.reload()
                }
                
            }
        });

    } else {
      //swal("Cancelled", "Your imaginary file is safe :)", "error");
      swal.close()
        
    }
 });
        
        

    })
</script>
<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }



    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }

    .test {
        margin-top: 6px;
        margin-left: 27px;
    }
.err{
    color: #dd4b39 !important;
}
a{
    color:#000;
}



.removeattachment{
    font-size:20px !important;
    color: #3c8dbc
}
.edit {
    font-size:20px !important;
    color:#3c8dbc;
}
.editbtn {
    font-size:20px !important;
    color:#3c8dbc;
    padding-right: 5px !important;
}
.delete{
    color:#3c8dbc
}
.fa-trash-o{
    font-size:20px !important
}

.fa-refresh {
    font-size:16px !important;
    cursor: pointer;
}

.fa-copy {
    font-size:16px !important;
    cursor: pointer;
}


</style>
@stop