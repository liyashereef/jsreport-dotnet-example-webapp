@extends('layouts.content-manager-layout')
@section('content')
    @if (count($videoList)>0 && count($attachmentList)>0)
        <div class="row">
            <div class="col-md-12">
                <table class="table " style="height: 500px">
                    <thead class="borderless">
                        <th class="borderless table_title" style="width: 75%">
                            <h4>{{$videoList[0]["attachment_title"] }}</h4>
                        </th>
                        <th class="borderless table_title">
                            <h4>Download Attachments</h4>
                        </th>
                    </thead>
                    <tbody>
                        <tr class="borderless">
                            <td class="borderright spacingclass" style="border-top: 0px !important">
                                <video controls src="{{$videoList[0]["attachment_file"] }}" style="border: solid"
                                width="100%" height="" controlsList="nodownload"></video>

                            </td>
                            <td class="borderless  spacingclass">
                                @foreach ($attachmentList as $value)
                                    <div class="col-md-12 attachmenttextright" >
                                        <a target="_blank" href="{{($value["attachment_file"])}}">
                                            {{($value["attachment_title"])}}
                                        </a>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    @elseif (count($videoList)>0 && count($attachmentList)==0)
    <div class="row">
            
        <div class="col-md-9 table_title" >
            <h4>{{$videoList[0]["attachment_title"] }}</h4>

            <video controls src="{{$videoList[0]["attachment_file"] }}" width="100%" height="94%" controlsList="nodownload"></video>
        </div>
        
    </div>
    @elseif (count($videoList)==0 && count($attachmentList)>0)
    <div class="row">
            
        
        <div class="col-md-12">
            <div class="container_fluid">
                <div class="row">
                    <div class="col-md-12 table_title">
                        <h4>
                            Download Attachments
                        </h4>
                    </div>
                    @foreach ($attachmentList as $value)
                        <div class="col-md-2 attachmenttext " >
                            <i class="fa fa-file-o docclass"></i>&nbsp;&nbsp;
                            <a target="_blank" href="{{($value["attachment_file"])}}">
                                {{($value["attachment_title"])}}
                            </a>
                        </div>
                    @endforeach
                    
                </div>
            </div>
        </div>
    </div>
    @endif
    <!--<div class="apply-job">COMMISSIONAIRES GREAT LAKES<br><span class="apply-span"> Candidate Tracking System (CTS)</span></div>-->
<style>
    body {
    background: #ffffff !important;
    }
    #login-trigger-btn:disabled {
    background-color: gray;
}
.table_title h4{
    font-size:18pt !important
}
ul li label{
    cursor: pointer;
}
.navbar{
    /* display: none; */
}
audio:focus {
    outline: none;
}

input[type=checkbox]{
    width:16px;
    height: 16px;
    vertical-align: text-top;
}
.content-submit-btn {
    width: 30%;
    border-radius: 5px;
    box-shadow: none;
    /*background: #E4E432;*/
    background: #ea660f;
    font-weight: bold;
    margin-top: 18px;
    cursor: pointer;
    color: #ffffff;
    border: 0;
    padding: 5px 0px;
}
.attachmenttext{
    height: 200px;
}

.attachmenttextright{
    /* height: 50px; */
    padding-left: 0px !important;
}

a{
    color: #000

}
a:hover{
    color:#000
}

.borderless  {
    border: none !important;
}
.borderright{
    border-right: solid 1px #000 !important;
}
.spacingclass{
    padding-top: 0px !important;
    padding-bottom: 0px !important

}
</style>
<script>


    $('#content-submit-btn').click(function (e) {
        e.preventDefault();
        var $form = $(this).parents('form');
        //var $form = $(this);
        var formData = new FormData($form[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "videos/validatelogin",
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    let vKey=$("#c_content_id").val()
                    var sites = {!! json_encode(route("content-manager.listcontentvideos",["key"])) !!};
                    sites =sites.replace("key", vKey);  
                    location.href = sites


                } else {
                    swal("Wrong inputs", "Please check credentials and try again", "info");
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });

    function showDocAlert() {
        swal({
            title: 'Prepare documents',
            text: "Please begin the application process when you\'ve scanned these documents to your PC",
            type: "info",
            showCancelButton: false,
            confirmButtonText: "OK",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        });
    }
</script>
@endsection
