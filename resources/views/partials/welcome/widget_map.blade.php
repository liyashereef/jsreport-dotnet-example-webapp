
@if($siteStatusFlag == 2)  

<div class="map site-status">
    {{-- https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d166655.30827522842!2d-123.2639867747921!3d49.2576507715125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x548673f143a94fb3%3A0xbb9196ea9b81f38b!2sVancouver%2C+BC%2C+Canada!5e0!3m2!1sen!2sin!4v1537360932136 --}}

    <iframe id="iframe" class="home-iframe" src="{{ $src }}" frameborder="0" style="border:0" allowfullscreen></iframe>
    <div id="block1" style="position: absolute;top: 5%;left: 0;width: 98%;height: 95%;background-color: #cac5bc;"><br /><br />&nbsp;&nbsp;Map is Loading......</div>
    <div id="block" style="position: absolute; top: 10%; left: 0; width: 100%; height: 100%"></div>

</div>
@elseif($siteStatusFlag == 1)
<div class="shadow bg-white rounded h-100">
    <div class="site-dashboard h-100" style="height: 100% !important;">
        <div class="embed-responsive embed-responsive-4by3" style="height: 100% !important;margin-bottom: -10px !important;">
            <div id="map" style="height:100%;" class="embed-responsive-item">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L
                o a d
                i n g . . . . . .
            </div>
        </div>
        {{--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2893.2169960558967!2d-79.68202338450662!3d43.51866747912607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb322b2347bb%3A0xf540f736eee68eef!2sCommissionaires+-+Oakville!5e0!3m2!1sen!2sin!4v1564661774342!5m2!1sen!2sin"
                            width="400" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
    </div>
</div>

</div>
@else
<tbody><tr><td class="custom-dashboard-th" style="border:0px;text-align:center;vertical-align:middle;font-weight:normal;">Don't have permission to view</td></tr></tbody>
@endif

<script>

    $('#iframe').on('load', function () {
        var navMenu = 0;
        var navHideInterval = null;
        // $("#iframe").contents().find("#map").css('top', '-20%');
        // $("#iframe").contents().find("#map").css('height', ' 500px');
        $("#iframe").contents().find(".wrapper").removeClass('wrapper');
        $("#iframe").contents().find("#wrapper").css('display', 'none');
        $("#iframe").contents().find("#sidebar").css('display', 'none');
        $("#iframe").contents().find(".navbar").css('display', 'none');
        $("#iframe").contents().find(".table_title").css('display', 'none');
        $("#iframe").contents().find("#footer").css('display', 'none');
        $("#iframe").contents().find(".embed-responsive").removeClass('embed-responsive');
        $("#iframe").contents().find("#map").removeClass("custom-map");
        $("#iframe").contents().find(".mapping-right").css('display', 'none');
        $("#iframe").contents().find("#content-div").css('padding', '0px');
        navHideInterval = setInterval(function () {
            // $("#iframe").contents().find("#map").css('top', '-20%');
            // $("#iframe").contents().find("#map").css('height', ' 500px');
            $("#iframe").contents().find("#sidebar").css('display', 'none');
            $("#iframe").contents().find(".navbar").css('display', 'none');
            navMenu++;
            if (navMenu == 6) {
                clearInterval(navHideInterval);
            }
        }, 2000);
        $('#block1').remove();
        document.getElementById("iframe").contentWindow.document.body.onclick =
                function (e) {
                    e.preventDefault();
                    //alert("iframe clicked"+$("#iframe").prop('src'));
                    //window.location = $("#iframe").prop('src');
                }
    });
    </script>
    @include('fmdashboard::customer_map')
    @include('partials.site_status_widgets')