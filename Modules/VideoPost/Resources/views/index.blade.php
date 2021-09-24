@extends('layouts.app')
@section('css')
@section('content')

<style>
#videoPostSubmit{
    margin-left: 25.5%;
}
#myProgress{  width: 100%;  background-color: grey; margin-top: 1em;}
#myBar {  width: 1%;  height: 30px;  background-color: green;}
.err{
    color: #dd4b39 !important;
}
.resize{
    resize: both;
    overflow: auto;
}
.upload-label{
    margin-left: 3%;
}
.table_title{
    margin-left: 1em;
}
small {
    font-size: 100%;
    font-weight: 400;
}
#upload_button{
    margin-top: 0.3em;
}
#video_post_file{
    margin-top: -2em;
}
.upload-video-file{
    margin-left: -0.5em;
}
</style>
<div class="table_title">
    <h4>Video Post</h4>
</div>
        {{ Form::open(array('url'=>'#','id'=>'videoPostForm','class'=>'form-horizontal', 'method'=> 'POST', 'novalidate'=>TRUE)) }}
        {{Form::hidden('video_file_name',null, ['id' => 'video_file_name',])}}
        {{ Form::hidden('id', null) }}

        <div class="form-group row" id="customer">
                <label for="customer_id" class="col-md-3 col-form-label">Choose Customer<span class="mandatory">*</span></label>
                <div class="col-sm-5">
                  {!!Form::select('customer_id',[null=>'Please Select'] + $project_list,null, ['class' => 'form-control customer_id','id'=>'customer_id'])!!}
                  <small class="help-block"></small>
                </div>
        </div>

        <div class="form-group row" id="videoPostType">
                <label for="type" class="col-md-3 col-form-label">Choose Video Post Type<span class="mandatory">*</span></label>
                <div class="col-sm-5">
                  {!!Form::select('type',[null=>'Please Select'] + $videoPostType, null, ['class' => 'form-control video_post_type','id'=>'type'])!!}
                  <small class="help-block"></small>
                </div>
        </div>

        <div class="form-group row"  id="file_name">
            <label for="file_name" class="col-form-label col-md-3">File Name<span class="mandatory">*</span></label>
            <div class="col-md-5">
              {!!Form::text('file_name',null, ['class' => 'form-control','id'=>'filename_id','placeholder'=>'Maximum 50 characters'])!!}
                <span class="help-block"></span>
            </div>
        </div>

        <div class="form-group row" id="videoPostFileType">
            <label for="file_type" class="col-md-3 col-form-label">Choose File Type<span class="mandatory">*</span></label>
            <div class="col-sm-5">
                {!!Form::select('file_type',[null=>'Please Select'] + $videoPostFileType,null, ['class' => 'form-control video_post_type','id'=>'file_type'])!!}
                <small class="help-block"></small>
            </div>
        </div>

         <div class="form-group row"  id="description">
            <label for="description" class="col-form-label col-md-3">File Description</label>
            <div class="col-md-5">
             {!! Form::textarea('description',null,['class'=>'form-control resize','id'=>'description_id', 'rows' => 6, 'cols' => 10, 'placeholder'=>'Maximum 500 characters']) !!}
                <span class="help-block"></span>
            </div>
        </div>

        <input type="hidden" name="video_url" id="video_url" value="">
        <input type="hidden" name="uploaded_date" id="uploaded_date" value="">

        {{ Form::close()}}

        <div class="form-group col-sm-12" id="video_post_file"><br/><br/>
                <form action="https://{{$uploadDet['my_bucket']}}.s3-{{$uploadDet['region']}}.amazonaws.com" method="post" id="aws_upload_video_form" class="form-horizontal" enctype="multipart/form-data">


                    <input type="hidden" name="acl" value="private">
                    <input type="hidden" name="success_action_status" value="201">
                    <input type="hidden" name="policy" value="{{$uploadDet['policybase64']}}">
                    <input type="hidden" name="X-amz-credential" value={{$uploadDet['amz_credentials']}}>
                    <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                    <input type="hidden" name="X-amz-date" value="{{$uploadDet['iso_date']}}">
                    <input type="hidden" name="X-amz-expires" value="{{$uploadDet['presigned_url_expiry']}}">
                    <input type="hidden" name="X-amz-signature" value="{{$uploadDet['signature']}}">
                    <input type="hidden" name="key" id="video-key" value="">
                    <input type="hidden" name="Content-Type" id="video-type" value="">


                    <div class="col-md-12 row">
                    <div class="col-md-3" >
                    <label for="fileUpload" class="col-form-label upload-label">Upload file<span class="mandatory">*</span></label><br>
                    </div>
                    <div class="col-md-4 upload-video-file">
                        <div>
                        <input class="form-control" type="file" id="video-file" name="file" />
                        <small id="file-error" class="help-block err nowrap"></small>
                        <div id="myProgress" style="display:none;">
                            <div id="myBar"></div>
                        </div>
                        <label id="uploaded_file_name"></label>
                        </div>
                    </div>
                    <div class="col-md-1" >
                        <div>
                        <input type="submit" name="submitbtn" id="upload_button" class="button btn btn-primary blue" value="Upload" />
                        </div>
                    </div>
                    </div>

                </form><br/>
                </div>

    <div class="div-button">
    {{ Form::submit('Save', array('class'=>' btn btn-primary ','id'=>'videoPostSubmit'))}}
    <a class="btn btn-primary blue" onclick="cancelVideoPost()">Cancel</a>
    </div>



@stop
@section('scripts')
<script>
     $(function () {
        $(".customer_id").select2();
        $('#videoPostSubmit').on('click', function(event) {
            event.preventDefault();
            var customer_id=$('#customer_id').val();
            var filename=$('#filename_id').val();
            var video_path=$("#video_url").val();
            if(customer_id == ""){
                swal("Warning","Please select customer","warning");
            }
            else if(filename == ""){
                swal("Warning","Filename is mandatory","warning");
            }
            else if(video_path == ""){
                swal("Warning","Please upload video file","warning");
            }
            else{
                $('#videoPostForm').submit();
            }

        });



        $('#videoPostForm').submit(function (e) {
            e.preventDefault();
            var id=$('#customer_id').val();
            console.log(id);
            var $form = $(this);
            var formData = new FormData($('#videoPostForm')[0]);
            var url = "{{route('videopost.store')}}";

            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                        if($('input[name="id"]').val())
                            {
                                var text= "Video Post has been updated successfully";
                                var status = "Updated";
                                swal("Updated", "Video Post has been updated successfully", "success");
                            }else{
                                var text= "Video Post has been saved successfully";
                                var status = "Saved";
                            }
                            swal({
                            title: status,
                            text: text,
                            type: "success",
                            confirmButtonText: "OK",
                            },function(){
                                $('.form-group').removeClass('has-error').find('.help-block').text('');
                                window.location='{{ route('videopost.summary') }}';
                            });
                    } else {
                        console.log(data);
                        swal("Oops", "Video Post save was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });


        var data={!!json_encode($result)!!};
        var videoPostId={!!json_encode($id)!!};

        if(data){
            $('select[name="customer_id"] option[value="'+data[0].customer_id+'"]').prop('selected',true);
            $(".customer_id").select2();
            $('select[name="type"]').val(data[0].type);
            $('input[name="file_name"]').val(data[0].file_name);
            $('select[name="file_type"]').val(data[0].file_type);
            $('textarea[name="description"]').val(data[0].description);
            $('#videoPostForm').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var progressDiv = document.getElementById("myProgress");
            progressDiv.style.display="none";
            $('#uploaded_file_name').text(data[0].video_path)
            var video_file_url=data[0].video_path;
            $('#video_file_name').val(video_file_url);
            $('#video_url').val(video_file_url);
            $('#uploaded_file_name').css('font-weight',500);
            $('input[name="id"]').val(videoPostId);
            $('#uploaded_date').val(data[0].video_uploaded_date);
        }



        $('#upload_button').on('click', function(event) {
            event.preventDefault();

            the_file = $("#video-file")[0].files[0];
            var allowedExtensions = /((\.mp4)|(\.pdf))$/i;
            var videoExtensions = /(\.mp4)$/i;
            if(the_file !==undefined)
            {
                if (!allowedExtensions.exec(the_file.name)) {
                    $('#file-error').text('Invalid file format. Supported format Mp4 with resolution 1920*1080 and below or PDF.');
                    return false;
                } else if(videoExtensions.exec(the_file.name)) {
                    const url = URL.createObjectURL(the_file);
                    const $video = document.createElement("video");
                    $video.src = url;
                    $video.addEventListener("loadedmetadata", function () {
                    console.log("width:", this.videoWidth);
                    console.log("height:", this.videoHeight);
                    var video =[];
                    video['height']=this.videoHeight;
                    video['width']=this.videoWidth;
                    sessionStorage.setItem("width", video['width']);
                    sessionStorage.setItem("height", video['height']);
                });
                } else {
                    sessionStorage.setItem("width", 5);
                    sessionStorage.setItem("height",5);
                }

                $('#aws_upload_video_form').submit();
            }

        });


        $("#aws_upload_video_form").submit(function(e) {
            e.preventDefault();
            the_file = $("#video-file")[0].files[0];

                if(the_file !==undefined)
                {
                    var width = sessionStorage.getItem("width");
                    var height = sessionStorage.getItem("height");
                    if(width > 1920 || height > 1080){
                        $('#file-error').text('Invalid file format. Supported format Mp4 with resolution 1920*1080 and below.');
                        return false;
                    }
                    else{
                        $('#file-error').text('');
                        var today = {!! json_encode($today) !!};
                        var contentname = Date.now() + '.' + the_file.name.split('.').pop();
                        var filename = 'temp/VideoPost/'+today + '/' + contentname;

                        $("#video-key").val(filename);
                        $("#video_url").val(filename);
                        $("#uploaded_date").val(today);
                        $("#video_file_name").val(contentname);
                        $("#video-type").val(the_file.type);
                        var results =  'video-results';
                        var progressDiv = document.getElementById("myProgress");
                        progressDiv.style.display="block";
                        var progressBar = document.getElementById("myBar");
                        var post_url = $(this).attr("action");
                        var form_data = new FormData(this); //Creates new FormData object
                        $.ajax({
                            url : post_url,
                            type: 'post',
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
                            swal("Success", "File uploaded successfully", "success");

                        });
                    }


                }
            });


});
function cancelVideoPost(){
    window.location='{{ route('videopost.summary') }}';
}

</script>
@stop
