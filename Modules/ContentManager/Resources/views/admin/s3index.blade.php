@extends('adminlte::page')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
            $(document).ready(function () {
                        setInterval(() => {
                            let uploadFile=$("#iframe1").contents().find("#uploadedS3VideoFileName");
                            //console.log(uploadFile.val());
                        }, 500);
            });
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
        "maxNumberOfFiles"=> 1
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
            </span>

        </div>
    </div>
<form  enctype="multipart/form-data">
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

    
  

    
</form>
@endsection

<script src="{{ asset('js/app.js') }}" defer></script>
@section('scripts')
    <script>
        
        


    </script>
@endsection