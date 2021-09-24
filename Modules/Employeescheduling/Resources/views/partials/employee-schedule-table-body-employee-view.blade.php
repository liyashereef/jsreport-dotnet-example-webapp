<style>
    .schedule-header-1 {
        padding-bottom: 16px !important;
    }

    .schedule-header-2 {
        padding-bottom: 6px !important;
    }


    .image-wrapper {
        position: absolute;
        overflow-y:auto;
        background: #003A63;
        border-radius: 5px;
        min-width: 200px;
        max-width: 900px;
        max-height: 100px;
        max-height: 200px;
        padding: 5px;
        padding-right: 25px;
        color: #fff;
        transform: translate(-50%, -50%);
        opacity: 1;
        font-size: 13px;
        z-index: 2;
        line-height: 16px;
    }
</style>

<table class="table_1" id="employee_list_schedule">
    @if(!empty($schedules))
    <tr>


        <td>
            <div class="schedule_user_header card-custom card-header-bg text-white text-center" style="width:300px;">
                <span>Employee Name</span><br>
                <small>Role</small>
            </div>
        </td>
    </tr>
    @endif
    @foreach($schedules as $schedule)
    <tr>

        <td style="width: 300px;">
            <div class="card-custom card-header-bg text-white text-center" style="height: 100%;">
                <span class="user-details" title="{{$schedule['title']}}">{{$schedule['user_name']}}</span><br />
                <small class="user-details" title="{{$schedule['role']}}">
                    {{$schedule['role']}}
                </small>

                @if(!empty($schedule['training_details']))
                <span style="padding-left:10px;cursor:pointer;" class="trainingdetail-approval text-center" data-expand="false" data-training="{{$schedule['training_details']}}">
                    <i class="fas fa-book-reader fa-sm" title="click here to view training details"></i>
                </span>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
</table>

<script>

    $(".trainingdetail-approval").on("click", function (e) {
        $(".image-wrapper").remove();
        var content = $(this).attr("data-training");
        var expand = $(this).attr("data-expand");
        if (content !== "" && expand == "false") {
            $(this).attr('data-expand', true);
            tooltipcreation(e, content);
        }else{
            $(this).attr('data-expand', false);
        }
    });
    
    function close_tooltip() {
        $(".image-wrapper").remove();
    }


    var tooltipcreation = function (ev, content) {
        var left = Number(ev.pageX) + 200;
        var ulcontentarray = content.split("|");
        var ulcontent = "<ol>";
        ulcontentarray.forEach(element => {
            if (element.trim() != "") {
                ulcontent += "<li>" + element + "</li>";
            }

        });
        ulcontent += "</ol>";
        var div = $('<div class="image-wrapper">')
                .addClass('image-wrapper')
                .css("cssText", "left:" + left + "px !important;" + "top:" + ev.pageY + 'px !important')


                .append("<p><b>Completed Training</b><span style='float:right;cursor:pointer' onclick='close_tooltip()'>x</span></p><p>" + ulcontent + "</p>")
                .appendTo(document.body);
    };

    $(".user-details").tooltip({
        position: {
            my: "center bottom-20",
            at: "center top",
            using: function (position, feedback) {
                $(this).css(position)
                $("<div>")
                        .addClass("arrow")
                        .addClass(feedback.vertical)
                        .addClass(feedback.horizontal)
                        .appendTo(this);
            }
        }
    });

    $(function () {
        var schedule_id = $('#schedule_element').val();
        if (schedule_id != '') {
            $('.schedule_user_header').addClass('schedule-header-1');
            $('.schedule_user_header').removeClass('schedule-header-2');
        } else {
            $('.schedule_user_header').addClass('schedule-header-2');
            $('.schedule_user_header').removeClass('schedule-header-1');
        }
    });
</script>