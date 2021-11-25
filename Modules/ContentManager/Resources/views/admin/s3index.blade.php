@extends('adminlte::page')
<style>
    .disabled{
        cursor: not-allowed;
        pointer-events: none;

    }
</style>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
            $(document).ready(function () {
                setInterval(() => {
                    let videoFile=$("#uploadedS3VideoFileName").val();
                            let uploadFile1=$("#iframe1").contents().find("#uploadedS3VideoFileName").val();
                            let uploadFile2=$("#iframe2").contents().find("#uploadedS3VideoFileName").val();
                            let uploadFile3=$("#iframe3").contents().find("#uploadedS3VideoFileName").val();
                            let uploadFile4=$("#iframe4").contents().find("#uploadedS3VideoFileName").val();
                            if(videoFile!="" || uploadFile1!="" || uploadFile2!="" || uploadFile3!="" || uploadFile4!="" ){
                                $("#mdl_save_change").removeClass("disabled")
                            }else{
                                $("#mdl_save_change").addClass("disabled")
                            }

                            //console.log(uploadFile.val());
                            if(uploadFile1!=""){
                                $("#uploadedS3AttachedFileName1").val(uploadFile1);
                            }
                            if(uploadFile2!=""){
                                $("#uploadedS3AttachedFileName2").val(uploadFile2);
                            }
                            if(uploadFile3!=""){
                                $("#uploadedS3AttachedFileName3").val(uploadFile3);
                            }
                            if(uploadFile2!=""){
                                $("#uploadedS3AttachedFileName4").val(uploadFile4);
                            }
                            // if(uploadFile2!=""){
                            // {
                            //     $("#uploadedS3AttachedFileName2").val(uploadFile2);
                            // }
                            // if(uploadFile3!=""){
                            // {
                            //     $("#uploadedS3AttachedFileName3").val(uploadFile3);
                            // }
                            // if(uploadFile4!=""){
                            // {
                            //     $("#uploadedS3AttachedFileName4").val(uploadFile4);
                            // }
                }, 500);
                        
            });
           

            $(document).on("click","#mdl_save_change",function(e){
                e.preventDefault();
                
                let valid = validateAttachment();
                let editId=$("input[name=id]").val();
                let message="";
                let submit=false;
                if(valid==true && editId==""){
                    //formSubmit($('#content_form'), "{{ route('content-manager.store') }}", null, e, message);
                    submitForm();
                }else if(editId>0 && valid==true){
                    //formSubmit($('#content_form'), "{{ route('content-manager.store') }}", nulll, e, message);
                    submitForm();
                }else{
                    swal("Warning","Please validate inputs/Click upload","warning")
                    //$('body').loading('stop');
                }

                
  

            })

            var submitForm=()=>{
                swal({
                    title: "Are you sure?",
                    text: "You are about to save the data. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-primary",
                    confirmButtonText: "OK",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    if(true){
                        submitFormAction()
                        
                    }else{
                        return false;
                    }
                });
            }

        var submitFormAction=function(){
            $.ajax({
                    url: "{{ route('content-manager.store') }}",
                    type: 'post',
                    data: {
                        "title":$("input[name=title]").val(), 
                        "uploadedS3VideoFileName":$("input[name=uploadedS3VideoFileName]").val(), 
                        "title_off_attachment1":$("input[name=title_off_attachment1]").val(), 
                        "title_off_attachment2":$("input[name=title_off_attachment2]").val(), 
                        "title_off_attachment3":$("input[name=title_off_attachment3]").val(), 
                        "title_off_attachment4":$("input[name=title_off_attachment4]").val(), 
                        "uploadedS3AttachedFileName1":$("input[name=uploadedS3AttachedFileName1]").val(), 
                        "uploadedS3AttachedFileName2":$("input[name=uploadedS3AttachedFileName2]").val(), 
                        "uploadedS3AttachedFileName3":$("input[name=uploadedS3AttachedFileName3]").val(), 
                        "uploadedS3AttachedFileName4":$("input[name=uploadedS3AttachedFileName4]").val(), 
                        "id":$("input[name=id]").val(), 
                        "expiry_date":$("input[name=expiry_date]").val()
                    },
                    
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                            // ... do something with the data...
                           // $('body').loading('stop');
                            let routeUrl="{{route("content-manager.view")}}";
                            swal({
                                title: "Uploaded",
                                text: "File Upload Completed",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "OK",
                                closeOnConfirm: false
                                },
                            function(){
                                location.href=routeUrl;
                            });
                    }
                });
        }
    var invokeAfterUpload=(url)=>{
        //$("#uploadedS3VideoFileName").trigger("change");
        $("#uploadedS3VideoFileName").val(url);
        $("#mdl_save_change").removeClass("disabled");
    }

    validateAttachment=function(){
            let valid=0;
            let nodata=0;
            

            if($("#uploadedS3VideoFileName").val()!="" && $("input[name=title]").val()==""){
                valid++;
                
            }else{
                nodata++;
            }
            
            $('.uploadedS3AttachedFileName').each(function(i, obj) {
                let controlName=(obj.id).replace("uploadedS3AttachedFileName","time_off_attachment");
                let textControlName=(obj.id).replace("uploadedS3AttachedFileName","title_off_attachment");
                let attachmentControl=$(this).val()
                let textControl=$("input[name="+textControlName+"]").val()
                if(attachmentControl!="" && obj.value==""){
                   valid++;
                   nodata=0;
                }
                if(textControl=="" && obj.value!=""){
                   valid++;
                   nodata=0;
                }
                if(attachmentControl!=""){
                  //  nodata++;
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


</script>
@section('title', 'Add S3 Content')
<style>
    .uploaded-files{
        display: none !important;
    }
</style>
@section('content')
@php
    $hiddenField="uploadedS3VideoFileName";
    $hiddenFieldDocument="uploadedS3DocFileName";
    $uppyOptions = json_encode([
        "debug"=> true,
        "autoProceed"=> true,
        "allowMultipleUploads"=> true,
        "restrictions"=> [        
        "maxNumberOfFiles"=> 1,
        "allowedFileTypes"=>[".mp4",".MP4"],
        ]
    ],true);
    $uppyOptionsDocument = json_encode([
        "debug"=> true,
        "autoProceed"=> true,
        "allowMultipleUploads"=> true,
        "restrictions"=> [        
        "maxNumberOfFiles"=> 1
        ]
    ],true);
@endphp
    <div class="row">
        <div class="col-md-12">
            <span class="add-new-label">            
                <h4>Add New Content</h4>
                <input type="hidden" name="id" value="{{$contentId>0?$contentId:""}}">
            </span>

        </div>
    </div>
<form method="POST" name="content_form" id="content_form" enctype="multipart/form-data">
@csrf
<div class="row">
        <label for="title" class="col-sm-3 control-label">Video Title</label>
        <input type="hidden" name="optioncontrol" id="optioncontrol" value="{{json_encode($uppyOptions)}}" />
        <div class="col-sm-8">
            {{ Form::text('title',null,array('class' => 'form-control', 'Placeholder'=>'Title')) }}
            <small class="help-block"></small>
        </div>
</div>
        <div class="row mt-3">

        <label for="pan_mac" class="col-sm-3 control-label">Video file[MP4]</label>
        <div class="col-sm-2" style="height: 250px !important">
            <input type="hidden" name="uploadedS3VideoFileName" id="uploadedS3VideoFileName" />
            <x-input.uppy :hiddenField="$hiddenField"  :options="$uppyOptions"  />
            <small class="help-block"></small>
                <div class="col-sm-12"   style="background-color: aliceblue;"
id="video-results"><!-- server response here --></div>
            
            
        </div>
        
    </div>
    <div class="row blockrow1" style="margin-top: 49px !important;">
        <label for="pan_mac" class="col-sm-3 control-label">Document Title</label>
        @for ($i = 0; $i < 4; $i++)
            <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Title {{$i+1}}" name="title_off_attachment{{$i+1}}"  id="title_off_attachment{{$i+1}}">
                <input type="hidden" class="uploadedS3AttachedFileName" name="uploadedS3AttachedFileName{{$i+1}}" id="uploadedS3AttachedFileName{{$i+1}}" value="">
                <small class="help-block"></small>
            </div>
        @endfor
        
        
        
    </div>
    <div class="row mt-3" style="">

        <label for="pan_mac" class="col-sm-3 control-label">Attachment</label>
        @for ($i = 0; $i < 4; $i++)
        <div class="col-sm-2" style="height: 400px !important;overflow: hidden;">
            <iframe id="iframe{{$i+1}}" scrolling="no" src="{{route("content-manager.s3uploader",["id"=>$i+1])}}" style="width: 100% !important;height: 100% !important;" frameborder="0"></iframe>
        </div>
        <script>
            
        </script>
        @endfor
    </div>
    <div class="row " style="">
        <label for="title" class="col-sm-3 control-label">Expiry Date</label>
            <div class="col-sm-2">
                <input type="text" readonly name="expiry_date" id="expiry_date" value="{{$expiryDate!=null?$expiryDate:""}}" class="form-control datepicker" />
                <small class="help-block"></small>
            </div>
    </div>
    <div class="row mt-2" style="">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-3">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue disabled','id'=>'mdl_save_change'))}}
        </div>
    </div>


    
  

    
</form>
@endsection

<script src="{{ asset('js/app.js') }}" defer></script>
@section('scripts')
   
@endsection