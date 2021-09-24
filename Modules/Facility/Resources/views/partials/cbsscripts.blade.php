<script>
    $(document).on("change","#usertype",function(e){
        let usertype = $(this).val();
        if(usertype=="internal"){
            $(".internaluserclass").show();
        }else{
            $(".internaluserclass").hide(); 
        }
    });

    //var maxbooking

    $(document).on("click",".active",function(e){
        var amenityselect = $("#amenityselect option:selected").text();
        var amenity = $("#amenityselect option:selected").val();
        var bookingdate = $(this).attr("attr-bookdate");
        var amenitycategoryselect = null;
        var amenitycateg=0;
        try {
             amenitycategoryselect =  $("#amenitycategoryselect option:selected").text();
             amenitycateg=$("#amenitycategoryselect option:selected").val();
        } catch (error) {
            
        }
        var starttime = $(this).attr("attr-starttime")
        var bookable = $(this).attr("attr-bookable");
        var endtime = $(this).attr("attr-endtime");
        var blockid = this.id;
        $("#modelamenity").val(amenityselect);
        if(amenitycategoryselect!=""){
            $(".amcategory").show();
            $("#modelamenitycategory").val(amenitycategoryselect);
        }else{
            $(".amcategory").hide();
        }
        $("#blockid").val(blockid);
        $("#modelstarttime").val(starttime);
        $("#modelendtime").val(endtime);
        $("#modelbookingdate").val($(this).attr("attr-bookdate"))
        $.ajax({
                type: "post",
                url: "{{route('cbs.bookingstatus')}}",
                data:{amenity:amenity,amenitycateg:amenitycateg,bookingdate:bookingdate,selectstarttime:starttime,endtime:endtime},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code==200){
                        modal.style.display = "block";
                    }else{
                        swal("warning",data.message,"warning");
                    }
                    
                }
            });
        
    })

    $(document).on("click","#savecondobooking",function(e){
        var amenityid = $("#amenityselect").val();
        var amenitycategory =0;
        try {
            amenitycategory = $("#amenitycategoryselect").val();
        } catch (error) {
            
        }
        var bookingdate = $("#modelbookingdate").val();
        var starttime = $("#modelstarttime").val();
        var endtime = $("#modelendtime").val();
        $.ajax({
            type: "post",
            url: "{{route('cbs.savecondobooking')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {amenityid:amenityid,amenitycategory:amenitycategory,bookingdate:bookingdate,starttime:starttime,endtime:endtime},
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code==200){
                    swal({
                        title: "success",
                        text: "Booking completed",
                        type: "success"
                    }, function() {
                        modal.style.display = "none";
                       
                        var blockid = $("#blockid").val();
                        
                        
                        $("#"+blockid).removeClass("active");
                        $("#"+blockid).addClass("inactive");
                        
                        $("#initiateschedule").trigger("click");
                        
                    });
                }else{
                    swal("warning",data.message,"warning");
                } 
            }
        });
    })

    $(document).on("click",".inactive",function(e){
        swal("warning","Not allowed to book !","warning");
    })

    $(document).on("change","#amenityselect",function(e){
        var amenityid = $(this).val();
        if(amenityid>0){
            $.ajax({
            type: "post",
            url: "{{route('cbs.populateusersamenitycategory')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {amenityid:amenityid},
            success: function (response) {
               
                try {
                    $(".prereqol").show();
                    var userdata = jQuery.parseJSON(response);
                var amenitydata = Object.values(userdata);
                if(userdata){
                    var amenityelement = '<select id="amenitycategoryselect" class="form-control"><option>Select any</option>';
                    $.each(userdata, function (indexInArray, valueOfElement) { 
  
                            amenityelement+='<option value="'+indexInArray+'">'+valueOfElement+"</option>";
    
                    });
                    amenityelement += "</select>";
                        amenityelement +='<span style="display:none" id="amenitycategoryselectlabel"></span>';
                        $("#categories").html(amenityelement);
                        $(".amenitycategories").show();
                }
                
                } catch (error) {
                    $("#categories").html("");
                    $(".amenitycategories").hide();
                }
                
                
            }
        });
        }else{
            $("#categories").html("");
            $(".amenitycategories").hide();
        }
        
    })

    var populateuserdata = function(){
        //$(".usertype").hide();
        
        
        
        
        @if(\Auth::guard('facilityuser')->user())
            $(".internaluserclass").hide();     
            $("#welcomeuser").html("Hello "+{!! json_encode(\Auth::guard('facilityuser')->user()->first_name." ".\Auth::guard('facilityuser')->user()->last_name) !!}+
                            '<a href="cbs/logout"> (Logout)</a>'); 

        @else
            $(".internaluserclass").show();
        @endif
        
        $(".loggedinternaluserclass").show().after(function(){
            
            
        });
        
    }
    $(document).on("click","#internaluserlogin",function(e){
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "{{route('cbs.login')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {username:$("#username").val(),password:$("#userpassword").val()},
            
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code==200){
                   // populateuserdata();
                   location.reload();
                    
                }else{
                    {
                    swal("warning","Invalid username or password !","warning");
                }
                }
            }
        });
    });

    $(document).on("click","#doneschedule",function(e){
        location.reload();
    })

    $(document).on("click","#initiateschedule",function(e){
        var amenityid = $("#amenityselect").val();
        var amenitycategory =0;
        try {
            amenitycategory = $("#amenitycategoryselect").val();
        } catch (error) {
            
        }
        var bookingdate = $("#bookingdate").val();
        if($("#amenitycategoryselect").length){
           if($("#amenitycategoryselect").val()=="Select any" || $("#amenitycategoryselect").val()==""){
               swal("warning","Please select an Category !","warning");
               return false;
           }else{
                amenitycategory = $("#amenitycategoryselect").val();
           }
            
        }
        if(amenityid<1 || amenityid==""){
            swal("warning","Please select an Amenity !","warning");
        }else if(bookingdate==""){
            swal("warning","Please select a date !","warning");
        }else{
            $.ajax({
                type: "post",
                url: "{{route('cbs.scheduleblock')}}",
                data:{amenityid:amenityid,amenitycategory:amenitycategory,bookingdate:bookingdate},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $(".colorblock").show();
                    $("#scheduleblock").html(response).after(function(e){
                        
                        $("#initiateschedule").hide();
                        $("#doneschedule").show();
                        $("#amenityselect").hide();
                        $("#amenityselectlabel").html($("#amenityselect option:selected").text()).show();
                        try {
                            $("#amenitycategoryselect").hide();
                            $("#amenitycategoryselectlabel").html($("#amenitycategoryselect option:selected").text()).show();
                        } catch (error) {
                            
                        }
                        
                        $("#schedtable").dataTable({
                            scrollY:"500px",
                            "scrollX": true,
                            scrollCollapse: true,
                            paging:         false,
                            fixedColumns:   {
                                leftColumns: 1,
                            },
                            ordering:false,
                        });
                    });
                }
            });
        }
        
    })

    $(document).ready(function () {
        var loggedinuser = $("#loggedinuser").val();
        if(loggedinuser>0){
            populateuserdata();
        }else{
            $(".internaluserclass").show();
        }
        $("#bookingdate").datepicker({
                                    format: "yyyy-mm-dd", maxDate: "+900y"
                                });

                                
    });

    // Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];



// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>