@extends('layouts.app')
@section('css')
<style>
        .editbutton{
                float: right;
                cursor: pointer;
        }
        .inputclass{
                display: none;
        }
        .col-md-4{
                display: inline-block !important;
        }

        .col-md-1{
                display: inline-block !important;
        }
        .updateamendment{
                cursor: pointer;
        }

</style>

@endsection
@section('scripts')
        <script type="text/javascript">
$("#yesbar").hide();
$(document).on("click","#reqinfo",function(e){
        $(this).hide()
        $(".viewreq").toggle()
        $("#yesbar").toggle();
})
$("#uploadcontract").on('click',function(evt){
            $('label').html("");
            evt.preventDefault();

            var hiddeninput = $(this).attr("attr_hidden");
            if( $("#cmuf_contract_document").val()!="")
            {
                    var formulario = $('#uploadform-data')[0];
                    var formData = new FormData(formulario);
                    var self =this;

                    formData.append("upload_file","cmuf_contract_document");
                    $.ajax({
                            type: "post",
                            url: "{{route('contracts.attachfile')}}",
                            data: formData,
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                    console.log(response)
                                    
                                    if(parseInt(response)>0)
                                    {
                                        let data = {contract_attachment_id:response};
                                        let jsondata = JSON.stringify(data);
                                        saveblocks(jsondata);

                                    }
                                    $(".uploaderror").html("");

                            }
                    }).fail(function(data){
                        var response = JSON.parse(data.responseText);

                        $(".uploaderror").html("");

                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString);
                        });

                });


            }
            else if($("#cmuf_contract_document").val()=="" )
            {
                        $('label[for="cmuf_contract_document"]').html("*Contract cannot be empty");
            }
            else{

                 $("#uploadform").slideUp();
                 //$('label[for="yes_no"]').html("*Please choose Yes and proceed");
                 swal("Warning", "Selection should be yes to proceed", "warning");


            }

        });
        $(document).on("click",".updateamendment",function($q){
                if($(this).attr("attr-text")!=""){
                        $("#amendment_description").val($(this).attr("attr-text"))
                        $("#editamendment").val($(this).attr("attr-id"))
                }
        })
        $(document).on('change','#supervisorassigned',function(event){
                
                var choice = $(this).val();
                if(choice == 1)
                {

                        $("#supervisorornot").css('display','block');
                        refreshSideMenu();
                }
                else
                {
                        $("#supervisorornot").css('display','none');
                }
        });
        $(".inputclass").hide();
        $(".markupval").on("keyup",function(e){
                var average_billrate = $("#average_billrate").val();
                var average_wagerate = $("#average_wagerate").val();
                var calcfield = 0;
                if(average_wagerate!="" && average_wagerate!=""){
                        calcfield = ((parseFloat(average_billrate)/parseFloat(average_wagerate))-1)*100 ;
                        if(calcfield.toFixed(2)<0){
                                $("#average_markup").val("0");
                        }else{
                                $("#average_markup").val(calcfield.toFixed(2));
                        }
                }
        })
        $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
                $("#uploadamend").on("click",function(event){
                        if($("#amendment_description").val()=="" && $("#amendment_attachment_id").val()=="" ){
                                swal("Warning", "Enter description or attach a file", false);
                        }else{
                                amendfileupload(event,this);
                        }


                });
                $(document).on('click','#multidivision',function(){
                var value = $(this).val();
                if(value == true){
                        $("#division_lookup").prop("disabled",false);
                        $("#leaddiv").slideDown("slow");
                }
                else{
                        $("#division_lookup").prop("disabled",true);
                        $("#leaddiv").slideUp("slow");
                }
        });

        $(document).on("click",".clientrem",function(e){
                var contractid = $(this).attr("attr-contractid");
                var clientid = $(this).attr("attr-id");
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    if(true){
                        var self = this;
                        $.ajax({
                        type: "post",
                        url: "{{route('contracts.removeclient')}}",
                        data:{contractid:contractid,clientid:clientid},
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                                var data = jQuery.parseJSON(response);
                                var successflag = "success";
                                if(data.success==false){
                                        successflag = "warning";
                                }

                                if (data.success) {
                                        swal(data.message, "Client has been removed successfully", successflag);
                                        $("#client-"+clientid).remove();

                                }else{
                                        swal(data.message, "The request has been cancelled", successflag);
                                }
                        }
                });
                    }
                });

        })
        $("#clientinfo").on("click",function(e){
                $("#primary_contact").val("").trigger('change.select2');
                $("#contact_name_1").val("");
                $("#contact_jobtitle_1").val("");
                $("#contact_emailaddress_1").val("");
                $("#contact_phoneno_1").val("");
                $("#contact_cellno_1").val("");
                $("#contact_faxno_1").val("");
                $(".clientcontactinformation").toggle();
        });

        $("#cancelclient").on("click",function(e){
                $("#clientinfo").trigger("click")
        })

        $("#addmoreclient").on("click",function(e){

                var contractid = $("#primary_contact").val();
                var clientuserid = $("#primary_contact").val();
                var clientname = $("#contact_name_1").val();
                var jobtitle = $("#contact_jobtitle_1").val();
                var emailaddress = $("#contact_emailaddress_1").val();
                var phoneno = $("#contact_phoneno_1").val();
                var cellno = $("#contact_cellno_1").val();
                var faxno = $("#contact_faxno_1").val();
                if(clientuserid==""){
                        swal("Warning", "Client cannot be empty", "warning");
                }else if(jobtitle==""){
                        swal("Warning", "Position cannot be empty", "warning");
                }
                else if(emailaddress==""){
                        swal("Warning", "Email address cannot be empty", "warning");
                }
                else if(phoneno==""){
                        swal("Warning", "Phone number cannot be empty", "warning");
                }else if(cellno==""){
                        swal("Warning", "Cell number cannot be empty", "warning");
                }else{
                        $.ajax({
                                type: "post",
                                url: "{{route('contracts.addmoreclient')}}",
                                data: {contractid:$(this).attr("attr-contractid"),clientuserid:clientuserid,
                                clientname:clientname,jobtitle:jobtitle,emailaddress:emailaddress,phoneno:phoneno,cellno:cellno,
                                faxno:faxno},
                                headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var data = jQuery.parseJSON(response);
                                     var successflag = "success";
                                     if(data.success==false){
                                        successflag = "warning";
                                        }

                                     if(data.code=="200"){

                                              location.reload();


                                     } else{
                                        swal("Warning", "There is some error adding record", "warning");
                                     }
                                }
                        });
                }

        })

        $("#primary_contact").on('select2:select',function(){
                var countattrtibute = 1;
                if($(this).val()=="")
                {
                        $("#contact_jobtitle_"+countattrtibute).val("");
                        $("#contact_name_"+countattrtibute).val("");
                        $("#contact_emailaddress_"+countattrtibute).val("");
                        $("#contact_phoneno_"+countattrtibute).val("");
                        $("#contact_cellno_"+countattrtibute).val("");
                        $("#contact_faxno_"+countattrtibute).val("");
                }
                else
                {
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {

                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;

                                     $("#contact_jobtitle_"+countattrtibute).val("Client");
                                     $("#contact_name_"+countattrtibute).val(primarycontact.name);
                                     $("#contact_emailaddress_"+countattrtibute).val(primarycontact.email);
                                     $("#contact_phoneno_"+countattrtibute).val(primarycontact.officenumber);
                                     $("#contact_cellno_"+countattrtibute).val(primarycontact.cellnumber);
                                     $("#contact_faxno_"+countattrtibute).val(primarycontact.faxnumber);

                                }
                        });
                }
        });
        $(document).on('click','#masterentity',function(){
                var value = $(this).val();
                if(value == true){
                        $("#master_customer").prop("disabled",false);
                        $("#parentdiv").slideDown("slow");
                }
                else{
                        $("#master_customer").prop("disabled",true);
                        $("#parentdiv").slideUp("slow");
                }
        });
        $(document).on("click",".removeamendment",function(e){
                let self=this;
                swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
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
                        type: "post",
                        url: "{{route('contracts.removeAmendment')}}",
                        data: {"attachment_id":$(self).attr("attr-id"),
                        "attached_file_id":$(self).attr("attr-attachid") },
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                                let data=jQuery.parseJSON(response)
                                if(data.code==200){
                                        swal("Success","Removed successfully","success")
                                }else{
                                        swal("Warning","System issue","warning")

                                }
                                setTimeout(() => {
                                        location.reload()
                                }, 500);
                        }
                });

                } else {
                swal("Warning", "Ok", "warning");
                        swal.close()
                }
                });

                
        })
        $(document).on('click','#supinfo',function(){
                try {
                        let supervisorassigned={!! json_encode($contractdata->supervisorassigned)!!};
                        let supervisoremployeenumber={!! json_encode($contractdata->supervisoremployeenumber)!!};
                        let employeename={!! json_encode($contractdata->employeename)!!};
                        let viewtrainingperformance={!! json_encode($contractdata->viewtrainingperformance)!!};
                        let employeecellphone={!! json_encode($contractdata->employeecellphone)!!};
                        let employeeemailaddress={!! json_encode($contractdata->employeeemailaddress)!!};
                        let employeetelephone={!! json_encode($contractdata->employeetelephone)!!};
                        let employeefaxno={!! json_encode($contractdata->employeefaxno)!!};
                        let contractcellphoneprovider={!! json_encode($contractdata->contractcellphoneprovider)!!};
                        let supervisortabletrequired={!! json_encode($contractdata->supervisortabletrequired)!!};
                        let supervisorcgluser={!! json_encode($contractdata->supervisorcgluser)!!};
                        let supervisorpublictransportrequired={!! json_encode($contractdata->supervisorpublictransportrequired)!!};
                        let direction_nearest_intersection={!! json_encode($contractdata->direction_nearest_intersection)!!};
                        let department_at_site={!! json_encode($contractdata->department_at_site)!!};
                        let delivery_hours={!! json_encode($contractdata->delivery_hours)!!};
                        let supervisorcanmailbesent={!! json_encode($contractdata->supervisorcanmailbesent)!!};
                        let contractdeviceaccess={!! json_encode($contractdata->contractdeviceaccess)!!};
                        $("#supervisorassigned").val(supervisorassigned)
                        $("#supervisoremployeenumber").val(supervisoremployeenumber).select2()
                        $("#employeename").val(employeename)
                        $("#viewtrainingperformance").val(viewtrainingperformance)
                        $("#employeecellphone").val(employeecellphone)
                        $("#employeefaxno").val(employeefaxno)
                        $("#employeeemailaddress").val(employeeemailaddress)
                        $("#employeetelephone").val(employeetelephone)
                        $("#contractcellphoneprovider").val(contractcellphoneprovider)
                        $("#supervisortabletrequired").val(supervisortabletrequired)
                        $("#supervisorcgluser").val(supervisorcgluser)
                        $("#supervisorpublictransportrequired").val(supervisorpublictransportrequired)
                        $("#direction_nearest_intersection").val(direction_nearest_intersection)
                        $("#department_at_site").val(department_at_site)
                        $("#delivery_hours").val(delivery_hours)
                        $("#supervisorcanmailbesent").val(supervisorcanmailbesent)
                        $("#contractdeviceaccess").val(contractdeviceaccess)
                }
                 catch (error) {
                   console.log(error);
                }

                $(this).hide();
                $(".supinfolabel").toggle();
                $(".supinfoinput").css("display","block");
                $("#supervisorassigned").trigger("change")
        });
                var amendfileupload = function(event,self){

                var buttonid = self.id;
                var self = self;
                var input = $(self).attr("attr_file");

                var hiddeninput = $(self).attr("attr_hidden");

                var formulario = $('#editcmuf')[0];
                var formData = new FormData(formulario);
                formData.append("upload_amend","amendments");
                formData.append("upload_file","amendment_attachment_id");

                if($("#amendment_attachment_id").val()!="")
                {


                $.ajax({
                        type: "post",
                        url: "{{route('contracts.attachfile')}}",
                        data: formData,
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        success: function (response) {
                                $("#"+hiddeninput).val(response);
                                //$(this).hide();
                                //$("#"+buttonid).off('click');

                                $("#amendment_attachment_id").val("");

                                $.ajax({
                                        type: "post",
                                        url: "{{route('contracts.addContractamendment')}}",
                                        data: {"contract_id":{{$contractid}},
                                        "attachment_id":response,
                                        "amendmentdescr":$("#amendment_description").val()},
                                        headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                                $.ajax({
                                                        type: "get",
                                                        url: "{{route('contracts.getAmendmentlist')}}",
                                                        data: {"contract_id":{{$contractid}}},
                                                        headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        },
                                                        success: function (response) {
                                                                $("#amendment_description").val("");
                                                                $("#contractamendments").html(response)
                                                        }
                                                });
                                        }
                                });
                        }
                }).fail(function(response){
                        var responsetext = JSON.parse(response.responseText);
                        var errorString = '';
                        $.each( responsetext.errors, function( key, value) {
                        errorString +=  value ;
                        });
                        errorString += '';
                        $("label[for='amendment_attachment_id']").text(errorString);
                });
                }
                else{
                        $.ajax({
                                        type: "post",
                                        url: "{{route('contracts.addContractamendment')}}",
                                        data: {"contract_id":{{$contractid}},"attachment_id":"0","amendmentdescr":$("#amendment_description").val()},
                                        headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                                $.ajax({
                                                        type: "get",
                                                        url: "{{route('contracts.getAmendmentlist')}}",
                                                        data: {"contract_id":{{$contractid}}},
                                                        headers: {
                                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        },
                                                        success: function (response) {
                                                                $("#amendment_description").val("");
                                                                $("#contractamendments").html(response)
                                                        },
                                                        fail:function(response){
                                                                alert("error");
                                                        }
                                                });
                                        }
                                }).fail(function(e){
                                        alert("failed");
                                });
                }

        }
        $(".contractinfoinput").hide();
        $("#contractinfo").on("click",function(e){
                try {
                var reason_for_submission = {!! json_encode($contractdata->reason_for_submission)!!};
                $("#reason_for_submission").val(reason_for_submission).select2();

                
                }
                 catch (error) {
                   console.log(error);
                }

                $(this).hide();
                $(".contractinfolabel").toggle();
                $(".contractinfoinput").css("display","block");
        });
        $(".businessinfoinput").hide();
        $("#businessinfo").on("click",function(e){
                try {
                var business_segment = {!! json_encode($prepopulateddata["Businesssegmentid"])!!};
                $("#business_segment").val(business_segment).select2();
                var line_of_business = {!! json_encode($prepopulateddata["Businesslineid"])!!};
                $("#line_of_business").val(line_of_business).select2();
                var multidivision = {!! json_encode($prepopulateddata["multidivisioncontract"])!!};
                $("#multidivision[value='"+multidivision+"']").attr("checked","checked");
                if(multidivision==1){
                        $("#leaddiv").show();
                        $("#division_lookup").show().prop("disabled",false);
                        var division_lookup = {!! json_encode($prepopulateddata["LeadDivisionlookupid"])!!};
                        $("#division_lookup").val(division_lookup);
                }
                }
                 catch (error) {
                   console.log(error);
                }

                $(this).hide();
                $(".businessinfolabel").toggle();
                $(".businessinfoinput").css("display","block");
        });

        $(".regionalmanagerinfoinput").hide();
        $("#regionalmanagerinfo").on("click",function(e){
                try {
                        var area_manager = {!! json_encode($prepopulateddata["area_manager_id"])!!};
                        $("#area_manager").val(area_manager).select2();
                        var area_manager_position_text = {!! json_encode($prepopulateddata["area_manager_position_text"])!!};
                        $("#area_manager_position_text").val(area_manager_position_text).select2();
                        var area_manager_email_address = {!! json_encode($prepopulateddata["area_manager_email_address"])!!};
                        $("#area_manager_email_address").val(area_manager_email_address);
                        var area_manager_office_number = {!! json_encode($prepopulateddata["area_manager_office_number"])!!};
                        $("#area_manager_office_number").val(area_manager_office_number);
                        var area_manager_cell_number = {!! json_encode($prepopulateddata["area_manager_cell_number"])!!};
                        $("#area_manager_cell_number").val(area_manager_cell_number);
                        var area_manager_fax_number = {!! json_encode($prepopulateddata["area_manager_fax_number"])!!};
                        $("#area_manager_fax_number").val(area_manager_fax_number);
                        var office_address = {!! json_encode($prepopulateddata["office_address"])!!};
                        $("#office_address").val(office_address);
                } catch (error) {
                    console.log(error);
                }

                $(this).hide();
                $(".regionalmanagerinfolabel").toggle();
                $(".regionalmanagerinfoinput").toggle();
        });
        $('.select2').select2();
        $(".salesmanagerinfoinput").hide();
        $("#salesmanagerinfo").on("click",function(e){
                try {
                        var sales_employee_id = {!! json_encode($prepopulateddata["sales_employee_id"])!!};
                        $("#sales_employee_id").val(sales_employee_id).select2();
                        var sales_contact_job_title = {!! json_encode($prepopulateddata["sales_contact_job_title"])!!};
                        $("#sales_contact_job_title").val(sales_contact_job_title).select2();
                        var sales_contact_emailaddress = {!! json_encode($prepopulateddata["sales_contact_emailaddress"])!!};
                        $("#sales_contact_emailaddress").val(sales_contact_emailaddress);
                        var sales_contact_office_number = {!! json_encode($prepopulateddata["sales_office_number"])!!};
                        $("#sales_contact_office_number").val(sales_contact_office_number);
                        var sales_contact_cell_number = {!! json_encode($prepopulateddata["sales_cell_number"])!!};
                        $("#sales_contact_cell_number").val(sales_contact_cell_number);
                        var sales_contact_faxno = {!! json_encode($prepopulateddata["sales_contact_faxno"])!!};
                        $("#sales_contact_faxno").val(sales_contact_faxno);
                        var sales_contact_division = {!! json_encode($prepopulateddata["sales_contact_division"])!!};
                        $("#sales_contact_division").val(sales_contact_division);
                        var sales_contact_office_address = {!! json_encode($prepopulateddata["sales_contact_office_address"])!!};
                        $("#sales_contact_office_address").val(sales_contact_office_address);
                } catch (error) {
                     console.log(error);
                }

                $(this).hide();
                $(".salesmanagerinfolabel").toggle();
                $(".salesmanagerinfoinput").toggle();
        });

        $(".contracttermsinfoinput").hide();
        $("#contracttermsinfo").on("click",function(e){
                try {
                var contract_startdate = {!! json_encode($contractdata->contract_startdate)!!};
                $("#contract_startdate").val(contract_startdate);
                var contract_length = {!! json_encode($contractdata->contract_length)!!};
                $("#contract_length").val(contract_length)
                var contract_enddate = {!! json_encode($contractdata->contract_enddate)!!};
                $("#contract_enddate").val(contract_enddate);
                var renewable_contract = {!! json_encode($contractdata->renewable_contract)!!};
                $("#renewable_contract").val(renewable_contract);
                if(renewable_contract==1){
                        $("#renewable_contract").trigger("change");
                        //$("#contract_length_renewal_years").attr("disabled",false);
                        $("#renewalspan").show();
                }
                var contract_length_renewal_years = {!! json_encode($contractdata->contract_length_renewal_years)!!};
                $("#contract_length_renewal_years").val(contract_length_renewal_years);
                var termination_clause_client = {!! json_encode($contractdata->termination_clause_client)!!};
                $("#termination_clause_client").val(termination_clause_client);
                var termination_clause = {!! json_encode($contractdata->termination_clause)!!};
                $("#termination_clause").val(termination_clause);
                if(termination_clause_client==1){
                        $("#termination_clause_client").trigger("change");
                        //$("#terminationnoticeperiod").prop("disabled",false);
                        $("#terminationnoticeperiodclient").val({!! json_encode($contractdata->terminationnoticeperiodclient)!!});
                        $("#terminationnoticeclient").show();
                }
                var terminationnoticeperiod = {!! json_encode($contractdata->terminationnoticeperiod)!!};
                $("#terminationnoticeperiod").val(terminationnoticeperiod);
                if(termination_clause==1){
                        $("#termination_clause").trigger("change");
                        //$("#terminationnoticeperiod").prop("disabled",false);
                        $("#terminationnoticeperiod").val({!! json_encode($contractdata->terminationnoticeperiod)!!});
                        $("#terminationnotice").show();
                }else{
                        $("#terminationnotice").hide();
                }
                var billing_ratechange = {!! json_encode($contractdata->billing_ratechange)!!};
                $("#billing_ratechange").val(billing_ratechange);
                var contract_annualincrease_allowed = {!! json_encode($contractdata->contract_annualincrease_allowed)!!};
                $("#contract_annualincrease_allowed").val(contract_annualincrease_allowed);
                var contractonourtemplate = {!! json_encode($contractdata->contractonourtemplate)!!};
                $("#contractonourtemplate").val(contractonourtemplate);



                } catch (error) {
                    console.log(error);
                }

                $(this).hide();
                $(".contracttermsinfolabel").toggle();
                $(".contracttermsinfoinput").toggle();
        });

        $('.statholidayinfoinput').hide();
        $("#statholidays").on("click",function(e){
                $(".statholidayinfoinput").show();
                $(".statholidayinfolabel").hide();
                $(this).hide();
        })
        $(".poinfoinput").hide();
        $("#poinfo").on("click",function(e){
                try {
                        var ponumber = {!! json_encode($contractdata->ponumber)!!};
                $("#ponumber").val(ponumber);
                var pocompanyname = {!! json_encode($contractdata->pocompanyname)!!};
                $("#pocompanyname").val(pocompanyname);
                var poattentionto = {!! json_encode($contractdata->poattentionto)!!};
                $("#poattentionto").val(poattentionto);
                var potitle = {!! json_encode($contractdata->potitle)!!};
                $("#potitle").val(potitle);
                var pomailingaddress = {!! json_encode($contractdata->pomailingaddress)!!};
                $("#pomailingaddress").val(pomailingaddress);
                var pocity = {!! json_encode($contractdata->pocity)!!};
                $("#pocity").val(pocity);
                var popostalcode = {!! json_encode($contractdata->popostalcode)!!};
                $("#popostalcode").val(popostalcode);
                var pophone = {!! json_encode($contractdata->pophone)!!};
                $("#pophone").val(pophone);
                var poemail = {!! json_encode($contractdata->poemail)!!};
                $("#poemail").val(poemail);
                var pocellno = {!! json_encode($contractdata->pocellno)!!};
                $("#pocellno").val(pocellno);
                var pofax = {!! json_encode($contractdata->pofax)!!};
                $("#pofax").val(pofax);
                var ponotes = {!! json_encode($contractdata->ponotes)!!};
                $("#ponotes").val(ponotes);
                } catch (error) {

                }
                $(this).hide();
                $(".poinfolabel").toggle();
                $(".poinfoinput").toggle();
        });
        $(".pricingdefinitioninfoinput").hide();
        $("#pricingdefinitioninfo").on("click",function(e){
                try {
                        var total_annual_contract_billing = {!! json_encode($contractdata->total_annual_contract_billing)!!};
                        $("#total_annual_contract_billing").val(total_annual_contract_billing);
                        var total_annual_contract_wages_benifits = {!! json_encode($contractdata->total_annual_contract_wages_benifits)!!};
                        $("#total_annual_contract_wages_benifits").val(total_annual_contract_wages_benifits);
                        var total_annual_expected_contribution_margin = {!! json_encode($contractdata->total_annual_expected_contribution_margin)!!};
                        $("#total_annual_expected_contribution_margin").val(total_annual_expected_contribution_margin);
                        var total_hours_perweek = {!! json_encode($tothoursperweek)!!};
                        if(total_hours_perweek.indexOf(".")>0){
                                let splithours = total_hours_perweek.split(".");
                                $("#total_hours_perweek").val(splithours[0]);
                                try {

                                        $("#total_hours_perweek_minutes").val(splithours[1].replace("00","0"));
                                } catch (error) {
                                        $("#total_hours_perweek_minutes").val("0");
                                }

                        }else{
                            $("#total_hours_perweek").val(total_hours_perweek);
                        }

                        var average_billrate = {!! json_encode($contractdata->average_billrate)!!};
                        $("#average_billrate").val(average_billrate);
                        var average_wagerate = {!! json_encode($contractdata->average_wagerate)!!};
                        $("#average_wagerate").val(average_wagerate);
                        var average_markup = {!! json_encode($contractdata->average_markup)!!};
                        if(average_markup>0){
                          $("#average_markup").val(average_markup);
                        }

                        var pophone = {!! json_encode($contractdata->pophone)!!};
                        $("#pophone").val(pophone);
                        var poemail = {!! json_encode($contractdata->poemail)!!};
                        $("#poemail").val(poemail);
                        var pocellno = {!! json_encode($contractdata->pocellno)!!};
                        $("#pocellno").val(pocellno);
                        var pofax = {!! json_encode($contractdata->pofax)!!};
                        $("#pofax").val(pofax);
                        var ponotes = {!! json_encode($contractdata->ponotes)!!};
                        $("#ponotes").val(ponotes);
                } catch (error) {

                }
                $(this).hide();
                $(".pricingdefinitioninfolabel").toggle();
                $(".pricingdefinitioninfoinput").toggle();
        });

        $(".pricinginfoinput").hide();
        $("#pricingdetailsinfo").on("click",function(e){
                try {
                        var contract_billing_cycle = {!! json_encode($contractdata->contract_billing_cycle)!!};
                        $("#contract_billing_cycle").val(contract_billing_cycle);
                        var contract_payment_method = {!! json_encode($contractdata->contract_payment_method)!!};
                        $("#contract_payment_method").val(contract_payment_method);
                } catch (error) {

                }
                $(this).hide();
                $(".pricinginfoinput").toggle();
                $(".pricinginfolabel").toggle();
        });
        $("#savepricinginfo").on("click",function(e){
                let contract_billing_cycle = $("#contract_billing_cycle").val();
                let contract_payment_method = $("#contract_payment_method").val();
                if(contract_billing_cycle==""){
                        swal("Warning", "Contract billing cycle cannot be empty", "warning");
                }else if(contract_payment_method==""){
                        swal("Warning", "Contract payment method cannot be empty", "warning");
                }else
                {
                  let data = {contract_billing_cycle:contract_billing_cycle,contract_payment_method:contract_payment_method};
                let jsondata = JSON.stringify(data);
                saveblocks(jsondata);
                }

        })

        $(".scopeinfoinput").hide();
        $("#scopedetailsinfo").on("click",function(e){
                try {
                        var scopeofwork = {!! json_encode($contractdata->scopeofwork)!!};
                        $("#scopeofwork").val(scopeofwork);
                } catch (error) {

                }
                $(this).hide();
                $(".scopeinfoinput").toggle();
                $(".scopeinfolabel").toggle();
        });

        $("#savescopeinfo").on("click",function(e){
                let scopeofwork = $("#scopeofwork").val();


                let data = {scopeofwork:scopeofwork};
                let jsondata = JSON.stringify(data);
                saveblocks(jsondata);
        })
        $("#savesupervisorinfo").on("click",function(e){
                let supervisorassigned=$("#supervisorassigned").val()
                let supervisoremployeenumber=0
                let employeename="";
                let viewtrainingperformance=0;
                let employeecellphone="";
                let employeeemailaddress="";
                let employeetelephone="";
                let employeefaxno="";
                let contractcellphoneprovider=0;
                let supervisortabletrequired=0;
                let supervisorcgluser=0;
                let supervisorpublictransportrequired=0;
                let direction_nearest_intersection="";
                let department_at_site="";
                let delivery_hours="";
                let supervisorcanmailbesent=0;
                let contractdeviceaccess=0;
                if(supervisorassigned>0){
                        supervisoremployeenumber=$("#supervisoremployeenumber").val()
                        employeename=$("#employeename").val();
                        viewtrainingperformance=$("#viewtrainingperformance").val();
                        employeecellphone=$("#employeecellphone").val();
                        employeeemailaddress=$("#employeeemailaddress").val();
                        employeetelephone=$("#employeetelephone").val();
                        employeefaxno=$("#employeefaxno").val();
                        contractcellphoneprovider=$("#contractcellphoneprovider").val();
                        supervisortabletrequired=$("#supervisortabletrequired").val();
                        supervisorcgluser=$("#supervisorcgluser").val();
                        supervisorpublictransportrequired=$("#supervisorpublictransportrequired").val();
                        direction_nearest_intersection=$("#direction_nearest_intersection").val();
                        department_at_site=$("#department_at_site").val();
                        delivery_hours=$("#delivery_hours").val();
                        supervisorcanmailbesent=$("#supervisorcanmailbesent").val();
                        contractdeviceaccess=$("#contractdeviceaccess").val();  
                }
                let data = {supervisorassigned:supervisorassigned,supervisoremployeenumber:supervisoremployeenumber,
                        employeename:employeename,viewtrainingperformance:viewtrainingperformance,employeecellphone:employeecellphone,
                        employeeemailaddress:employeeemailaddress,employeetelephone:employeetelephone,employeefaxno:employeefaxno,
                        contractcellphoneprovider:contractcellphoneprovider,supervisortabletrequired:supervisortabletrequired,
                        supervisorcgluser:supervisorcgluser,supervisorpublictransportrequired:supervisorpublictransportrequired,
                        direction_nearest_intersection:direction_nearest_intersection,department_at_site:department_at_site,
                        delivery_hours:delivery_hours,supervisorcanmailbesent:supervisorcanmailbesent,contractdeviceaccess:contractdeviceaccess};
                let jsondata = JSON.stringify(data);
                saveblocks(jsondata);
        })
        $("#savecontractinfo").on("click",function(e){
                let reason_for_submission = $("#reason_for_submission").val();
                
                 if(reason_for_submission<1){
                        swal("Warning", "Reason for submission cannot be empty", "warning");
                }else{
                        let data = {reason_for_submission:reason_for_submission};
                        let jsondata = JSON.stringify(data);
                        saveblocks(jsondata);
                }

        })

        $("#savebusinessinfo").on("click",function(e){
                let business_segment = $("#business_segment").val();
                let lineofbusiness = $("#line_of_business").val();
                let multidivision = $("#multidivision:checked").val();
                let division_lookup = $("#division_lookup").val();
                if(division_lookup==""){
                        division_lookup=0;
                }
                let masterentity = $("#masterentity:checked").val();
                let master_customer = $("#master_customer").val();
                if(master_customer==""){
                        master_customer=0;
                }
                if(business_segment==""){
                        swal("Warning", "Business segment cannot be empty", "warning");
                }else if(lineofbusiness==""){
                        swal("Warning", "Line of business cannot be empty", "warning");
                }else if(multidivision=="1" && division_lookup=="0"){
                        swal("Warning", "Lead division cannot be empty", "warning");
                }else if(masterentity=="1" && master_customer=="0"){
                        swal("Warning", "Parent customer cannot be empty", "warning");
                }else{
                        let data = {business_segment:business_segment,line_of_business:lineofbusiness,
                                multidivisioncontract:multidivision,lead_division:division_lookup,
                                master_entity:masterentity,parent_customer:master_customer};
                        let jsondata = JSON.stringify(data);
                        saveblocks(jsondata);
                }

        })


        $("#saveregionalmanagerinfo").on("click",function(e){
                let area_manager_id = $("#area_manager").select2('data')[0]["id"];
                let area_manager = $("#area_manager").select2('data')[0]["text"];
                let positionid = $("#area_manager_position_text").val();
                //var positionid = $("#area_manager_position_text").find("[value='" + position + "']").attr("id-value");
                let area_manager_email_address = $("#area_manager_email_address").val();
                let area_manager_office_number = $("#area_manager_office_number").val();

                let area_manager_cell_number = $("#area_manager_cell_number").val();
                let area_manager_fax_number = $("#area_manager_fax_number").val();
                let office_address = $("#office_address").val();

                if(area_manager=="Select"){
                        swal("Warning", "Area Manager cannot be empty", "warning");
                }else if(positionid==""){
                        swal("Warning", "Position cannot be empty", "warning");
                }else if(area_manager_email_address==""){
                        swal("Warning", "Email address cannot be empty", "warning");
                }else if(area_manager_office_number==""){
                        swal("Warning", "Office number cannot be empty", "warning");
                }else if(area_manager_cell_number==""){
                        swal("Warning", "Cell number cannot be empty", "warning");
                }else if(office_address==""){
                        swal("Warning", "Office cannot be empty", "warning");
                }else{
                        let data = {area_manager_id:area_manager_id,area_manager:area_manager,area_manager_position_text:positionid,
                                area_manager_email_address:area_manager_email_address,area_manager_office_number:area_manager_office_number,
                                area_manager_cell_number:area_manager_cell_number,area_manager_fax_number:area_manager_fax_number,
                                office_address:office_address};
                        let jsondata = JSON.stringify(data);
                        saveblocks(jsondata);
                }



        })

        $("#savesalesinfo").on("click",function(e){
                let sales_employee_id = $("#sales_employee_id").select2('data')[0]["id"];
                let sales_manager = $("#sales_employee_id").select2('data')[0]["text"];

                let positionid = $("#sales_contact_job_title").val();
                //var positionid = $("#area_manager_position_text").find("[value='" + position + "']").attr("id-value");
                let sales_contact_emailaddress = $("#sales_contact_emailaddress").val();
                let sales_contact_office_number = $("#sales_contact_office_number").val();

                let sales_contact_cell_number = $("#sales_contact_cell_number").val();
                let sales_contact_faxno = $("#sales_contact_faxno").val();
                let sales_contact_division = $("#sales_contact_division").val();
                let sales_contact_office_address = $("#sales_contact_office_address").val();



                if(sales_manager=="Select"){
                        swal("Warning", "Sales Manager cannot be empty", "warning");
                }else if(positionid==""){
                        swal("Warning", "Position cannot be empty", "warning");
                }else if(sales_contact_emailaddress==""){
                        swal("Warning", "Email address cannot be empty", "warning");
                }else if(sales_contact_office_number==""){
                        swal("Warning", "Office number cannot be empty", "warning");
                }else if(sales_contact_cell_number==""){
                        swal("Warning", "Cell number cannot be empty", "warning");
                }else if(sales_contact_office_address==""){
                        swal("Warning", "Office cannot be empty", "warning");
                }else if(sales_contact_office_address==""){
                        swal("Warning", "Division cannot be empty", "warning");
                }else{
                        let data = {sales_employee_id:sales_employee_id,sales_contact_job_title:positionid,sales_contact_emailaddress:sales_contact_emailaddress,
                        sales_office_number:sales_contact_office_number,sales_cell_number:sales_contact_cell_number,
                        sales_contact_faxno:sales_contact_faxno,sales_contact_division:sales_contact_division,
                        sales_contact_office_address:sales_contact_office_address};
                let jsondata = JSON.stringify(data);
                saveblocks(jsondata);
                }


        })
        $("#renewable_contract").on('change',function(){
                var value = $(this).val();

                $("#contract_length_renewal_years").val("0");
                if(value == 1){
                        $("#renewalspan").show();

                        $("#contract_length_renewal_years").prop("readonly",false);
                }
                else{
                        $("#renewalspan").hide();
                        $("#contract_length_renewal_years").prop("readonly",true);
                }
        })

        $("#termination_clause_client").on('change',function(){
                var value = $(this).val();

                $("#terminationnoticeperiodclient").val("0");
                if(value == 1){

                        $("#terminationnoticeperiodclient").prop("readonly",false);
                        $("#terminationnoticeclient").show();
                }
                else{
                        $("#terminationnoticeperiodclient").prop("readonly",true);
                        $("#terminationnoticeclient").hide();
                }

        })

        $("#termination_clause").on('change',function(){
                var value = $(this).val();

                $("#terminationnoticeperiod").val("0");
                if(value == 1){

                        $("#terminationnoticeperiod").prop("readonly",false);
                        $("#terminationnotice").show();
                }
                else{
                        $("#terminationnoticeperiod").prop("readonly",true);
                        $("#terminationnotice").hide();
                }

        })
        $("#savecontracttermsinfo").on("click",function(e){
                let contract_startdate = $("#contract_startdate").val();
                let contract_length = $("#contract_length").val();

                let contract_enddate = $("#contract_enddate").val();
                //var positionid = $("#area_manager_position_text").find("[value='" + position + "']").attr("id-value");
                let renewable_contract = $("#renewable_contract").val();
                let contract_length_renewal_years = $("#contract_length_renewal_years").val();

                let termination_clause_client = $("#termination_clause_client").val();
                let terminationnoticeperiodclient = $("#terminationnoticeperiodclient").val();
                let termination_clause = $("#termination_clause").val();
                let terminationnoticeperiod = $("#terminationnoticeperiod").val();
                let billing_ratechange = $("#billing_ratechange").val();
                let contract_annualincrease_allowed = $("#contract_annualincrease_allowed").val();
                let contractonourtemplate = $("#contractonourtemplate").val();

                if(contract_startdate==""){
                        swal("Warning", "Start date cannot be empty", "warning");
                }else if(contract_length==""){
                        swal("Warning", "Length cannot be empty ", "warning");
                }else if(contract_enddate==""){
                        swal("Warning", "End date cannot be empty", "warning");
                }else if(renewable_contract==""){
                        swal("Warning", "Renewable contract cannot be empty", "warning");
                }else if(renewable_contract=="1" && contract_length_renewal_years<1){
                        swal("Warning", "Contract renewal length cannot be empty", "warning");
                }else if(termination_clause_client=="1"  && terminationnoticeperiodclient<1){
                        swal("Warning", "Terminate period client cannot be empty", "warning");
                }else if(termination_clause=="1"  && terminationnoticeperiod<1){
                        swal("Warning", "Terminate period client cannot be empty", "warning");
                }else if(billing_ratechange==""){
                        swal("Warning", "Pay/Bill rate cannot be empty", "warning");
                }else if(contract_annualincrease_allowed==""){
                        swal("Warning", "Annual increase cannot be empty", "warning");
                }else if(contractonourtemplate==""){
                        swal("Warning", "Contract on our template cannot be empty", "warning");
                }else if(contractonourtemplate=="true" && contracttemplatename==""){
                        swal("Warning", "Contract Written template cannot be empty", "warning");
                }else{

                        let data = {contract_startdate:contract_startdate,contract_length:contract_length,contract_enddate:contract_enddate,
                        renewable_contract:renewable_contract,contract_length_renewal_years:contract_length_renewal_years,
                        termination_clause_client:termination_clause_client,terminationnoticeperiodclient:terminationnoticeperiodclient,
                        termination_clause:termination_clause,terminationnoticeperiod:terminationnoticeperiod,
                        billing_ratechange:billing_ratechange,contract_annualincrease_allowed:contract_annualincrease_allowed,
                        contractonourtemplate:contractonourtemplate,contracttemplatename:contracttemplatename};
                        let jsondata = JSON.stringify(data);
                        saveblocks(jsondata);
                }


        })

        $("#savepricingdefinitioninfo").on("click",function(e){
                let data = {};
                let total_annual_contract_billing = $("#total_annual_contract_billing").val();
                let total_annual_contract_wages_benifits = $("#total_annual_contract_wages_benifits").val();

                let total_annual_expected_contribution_margin = $("#total_annual_expected_contribution_margin").val();
                //var positionid = $("#area_manager_position_text").find("[value='" + position + "']").attr("id-value");
                let total_hours_perweek = $("#total_hours_perweek").val();
                let total_hours_perweek_minutes = $("#total_hours_perweek_minutes").val();
                let average_billrate = $("#average_billrate").val();

                let average_wagerate = $("#average_wagerate").val();
                let average_markup = $("#average_markup").val();
                if(total_annual_contract_billing>0){
                        data["total_annual_contract_billing"]=total_annual_contract_billing;
                }else{
                        data["total_annual_contract_billing"]=0;
                }
                if(total_annual_contract_wages_benifits>0){
                        data["total_annual_contract_wages_benifits"]=total_annual_contract_wages_benifits;
                }else{
                        data["total_annual_contract_wages_benifits"]=0;
                }
                if(total_annual_expected_contribution_margin>0){
                        data["total_annual_expected_contribution_margin"]=total_annual_expected_contribution_margin;
                }else{
                        data["total_annual_expected_contribution_margin"]=0;
                }
                if(total_hours_perweek>0){
                        data["total_hours_perweek"]=total_hours_perweek;
                        if(total_hours_perweek_minutes>0){
                                let decimalval =Math.round(parseInt(total_hours_perweek_minutes)/.6);

                                data["total_hours_perweek"]=total_hours_perweek+"."+decimalval;
                        }
                }else{
                        data["total_hours_perweek"]=0;
                }
                if(average_billrate>0){
                        data["average_billrate"]=average_billrate;
                }else{
                        data["average_billrate"]=0;
                }
                if(average_wagerate>0){
                        data["average_wagerate"]=average_wagerate;
                }else{
                        data["average_wagerate"]=0;
                }
                if(average_markup>0){
                        data["average_markup"]=average_markup;
                }else{
                        data["average_markup"]=0;
                }
                /*
                let data = {total_annual_contract_billing:total_annual_contract_billing,total_annual_contract_wages_benifits:total_annual_contract_wages_benifits,
                        total_annual_expected_contribution_margin:total_annual_expected_contribution_margin,
                        total_hours_perweek:total_hours_perweek,average_billrate:average_billrate,
                        average_wagerate:average_wagerate,average_markup:average_markup};
                        */
                let jsondata = JSON.stringify(data);
                saveblocks(jsondata);

        })

        $("#savepoinfo").on("click",function(e){
                let ponumber = $("#ponumber").val();
                let pocompanyname = $("#pocompanyname").val();

                let poattentionto = $("#poattentionto").val();
                //var positionid = $("#area_manager_position_text").find("[value='" + position + "']").attr("id-value");
                let pomailingaddress = $("#pomailingaddress").val();
                let potitle = $("#potitle").val();

                let pocity = $("#pocity").val();
                let popostalcode = $("#popostalcode").val();
                let pophone = $("#pophone").val();
                let poemail = $("#poemail").val();
                let pocellno = $("#pocellno").val();
                let pofax = $("#pofax").val();
                let ponotes = $("#ponotes").val();

                if(ponumber==""){
                        swal("Warning", "PO Number cannot be empty", "warning");
                }else if(pocompanyname==""){
                        swal("Warning", "Company name cannot be empty ", "warning");
                }else{
                        let data = {ponumber:ponumber,pocompanyname:pocompanyname,poattentionto:poattentionto,
                        potitle:potitle,pomailingaddress:pomailingaddress,
                        pocity:pocity,popostalcode:popostalcode,
                        pophone:pophone,poemail:poemail,
                        pocellno:pocellno,pofax:pofax,ponotes:ponotes};
                        let jsondata = JSON.stringify(data);
                        saveblocks(jsondata);
                }


        })

$("#savestatinfo").on("click",function(e){
        holidayarray = {};
        var i =0;
        let contractid = {!! json_encode($contractid)!!}
        $(".holidaypayment").each(function(){

                var holidayid = $(this).attr("id");
                var holidayvalue = $(this).val();
                if(holidayvalue>0){
                        holidayarray[i]={holidayid:holidayid,holidayvalue:holidayvalue};
                        i++;
                }


        })  ;


          $.ajax({
                        type: "post",
                        url: '{{route("contracts.editcontractblocks")}}',
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {dbvariable:null,contractid:contractid,holidayarray:JSON.stringify(holidayarray)},
                        success: function (response) {
                                location.reload();
                        }
        });



})
        var saveblocks = function(jsondata){
                $(this).hide();
                let contractid = {!! json_encode($contractid)!!}
                $.ajax({
                        type: "post",
                        url: '{{route("contracts.editcontractblocks")}}',
                        data: {dbvariable:jsondata,contractid:contractid},
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                                location.reload();
                            console.log(response);
                        }
                });
        }

        $("#area_manager_position_text").select2();
        $("#area_manager").select2();
        $("#area_manager").on('select2:select',function(e){

                if($(this).val()=="")
                {
                        $("#area_manager_position_text").val("");
                        $("#area_manager_email_address").val("");
                        $("#area_manager_office_number").val("");
                        $("#area_manager_cell_number").val("");
                        $("#area_manager_fax_number").val("");
                }
                else{
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;

                                     $("#area_manager_email_address").val(primarycontact.email);
                                     $("#area_manager_office_number").val(primarycontact.officenumber);
                                     $("#area_manager_cell_number").val(primarycontact.cellnumber);
                                     //$("#sales_contact_faxno").val(primarycontact.faxnumber);
                                     //var salesmanagerpositiontext = $("#area_manager_position_text").find("[id-value='" + position + "']").val();
                                     var salesmanagerpositiontext = $("#area_manager_position_text").find("[id-value='" + position + "']").val();
                                     //$("#area_manager_position_text[id-value='"+position+"']").prop("selected").select2();
                                     $("#area_manager_position_text").val(salesmanagerpositiontext).select2();

                                }
                        });
                }
        });
        $("#sales_employee_id").select2();
        $("#sales_employee_id").on('select2:select',function(e){

                if($(this).val()=="")
                {
                        $("#sales_contact_job_title").val("");
                        $("#sales_contact_emailaddress").val("");
                        $("#sales_contact_office_number").val("");
                        $("#sales_contact_cell_number").val("");
                        $("#sales_contact_faxno").val("");
                }
                else{
                        $.ajax({
                                type: "post",
                                url: "{{ route('contracts.get-user-details')}}",
                                data: {"userid":$(this).val()},
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                     var primarycontact = $.parseJSON(response);
                                     var position = primarycontact.positionid;

                                     $("#sales_contact_emailaddress").val(primarycontact.email);
                                     $("#sales_contact_office_number").val(primarycontact.officenumber);
                                     $("#sales_contact_cell_number").val(primarycontact.cellnumber);
                                     //$("#sales_contact_faxno").val(primarycontact.faxnumber);
                                     var salesmanagerpositiontext = $("#area_manager_position_text").find("[id-value='" + position + "']").val();


                                     $("#sales_contact_job_title").val(position).select2().trigger("change");


                                }
                        });
                }
        });

        $('.dollar').keypress(function(event) {
                        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                        event.preventDefault();
                        }
                        var input = $(this);
                        var oldVal = input.val();
                        var regex = new RegExp(input.attr('pattern'), 'g');

                        setTimeout(function(){
                        var newVal = input.val();
                        if(!regex.test(newVal)){
                        input.val(oldVal);
                        }
                        }, 0);
                });
        var editpermission = {!! json_encode($editpermission)!!};
        if(editpermission<1){
                // $(".editbutton").hide();
        }

        $(".cancelbutton").on("click",function (e) {
                ///location.reload();
                let inputcontrol = $(this).attr("attr-input");
                let labelcontrol = $(this).attr("attr-label");
                $("."+inputcontrol).hide();
                $(".subrows").hide();
                $("."+labelcontrol).show();
                $(".editbutton").show();


         })   ;

        preloadvalues = function(){

        }
        $(document).ready(function() {
                $('body').loading('stop');
                let contractholidayagreement = {!! json_encode($contractholidayagreement)!!};
                contractholidayagreement.forEach(element => {
                       var paymentstatus_id = element.paymentstatus_id;
                       var holiday_id = element.holiday_id;

                       $("#holiday-payment-"+holiday_id).val(paymentstatus_id);
                });
                $("#primary_contact").select2();
                $("#supervisoremployeenumber").select2();

        })
        preloadvalues();
        refreshSideMenu();

        $("#supervisoremployeenumber").on('select2:select',function(){

if($(this).val()=="")
{
        $("#employeename").val("");
        $("#employeeemailaddress").val("");
        $("#employeetelephone").val("");
        $("#employeecellphone").val("");
        //$("#employeefaxno").val("");
}
else
{
        $.ajax({
                type: "post",
                url: "{{ route('contracts.get-user-details')}}",
                data: {"userid":$(this).val()},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                     var primarycontact = $.parseJSON(response);
                     var position = primarycontact.positionid;
                     $("#employeename").val(primarycontact.name);
                     $("#employeeemailaddress").val(primarycontact.email);
                     $("#employeetelephone").val(primarycontact.officenumber);
                     $("#employeecellphone").val(primarycontact.cellnumber);
                     //$("#employeefaxno").val(primarycontact.faxnumber);

                }
        });
}
});

        $(document).on("keydown",".notdecimal",function(event){
                // console.log(event.keyCode)
                if(event.keyCode==8){
                        return true
                }
                else if (event.keyCode < 48 || event.keyCode > 57 ){
                         return false;
                }

                });
        </script>
@endsection
@section('content')
<div class="table_title">
        <h4>Contracts Management Upload Form</h4>
    </div>
<div class="container-fluid">




<div class="form-group row ">
                                <div class="col-sm-12 candidate-screen-head"> Prerequisites
                                        @canany(["edit_contract_information","super_admin"])
                                        <span class="editbutton fas fa-edit" id="reqinfo">&nbsp;</span>
                                        @endcanany
                                </div>

                        </div>

                        <div class="form-group row viewreq fileinfolabel">
        <div class="col-md-4">Contract</div>
        <div class="col-md-4">


        <a style="color:black;text-decoration:none"
        href="{{route("contracts.downloadcontractattachment",[
                "contract_id"=>$contractid,"file_id"=>$contract_attachment_id,"date"=>$createddate,"filetype"=>"contract"
        ])}}"

         target="_blank" >Contract file &nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>
                </div>
        <div class="col-md-4">
                </div>
</div>
<form method="POST" id="uploadform-data" enctype="multipart/form-data">
<div class="form-group row fileinfoinput" id="yesbar" >
        <div class="col-sm-4" style="display: inline-block">Upload Contract <small>(doc,docx,pdf,xls,xlsx,ods,ppt,pptx)</small><span class="mandatory">*</span></div>
        <div class="col-sm-3" style="display: inline-block">
                <input  type="file" name="cmuf_contract_document" id="cmuf_contract_document"  />
                <input type="hidden" name="contract_document_attachment" id="contract_document_attachment" value="0" />
                <span id="fname" style="display:none"></span>
        </div>
        <div class="col-sm-1" style="display: inline-block">
                <input type="button" id="uploadcontract" attr_file="cmuf_contract_document" attr_hidden="contract_document_attachment" class="button btn submit form-control" value="Upload" />
                <input style="display:none" type="button" id="swap" value="Swap" />
                
        </div><div class="col-sm-1" style="display: inline-block">
                <button type="button" attr-label="fileinfolabel" attr-input="fileinfoinput" 
                name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
                
        </div>
        
        <div class="col-sm-3" style="display: inline-block">

                <label  for="cmuf_contract_document" class="uploaderror text-danger"></label>
        </div>
</div>
</form>
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Contract Information
        @canany(["edit_contract_information","super_admin"])
                    <span class="editbutton fas fa-edit" id="contractinfo">&nbsp;</span>
        @endcanany
</div>
</div>

<div class="form-group row contractinfolabel">
<div class="col-md-4">Contract Name</div>
<div class="col-md-4">  {{$contractdata->getContractname->client_name}}</div>
<div  class="col-md-4"><label class="error" for="customer_client"></label>   </div>
</div>
<div class="form-group row contractinfolabel">
<div class="col-md-4">Contract Number</div>
<div class="col-md-4">{{$contractdata->contract_number}}</div>
<div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
</div>
<div class="form-group row contractinfolabel">
<div class="col-md-4">Submission Date</div>
<div class="col-md-4">{{date("M d,Y",strtotime($contractdata->submission_date))}}</div>
<div  class="col-md-4">   </div>
</div>
<div class="form-group row contractinfolabel">
<div class="col-md-4">Regional Manager</div>
<div class="col-md-4">
         {{$contractdata->area_manager}}
</div>
<div  class="col-md-4"><label class="error" for="area_manager_text"></label>   </div>
</div>
<div class="form-group row contractinfolabel">
<div class="col-md-4">Reason for Submission</div>
<div class="col-md-4">
         {{$contractdata->getReasonforsubmission->reason}}
</div>
<div  class="col-md-4"><label class="error" for="reason_for_submission"></label>   </div>
</div>




<div class="form-group row contractinfoinput inputclass">
        <div class="col-sm-4" style="display: inline-block !important">Reason for Submission<span class="mandatory">*</span></div>
        <div class="col-sm-4" style="display: inline-block !important">
                <select class="form-control" name="reason_for_submission" required id="reason_for_submission" placeholder="Select">
                                <option value="">Select</option>
                                @foreach ($lookUps['reasonforsubmissionLookup'] as $reasonforsubmission)
                                        <option value="{{$reasonforsubmission->id}}">{{$reasonforsubmission->reason}}</option>
                                @endforeach

                </select>
        </div>
</div>
<div class="form-group row contractinfoinput inputclass"  id="parentdiv" >
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savecontractinfo" id="savecontractinfo" class="button btn submit">Save</button>
                <button type="button" attr-label="contractinfolabel" attr-input="contractinfoinput" 
                name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row">
        <div class="col-sm-12 candidate-screen-head" >Business Information
                @canany(["edit_contract_business_information","super_admin"])
                                <span class="editbutton fas fa-edit" id="businessinfo">&nbsp;</span>
                @endcanany
        </div>
</div>
<div class="form-group row businessinfoinput inputclass">
        <div class="col-md-4">What business segment does the contract fall under  <span class="mandatory">*</span></div>
        <div class="col-md-4">
                        <select class="form-control" name="business_segment" required id="business_segment" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['businessSegmentLookup'] as $businesssegment)
                                                <option value="{{$businesssegment->id}}">{{$businesssegment->segmenttitle}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row businessinfoinput inputclass">
        <div class="col-md-4">What line of business does the contract fall under  <span class="mandatory">*</span></div>
        <div class="col-md-4">
                        <select class="form-control" name="line_of_business" required id="line_of_business" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['lineofBusinessLookup'] as $lineofbusiness)
                                                <option value="{{$lineofbusiness->id}}">{{$lineofbusiness->lineofbusinesstitle}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row businessinfoinput inputclass">
        <div class="col-md-4">Is this a multi division contract  <span class="mandatory">*</span> </div>
        <div class="col-md-1">
        <input type="radio" name="multidivision" id="multidivision" value="1"  />Yes<label class="error text-danger yesnolabel" for="multidivision"></label>
        </div><div class="col-md-1"><input type="radio" name="multidivision" id="multidivision" value="0" checked />No<label class="error text-danger yesnolabel" for="multidivision"></label></div>
</div>
<div class="form-group row subrows inputclass" id="leaddiv" style="display:none">
        <div class="col-md-4">Who is the lead division  </div>
        <div class="col-md-4">
                        <select disabled class="form-control" name="division_lookup" required id="division_lookup" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['divisionlookuprepository'] as $divisionlookup)
                                                <option value="{{$divisionlookup->id}}">{{$divisionlookup->division_name}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row businessinfoinput inputclass">
        <div class="col-md-4">Is there a master entity  <span class="mandatory">*</span></div>
        <div class="col-md-1">
                <input type="radio" name="masterentity" id="masterentity"  value="1" checked />Yes <label class="error text-danger yesnolabel" for="masterentity"></label>
        </div> <div class="col-md-1"> <input type="radio" name="masterentity" id="masterentity" value="0" checked />No<label class="error text-danger yesnolabel" for="masterentity"></label>
        </div>
        </div>

<div class="form-group row subrows"  id="parentdiv" style="display:none">
        <div class="col-md-4">Parent Project Number</div>
        <div class="col-md-4">

                        <select disabled class="form-control" name="master_customer" required id="master_customer" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['parentcustomerlookuprepository'] as $key=>$value)
                                                <option value="{{$key}}">{{$value}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row businessinfoinput inputclass"  id="parentdiv" >
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savebusinessinfo" id="savebusinessinfo" class="button btn submit">Save</button>
                <button type="button" attr-label="businessinfolabel" attr-input="businessinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row businessinfolabel" >
<div class="col-md-4">What business segment does the contract fall under  </div>
<div class="col-md-4">
               {{$contractdata->getBusinesssegment->segmenttitle}}
</div>
</div>
<div class="form-group row businessinfolabel" >
<div class="col-md-4">What line of business does the contract fall under  </div>
<div class="col-md-4">
                {{$contractdata->getBusinessline->lineofbusinesstitle}}
</div>
<div  class="col-md-4"><label class="error" for="line_of_business"></label>   </div>
</div>
<div class="form-group row businessinfolabel" >
<div class="col-md-4">Is this a multi division contract </div>
<div class="col-md-4">
@if($contractdata->multidivisioncontract ==1)
Yes
@else
No
@endif
</div>
<div  class="col-md-4"><label class="error" for="multidivision"></label>   </div>
</div>
@if($contractdata->multidivisioncontract ==1)
<div class="form-group row businessinfolabel" >
<div class="col-md-4">Who is the lead division  </div>
<div class="col-md-4">
        @if($contractdata->getLeadDivisionlookup!=null)
               {{$contractdata->getLeadDivisionlookup->division_name}}
        @endif
</div>
<div  class="col-md-4"><label class="error" for="division_lookup"></label>   </div>
</div>
@endif
<div class="form-group row businessinfolabel" >
<div class="col-md-4">Is there a master entity </div>
<div class="col-md-4">
                @if($contractdata->master_entity >0)
                Yes
                @else
                No
                @endif
</div>
<div  class="col-md-4"><label class="error" for="masterentity"></label>   </div>
</div>
@if($contractdata->master_entity >0)
<div class="form-group row businessinfolabel">
<div class="col-md-4">Parent Project Number </div>
<div class="col-md-4">
@if($contractdata->getParentcustomer!=null)
      {{$contractdata->getParentcustomer->project_number}} - {{$contractdata->getParentcustomer->client_name}}
      @endif

</div>
<div  class="col-md-4"><label class="error" for="master_customer"></label>   </div>
</div>
@endif
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Enter Regional Manager Information
        @canany(["edit_contract_regionalmanager_information","super_admin"])
                <span class="editbutton fas fa-edit" id="regionalmanagerinfo">&nbsp;</span>
        @endcanany
</div>

</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
        <div class="col-md-4">Who is the Regional Manager assigned to the account  <span class="mandatory">*</span> </div>
        <div class="col-md-4">

                <select class="form-control" name="area_manager" required id="area_manager" placeholder="Select">
                        <option value="">Select</option>

                        @foreach ($lookUps['areamanagerlookup'] as $manager)
                                 <option 
                                                        value="{{$manager->id}}">{{$manager->getFullNameAttribute()}} </option>
                        @endforeach

                </select>
                <input type="hidden" name="rmanagerid" id="rmanagerid" value="" />
        </div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">What is the person's job title  <span class="mandatory">*</span> </div>
<div class="col-md-4">
         <!--
        <input type="text" class="form-control" id="area_manager_position_text" name="area_manager_position_text" value="" />
         -->
         <select class="form-control" name="area_manager_position_text" required id="area_manager_position_text" placeholder="Select">
                        <option value="">Select</option>

                        @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                        <option id-value="{{$value['id']}}" value="{{$value['position']}}">{{$value['position']}} </option>
                        @endforeach

        </select>

        <input type="hidden"  id="area_manager_position_id" name="area_manager_position_id" value="" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">What is the person's email address <span class="mandatory">*</span> </div>
<div class="col-md-4">
        <input  type="email" class="form-control" placeholder="Email Address" id="area_manager_email_address" name="area_manager_email_address" value="" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">What is the person's office number <span class="mandatory">*</span>
</div>
<div class="col-md-4">
        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Office No [ format (XXX)XXX-XXXX ]" class="form-control phone" id="area_manager_office_number" name="area_manager_office_number" value="" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">What is the person's cell number  <span class="mandatory">*</span>
</div>
<div class="col-md-4">
        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Cell No [ format (XXX)XXX-XXXX ]" class="form-control phone" id="area_manager_cell_number" name="area_manager_cell_number" value="" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">What is the fax number associated with the individual
</div>
<div class="col-md-4">
        <input  type="text" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" placeholder="Fax No [ format (XXX)XXX-XXXX ]"  class="form-control phone" id="area_manager_fax_number" name="area_manager_fax_number" value="0" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass">
<div class="col-md-4">Where is the contact's primary office located <span class="mandatory">*</span>

</div>
<div class="col-md-4">
                <select class="form-control" name="office_address" required id="office_address" placeholder="Select">
                                <option value="">Select</option>
                                @foreach ($lookUps['officeaddresslookuprepository'] as $officeaddress)
                                        <option value="{{$officeaddress->id}}">{{$officeaddress->addresstitle}}</option>
                                @endforeach

                </select>
</div>
</div>
<div class="form-group row regionalmanagerinfolabel">
        <div class="col-md-4">Who is the Regional Manager assigned to the account </div>
        <div class="col-md-4">
               {{$contractdata->area_manager}}
        </div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">What is the person's job title </div>
<div class="col-md-4">
         <!--
        <input type="text" class="form-control" id="area_manager_position_text" name="area_manager_position_text" value="" />
         -->
         {{$contractdata->area_manager_position_text}}

        <input type="hidden"  id="area_manager_position_id" name="area_manager_position_id" value="" />
</div>
</div>
<div class="form-group row regionalmanagerinfoinput inputclass" id="parentdiv" style="">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="saveregionalmanagerinfo" id="saveregionalmanagerinfo" class="button btn submit">Save</button>
                <button type="button" attr-label="regionalmanagerinfolabel" attr-input="regionalmanagerinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">What is the person's email address </div>
<div class="col-md-4">
                {{$contractdata->area_manager_email_address}}
</div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">What is the person's office number
</div>
<div class="col-md-4">
                {{$contractdata->area_manager_office_number}}
</div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">What is the person's cell number
</div>
<div class="col-md-4">
                {{$contractdata->area_manager_cell_number}}
</div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">What is the fax number associated with the individual
</div>
<div class="col-md-4">
                {{$contractdata->area_manager_fax_number}}
</div>
</div>
<div class="form-group row regionalmanagerinfolabel">
<div class="col-md-4">Where is the contact's primary office located

</div>

<div class="col-md-4">
                {{$contractdata->getOfficeAddressareamanager->addresstitle}}
</div>
</div>
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Sales Information
        @canany(["edit_contract_sales_information","super_admin"])
                <span class="editbutton fas fa-edit" id="salesmanagerinfo">&nbsp;</span>

        @endcanany
</div>

</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">Who won the contract  <span class="mandatory">*</span>
        </div>
        <div class="col-md-4">
                        <select class="form-control" name="sales_employee_id" required id="sales_employee_id" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                 <option data-empno="{{$value["emp_no"]}}"
                                                                        value="{{$value['id']}}">{{$value['full_name']}} </option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">What is the person's job title <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                        <select class="form-control" name="sales_contact_job_title" required id="sales_contact_job_title" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                        <option data-empno="{{$value["id"]}}"
                                                                        value="{{$value['id']}}">{{$value['position']}} </option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">What is the person's email address <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                        <input type="email"  name="sales_contact_emailaddress" id="sales_contact_emailaddress" value=" " class="form-control">
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">What is the person's office number <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                        <input  type="text"  id="sales_contact_office_number" placeholder="Office No [ format (XXX)XXX-XXXX ]" name="sales_contact_office_number" value="" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">What is the person's cell number <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                        <input type="text"   id="sales_contact_cell_number" name="sales_contact_cell_number" placeholder="Cell No [ format (XXX)XXX-XXXX ]" value="" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">What is the fax number associated with the individual
</div>
        <div class="col-md-4">
                        <input type="text" name="sales_contact_faxno" id="sales_contact_faxno" value=" " placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">Which division won the bid <span class="mandatory">*</span>

</div>
        <div class="col-md-4">
                        <select class="form-control select2" name="sales_contact_division" required id="sales_contact_division" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['divisionlookuprepository'] as $divisionlookup)
                                                <option value="{{$divisionlookup->id}}">{{$divisionlookup->division_name}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4">Where is the contact's primary office located <span class="mandatory">*</span>

</div>
        <div class="col-md-4">

                        <select class="form-control" name="sales_contact_office_address" required id="sales_contact_office_address" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['officeaddresslookuprepository'] as $officeaddress)
                                                <option value="{{$officeaddress->id}}">{{$officeaddress->addresstitle}}</option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row salesmanagerinfoinput inputclass">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savesalesinfo" id="savesalesinfo" class="button btn submit">Save</button>
                <button type="button"  attr-label="salesmanagerinfolabel" attr-input="salesmanagerinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">Who won the contract
</div>
<div class="col-md-4">
        {{$salesuser    }}
</div>
<div  class="col-md-4"><label class="error" for="sales_employee_id"></label>   </div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">What is the person's job title
</div>
<div class="col-md-4">
                {{$contractdata->getPositiontitle->position}}
</div>
<div  class="col-md-4"><label class="error" for="sales_contact_job_title"></label>   </div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">What is the person's email address
</div>
<div class="col-md-4">
                {{$contractdata->sales_contact_emailaddress}}
</div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">What is the person's office number
</div>
<div class="col-md-4">
                {{$contractdata->sales_office_number}}
</div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">What is the person's cell number
</div>
<div class="col-md-4">
                {{$contractdata->sales_cell_number}}
</div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">What is the fax number associated with the individual
</div>
<div class="col-md-4">
                {{$contractdata->sales_contact_faxno}}
</div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">Which division won the bid

</div>
<div class="col-md-4">
                {{$contractdata->getSalesDivisionlookup->division_name}}
</div>
<div  class="col-md-4"><label class="error" for="sales_contact_division"></label>   </div>
</div>
<div class="form-group row salesmanagerinfolabel">
<div class="col-md-4">Where is the contact's primary office located

</div>
<div class="col-md-4">

               {{$contractdata->getOfficeAddresssalesmanager->addresstitle}}
</div>
</div>
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Client Contact Information

        @canany(["edit_contract_clientcontact_information","super_admin"])
                <span class="editbutton fas fa-edit" id="clientinfo">&nbsp;</span>
        @endcanany
                
</div>
</div>
<div class="container-fluid" id="clientcontactinformation" style="padding:0">
<div class="form-group row candidate-screen-head" >
        <div class="col-sm-2">Client Contact</div>
        <div class="col-sm-1">Job Title</div>
        <div  class="col-sm-2">Email Address   </div>
        <div  class="col-sm-2">Office Number  </div>
        <div  class="col-sm-2">Cell Number   </div>
        <div  class="col-sm-2">Fax Number   </div>
        <div  class="col-sm-1"></div>
</div>
@foreach ($contractclients as $contractclient)
        <div id="client-{{$contractclient->id}}" class="form-group row">
                        <div class="col-sm-2">{{$contractclient->contact_name}}</div>

                        <div class="col-sm-1">{{$contractclient->contact_jobtitle}}</div>
                        <div  class="col-sm-2">{{$contractclient->contact_emailaddress}}   </div>
                        <div  class="col-sm-2">{{$contractclient->contact_phoneno}}  </div>
                        <div  class="col-sm-2">{{$contractclient->contact_cellno}}   </div>
                        <div  class="col-sm-2">{{$contractclient->contact_faxno}}   </div>
                        <div  class="col-sm-1">
                                <i class="fa fa-trash clientrem" attr-id="{{$contractclient->id}}"  attr-contractid="{{$contractclient->contractid}}" style="cursor: pointer;" aria-hidden="true"></i>
                        </div>
        </div>
@endforeach





</div>
<div class="container-fluid clientcontactinformation"  style="padding:0;display:none">
        <div class="form-group row">
                <div class="col-sm-4">Contact Name <span class="mandatory">*</span>
        </div>
                <div class="col-sm-4">
                        <select class="form-control" name="primary_contact" required id="primary_contact" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                        <option data-empno="{{$value["emp_no"]}}"
                                                                        value="{{$value['id']}}">{{$value['full_name']}} </option>
                                        @endforeach

                        </select>
                </div>
                <div  class="col-sm-4"><label class="error text-danger" for="primary_contact"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">Who is the primary client contact for this contract <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
                <input readonly type="text" name="contact_name_1" id="contact_name_1" value=" " class="form-control" />
        </div>
        <div  class="col-sm-4"><label class="error text-danger" for="contact_name"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">What is the person's job title <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">

                <input class="form-control" name="contact_jobtitle_1" required id="contact_jobtitle_1" placeholder="Position" value="Client"  />
                        {{-- <select class="form-control" name="contact_jobtitle_1" required id="contact_jobtitle_1" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['positionlookuprepository'] as $key=>$value)
                                                        <option data-empno="{{$value["id"]}}"
                                                                        value="{{$value['id']}}">{{$value['position']}} </option>
                                        @endforeach


                        </select> --}}
        </div>
        <div  class="col-sm-4"><label class="error text-danger" for="contact_jobtitle_1"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">What is the person's email address <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
                <input  type="email" name="contact_emailaddress_1" id="contact_emailaddress_1" value=" " class="form-control" />
        </div>
        <div  class="col-sm-4"><label class="error text-danger" for="contact_emailaddress"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">What is the person's office number <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
                <input  type="text" name="contact_phoneno_1" id="contact_phoneno_1" placeholder="Office No [ format (XXX)XXX-XXXX ]" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
        </div>
        <div  class="col-sm-4"><label class="error text-danger" for="contact_phoneno"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">What is the person's cell number <span class="mandatory">*</span>
        </div>
        <div class="col-sm-4">
                <input  type="text" name="contact_cellno_1" id="contact_cellno_1" placeholder="Cell No [ format (XXX)XXX-XXXX ]" value=" " pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
        </div>
        <div  class="col-sm-4"><label class="error text-danger" for="contact_cellno"></label>   </div>
        </div>
        <div class="form-group row">
        <div class="col-sm-4">What is the fax number associated with the individual
        </div>
        <div class="col-sm-4">
                <input   type="text" name="contact_faxno_1" id="contact_faxno_1" placeholder="Fax No [ format (XXX)XXX-XXXX ]" value="0" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" />
        </div>
        <div  class="col-sm-4">
                <label class="error text-danger" for="contact_faxno"></label>   </div>
        </div>
</div>
<div class="form-group row clientcontactinformation"  style="display:none;">
        <div class="col-sm-4" style="text-align:center"></div>
        <div class="col-xs-1" >&nbsp;&nbsp;&nbsp;<button attr-contractid="{{$contractid}}" type="button" id="addmoreclient" class="btn btn-primary" style="">Save</button></div>
        <div class="col-xs-1" >&nbsp;&nbsp;<button type="button" id="cancelclient" class="btn btn-primary">Cancel</button></div>

</div>
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Contract Terms
        @canany(["edit_contract_contractterms_information","super_admin"])
        <span class="editbutton fas fa-edit" id="contracttermsinfo">&nbsp;</span>

        @endcanany
</div>

</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">Contract Start Date <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <input type="text"  name="contract_startdate" id="contract_startdate" placeholder='Project Open Date (Y-m-d)' value="" class="form-control  datepicker" />
        </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">Contract Length (Years) <span class="mandatory">*</span>
</div>
        <div class="col-md-4"><input min="0" type="number"  name="contract_length" id="contract_length" value="" class="form-control" />
        </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">Contract Expiry   <span class="mandatory">*</span>
</div>
        <div class="col-md-4">

                <input type="text"  name="contract_enddate" id="contract_enddate" placeholder='Project End Date (Y-m-d)' value="" class="form-control  datepicker" />
        </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass" >
        <div class="col-md-4">Is there a renewal option <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <select name="renewable_contract" id="renewable_contract" class="form-control" >
                        <option value=" ">select</option>
                        <option value="1">Yes</option>
                        <option value="0" selected>No</option>
                </select>
        </div>
</div>
<div class="form-group row subrows inputclass" id="renewalspan" style="display:none">
        <div class="col-md-4">How long is the renewal option (Years)
</div>
        <div class="col-md-4"><input pattern="^\d*(\.\d{0,2})?$" min="0" type="text" min="0" value="0" class="form-control dollar" name="contract_length_renewal_years" id="contract_length_renewal_years" value="0" /></div>
        <div  class="col-md-4"><label class="error text-danger" for="contract_length_renewal_years"></label>   </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">Does the client have a termination clause <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <select name="termination_clause_client" id="termination_clause_client" class="form-control" >
                        <option value=" ">select</option>
                        <option value="1">Yes</option>
                        <option value="0" selected>No</option>
                </select>
        </div>
</div>
<div class="form-group row  subrows inputclass"  id="terminationnoticeclient" style="display:none">
        <div class="col-md-4">Termination notice period (Days)
</div>
        <div class="col-md-4"><input readonly min="0" pattern="^\d*(\.\d{0,2})?$" type="number" min="0" value="0" class="form-control dollar" name="terminationnoticeperiodclient" id="terminationnoticeperiodclient" value="0" /></div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">Does service provider have a termination clause <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <select name="termination_clause" id="termination_clause" class="form-control" >
                        <option value=" ">select</option>
                        <option value="1">Yes</option>
                        <option value="0" selected>No</option>
                </select>
        </div>
</div>
<div class="form-group row  subrows" id="terminationnotice" style="display:none">
        <div class="col-md-4">Termination notice period (Days)
</div>
        <div class="col-md-4"><input readonly min="0" type="number" min="0" value="0" pattern="^\d*(\.\d{0,2})?$" class="form-control dollar" name="terminationnoticeperiod" id="terminationnoticeperiod" value="0" /></div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4">How often will the pay/bill rate change <span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                        <select class="form-control" name="billing_ratechange" required id="billing_ratechange" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['billingratechangerepository'] as $billingratechange)
                                                        <option value="{{$billingratechange->id}}">{{$billingratechange->ratechangetitle}} </option>
                                        @endforeach

                        </select>
        </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass">
        <div class="col-md-4" style="display: inline-block !important">What is the annual increase allowable <span class="mandatory">*</span>
</div>
        <div class="col-md-4"  style="display: inline-block !important">
                <input type="text" name="contract_annualincrease_allowed" id="contract_annualincrease_allowed" class="form-control" placeholder="Annual Increase Allowed" />

        </div>
</div>
<div class="form-group row  contracttermsinfoinput inputclass">
        <div class="col-sm-4"  style="display: inline-block !important">Contract written template <span class="mandatory">*</span>
</div>
        <div class="col-sm-4" style="display: inline-block !important">
                        <select class="form-control" name="contractonourtemplate" required id="contractonourtemplate" placeholder="Select">
                                        <option value="">Select</option>
                                        @foreach ($lookUps['contractprovidertemplate'] as $contractprovidertemplate)
                                                    <option value="{{$contractprovidertemplate->id}}">{{$contractprovidertemplate->templateparty}}</option>
                                        @endforeach
                        </select>
        </div>
</div>
<div class="form-group row  inputclass" id="templatenamediv" style="display: none">
        <div class="col-sm-4" style="display: inline-block !important">Template Name </div>
        <div class="col-sm-4" style="display: inline-block !important">
                <select class="form-control" name="contracttemplatename" required id="contracttemplatename" placeholder="Select">
                        <option value="">Select</option>
                        <option value="Client">Client</option>
                        <option value="Security Provider">Security Provider</option>
                </select>
        </div>
</div>
<div class="form-group row contracttermsinfoinput inputclass" style="">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savecontracttermsinfo" id="savecontracttermsinfo" class="button btn submit">Save</button>
                <button type="button" attr-label="contracttermsinfolabel" attr-input="contracttermsinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">Contract Start Date
</div>
<div class="col-md-4">
                {{date("M d,Y",strtotime($contractdata->contract_startdate))}}
</div>
<div  class="col-md-4"><label class="error" for="contract_startdate"></label>   </div>
</div>
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">Contract Length (Years)
</div>
<div class="col-md-4">
        @if($contractdata->contract_length>0)
                {{str_replace(".00","",$contractdata->contract_length)}}
        @endif
</div>
</div>
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">Contract Expiry
</div>
<div class="col-md-4">
                {{date("M d,Y",strtotime($contractdata->contract_enddate))}}

</div>
</div>
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">Is there a renewal option
</div>
<div class="col-md-4">
        @if($contractdata->renewable_contract>0)
        Yes
        @else
        No
        @endif
</div>
</div>
@if($contractdata->renewable_contract>0)
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">How long is the renewal option (Years)
</div>
<div class="col-md-4">
                @if($contractdata->contract_length_renewal_years>0)
                        {{str_replace(".00","",$contractdata->contract_length_renewal_years)}}

                        @else
                @endif

</div>
</div>
@endif
<div class="form-group row contracttermsinfolabel">
        <div class="col-md-4">Does the client have a termination clause
        </div>
        <div class="col-md-4">
                @if($contractdata->termination_clause_client>0)
                Yes
                @else
                No
                @endif
        </div>
        <div  class="col-md-4"></div>
        </div>
        @if($contractdata->terminationnoticeperiodclient>0)
        <div class="form-group row contracttermsinfolabel">
        <div class="col-md-4">Termination notice period (Days)
        </div>
        <div class="col-md-4">
                        @if($contractdata->terminationnoticeperiodclient>0)
                                {{$contractdata->terminationnoticeperiodclient}}
                                @else
                                No
                        @endif

        </div>
        <div  class="col-md-4"></div>
        </div>
        @endif
        <div class="form-group row contracttermsinfolabel">
                <div class="col-md-4">Does service provider have a termination clause
                </div>
                <div class="col-md-4">
                        @if($contractdata->termination_clause>0)
                        Yes
                        @else
                        No
                        @endif
                </div>
                <div  class="col-md-4"></div>
                </div>
                @if($contractdata->terminationnoticeperiod>0)
                <div class="form-group row contracttermsinfolabel">
                <div class="col-md-4">Termination notice period (Days)
                </div>
                <div class="col-md-4">
                                @if($contractdata->terminationnoticeperiod>0)
                                        {{$contractdata->terminationnoticeperiod}}
                                        @else
                                        0
                                @endif

                </div>
                <div  class="col-md-4"></div>
                </div>
                @endif
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">How often will the pay/bill rate change
</div>
<div class="col-md-4">
              {{$contractdata->getBillingratechange->ratechangetitle}}
</div>
</div>
<div class="form-group row contracttermsinfolabel">
<div class="col-md-4">What is the annual increase allowable
</div>
<div class="col-md-4">
                {{$contractdata->contract_annualincrease_allowed}}

</div>
</div>

<div class="form-group row contracttermsinfolabel">
        <div class="col-md-4">Contract written template
        </div>
        <div class="col-md-4">
                {{$contractonourtemplatetitle}}

        </div>
</div>

<div class="form-group row ">
<div class="col-sm-12 candidate-screen-head" >Pricing Definition
        @canany(["edit_contract_pricingdefinition_information","super_admin"])
                <span class="editbutton fas fa-edit" id="pricingdefinitioninfo">&nbsp;</span>

        @endcanany
</div>

</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Total Annual Contract Billing
</div>
        <div class="col-md-4">
              <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" step=".01" name="total_annual_contract_billing" id="total_annual_contract_billing" placeholder="$" />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Total Annual Contract Wages & Benefits
</div>
        <div class="col-md-4">
                <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" name="total_annual_contract_wages_benifits" id="total_annual_contract_wages_benifits" placeholder="$" />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Total Annual (Expected) Contribution Margin
</div>
        <div class="col-md-4">
                <input type="text" class="form-control dollar" pattern="^\d*(\.\d{0,2})?$" name="total_annual_expected_contribution_margin" id="total_annual_expected_contribution_margin" placeholder="$" />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Total Hours per Week

</div>
<div class="col-sm-2" style="display:inline-block">
        <input type="number" class="form-control notdecimal"
        name="total_hours_perweek" id="total_hours_perweek" />
</div>
<div class="col-sm-1" style="display:inline-block">
     Minutes
</div>
<div class="col-sm-1" style="display:inline-block;">
        <select class="form-control"
                class="form-control notdecimal"
                name="total_hours_perweek_minutes" id="total_hours_perweek_minutes">
                @for($i=0;$i<60;$i++)
                        <option value="{{$i}}">{{$i}}</option>
                @endfor

        </select>
</div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Average Bill Rate

</div>
        <div class="col-md-4">
                <input type="text" class="form-control dollar markupval" name="average_billrate" pattern="^\d*(\.\d{0,2})?$" id="average_billrate" placeholder="$" />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Average Wage Rate

</div>
        <div class="col-md-4">
                <input type="text" class="form-control dollar  markupval" name="average_wagerate" pattern="^\d*(\.\d{0,2})?$" id="average_wagerate" placeholder="$" />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass">
        <div class="col-md-4">Average Markup


</div>
        <div class="col-md-4">
                <input type="text" class="form-control dollar" name="average_markup" pattern="^\d*(\.\d{0,2})?$" id="average_markup" placeholder="%" readonly />
        </div>
</div>
<div class="form-group row pricingdefinitioninfoinput inputclass" style="">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savepricingdefinitioninfo" id="savepricingdefinitioninfo" class="button btn submit">Save</button>
                <button type="button" attr-label="pricingdefinitioninfolabel" attr-input="pricingdefinitioninfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Load RFP Pricing Template
</div>
<div class="col-md-4">


<a style="color:black;text-decoration:none"
href="{{route("contracts.downloadcontractattachment",[
                "contract_id"=>$contractid,"file_id"=>$rfc_pricing_tamplate_attachment_id,"date"=>$createddate,"filetype"=>"rfc"
        ])}}"
 target="_blank" >RFC Template &nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>



</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Total Annual Contract Billing
</div>
<div class="col-md-4">
      @if ($contractdata->total_annual_contract_billing>0)
          {{str_replace(".00","",$contractdata->total_annual_contract_billing)}}
      @endif

</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Total Annual Contract Wages & Benefits
</div>
<div class="col-md-4">
        @if ($contractdata->total_annual_contract_wages_benifits>0)
            {{str_replace(".00","",$contractdata->total_annual_contract_wages_benifits)}}
        @endif

</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Total Annual (Expected) Contribution Margin
</div>
<div class="col-md-4">
        @if ($contractdata->total_annual_expected_contribution_margin>0)
            {{str_replace(".00","",$contractdata->total_annual_expected_contribution_margin)}}
        @endif


</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Total Hours per Week

</div>
<div class="col-md-4">
        @if ($contractdata->total_hours_perweek>0)
            {{ str_replace(".",":",$tothoursperweek)}}
        @endif


</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Average Bill Rate

</div>
<div class="col-md-4">
        @if ($contractdata->average_billrate>0)
            {{str_replace(".00","",$contractdata->average_billrate)}}
        @endif


</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Average Wage Rate

</div>
<div class="col-md-4">
        @if ($contractdata->average_wagerate>0)
            {{str_replace(".00","",$contractdata->average_wagerate)}}
        @endif


</div>
</div>
<div class="form-group row pricingdefinitioninfolabel">
<div class="col-md-4">Average Markup


</div>
<div class="col-md-4">
        @if ($contractdata->average_markup!="" && $contractdata->average_markup!="0")
            {{$contractdata->average_markup}}
        @endif


</div>
</div>
<div class="form-group row">
        <div class="col-sm-12 candidate-screen-head" >Pricing Details
                @canany(["edit_contract_pricingdetails_information","super_admin"])
                <span class="editbutton fas fa-edit" id="pricingdetailsinfo">&nbsp;</span>
                @endcanany

        </div>

</div>
<div class="form-group row pricinginfoinput inputclass">
        <div class="col-md-4">Billing Frequency<span class="mandatory">*</span>
        </div>
        <div class="col-md-4">
                <select class="form-control" required id="contract_billing_cycle" name="contract_billing_cycle" required>
                                <option value="">Select</option>
                                @foreach  ($lookUps['contractbillingcyclerepository'] as $contractbillingcycle)
                                        <option value="{{$contractbillingcycle->id}}">{{$contractbillingcycle->title}}</option>
                                @endforeach
                </select>
        </div>
</div>
<div class="form-group row pricinginfoinput inputclass">
        <div class="col-md-4">Payment Method<span class="mandatory">*</span>
        </div>
        <div class="col-md-4">
                <select class="form-control" required id="contract_payment_method" name="contract_payment_method" required>
                                <option value="">Select</option>
                                @foreach  ($lookUps['contractpaymentmethodrepository'] as $contractpaymentmethod)
                                        <option value="{{$contractpaymentmethod->id}}">{{$contractpaymentmethod->paymentmethod}}</option>
                                @endforeach
                </select>
        </div>
</div>
<div class="form-group row pricinginfoinput inputclass" style="">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savepricinginfo" id="savepricinginfo" class="button btn submit">Save</button>
                <button type="button" attr-label="pricinginfolabel" attr-input="pricinginfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row pricinginfolabel">
        <div class="col-md-4">Billing Frequency
        </div>
        <div class="col-md-4">
                {{$contractdata->getBillingFrequency->title}}
        </div>
        <div  class="col-md-4"><label class="error" for="contract-billing-cycle"></label>   </div>
</div>
<div class="form-group row pricinginfolabel">
        <div class="col-md-4">Payment Method
        </div>
        <div class="col-md-4">
                {{$contractdata->getPaymentmethod->paymentmethod}}
        </div>
</div>
@if(count($contractholidayagreement)>0)
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" >Stat Holidays
        @canany(["edit_contract_holiday_information","super_admin"])
                <span class="editbutton fas fa-edit" id="statholidays">&nbsp;</span>
        @endcanany
        
</div>

</div>
@foreach ($lookUps['holidayrepository'] as $holidays)
                @if($holidays->id > 0)
                <div class="form-group row statholidayinfoinput">
                        <div class="col-sm-4">{{$holidays->holiday}}
                        </div>
                        <div class="col-sm-4">
                                <select class="form-control holidaypayment" required id="holiday-payment-{{$holidays->id}}" name="holiday-payment-{{$holidays->id}}">
                                       <option value="0">Select</option>
                                        @foreach  ($lookUps['holidaypaymentallocationrepository'] as $holidaypayments)
                                        <option value="{{$holidaypayments->id}}">{{$holidaypayments->paymentstatus}}</option>
                                        @endforeach
                               </select>
                        </div>
                        <div  class="col-sm-4">   </div>
                </div>
                @endif
        @endforeach
        <div class="form-group row statholidayinfoinput inputclass" style="display: block;">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                        <button type="button" name="savestatinfo" id="savestatinfo" class="button btn submit">Save</button>
                        <button type="button" attr-label="statholidayinfolabel" attr-input="statholidayinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
                </div>
        </div>
<div class="form-group row candidate-screen-head statholidayinfolabel">
<div class="col-md-4 ">Holiday</div>
<div class="col-md-4">Payment Type</div>
</div>
@endif
@foreach ($contractholidayagreement as $contractholidays)
@if($contractholidays->getHolidaypayment!=null)
        <div class="form-group row statholidayinfolabel">
                        <div class="col-md-4">{{$contractholidays->getHoliday->holiday}}</div>
                        <div class="col-md-4">{{$contractholidays->getHolidaypayment->paymentstatus}}</div>
        </div>
@endif
@endforeach




<div class="form-group row">
<div class="col-sm-12 candidate-screen-head" > PO Information
        @canany(["edit_contract_po_information","super_admin"])
                <span class="editbutton fas fa-edit" id="poinfo">&nbsp;</span>
        @endcanany
        

</div>

</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Purchase Order (PO) Number<span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <input type="text" name="ponumber" id="ponumber" placeholder="Purchase Order" class="form-control" />
        </div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Company Name<span class="mandatory">*</span>
</div>
        <div class="col-md-4">
                <input type="text" name="pocompanyname" id="pocompanyname" placeholder="Company Name" class="form-control" />
        </div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Attention Name

</div>
        <div class="col-md-4"><input type="text" name="poattentionto" id="poattentionto" placeholder="Attention Name" class="form-control" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Title

</div>
        <div class="col-md-4"><input type="text" name="potitle" id="potitle" placeholder="Title" class="form-control" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
                <div class="col-md-4">Mailing Address

 </div>
                <div class="col-md-4">
                        <textarea type="text" name="pomailingaddress" id="pomailingaddress"  class="form-control">
                        </textarea>
                        </div>
                <div  class="col-md-4">
                           </div>
</div>

<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">City

</div>
        <div class="col-md-4">
                <input type="text" name="pocity" id="pocity" placeholder="City" class="form-control" />
        </div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Postal Code

</div>
        <div class="col-md-4"><input type="text" name="popostalcode" id="popostalcode" placeholder="Postal Code" class="form-control postal-code" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Phone Number

</div>
        <div class="col-md-4"><input type="text" name="pophone" id="pophone" placeholder="Phone No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Email

</div>
        <div class="col-md-4"><input type="email" name="poemail" id="poemail" placeholder="Email Address" class="form-control" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Cell


</div>
        <div class="col-md-4"><input type="text" name="pocellno" id="pocellno" placeholder="Cell No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Fax Number


</div>
        <div class="col-md-4"><input type="text" name="pofax" id="pofax" placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone" /></div>
</div>
<div class="form-group row poinfoinput inputclass">
        <div class="col-md-4">Billing Notes
</div>
        <div class="col-md-4"><input type="text" name="ponotes" id="ponotes" placeholder="Billing Notes" class="form-control" /></div>
</div>
<div class="form-group row poinfoinput inputclass" style="">
        <div class="col-md-4"></div>
        <div class="col-md-4">
                <button type="button" name="savepoinfo" id="savepoinfo" class="button btn submit">Save</button>
                <button type="button" attr-label="poinfolabel" attr-input="poinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
        </div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Purchase Order (PO) Number
</div>
<div class="col-md-4">
                {{$contractdata->ponumber}}

</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Company Name
</div>
<div class="col-md-4">
                {{$contractdata->pocompanyname}}

</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Attention Name

</div>
<div class="col-md-4">
                {{$contractdata->poattentionto}} </div>

</div>
<div class="form-group row poinfolabel">
        <div class="col-md-4">Title

        </div>

        <div class="col-md-4">
                        {{$contractdata->potitle}}
                </div>
        </div>
<div class="form-group row poinfolabel">
                <div class="col-md-4">Mailing Address

                </div>

                <div class="col-md-4">
                                {{$contractdata->pomailingaddress}}
                        </div>
                </div>

<div class="form-group row poinfolabel">
<div class="col-md-4">City

</div>
<div class="col-md-4">
                {{$contractdata->pocity}}

</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Postal Code

</div>
<div class="col-md-4">
                {{$contractdata->popostalcode}}
                </div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Phone Number

</div>
<div class="col-md-4">
                {{$contractdata->pophone}}

</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Email

</div>
<div class="col-md-4">
                {{$contractdata->poemail}}

</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Cell


</div>
<div class="col-md-4">
                {{$contractdata->pocellno}}
</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Fax Number


</div>
<div class="col-md-4">
                {{$contractdata->pofax}}
</div>
</div>
<div class="form-group row poinfolabel">
<div class="col-md-4">Billing Notes
</div>
<div class="col-md-4">
                {{$contractdata->ponotes}}
</div>
</div>
@if($po_attachment>0)
        <div class="form-group row poinfolabel">
        <div class="col-md-4">Upload</div>
        <div class="col-md-4">


        <a style="color:black;text-decoration:none"
        href="{{route("contracts.downloadcontractattachment",[
                "contract_id"=>$contractid,"file_id"=>$po_attachment,"date"=>$createddate,"filetype"=>"po"
        ])}}"
         target="_blank" >Purchase order &nbsp;&nbsp;&nbsp;<i class="fa fa-download" aria-hidden="true"></i></a>
        </div>
        </div>
@endif
<div class="form-group row">
<div class="col-sm-12 candidate-screen-head">Supervisor Information
        @canany(["edit_contract_supervisor_information","super_admin"])
                <span class="editbutton fas fa-edit" id="supinfo">&nbsp;</span>
        @endcanany
        
</div>

</div>
@if($contractdata->supervisorassigned < 1)
<div class="form-group row supinfolabel">
<div class="col-md-4 ">
Supervisor Assigned
</div>
<div class="col-md-4 ">
No
</div>

</div>
@endif

@if($contractdata->supervisorassigned>0)
<div class="container-fluid"  >
                <div class="form-group row supinfolabel">
                                <div class="col-md-4">Is there a supervisor assigned to site
                                </div>
                                <div class="col-md-4">
                                        Yes
                                </div>
                </div>
        <div class="form-group row supinfolabel">
                        <div class="col-md-4">Employee Number
                </div>
                        <div class="col-md-4">
                                        {{$contractdata->getSupervisoremployees->employee_no }}
                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Employee Name
                </div>
                <div class="col-md-4">
                                {{$contractdata->getSupervisorname->first_name }} {{$contractdata->getSupervisorname->last_name }}
                </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">View Training/Performance/Profile, etc.
                </div>
                        <div class="col-md-4">

                                @if ($contractdata->viewtrainingperformance==true)
                                    Yes
                                @else
                                    No
                                @endif
                                </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Cell Phone
         </div>
                        <div class="col-md-4">
                                        {{$contractdata->employeecellphone }}
                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Email Address</div>
                        <div class="col-md-4">
                                        {{$contractdata->employeeemailaddress }}
                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Telephone</div>
                        <div class="col-md-4">
                                        {{$contractdata->employeetelephone }}
                        </div>
                        </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Fax Number
                </div>
                        <div class="col-md-4">
                                        {{$contractdata->employeefaxno }}   </div>
                        <div  class="col-md-4">
                                  </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Who provides the cell phone
                </div>
                <div class="col-md-4">
                        @if (isset($contractdata->getCellphoneprovider->providername))
                            {{$contractdata->getCellphoneprovider->providername }}
                        @endif

                </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Tablet Required</div>
                        <div class="col-md-4">
                            @if ($contractdata->supervisortabletrequired == true)
                                Yes
                                @else
                                No
                            @endif
                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">CGL 360 in Use</div>
                        <div class="col-md-4">
                                        @if ($contractdata->supervisorcgluser == true)
                                        Yes
                                        @else
                                        No
                                    @endif

                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Public Transport or Car Required</div>
                        <div class="col-md-4">
                                        @if ($contractdata->supervisorpublictransportrequired == true)
                                        Yes
                                        @else
                                        No
                                    @endif


                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Directions or Nearest Intersection</div>
                        <div class="col-md-4">
                                {{$contractdata->direction_nearest_intersection}}

                        </div>
                        <div  class="col-md-4">  </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Department at Site</div>
                        <div class="col-md-4">
                                        {{$contractdata->department_at_site}}

                                </div>
                        <div  class="col-md-4">  </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Delivery Hours</div>
                        <div class="col-md-4">
                                        {{$contractdata->delivery_hours}}
                                </div>

                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Can Mail be Sent</div>
                        <div class="col-md-4">
                                        @if ($contractdata->supervisorcanmailbesent == true)
                                        Yes
                                        @else
                                        No
                                    @endif

                        </div>
                </div>
                <div class="form-group row supinfolabel">
                        <div class="col-md-4">Computer/Internet Access
                </div>
                        <div class="col-md-4">
                                        @if($contractdata->getSupervisordeviceaccess!=null)
                                                {{$contractdata->getSupervisordeviceaccess->DeviceType}}
                                        @endif

                        </div>
                </div>


</div>



@endif
<div class="form-group row supinfoinput inputclass">
        <div class="col-sm-4" style="display: inline-block">Is there a supervisor assigned to site
</div>
        <div class="col-sm-4" style="display: inline-block">
                <select class="form-control" id="supervisorassigned" name="supervisorassigned">
                        <option>Select any</option>
                        <option value="1">Yes</option>
                        <option value="0" selected>No</option>
                </select>
        </div>
</div>
<div class="container-fluid supinfoinput inputclass" id="supervisorornot" style="display:none;padding:0">
                <div class="form-group row ">
                                <div class="col-sm-4">Employee
                        </div>
                                <div class="col-sm-4">

                                                <select class="form-control" name="supervisoremployeenumber" required id="supervisoremployeenumber">
                                                                <option value="">Select</option>

                                                                @foreach ($lookUps['userlookuprepository'] as $key=>$value)
                                                                @if($value['emp_no']!="")
                                                                                <option data-empno="{{$value["emp_no"]}}"
                                                                                                value="{{$value['id']}}">{{$value['emp_no']}} - {{$value['full_name']}} </option>
                                                                @endif
                                                                @endforeach

                                                </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="supervisoremployeenumber"></label>   </div>
                        </div>
                        <div class="form-group row" style="display:none">
                                <div class="col-sm-4">Employee Name
                        </div>
                        <div class="col-sm-4">
                                <input type="text" readonly name="employeename" id="employeename" placeholder="Employee Name" class="form-control">
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="employeename"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">View Training/Performance/Profile, etc.
                        </div>
                                <div class="col-sm-4">

                                        <select class="form-control" id="viewtrainingperformance" name="viewtrainingperformance">
                                                        <option>Select Any</option>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected>No</option>
                                        </select>
                                        </div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="viewtrainingperformance"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Cell Phone
                 </div>
                                <div class="col-sm-4">
                                        <input  type="text" name="employeecellphone" id="employeecellphone" placeholder="Cell No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone">
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="employeecellphone"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Email Address</div>
                                <div class="col-sm-4">
                                        <input  type="email" name="employeeemailaddress" id="employeeemailaddress" placeholder="Email Address" class="form-control">
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="employeeemailaddress"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Telephone</div>
                                <div class="col-sm-4">
                                        <input  type="text" name="employeetelephone" id="employeetelephone" placeholder="Phone No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone"></div>
                                        <div  class="col-sm-4"><label class="error text-danger" for="employeetelephone"></label>   </div>
                                </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Fax Number
                        </div>
                                <div class="col-sm-4"><input type="text" name="employeefaxno" id="employeefaxno" placeholder="Fax No [ format (XXX)XXX-XXXX ]" pattern="[\(]\d{3}[\)]\d{3}[\-]\d{4}" class="form-control phone    "></div>
                                <div  class="col-sm-4"><label class="error text-danger" for="employeefaxno"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Who provides the cell phone
                        </div>
                        <div class="col-sm-4">
                                <select class="form-control" name="contractcellphoneprovider" required id="contractcellphoneprovider" placeholder="Select">
                                        <option value="">Select</option>

                                        @foreach ($lookUps['contractcellphoneproviderrepository'] as $cellphoneprovider)
                                                        <option   value="{{$cellphoneprovider->id}}">{{$cellphoneprovider->providername}} </option>
                                        @endforeach

                                </select>
                        </div>
                        <div  class="col-sm-4"><label class="error text-danger" for="contractcellphoneprovider"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Tablet Required </div>
                                <div class="col-sm-4">
                                        <select class="form-control" id="supervisortabletrequired" name="supervisortabletrequired">
                                                        <option value="0">Select Any</option>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected="">No</option>
                                        </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="supervisortabletrequired"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">CGL 360 in Use</div>
                                <div class="col-sm-4">
                                        <select class="form-control" id="supervisorcgluser" name="supervisorcgluser">
                                                        <option>Select Any</option>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected="">No</option>
                                        </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="supervisorcgluser"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Public Transport or Car Required </div>
                                <div class="col-sm-4">
                                        <select class="form-control" id="supervisorpublictransportrequired" name="supervisorpublictransportrequired">
                                                <option>Select Any</option>
                                                <option value="1">Yes</option>
                                                <option value="0" selected="">No</option>
                                        </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="supervisorpublictransportrequired"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Directions or Nearest Intersection</div>
                                <div class="col-sm-4"><input type="text" name="direction_nearest_intersection" id="direction_nearest_intersection" placeholder="Nearest Intersection" class="form-control"></div>
                                <div  class="col-sm-4"><label class="error text-danger" for="direction_nearest_intersection"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Department at Site</div>
                                <div class="col-sm-4"><input type="text" name="department_at_site" id="department_at_site" placeholder="Department at Site" class="form-control"></div>
                                <div  class="col-sm-4"><label class="error text-danger" for="department_at_site"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Delivery Hours</div>
                                <div class="col-sm-4"><input type="number" name="delivery_hours" id="delivery_hours" placeholder="Delivery Hours" class="form-control"></div>
                                <div  class="col-sm-4"><label class="error text-danger" for="delivery_hours"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Can Mail be Sent  </div>
                                <div class="col-sm-4">
                                                <select class="form-control" id="supervisorcanmailbesent" name="supervisorcanmailbesent">
                                                                <option>Select Any</option>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected="">No</option>
                                                </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="supervisorcanmailbesent"></label>   </div>
                        </div>
                        <div class="form-group row">
                                <div class="col-sm-4">Computer/Internet Access
                        </div>
                                <div class="col-sm-4">
                                                <select class="form-control" name="contractdeviceaccess" required id="contractdeviceaccess" placeholder="Select">
                                                                <option value="">Select</option>

                                                                @foreach ($lookUps['contractdeviceaccessrepository'] as $contractdeviceaccess)
                                                                                <option   value="{{$contractdeviceaccess->id}}">{{$contractdeviceaccess->DeviceType}} </option>
                                                                @endforeach

                                                </select>
                                </div>
                                <div  class="col-sm-4"><label class="error text-danger" for="contractdeviceaccess"></label>   </div>
                        </div>
                        


        </div>
        <div class="form-group row supinfoinput inputclass"   >
                <div class="col-md-4"></div>
                <div class="col-md-4">
                        <button type="button" name="savesupervisorinfo" id="savesupervisorinfo" class="button btn submit">Save</button>
                        <button type="button" attr-label="supinfolabel" attr-input="supinfoinput" 
                        name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
                </div>
        </div>
<div class="container-fluid" style="padding:0">
                <div class="form-group row">
                                <div class="col-sm-12 candidate-screen-head">Scope of Work
                                        @canany(["edit_contract_scopeofwork_information","super_admin"])
                                                <span class="editbutton fas fa-edit" id="scopedetailsinfo">&nbsp;</span>
                                        @endcanany
                                        
                </div></div>
                <div class="form-group row scopeinfoinput inputclass">
                        <div class="col-md-4" style="vertical-align: top">Scope of Work<span class="mandatory">*</span></div>
                        <div class="col-md-4">
                                <textarea maxlength="3000" class="form-control" name="scopeofwork" id="scopeofwork" rows="7" style="resize:none"></textarea>
                                <small>(maximum 3000 chars)</small>
                        </div>

                </div>
                <div class="form-group row scopeinfoinput inputclass"  id="parentdiv" >
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                                <button type="button" name="savescopeinfo" id="savescopeinfo" class="button btn submit">Save</button>
                                <button type="button" attr-label="scopeinfolabel" attr-input="scopeinfoinput" name="cancelbutton" id="cancelbutton" class="button btn submit cancelbutton">Cancel</button>
                        </div>
                </div>
                <div class="form-group row scopeinfolabel">

                <div class="col-sm-12">
                                {{$contractdata->scopeofwork}}
                </div>


        </div>

<div class="container-fluid" id="contractamendments" style="padding:0">
                <div class="form-group row ">
                        <div class="col-md-4 candidate-screen-head">Amendment Description</div>
                        <div class="col-md-4 candidate-screen-head">Attachment</div>
                        <div class="col-md-2 candidate-screen-head">Created By</div>
                        <div class="col-md-2 candidate-screen-head"></div>
                </div>
                @php
                    $i=0;
                @endphp
                @foreach ($contractamendments as $amendments)
                @php
                    $i++;
                @endphp
                        <div class="form-group row">
                                        <div class="col-md-4 " style="overflow-wrap: break-word;" >
                                            {{$amendments->amendment_description}}
                                        </div>
                                        <div class="col-md-4 ">
                                                @if($amendments->amendment_attachment_id>0)
                                        <a style="color:black;text-decoration:none"
                                        href="{{route("contracts.downloadcontractattachment",[
                                                "contract_id"=>$contractid,"file_id"=>$amendments->amendment_attachment_id,"date"=>date("Y-m-d",strtotime($amendments->created_at)),"filetype"=>"amendment"
                                        ])}}"
                                         target="_blank" >Amendment {{$i}} &nbsp;&nbsp;&nbsp;
                                         <i class="fa fa-download" aria-hidden="true"></i></a>
                                        @else
                                        No attachment
                                        @endif
                                        </div>
                                        <div class="col-md-2 ">
                                                        {{$amendments->getCreateduser->first_name}} {{$amendments->getCreateduser->last_name}}
                                        </div>
                                        <div class="col-md-2 "><i attr-text="{{$amendments->amendment_description}}" 
                                                attr-attachid="{{$amendments->amendment_attachment_id}}" attr-id="{{$amendments->id}}" class="fa fa-trash removeamendment"></i></div>

                        </div>
                @endforeach



</div>

<div class="container-fluid" id="contractamendments" style="padding:0">
        <form method="POST"  id="editcmuf" name="editcmuf" enctype="multipart/form-data">

        <div class="form-group row">
                <div class="col-sm-12 candidate-screen-head"> Amendments
                        <input type="hidden" name="amendmentcount" id="amendmentcount" value="1">
                </div>
        </div>
                <div class="form-group row">
                    <div class="col-md-4">Description</div>
                    <div class="col-md-4">
                            <textarea id="amendment_description" name="amendment_description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                        <div class="col-md-4">Attachment</div>
                        <div class="col-md-4">
                                <input type="file" name="amendment_attachment_id" id="amendment_attachment_id" class="form-control">
                                <input type="hidden" name="amendment_document_attachment" id="amendment_document_attachment" value="">
                        </div>
                        <div class="col-md-4"><label class="error" for="amendment_attachment_id"></label> </div>
                </div>
                @canany(["edit_contract_amendment_information","super_admin"])

                <div class="form-group row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                                <input type="hidden" name="editamendment" id="editamendment" value="">
                                <button type="button" attr_file="amendment_attachment_id"  attr_hidden="amendment_document_attachment" id="uploadamend" class="button btn submit uploadamend">Add Amendment</button>
                        </div>
                </div>
                @endcanany
        </form>
</div>





    <div class="form-group row">
            <div class="col-sm-12">   </div>
    </div>
</div>
</div>
</div>
@endsection
