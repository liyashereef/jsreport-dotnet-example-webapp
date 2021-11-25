<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<style>
    .uploaded-files{
        display: none !important;
    }
</style>

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
<div class="container_fluid">
    <div class="row uploader">
        <div class="col-md-3">
            <form  enctype="multipart/form-data">
            @csrf

                        <input attr-block="{{$blockId}}" type="hidden" name="uploadedS3VideoFileName" id="uploadedS3VideoFileName" />
                        <x-input.uppy :hiddenField="$hiddenField"  :options="$uppyOptions"  />    
            </form>
        </div>
    </div>
</div>
<script>
    // $(document).on("change",".uploadedS3VideoFileName",function(e){
    //     e.preventDefault();
    //     alert("changed")
    // })
    
    $(document).on("change","#contentname",function(e){

    })

 
    var invokeAfterUpload=(url)=>{
        $("#uploadedS3VideoFileName").val(url);
        
    }
</script>
<script src="{{ asset('js/app.js') }}" defer></script>