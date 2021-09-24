<script>
    $(function(e){
        $(".afterprojectselect").hide();
        $('body').loading({
                stoppable: false,
                message: 'Please wait...'
            });
        $('body').css('overflowY', 'hidden');


        $(".afterprojectselect").hide();
        $("#project").select2({
            tags: true,
            placeholder: "Select Customer",
            allowClear: true
        }).after(function(e){
           $(".filterbox").css("display","block")
        });

        $("#payperiod").select2({
            placeholder: "Select Pay Period"
        });

        if($("#hiddenrejectedcustomer").val()!=""){
            $("#project").val($("#hiddenrejectedcustomer").val()).select2();
            var payperiod = $("#hiddenrejectedpayperiodid").val();
            payperiod = jQuery.parseJSON(payperiod);
            $("#payperiod").val(payperiod).select2();
            // payperiod.forEach(element => {
            //     $("#payperiod").val(element).select2();
            // });
            $(".addblock").trigger('click');
        }
    });
    $(document).ready(function () {
        $('body').loading('stop');
    });
    $(".cancelmodal").on("click",function(e){
        e.preventDefault();
        $("#hiddenemployeeid").val("");
        $("#hiddenscheduledate").val("");
        $("#hiddenpayperiodid").val("");
        $("#editflag").val("0");

        $("#myModal").modal('hide');
    });
    $(".close").on("click",function(e){
        e.preventDefault();
        $("#hiddenemployeeid").val("");
        $("#hiddenscheduledate").val("");
        $("#hiddenpayperiodid").val("");
        $("#editflag").val("0");

        $("#myModal").modal('hide');
    });
    $(".saveschedule").on("click",function(e){
        e.preventDefault();
        var customerid = $("#project").val();


        var supervisornotes = $("#supervisornotes").val();
        $.ajax({
            type: "get",
            url: "{{route('scheduling.saveprecheck')}}",
            data: {"customer":customerid,supervisornotes:supervisornotes},
            success: function (response) {
                var jqdata = jQuery.parseJSON(response);

                if(jqdata.code!="200"){
                    swal("Alert", jqdata.message, "warning");
                }else{
                    savefunction();
                }
            }
        });

    })

    var savefunction = function(){
        var customerid = $("#project").val();
        var payparray = [];
        i=0;
        $(".payperiodsweekcontrol").each(function(e){
             payper = $(this).attr("attr-payperiod");
             week = $(this).attr("week");
            weeklyhours = $(this).val();
            payparray[i]={"payperiod":payper,"week":week,"weeklyhours":weeklyhours};
            //payparray[i]["week"]=week;
           // payparray[i]["weeklyhours"]=weeklyhours;

            i++;
        });
        swal({
            title: "Are you sure?",
            text: "You are about to save the schedule. Proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
        function () {
            var supervisornotes = $("#supervisornotes").val();
            var variance = $("#variance").val();
            var scheduleindicator = $("#scheduleindicator").val();
            var avghoursperweek = $(".avghoursperweek").val();
            var contractual_hours = $("#contracthours").val();

            $.ajax({
                                    type: "post",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: "{{route('scheduling.saveschedule')}}",
                                    data: {
                                        "customerid": customerid,"initialscheduleid":$("#initialrequirementid").val(),
                                        "supervisornotes":supervisornotes,"payparray":JSON.stringify(payparray),
                                        "variance":variance,"scheduleindicator":scheduleindicator,
                                        "avghoursperweek":avghoursperweek,"contractual_hours":contractual_hours},
                                    success: function(response) {
                                        // $('a[data-id="'+id+'"]').get(0).click();
                                             var jqdata = jQuery.parseJSON(response);
                                             if(jqdata.code!="200"){
                                                swal("Alert", jqdata.message, "warning");
                                             }else{
                                                swal("Saved", "Scheduling request submitted successfully", "success");
                                                $(".saveschedule").hide().after(function(e,jqdata){


                                                $(".allocatedblock").off("click");
                                                $(".clickme").off("click");
                                                $(".editsched").off("click");
                                                $(".resetschedule").hide();
                                                $(".confirmschedule").hide();
                                                $("#addspares").hide();
                                                $("#supervisorModal").modal("hide");




                                                });

                                             }

                                    }
                                });
        });

    }
    var tooltipcreation = function(ev,content){
        var ulcontentarray = content.split("|");
        var ulcontent ="<ol>";
        ulcontentarray.forEach(element => {
            if(element.trim()!=""){
                ulcontent+="<li>"+element+"</li>";
            }

        });
        ulcontent+="</ol>";
        var div = $('<div class="image-wrapper">')
        .addClass('image-wrapper')
        .css("cssText","left:400px !important;"+"top:"+ ev.pageY + 'px !important')


            .append("<p><b>Completed Training</b><span id='closetraining' title='Press ESC' style='float:right;cursor:pointer'>x</span></p><p>"+ulcontent+"</p>")
            .appendTo(document.body);

            $("#closetraining").on("click",function(e){
                $(".image-wrapper").remove();
            })
/*
        setTimeout(function() {
            div.addClass('fade-out');
            setTimeout(function() { div.remove(); }, fadeDuration);
        }, 200);
        */
    };

    $(".addblock").on("click",function(e){
        e.preventDefault();
        var screenheight = screen.height;
        var mainblockheight = screenheight*0.50;
        $("#mainblock").css("max-height",mainblockheight+"px")
        var blockcontent  = $("#samplecontent").html();
        var project = $("#project").val();
        var payperiod = $("#payperiod").val();
        var initialrequirementid = $('#initialrequirementid').val();
        if(project < 1){
            swal("Alert", "Customer cannot be empty", "warning");

        }else if(payperiod ==""){
            swal("Alert", "Pay period cannot be empty", "warning");

        }else{
            $.ajax({
            type: "get",
            url: "{{route('scheduling.precheck')}}",
            data: {projectid:$("#project").val(),payperiod:$("#payperiod").val(), initialrequirementid: initialrequirementid},
            success: function (response) {
                var jqdata = jQuery.parseJSON(response);
                if(jqdata.code!="200"){
                   // swal("Alert  : "+jqdata.message);
                    swal("Alert", jqdata.message, +jqdata.message);
                }else{
                    $(this).hide();
                    $(".beforeprojectselect").hide();
                    $.ajax({
            type: "get",
            url: "{{route('scheduling.processedblock')}}",
            data: {projectid:$("#project").val(),payperiod:$("#payperiod").val(),initialrequirementid: initialrequirementid},
            success: function (response) {
                customershifts(e);
                var projectname = $("#project").select2('data')[0].text;
                $(".projectbox").html(projectname);
                var payperiodttext = "";
                var payperiods =$("#payperiod").select2('data');
                var c = 1;
                payperiods.forEach(element => {
                    if(c == "1"){
                        payperiodttext+=(element.text);

                    }else{
                        payperiodttext+=" , "+(element.text);
                    }

                    c++;
                });
                $(".payperiodbox").html(payperiodttext);
                $(".afterprojectselect").show();
                var height = $( document ).height()*.70;
                var mainheight = $( document ).height()*.65;
                var divheight = $( document ).height()*.65;
                var payperiodcount = $("#payperiod").select2('data').length;
                // $("#mainarea").css("height",mainheight+"px");
                if(payperiodcount>1){

                    //$(".schedules").css("width",2500*payperiodcount);

                }else{
                   // $(".schedules").css("width",2500);

                }
                $("#content-div").css("width","99%");
                $("#schedules").html(response).after(function(e){

                    $(".trainingdetail").on("click",function(e){

                        $(".image-wrapper").remove()
                        var content = $(this).attr("attr-title");
                        tooltipcreation(e,content);
                    })


                    $('body').on("click",function(e){

                       // $(".image-wrapper").remove();
                       /*
                        $(".trainingdetail").on("click",function(e){
                            var content = $(this).attr("title");
                        tooltipcreation(e,content);
                        $(".trainingdetail").unbind("mouseover mouseout");;

                    });*/
                    })


                    $('#mainheaddiv').on('scroll', function () {
                    //$('.column2').scrollTop($(this).scrollTop());
                        $('.contentdiv').scrollLeft($(this).scrollLeft());
                    });
                    if(initialrequirementid>0){
                        $(".payperiodsweekcontrol").each(function(e){
                            var controlname = this.id;
                            triggerpayperiodcalculation(controlname);
                        })

                    }
                    if($("#contracthours").val()!=""){
                        var contractual_hours_string = $("#contracthours").val();
                        let minval = Math.ceil(parseInt(contractual_hours_string.split(".")[1])*1)
                        let hourvalue = contractual_hours_string.split(".")[0]+":"+minval;
                        if(contractual_hours_string !== "") {
                            contractual_hours_string = contractual_hours_string.replace(".",":");
                        }
                        $(".contracthours").html(hourvalue);
                    }else{
                        $(".contracthours").html("Nil");
                        $(".schedindicator").html("False");
                    }
                    $.ajax({
                        type: "get",
                        url: "{{route('scheduling.prepopulatelogs')}}",
                        data: {projectid:$("#project").val(),payperiod:$("#payperiod").val()},
                        success: function (response) {
                            var jqdata = jQuery.parseJSON(response);
                            jqdata.forEach(element => {

                                var editbuttonblock ='<div style="'+(element.overlaps? 'background:red':'')+';"  class="allocatedblock clickme editsched" attr-employeeid="'+ element.employee
                                +'"  attr-starttime="'+element.starttime+'" attr-payperiod="'+element.payperiod+'"  attr-hours="'+element.hours+'"  attr-endtime="'+element.endtime
                                +'"  attr-date="'+element.scheduledate
                                +'" style="text-align:center;cursor:pointer">&nbsp;</div>';
                                var block = element.starttime+"<br/>"+element.endtime+"<br/>"
                                +'<a class="clickme editsched" attr-employeeid="'+ element.employee
                                +'"  attr-starttime="'+element.starttime+'" attr-payperiod="'+element.payperiod+'"  attr-endtime="'+element.endtime+'"  attr-date="'+element.scheduledate
                                +'" style="text-align:center;cursor:pointer">Edit</a>';
                                var blockid = element.employee+"-"+element.scheduledate;
                                $("#"+blockid).html(editbuttonblock);
                                toolipfunction(e);
                            });

                        }
                    }).done(function(e){

                    postevents(e,"response");
                });
                });
                $(".ajaxselect").select2();
            }
        }).done(function(e){
            $(".saveschedule").show();
            /*
            $('.starttime').timepicki({
                start_time: ["09", "00", "AM"]
            });

            $('.endtime').timepicki({
                start_time: ["06", "00", "PM"]
            });
*/
            $('.enddate').datepicker({
                "format": "yyyy-mm-dd",
                "setDate": new Date(),
            });
            var height = $( window ).height()*.58;
            var divheight = $( document ).height()*.65;
            var windwidth = $( window ).width()*.75;
            /*
            var table = $("#scheduletable").dataTable({
                "ordering": false,
                "scrollX": true,
                "scrollY": height,
                "paging":false,
                "scrollCollapse": true,
                "searching": false,

                });
                */
                var payperiodcount = $("#payperiod").select2('data').length;

                if(payperiodcount>1){
                    //$("#contentblock").css("width",2500*payperiodcount+"px");

                    $("#mainheadtable").css("width",2500*payperiodcount+"px");
                    $("#scheduletable").css("width",2500*payperiodcount+"px");


                }else{
                    //$("#contentblock").css("width","2500px");

                    $("#mainheadtable").css("width","2500px");
                    $("#scheduletable").css("width","2500px");
                }
                var nametablewidth = (screen.width)*.20;
                $("#nametable").css("width",nametablewidth+"px");
                $("#mainheaddiv").css("width",windwidth+"px");
                $(".contentdiv").css("width",(windwidth)+"px");




            $("#mdl_remove_cart").on("click",function(e){
                e.preventDefault();
                var customer = $("#project").val();
                var employeeid = $("#hiddenemployeeid").val();
                var scheduledate = $("#hiddenscheduledate").val();
                var payperiod = $("#hiddenpayperiodid").val();
                var starttime = $("#starttime").val();
                var endtime = $("#endtime").val();
                $.ajax({
                    type: "get",
                    url: "{{route('scheduling.removeschedulecartentry')}}",
                    data: {customer:customer,employeeid:employeeid,scheduledate:scheduledate,starttime:starttime,endtime:endtime,payperiod:payperiod},
                    success: function (response) {
                        var jqdata = jQuery.parseJSON(response);
                        if(jqdata.response>0){
                            var weekcontrol ="week"+jqdata.week+"-payp"+jqdata.payperiod;
                            var totalworkhoursarray = $("#"+weekcontrol).val().split(".");
                            var removedworkhoursarray =(jqdata.hours).split(".");
                            var totalworkhourminutes = ((parseInt(totalworkhoursarray[0]*60))+parseInt(totalworkhoursarray[1]));
                            var totalremovedworkhourminutes = ((parseInt(removedworkhoursarray[0]*60))+parseInt(removedworkhoursarray[1]));
                            var afterworkhourminutes = timeConvert(totalworkhourminutes - totalremovedworkhourminutes);


                            $("#"+weekcontrol).val(parseFloat(afterworkhourminutes));

                            var block = '<div  class="emptyallocation clickme editsched" attr-starttime=""  attr-hours="0" attr-endtime=""';
                            block += 'attr-employeeid="'+employeeid+'" attr-payperiod="'+payperiod+'" attr-date="'+scheduledate+'" >1</div>';
                            var blockid = employeeid+"-"+scheduledate;
                            $("#"+blockid).html(block);
                            $("#myModal").modal('hide');
                            triggerpayperiodcalculation(weekcontrol);


                            $(document).on("click",".editsched",function(e){
                                $("#hiddenemployeeid").val($(this).attr("attr-employeeid"));
                                $("#hiddenscheduledate").val($(this).attr("attr-date"));
                                $("#hiddenpayperiodid").val($(this).attr("attr-payperiod"));
                                $("#editflag").val("1");
                                $("#starttime").val($(this).attr("attr-starttime"));
                                $("#endtime").val($(this).attr("attr-endtime"));

                                $("#myModal").modal();
                                tagevents();
                            });
                        }else{
                            $("#myModal").modal('hide');
                        }
                    }
                });
            });

            $("#mdl_save_change").on("click",function(e){
                e.preventDefault();
                var cls = "weekday";
                var customer = $("#project").val();
                var employeeid = $("#hiddenemployeeid").val();
                var scheduledate = $("#hiddenscheduledate").val();
                var payperiod = $("#hiddenpayperiodid").val();

                var starttime = $("#starttime").val();
                var endtime = $("#endtime").val();
                var schedules = $("#emp-"+employeeid).val();
                var editflag = $("#editflag").val();
                if(starttime=="" || endtime==""){
                    swal("Alert", "Time is a madatory field", "warning");
                }
                else
                {
                $.ajax({
                    type: "post",
                    url: "{{route('scheduling.analyseandprocessdata')}}",
                    headers: {'schedules':schedules},
                    data: {customer:customer,employeeid:employeeid,scheduledate:scheduledate,starttime:starttime,endtime:endtime,payperiod:payperiod,schedules:schedules,editflag:editflag},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var jqdata = jQuery.parseJSON(response);
                        if(jqdata.code=="200"){
                            let weekhours = jqdata.extras.weekhours;
                            let week = jqdata.extras.week;
                            let payperiod = jqdata.extras.payperiod;
                            let controlname = "week"+week+"-payp"+payperiod;
                            if(weekhours>0){
                                $("#"+controlname).val(weekhours);
                                //$("#"+controlname).trigger("change");
                                triggerpayperiodcalculation(controlname);
                            }

                            $("#emp-"+employeeid).val(response);
                            var editbuttonblock ='<div  class="allocatedblock clickme editsched" attr-employeeid="'+ $("#hiddenemployeeid").val()
                        +'"  attr-starttime="'+starttime+'" attr-payperiod="'+payperiod+'"  attr-hours="'+jqdata.extras.hours+'"  attr-endtime="'+endtime+'"  attr-date="'+$("#hiddenscheduledate").val()
                        +'" style="text-align:center;cursor:pointer">&nbsp;</div>';
                        var block = starttime+"<br/>"+endtime+"<br/>"
                        +'<a class="clickme editsched" attr-employeeid="'+ $("#hiddenemployeeid").val()
                        +'"  attr-starttime="'+starttime+'" attr-payperiod="'+payperiod+'"  attr-endtime="'+endtime+'"  attr-date="'+$("#hiddenscheduledate").val()
                        +'" style="text-align:center;cursor:pointer">Edit</a>';
                        var blockid = $("#hiddenemployeeid").val()+"-"+$("#hiddenscheduledate").val();
                        $("#"+blockid).html(editbuttonblock);

                        $("#myModal").modal('hide');

                        var weekday=new Array(7);
                        weekday[0]="Sunday";
                        weekday[1]="Monday";
                        weekday[2]="Tuesday";
                        weekday[3]="Wednesday";
                        weekday[4]="Thursday";
                        weekday[5]="Friday";
                        weekday[6]="Saturday";

                        var dayrecognition = new Date($("#hiddenscheduledate").val());
                        var day = weekday[dayrecognition.getDay()];

                        if(day == "Sunday" || day == "Saturday"){
                            cls = "weekend";
                           // $("#"+blockid).removeClass("weekday");
                           // $("#"+blockid).addClass("weekend");
                        }else{
                           // $("#"+blockid).addClass("weekday");
                        }

                        $("#hiddenemployeeid").val("");
                        $("#hiddenscheduledate").val("");
                        $("#hiddenpayperiodid").val("");
                        $("#editflag").val("0");

                        postevents(e,response);

                        toolipfunction(e);




                        }else{
                            swal("Alert", jqdata.message, "warning");
                        }



                    }
                });
            }


            })

            postclick = function(e){
                $("#myModal").modal();

                tagevents();


            }
            $(document).on("click",".clickme",function(e){
                $("#hiddenemployeeid").val($(this).attr("attr-employeeid"));
                $("#hiddenscheduledate").val($(this).attr("attr-date"));
                $("#hiddenpayperiodid").val($(this).attr("attr-payperiod"));
                $('#starttime').val("");
                $('#endtime').val("");
                $('#starttime').timepicker('setTime', '09:00 AM');
                $('#endtime').timepicker('setTime', '06:00 PM');
                var date = $(this).attr("attr-date");
                var g1 = new Date();
                var updatePastScheduleAllowed = $('#updatePastScheduleAllowed').val();
                // (YYYY-MM-DD)
                var g2 = new Date(date);
                var Difference_In_Days = (g1.getTime() - g2.getTime()) / (1000 * 3600 * 24);

                if((Difference_In_Days<1) || updatePastScheduleAllowed){
                    postclick();
                }else{
                    swal("Alert", "Schedule allowed for future dates only", "warning");
                    return false;
                }

            })
        })
                }
            }
        });
        }


    });


    var triggerpayperiodcalculation = function(weekcontrol){
        let contracthours = $("#contracthours").val();
        var i =0;
        let weekcount = 0;
        let schedindicator = "true";
        var weeklyhours = 0;
        $(".varianceblock").removeClass("greenbg");
        $(".varianceblock").removeClass("redbg");
        if(contracthours<1){
            $(".varianceblock").html("Nil");
            $(".varianceblock").addClass("redbg");
        }
        var totalscheduletime="00.00";
        var loopindicator = 0;
        $(".payperiodsweekcontrol").each(function(e){
            var payperiod = $(this).attr("attr-payperiod");
            var time = $(this).val();
            var  whminutes = Convertominutes($(this).val());

            weekcount = weekcount + whminutes;
            weeklyhours = $(this).val();
            if(parseFloat(weeklyhours)!=contracthours && schedindicator=="true"){
                schedindicator = "false";
                loopindicator = 1;
                $(".varianceblock").addClass("redbg");
            }

            i++;
        });

        //alert(Convertohours(weekcount,i));
        let avghoursperweek = Convertohours(weekcount,i);
        $(".avghoursperweek").val(avghoursperweek);
        var afterlen = 0;
        try {
           var avghoursperweekarray = avghoursperweek.split(".");
           if(avghoursperweekarray[1].length==2){
                afterlen=2;
           }else{
                afterlen=1;
           }
        } catch (error) {

        }
        if(afterlen==1){
            $(".avghoursperweek").html(avghoursperweek.replace(".",":")+"0");
        }else{
            $(".avghoursperweek").html(avghoursperweek.replace(".",":"));
        }

       // alert(avghoursperweek);

        let variance = 0;
        if(contracthours>0){
            variance = Convertominutes(avghoursperweek) - Convertominutes(contracthours);
            var sign = "+";
            var variancehtml = "";
            varianceafterlen = 0;
            if(variance<0){
                sign="-";
            }else if(variance>0){


            }

            if(avghoursperweek>0 && contracthours>0){
                var variancehtmlval = Convertohourssingle(variance)
                if(variancehtmlval>0){
                    try {
                    variancehtmlarray = variancehtmlval.split(".");
                    varianceafterlen = variancehtmlarray[1].length;
                    } catch (error) {

                    }
                    if(varianceafterlen==1){
                        variancehtml = variancehtmlval.replace(".",":")+"0";
                    }else if(varianceafterlen>1){
                        variancehtml = variancehtmlval.replace(".",":")
                    }else{
                        variancehtml = variance;
                    }

                }
                if(variance==0){
                    $(".varianceblock").html("0");
                    $("#variance").val("0");//Valid block
                    schedindicator = "true";
                    $(".varianceblock").addClass("greenbg");

                }
                else if(variance>0){
                    $(".varianceblock").html(sign+""+variancehtml);
                    $("#variance").val(sign+""+Convertohourssingle(variance));
                    $(".varianceblock").addClass("redbg");
                    schedindicator = "true";
                }else{
                    $(".varianceblock").html(sign+""+variancehtml);
                    $("#variance").val(sign+""+Convertohourssingle(variance));
                    $(".varianceblock").addClass("redbg");
                }

            }else{
                $("#variance").val("0");//Means Invalid data
                $(".varianceblock").html("Nil");
                $(".varianceblock").addClass("redbg");
            }

        }else{
            $("#variance").val("0");//Means Invalid data
            $(".varianceblock").html("Nil");
            $(".varianceblock").addClass("redbg");
        }

        if(schedindicator=="false"){
            $("#scheduleindicator").val("false");
            $(".schedindicator").html("False");
            $(".schedindicator").removeClass("greenbg");
            $(".schedindicator").addClass("redbg");
        }else{

            if(loopindicator==0 && contracthours>0){
                $("#scheduleindicator").val("true");
                $(".schedindicator").html("True");
                $(".schedindicator").addClass("greenbg");
                $(".schedindicator").removeClass("redbg");
            }else if(loopindicator==1){
                $("#scheduleindicator").val("false");
                $(".schedindicator").html("False");
                $(".schedindicator").removeClass("greenbg");
                $(".schedindicator").addClass("redbg");
            }else {
                $("#scheduleindicator").val("false");
                $(".schedindicator").html("False");
                $(".schedindicator").removeClass("greenbg");
                $(".schedindicator").addClass("redbg");
            }

        }
    }

    toolipfunction = function(e){
        $('.allocatedblock').on("mouseover",function(e){
                            var sttime = $(this).attr("attr-starttime");
                            var entime = $(this).attr("attr-endtime");
                            var hours = $(this).attr("attr-hours");
                            hovershowdiv(e,'tooltip',sttime,entime,hours);
                        });

                        $('.allocatedblock').on("mouseout",function(e){

                            hoverhidediv(e,'tooltip');
                        });
    }
    var postevents=function(e,response){


                        $(document).on("click",".editsched",function(e){
                            $("#hiddenemployeeid").val($(this).attr("attr-employeeid"));
                            $("#hiddenscheduledate").val($(this).attr("attr-date"));
                            $("#hiddenpayperiodid").val($(this).attr("attr-payperiod"));
                            $("#editflag").val("1");
                            $("#starttime").val($(this).attr("attr-starttime"));
                            $("#endtime").val($(this).attr("attr-endtime"));

                            $("#myModal").modal();
                            tagevents();
                        });
    }

    function Convertohours(minutes,count){
        let hour = 0;
        let hourminutes = 0;
        let remainingminutes = 0;

        let convertedhours = parseInt(minutes)/parseInt(count);
        hour = Math.floor(convertedhours/60);
        remainingminutes = convertedhours - (hour*60);
        let returnVar=(hour+(remainingminutes/100)).toFixed(2);
        return returnVar;

    }

    function Convertohourssingle(minutes){
        if(minutes<1){
            minutes = minutes*-1;
        }
        let hour = 0;
        let hourminutes = 0;
        let remainingminutes = 0;
        let convertedhours = minutes;
        hour = Math.floor(convertedhours/60);
        remainingminutes = convertedhours - (hour*60);
        let returnVar=(hour+(remainingminutes/100)).toFixed(2);

        return returnVar;

    }
    function Convertominutes(hours){
        if(hours>0){
            try {
                var splithours = hours.split(".");
                var mainhour = (splithours[0]);
                var minutes = (splithours[1]);
                try {
                if(minutes.length<2){
                    minutes =minutes+"0";
                    }
                } catch (error) {
                    minutes = 0;
                }
                return (parseInt(mainhour)*60)+parseInt(minutes);
            } catch (error) {
            }

        }else{
            return 0;
        }



    }
    function hovershowdiv(e,divid,sttime,entime,hours){
        var splithours = hours.split(".");
        var mainhour = (splithours[0]);
        var minutes = (splithours[1]);
        if(splithours[1]!=""){
            if(minutes.length>2){
                minutes = minutes.substr(0,2);
            }
            if(mainhour.length<2){
                mainhour = "0"+mainhour;
            }
            hours = mainhour+" : "+minutes;
        }else{
            hours = mainhour;
        }
        //hours = hours.replace("."," : ");
        $("#timing").html(sttime+" - "+entime+" ("+hours+" hours)");
        var left  = e.clientX  + "px";
        var top  = e.clientY  + "px";
        var div = document.getElementById(divid);
        div.style.left = left;
        div.style.top = top;

        $("#"+divid).show();
        //return false;
}
var tagevents = function(){
$(".tags").on("click",function(e){
    e.preventDefault();
    var tagid = this.id;
    var starttime = $(this).attr("start");
    var endtime = $(this).attr("end");


    var starthour = $(this).attr("starthour");
    var startminute = $(this).attr("startminute");
    var startmeredian = $(this).attr("startmeredian");

    var endhour = $(this).attr("endhour");
    var endminute = $(this).attr("endminute");
    var endmeredian = $(this).attr("endmeredian");;

    var startmeridiemTime = starthour+":"+ startminute+" "+startmeredian;
    var endmeridiemTime = endhour+":"+ endminute+" "+endmeredian;
    $(".timepicker_wrap").remove();
    $("#starttime").val(startmeridiemTime);
    $("#endtime").val(endmeridiemTime);
    //$('#starttime').timepicki({start_time: [starthour, startminute, startmeredian]});
    //$('#endtime').timepicki({start_time: [endhour, endminute, endmeredian]});

})
};

function timeConvert(n) {
var num = n;
var hours = (num / 60);
var rhours = Math.floor(hours);
var minutes = (hours - rhours) * 60;
var rminutes = Math.round(minutes);
return parseFloat(rhours+"."+rminutes);
}

function hoverhidediv(e,divid){
$("#"+divid).hide();
}

    $(function(e){
        /*
        $('#starttime').timepicki({
            start_time: ["09", "00", "AM"]
        });

        $('#endtime').timepicki({
            start_time: ["06", "00", "PM"]
        });
*/
        $('.timepicker').timepicker({
            timeFormat: 'h:i A',
            interval: 60,
            defaultTime: '11',
            startTime: '09:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
    })

 $(".canceschedule").on("click",function(e){
     window.location = '{{ route("scheduling.create")}}';
 });

 $(".resetschedule").on("click",function(e){
     e.preventDefault();

     var customerid = $("#project").val();
        swal({
            title: "Are you sure?",
            text: "You will loose the progress with this action. Proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
        function () {
            var customerid = $("#project").val();
                $.ajax({
                    type: "get",
                    url: "{{route('scheduling.resetscheduleprogress')}}",
                    data: {"customer":customerid},
                    success: function (response) {
                        $(".addblock").trigger('click').after(function(e){
                            $(".avghoursperweek").html("");
                            $(".avghoursperweek").html("Nil");
                            $(".varianceblock").html("Nil");
                            $(".varianceblock").removeClass("redbg");
                            $(".varianceblock").removeClass("greenbg");
                            $(".schedindicator").html("Nil");
                            $(".schedindicator").addClass("redbg");


                        });
                    }
                });
        });

 })

 var customershifts = function(e){

     var customerid = $("#project").val();
     $.ajax({
         type: "get",
         url: "{{route('customer.customershifts')}}",
         data: {"customer":customerid},
         success: function (response) {
            var shifts = jQuery.parseJSON(response);
            var shift = "";
            shifts.forEach(element => {
                var startsplit = (element.starttime).split(":");
                var endsplit = (element.endtime).split(":");
                shift+='<span id="'+element.shiftname+'" start="'+element.starttime+'" end="'+element.endtime+'"'
                shift+='starthour="'+element.starthour+'" startminute="'+element.startminute+'"'
                shift+='startmeredian="'+element.startmeredian+'" endhour="'+element.endhour+'"'
                shift+='endminute="'+element.endminute+'" endmeredian="'+element.endmeredian+'"'
                shift+=' class="tags btn btn-primary" style="padding-bottom:5px !important">'+element.shiftname+'</span>&nbsp;';
            });

            $("#tags").html(shift).after(function(e){
                tagevents();
            });
         }
     });
 }

 $("#addspares").on("click",function(e){
    $(".filter-text").css("display","none");
    $("#mainblock").css("display","none");
    var divheight = $( document ).height()*.64;
    $(".sparearea").css("maxHeight",divheight+"px");
    $(".sparearea").css("overflow-y","auto");
    $(".sparearea").css("display","block");
    $(".postprojectarea").css("display","none");
 })

 $(".cancelspares").on("click",function(e){
    $(".postprojectarea").css("display","block");
    $(".filter-text").show();
    $("#mainblock").show();
    $(".sparearea").css("display","none");
 })

 scrollfunction = function(){
//alert("scrollerd")
 }

 $(".addsparesblock").on("click",function(e){
     e.preventDefault();
     var self = this;
     var useridcol = $(this).attr("attr-id");
    try {
        if(($("#"+useridcol).html()).replace(" ","")=="undefined"){

        }else{
            swal("Already in the list")

        }
    } catch (error) {
       $.ajax({
         type: "get",
         url: "{{route('scheduling.processedblockspares')}}",
         data: {projectid:$("#project").val(),payperiod:$("#payperiod").val(),employee:$(this).attr("attr-id")},
         success: function (response) {
            $("#scheduletable > tbody").append(response).after(function(){
                try {
                    loadclickevents();
                    var d = $('#mainblock');
                    d.scrollTop(d.prop("scrollHeight"));


                } catch (error) {
//                    console.log(error);
                }


            });
            let trainingdescid = $(self).attr("attr-id")+"-training";
            let trainingdesc = $("#"+trainingdescid).html();
            trainingdesc = trainingdesc.replace("<br>"," | ");
            let namearea = '<tr>';
            namearea+='<td id="147" class="cells sparespool">'+$(self).attr("attr-firstname")+" "+$(self).attr("attr-lastname")+' (Spares pool) ';
            if((trainingdesc.trim())!=""){
            namearea+='<span id="training-'+$(self).attr("attr-id")+'" style="padding-left:10px;cursor:pointer" attr-title="'+trainingdesc+' | " class="trainingdetail '+$(self).attr("attr-id")+'-training"=""><i class="fas fa-book-reader"></i></span>'
            }
            namearea+='<input type="hidden" name="emp-147" id="emp-147" class="assignedemployees" value=""></td></tr>';
            $("#nametable > tbody").append(namearea).after(function(resp){
                if(($("#"+trainingdescid).html()).trim().length>0){

                    $("#training-"+$(self).attr("attr-id")).on("click",function(e){

                        $(".image-wrapper").remove()
                        var content = $(this).attr("attr-title");
                        tooltipcreation(e,content);
                        //$(".trainingdetail").unbind("mouseover mouseout");
                    })


                    $('body').on("click",function(e){

                        //$(".image-wrapper").remove();
                        /*
                        $(".trainingdetail").on("mouseover",function(e){
                            var content = $(this).attr("title");
                        tooltipcreation(e,content);
                        $(".trainingdetail").unbind("mouseover mouseout");;

                    });*/
                    })
                }


            });
            $(self).hide();

         }
     }).done(function(response){

     });
    }


 })

 $(".confirmschedule").on("click",function(e){
     $("#supervisorModal").modal("show");
 })
$(document).keyup(function(e) {
     if (e.key === "Escape") { // escape key maps to keycode `27`
     try {
         $(".cancelspares").trigger('click');
     } catch (error) {

     }
     try {
         $(".image-wrapper").remove();
     } catch (error) {

     }

    }
});
 //window.onscroll = function() {scrollfunction()};

 var loadclickevents = function(){


 }
 $('#example').DataTable({
    fixedHeader: true,
    aoColumnDefs: [
          { sWidth: "10%", aTargets: [0, 2,4,5 ] },
    ]
});

$(".cancelmodal").on("click",function(e){
    $("#supervisornotes").val("");
})


</script>
