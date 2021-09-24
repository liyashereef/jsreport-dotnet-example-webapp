@extends('layouts.app')
@section('content')
<form  name="blastform" id="blastform">
    <div class="container_fluid" style="padding: 10px">
    <div class="row">
        <div class="col-md-10 table_title">
               <h4>Blast Communication</h4>
        </div>
        <div class="col-md-2 mb-4" >
            <button class="btn btn-primary conferencebutton" style="display: none;float: right;">
                Switch to Conference
            </button>
            {{-- <button class="btn btn-primary archivebutton" style="float: right"> --}}
                {{-- Switch to Archives --}}
            {{-- </button> --}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">
            Mail From
        </div>
        <div class="col-md-8">
            <select name="mail_from"
            class="form-control" id="mail_from" >
                <option value="0">Default</option>
                @foreach ($emailMasters as $emailMaster)
                    <option
                    @if ($emailMaster->default==1)
                        selected
                    @endif
                    value="{{$emailMaster->id}}">{{$emailMaster->display_name}}-
                        {{$emailMaster->email_address}}</option>
                @endforeach
        </select>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-md-2 mb-4">
            Mail From
        </div>
        <div class="col-md-8">
            <select name="mail_fromaddress" id="mail_fromaddress" class="form-control">
                <option value="0">Default</option>
            </select>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-md-2 mb-4">
            Subject
        </div>
        <div class="col-md-8">
            <input name="mail_subject" id="mail_subject"
            class="form-control" id="" cols="30" rows="10" />
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">
            Select Recipients
       </div>       
       <div class="col-md-2 mb-4">
        <input type="checkbox" name="individual_mail" id="individual_mail" /> <label for="individual_mail">Individual Mail</label>
       </div><div class="col-md-2 mb-4">
            <input type="checkbox" name="employeegroup_mail" id="employeegroup_mail" /> <label for="employeegroup_mail">Employee Group</label>
        </div><div class="col-md-2 mb-4">
            <input type="checkbox" name="clientgroup_mail" id="clientgroup_mail" /> <label for="clientgroup_mail">Client Group</label>
    </div>       
        <div class="col-md-2 mb-4">

        </div>       
        <div class="col-md-2 mb-4">

        </div>       
        
    </div>
    <div class="row">
        <div class="col-md-2  mb-4">
            Enter Email Address
        </div>  
        <div class="col-md-8">
            <select name="email_address[]" id="email_address" multiple></select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">
             Employee Group
        </div>
        <div class="col-md-8">
            <input type="hidden" name="clientRole" id="clientRole" value="{{$clientRole->id}}" />
            <select name="groups[]"
            class="form-control" id="groups" multiple>
                <option value="-1">All Roles</option>
                @foreach ($allURoles as $key=>$value)
                    <option value="{{$key}}">{{$value }}</option>
                @endforeach
        </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">
            Client Groups
        </div>
        <div class="col-md-8">
            <select name="client_groups[]"
            class="form-control" id="client_groups" multiple>
                @foreach ($emailGroups as $emailGroup)
                    <option value="{{$emailGroup->id}}">{{$emailGroup->group_name}}</option>
                @endforeach
        </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">
            Clients
        </div>
        <div class="col-md-8">
            <select id="clients" name="clients[]" placeholder="Select a Project" multiple>

                @foreach ($customers as $value)
                    <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                @endforeach

            </select>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-2 mb-4">
            Message
        </div>
        <div class="col-md-8">
            <textarea name="message"
            class="form-control messageeditor" id="message" cols="30" rows="10"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 mb-4">

        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary sendmessage">Send</button>
        </div>
    </div>
</div>
</form>

@endsection
@section('scripts')
<script src="https://cdn.tiny.cloud/1/kvbj2natfu87vr05si92c6uxcroyan8hp5xonlfjadzf9mt2/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script type="text/javascript">
    tinymce.init({
        selector: 'textarea.messageeditor',
        height: 480,
        menubar: false,
        branding: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_css: '//www.tiny.cloud/css/codepen.min.css'
    });

        

        $(document).on("click",".sendmessage",function(e){
            e.preventDefault();
            let email_address=$("#email_address").val();
            let groups=$("#groups").val();
            let client_groups=$("#client_groups").val();
            let clients=$("#clients").val();
            if(email_address=="" && groups==""  && client_groups==""  && clients=="" )
            {
                swal("Warning","Please choose any of the combination","warning")
            }
            else{
            let mail_subject=$("#mail_subject").val();
            let mail_message=tinymce.get("message").getContent();
            if(mail_subject=="" || mail_message==""){
                swal("Warning","Subject/Message cannot be empty","warning")
            }else{
            $('body').loading({
                   stoppable: false,
                   message: 'Please wait...'
               });
            var content = tinymce.get("message").getContent();

            let formData=$("#blastform").serialize();
            formData = formData.concat([
                {name: "customer_id", value: "test content"}
            ]);
            $.ajax({
                type: "post",
                url: '{{route("mailblast.saveblastmail")}}',
                data: {
                    "individual_mail":$("#individual_mail").is(":checked"),
                    "employeegroup_mail":$("#employeegroup_mail").is(":checked"),
                    "clientgroup_mail":$("#clientgroup_mail").is(":checked"),
                    "mail_subject":$("#mail_subject").val(),
                    "groups":$("#groups").val(),
                    "email_address":$("#email_address").val(),
                    "client_groups":$("#client_groups").val(),
                    "clients":$("#clients").val(),
                    "message":tinymce.get("message").getContent(),
                    "mail_from":$("#mail_from").val()

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('body').loading('stop');
                    swal("Success","Completed successfully","success")
                    setTimeout(() => {
                        // location.reload()
                    }, 500);
                    swal({ 
                        title: "Success",
                        text: "Sent successfully",
                        type: "success" 
                    },
                    function(){
                        location.reload()
                    });

                }
                });
            }
        }
        })

        $(document).ready(function () {
            $('#clients').select2({
            placeholder: "Please Select a Client ",
            allowClear: true // This is for clear get the clear button if wanted 

            });
            $('#client_groups').select2({
                placeholder: "Please Select a Client Group",
                allowClear: true // This is for clear get the clear button if wanted 
            });
            $('#groups').select2({
                placeholder: "Please Select a Role Group",
                allowClear: true // This is for clear get the clear button if wanted 
            });
            $("#email_address").select2({
                placeholder: "Enter Email Address",
                tags : true,
                createTag: function(term, data) {
                    var value = term.term;
                    if(validateEmail(value)) {
                        return {
                        id: value,
                        text: value
                        };
                    }
                    return null;            
                }



            })
            $('#email_address').val('').prop('disabled','disabled')
            $('#clients').val('').prop('disabled','disabled')
            $('#client_groups').val('').prop('disabled','disabled')
            $('#groups').val('').prop('disabled','disabled')
        });

        function validateEmail(email) { 
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        } 

        $(document).on("click","#individual_mail",function(e){
            if($(this).is(":checked")==true){
                $('#email_address').val('').prop('disabled',false)

                $("#email_address").val("").select2({
                placeholder: "Enter Email Address",
                tags : true,
                createTag: function(term, data) {
                    var value = term.term;
                    if(validateEmail(value)) {
                        return {
                        id: value,
                        text: value
                        };
                    }
                    return null;            
                }



            })            
            }else{
                $('#email_address').val('').select2().prop('disabled','disabled')

            }
        })
        $(document).on("click","#employeegroup_mail",function(e){
            if($(this).is(":checked")==true){
                $('#groups').val('').prop('disabled',false).select2({
                    placeholder: "Please select a Role Group",
                    allowClear: true // This is for clear get the clear button if wanted 
                })
            }else{
                $('#groups').val('').select2().prop('disabled',true)

            }
        })

        $(document).on("click","#clientgroup_mail",function(e){
            if($(this).is(":checked")==true){
                $('#client_groups').val('').prop('disabled',false).select2({
                    placeholder: "Please select a Client Group",
                    allowClear: true // This is for clear get the clear button if wanted 
                })
                $('#clients').val('').prop('disabled',false).select2({
                    placeholder: "Please select a Client",
                    allowClear: true // This is for clear get the clear button if wanted 
                })
            }else{
                $('#client_groups').val('').select2().prop('disabled',true)
                $('#clients').val('').select2().prop('disabled',true)

            }
        })




    </script>
@endsection
