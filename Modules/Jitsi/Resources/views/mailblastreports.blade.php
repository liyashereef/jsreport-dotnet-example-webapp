@extends('layouts.app')
@section('css')
    <style>
        .control-label{
            font-weight: bold;
            text-transform: uppercase;
        }
        .failed{
            color: red;
            font-weight: bold
        }
        .readMore{
            height: 100px;
            overflow-y:hidden;
        }
        .showall{
            overflow-y:auto;

        }
        .hidediv{
            display: none;
            color: #003A63;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;

        }

        .showallbutton{
            color: #003A63;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;

        }
    </style>
@endsection
@section('content')
<form  name="blastform" id="blastform">
    <div class="container_fluid" style="padding: 10px">
    <div class="row">
        <div class="col-md-10 table_title">
               <h4>Blast Communication - Reports</h4>
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
    {{-- <div class="row">
        <div class="col-md-2 mb-4">
            Mail From
        </div>
        <div class="col-md-8">
            <select name="mail_from"
            class="form-control" id="mail_form" >
                <option value=""></option>
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
    </div> --}}
    <div class="row" style="margin-top: 10px">
        <div class="col-md-1 mb-4" style="padding-top: 5px">
            Start Date
        </div>
        <div class="col-md-2">
            <input name="start_date" id="start_date"
            class="form-control" value="{{date('Y-m-d', strtotime('-15 day', strtotime(date("Y-m-d"))))}}" cols="30" rows="10" />
        </div>
        <div class="col-md-1 mb-4" style="text-align: right;padding-top: 5px">
            End Date
        </div>
        <div class="col-md-2">
            <input name="end_date" id="end_date"
            class="form-control" value="{{date("Y-m-d")}}" cols="30" rows="10" />
        </div>
        <div class="col-md-1 mb-4" style="text-align: right;padding-top: 5px">
            Roles
       </div>
       <div class="col-md-2">
           <select name="groups[]"
           class="form-control" id="groups" multiple>
               <option value=""></option>
               @foreach ($allURoles as $key=>$value)
                   <option value="{{$key}}">{{$value}}</option>
               @endforeach
       </select>
       </div>
       <div class="col-md-1 mb-4" style="text-align: right;padding-top: 5px">
        Clients
    </div>
    <div class="col-md-2">
        <select id="clients" name="clients[]" placeholder="Select a Project" multiple>
            <option value=""></option>

            @foreach ($customers as $value)
                <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
            @endforeach

        </select>
    </div>
    </div>

    <div class="row">
        
    </div>

    <div class="row">
        <div class="col-md-10 mb-4">

        </div>
        <div class="col-md-2" >
            <button type="button" style="float: right;width:200px !important" class="form-control btn btn-primary sendmessage">Search</button>
        </div>
    </div>
    <div class="row" style="margin-top:10px">
        <div class="col-md-12" id="dTable">
            <table class="table table-bordered" id="mail-table">
                <thead>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>Roles</th>
                    <th>Client</th>
                    <th>Sender</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
</form>
<div class="modal fade"
    id="myModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>

            <div class="modal-body">
                <div class="form-group" id="subgroup">
                    <label for="subjcol" class="col-sm-3 control-label">Subject :</label>
                    <div class="col-sm-9" id="subjcol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="empgroup">
                    <label for="rolecol" class="col-sm-3 control-label">Employee Group :</label>
                    <div class="col-sm-9" id="rolecol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="client">
                    <label for="clientcol" class="col-sm-3 control-label">Clients :</label>
                    <div class="col-sm-9" id="clientcol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="clientgroup">
                    <label for="clientgroupcol" class="col-sm-3 control-label">Client Groups :</label>
                    <div class="col-sm-9" id="clientgroupcol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="usersassociated">
                    <label for="usersassociatedcol" class="col-sm-3 control-label">Recipients :</label>
                    <div class="col-sm-9" id="usersassociatedcol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="msg">
                    <label for="msgcol" class="col-sm-3 control-label">Message :</label>
                    <div class="col-sm-9" id="msgcol">
                        
                        <small class="help-block"></small>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<div id="detailedView" class="container_fluid" style="padding: 10px;display: none">
    <table class="table table-bordered">
        <thead>
            <th colspan="2" style="color: #fff;padding:5px;height:40px">
                <p style="float: right;cursor: pointer;width:30px;padding-top:10px" class="closedialog">x</p>
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight:bold;width: 20%;padding:5px">Subject</td>
                <td id="subjcol" style="padding:5px"></td>
            </tr>
            <tr>
                <td style="font-weight:bold;width: 20%;padding:5px">Message</td>
            </tr>
            <tr>
                <td id="msgcol" style="padding:5px"></td>
            </tr>
            <tr>
                <td style="font-weight:bold;width: 20%;padding:5px">Employee Group</td>
                <td id="rolecol" style="padding:5px"></td>
            </tr>
            <tr>
                <td style="font-weight:bold;width: 20%;padding:5px">Clients</td>
                <td id="clientcol" style="padding:5px"></td>
            </tr>
            <tr>
                <td style="font-weight:bold;width: 20%;padding:5px">Client Groups</td>
                <td id="clientgroupcol" style="padding:5px"></td>
            </tr>
        </tbody>
    </table>
    

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
</script>
    <script>
        $(document).ready(function () {
            $("#start_date").datepicker({format: 'yyyy-mm-dd' , maxDate: new Date() });
            $("#end_date").datepicker({format: 'yyyy-mm-dd' , maxDate: new Date() });
        });
        $('#clients').select2({
            placeholder: "Please Select a Client "
        });
        $('#client_groups').select2({
            placeholder: "Please Select a Client Group"
        });
        $('#groups').select2({
            placeholder: "Please Select a Role Group"
        });

        $(document).on("click",".sendmessage",function(e){
            e.preventDefault();
            var userRoles= {!! json_encode($uRoles) !!};
            var assignedClients= {!! json_encode($customerArray) !!};
            
            try{
            $.ajax({
                type: "get",
                url: "{{ route('blastcomreport.list') }}",
                data: {
                    start_date: $("#start_date").val(),
                    end_date:  $("#end_date").val(),
                    groups:  $("#groups").val(),
                    clients:  $("#clients").val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $("#dTable").html(response).after(function(e){
                        $("#mail-table").DataTable({
                            "columnDefs": [
                                { "width": "10%", "targets": 0 },
                                { "width": "10%", "targets": 1 },
                                { "width": "20%", "targets": 2 },
                                { "width": "15%", "targets": 3 },
                                { "width": "10%", "targets": 5 }
                            ]
                        })
                    })
                }
            });
        } catch(e){
            console.log(e.stack);
        }
        })

        $(document).ready(function () {
            $(".sendmessage").trigger("click")
        });

        $(document).on("click",".viewMessage",function(e){
            e.preventDefault();
            // $("#blastform").hide()
            let attrid=$(this).attr("attr-id")
            $("#myModal").modal();

            $.ajax({
                type: "get",
                url: "{{ route('mailblast.detailedview') }}",
                data: {
                    id:attrid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var jqdata = jQuery.parseJSON(response);
                    $("#myModal #subjcol").html(jqdata.subject)
                    $("#myModal #msgcol").html(jqdata.message)
                    if(jqdata.rolesText!==""){
                        $("#myModal #empgroup").show();
                        $("#myModal #rolecol").html(jqdata.rolesText)
                    }else{
                        $("#myModal #empgroup").hide();
                        $("#myModal #rolecol").html("")
                    }

                    if(jqdata.clientsText!==""){
                        $("#myModal #client").show();
                        $("#myModal #clientcol").html(jqdata.clientsText)
                    }else{
                        $("#myModal #client").hide();
                        $("#myModal #clientcol").html("")
                    }

                    if(jqdata.clientGroups!==""){
                        $("#myModal #clientgroup").show();
                        $("#myModal #clientgroupcol").html(jqdata.clientGroups)
                    }else{
                        $("#myModal #clientgroup").hide();
                        $("#myModal #clientgroupcol").html("")
                    }

                    if(jqdata.userContent!==""){
                        $("#myModal #usersassociated").show();
                        $("#myModal #usersassociatedcol").html(jqdata.userContent)
                    }else{
                        $("#myModal #usersassociated").hide();
                        $("#myModal #usersassociatedcol").html("")
                    }

                    $("#myModal #detailedView").show()
                },
                error: function (jqXHR, exception) {
                    $("#blastform").show()

                }
            });

        })
        

        $(document).on("click",".closedialog",function(e){
            e.preventDefault();
            $("#detailedView").hide()
            $("#blastform").show()
        })

        $(document).on("click",".showallbutton",function(e){
            e.preventDefault();
            $("#readMore").removeClass("readMore")
            $("#readMore").addClass("showall")
            $("#hidediv").show();
            $(this).hide();
        })
        $(document).on("click",".hidediv",function(e){
            e.preventDefault();
            $("#readMore").addClass("readMore")
            $("#readMore").removeClass("showall")
            $(".showallbutton").show();
            $(this).hide();
        })
    </script>
@endsection
