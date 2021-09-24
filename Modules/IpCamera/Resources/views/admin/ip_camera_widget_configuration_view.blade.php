@extends('adminlte::page')

@section('title', 'Recruitment Widget Configurations')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3 class="orange">Ip Camera Widget Configurations</h3>
@stop

@section('content')
<style>
  /* Style the caret/arrow */
  .cpid-div{
    margin-left:23%;
  }
  .fa-lg {
    font-size: 1.33333333em !important;
}
.fa-2x{
        font-size:2em !important;
    };
  .cpid-cncl-btn{
      margin-left:37px;
  }
  .table_height{
    height:50px;
  }
  .caret {
            cursor: pointer;
            border: 0;
            margin: 0;
            display: inline;
            padding: 1rem;
            user-select: none; /* Prevent text selection */
        }

        .list-group-item {
            border: none;
        }

        .custom-list-group-item {
            border: none;
            position: relative;
            display: block;
            padding: 15px 5px;
            margin-bottom: -1px;
            background-color: #fff;
        }

        /* Create the caret/arrow with a unicode, and style it */
        .caret::before {
            content: "\25B6";
            color: black;
            display: inline-block;
            margin-bottom: 6px;
        }

        /* Rotate the caret/arrow icon when clicked on (using JavaScript) */
        .caret-down::before {
            transform: rotate(90deg);
        }

        /* Hide the nested list */
        .nested {
            display: none;
        }

        /* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
        .active {
            display: block;
        }
 .user_tab{
     margin-top:-80px;
 }
.preference_button{
    margin-bottom:5%;
}

.nav-item{
    margin-left: 14px;
}

#tabList {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
.fa-toggle-on{
    margin-top:-30px;
}
.table th {
    padding: .75rem;
    margin-left:150px;
    vertical-align: top;
    border-top: 1px solid #e9ecef;

}
.editTab{
    margin-left:30px;
}
.editActiveTab{
    margin-left:0px;
    margin-top:-18px;
}
.landing_page_delete_icon{
    margin-left:0px;
}
.editTab{
   margin-top:-10px;
}
.short_head{
    margin-left:30px;
}
.cpid_head{
    margin-left:-60px;
}
.pos_head{
    margin-left:30px;
}
.break {
  width: 150px;
  word-wrap: break-word;
}
.break_qr{
    margin-left:-17px;
}
.document-screen-head{
    width:100%;
}
.document-list-label{
    width:100%;
}
.data-list-label{
    height:40px;
}
#myModal {
  align: center;

}
.fence{
    margin-left:33px;
}

#exp-id{
    margin-right:50px;
}
.sub_title_name{
    color: #f26222;
}

.tab-content{
    margin-top:15px;
}
#tab1{
  display:inline-block;
  padding:0px;
  margin:0px;
}
#tab0{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:100%;
  margin-left:10px;
}
#tab7{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:30px;
  margin-left:-15px;
}
#tab2{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:50px;
  margin-left:20px;
}
#tab-last{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:115%;
  margin-right:60px;
  margin-left:-15px;
}
.preference_tab_align{
    margin-left:45px;
}
#tab-new{
  display:inline-block;
  padding:0px;
  margin:0px;
  width:110%;
  margin-right:60px;
  margin-left:-10px
}
.cpid{
    margin-left:-60px;
}
.act{
    width:75px;
}
.exp{
    width:75px;
}
.exp1{
    width:80px;
}
.act1{
    width:80px;
}
.btn-sm{
    margin-top: 14px;
}
.desc{
    margin-left:-30px;
    margin-bottom:15px;
}
.short{
    margin-left:-35px;
}
.blue{
    font-size: 18px;
}
.preference{
    margin-left:-20px;

}
.attempt{
    margin-left:-20px;
}
.checkpoint{
    margin-left:-30px;
}
.picture{
    margin-left:-70px;
}
.acti{
    margin-left:-70px;
}
.qr-button{
    margin-top:-10px;
}
.mandator{
    margin-left:10px;
}
.incident{
    margin-left:-55px;

}
.location{
    margin-left:-20px;
}
#tabIncid{
    margin-left:-55px;
}
#tabFence{
    margin-left:-90px;
}
.user-screen-head {
    background: #0e3b5e;
    color: #ffffff;
    margin: 8px 0px;
    padding: 10px 5px;
    margin-left: 0px;
}
.user-screen {
    background: #0e3b5e;
    color: #ffffff;
    margin: 8px 0px;
    padding: 10px 5px;
}
.btn-align{
    margin-right:10px;
}
.qrcode-location-div{
    width:100%;
    display: inline-block;
    margin-left:0px;
    padding-left:0px;
}
.qrcode-location-div label {
    width: 60px;
    margin-left: 0px;
    margin-right: 1%;
}
.fencetab{
    margin-left:-80px;
    margin-top:30px;
}
.active_button_align{
    margin-top:-20px;
    margin-left: -500px;
}
.nav-link{
   width:100%;
}
.incident_tabs{
    margin-left:-5px;
}
.editbutton1, .editbutton4, .editbutton7{
     float: right;
     cursor: pointer;
        }
.editbutton2,.editbutton6{
    float: right;
    cursor: pointer;
        }
.editbutton3, .editbutton5{
    float: right;
    cursor: pointer;
        }
.position_cls{
    margin-left:580px;
}
.sname{
    margin-left:180px;
}
ul, #myUL {
  list-style-type: none;
}

#myUL {
  margin: 0;
  padding: 0;
}
.breadcrumb-arrow li {
    width: 13.93%;
}
.breadcrumbs li a {
    white-space: nowrap;
}
.add-button {
            background-color: #f26222;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 5px;
            padding: 5px 0px;
            margin-left: 5px;
            cursor: pointer;
            width:175px;
            float:right;
        }
.profile_btn{
    margin-bottom:5%;
}
</style>
<div id="landing_page" class="tab-pane candidate-screen">
        <div class="row">
            <div class="col-sm-12">
                <div class="row landing_page_tab">
                    <div class="col-sm-12 landing_page">
                        <div id="landingPage">
                                <div class="container-fluid" id="newTabContainer">
                                    <input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary" style="float: right; margin-right: 1%;"/>
                                </div>
                                <div class="row">
                                    <div class="col-md-4" id="tab">loading please wait...</div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    function open_new_configuration() {
        window.location = "{{route('ip-camera-dashboard.new_configuration_new')}}";
    }

    $(function(){
            $.ajax({
            type: "get",
            url : "{{route('ip-camera-dashboard.getRecruitingAnalyticsDetails')}}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if(data.length > 0) {
                    $('#tab').removeClass('text-center')
                    var prevTabId = null;
                    var prevWidget = null;
                    var widgetNum = 0;
                    $('#tabList').remove();
                    $('#tab').html('<ul class="list-group" style="padding-bottom: 2rem;white-space: nowrap;" id="tabList"></ul>');
                    $('#newTabContainer').html('<input type="button" onclick="open_new_configuration();" value="Add New" class="btn btn-primary" style="float: right; margin-right: 1%;"/>');
                        $.each(data, function(tabKey, tabValue) {
                            if (prevTabId != tabValue.id) {
                                prevTabId = tabValue.id;
                                $('#tabList').append(
                                    '<li style="list-style-type: none;" class="custom-list-group-item" id=tabName'+prevTabId+'>'
                                    +'<span class="caret" title="Tab Name"></span>'+tabValue.tab_name
                                    +'<span onclick="edit_activeTab('+prevTabId+')"><a href="#" class="editActiveTab fa fa-toggle-on fa-2x" style="float: right; padding-right: 1rem;padding-top: 0.633em;" value="'+tabValue.active+'"></a></span>'
                                    +'<span onclick="delete_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-trash fa-lg landing_page_delete_icon" style="float: right; padding-right: 1rem;padding-top: 0.9em;" data-id="'+tabValue.id+'"></a></span>'
                                    +'<span onclick="edit_tab('+tabValue.id+')"><a href="#" class="editTab fa fa-pencil fa-lg" style="float: right; padding-right: 1rem;padding-top: 0.9em;" data-id="'+tabValue.id+'"></a></span>'
                                    +'</li><br>');
                                if (tabValue.active == 0) {
                                    $('#tabList #tabName'+prevTabId+' a.editActiveTab').removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                                }

                                $('#tabName'+prevTabId).append('<ul class="nested" id="nested'+prevTabId+'"></ul>');
                            }
                            $.each(tabValue.tabDetails, function(widgetkey, widgetvalue) {
                                if (prevWidget != widgetkey) {
                                    widgetNum++;
                                    $('#nested'+prevTabId).append('<li class="custom-list-group-item nest1" id="nest'+widgetNum+'"><span class="caret" title="Widget Name"></span>'+widgetkey+'</li>');
                                    $('#nest'+widgetNum).append('<ul class="nested" style="margin-top:1% !important;" id="colName'+widgetNum+'"></ul>');
                                    $('#colName'+widgetNum).append('<table class="table" id="table'+widgetNum+'">'
                                                                    +'<thead>'
                                                                    +'<tr>'
                                                                    +'<th scope="col" class="text-left">Display Field Name</th>'
                                                                    +'<th scope="col"><center>Sort By<center></th>'
                                                                    +'</tr>'
                                                                    +'</thead>'
                                                                    +'<tbody>'
                                                                    +'</tbody>'
                                                                    +'</table>');
                                }
                                $.each(widgetvalue, function(key, value) {
                                    $.each(value, function(k,v){
                                        console.log(v);
                                        $('#table'+widgetNum).append('<tr>'
                                                                +'<td >'+v.field_display_name+'</td>'
                                                                +'<td class="text-center">'+[(v.default_sort == 1)? '<span class="fa fa-check" style="color:green;"></span>':'<span class="fa fa-times" style="color:red;"></span>']+'</td>'
                                                                +'</tr>');

                                    });
                                });
                            })
                        });

                }else {
                    $('#tab').addClass('text-center').html('Not Found');
                }
            }
        });
    });

    $("#landing_page").on("click", function() {
        let toggler = document.getElementsByClassName("caret");
        let i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    });

    function edit_tab(tab_id) {
        $(".close").trigger('click');
        let url = "{{ route('ip-camera-dashboard.new_configuration_new',['tab_id' => ''])}}" + tab_id + '';
        window.open(url);
    }

function delete_tab(tab_id) {
    swal({
             title: "Are you sure?",
            text: "You will not be able to undo this action",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, remove",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },function() {
            $.ajax({
                type: "POST",
                url: "{{route('ip-camera-dashboard.removeTab')}}",
                data: {'tabid': tab_id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status == "success") {
                        $('#tabName' + tab_id).remove();
                    }
                    swal(response.status_msg,response.msg, response.status);
                }
            });
    });
}

function edit_activeTab(prevTabId) {
   name = 'li'+'#tabName' + prevTabId + ' a.editActiveTab';
   status = document.querySelector(name).getAttribute("value");
   var customer_id = $('input[name="id"]').val()
   $.ajax({
            type: "POST",
            url: "{{route('ip-camera-dashboard.saveTabActiveStatus')}}",
            data: {
                'customerid': customer_id,
                'tabid': prevTabId,
                'status': (status==1)?0:1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === "success") {
                    swal({
                        title: "Success",
                        text: response.msg,
                        type: response.status,
                        confirmButtonText: "OK"
                    });

                    if (status == 1) {
                    $(name).removeClass("fa-toggle-on fa-2x").addClass("fa-toggle-off fa-2x");
                    document.querySelector(name).setAttribute("value", "0");
                    } else {
                    $(name).removeClass("fa-toggle-off fa-2x").addClass("fa-toggle-on fa-2x");
                    document.querySelector(name).setAttribute("value", "1");
                }
                }else {
                    swal(response.status_msg, response.msg, response.status);
                }
            }
        });

}
</script>
@stop
