<table id="tabuid_0" class="ml-2 mt-2 dashboard-tables"
style="table-layout: fixed; margin-bottom: 0px !important; width: 97.5% !important; height: 100% !important;"><thead><tr><td><table class="table_row" style="width: 100%;table-layout: fixed !important;">    <tr>
        <td style="padding-top:5px;vertical-align: top;">
            <div class="card-table" style="width:99%">
                <div class="card-header"><img src="{{asset('images/camera.png')}}" style="width: 2%;"><span class="pl-2" style="white-space: nowrap;"><a class="inner-page-nav widget-cam-1-tittle" id="heading-span-0-cam-0" href="#"></a></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="span-site-schedule filter-content" id="span-1-cam-1" style="text-align:center;"></span></div>
                            <iframe frameborder="0" id="ipcamera-genral-tab-view" data-parent-tbl="0"
            class="table-responsive widget-div dasboard-card-body"
            style="padding-right: 0.09em !important;padding-top: 0.10em !important;padding-bottom: 0.15em !important;overflow-x:scroll !important;flex: 1 1 auto !important;height:80vh !important;"></iframe>
            </div>

        </td>
        <td style="width: 20%;padding-top:5px;overflow-y:auto" class="cameraclass" valign="top">
        <div class="row-span" >
            <div class="table_title card-header" style="padding-top: 5px;">
                <h4 style="padding-top: 10px;">Camera</h4>
            </div>
            <div class="cam-div" style="height: 80vh;overflow-y:auto">
                @foreach ($ipCameras as $camera)
                <p style="cursor: pointer;border-bottom:solid 1px #FAFAFA;
                border-right:solid 1px #FAFAFA;margin-top: 5px;margin-bottom: 5px;" class="cameraid" attr-name="{{$camera->name}}"
                attr-id="{{$camera->id}}">
                   <span>{{ucwords($camera->name)}} <i class="fas fa-angle-double-right"></i></span> <br/></p>
                   <div class="cam-frame">
                    @if (isset($ipCameraUrl[$camera->id]))
                    <iframe scrolling="no" frameborder="0" src="{{$ipCameraUrl[$camera->id]}}" id="ipcamera-genral-tab-view-{{$camera->id}}" data-parent-tbl="0"
                        class="table-responsive widget-div dasboard-card-body"
                        style="padding-right: 0.50em !important;
                        overflow:hidden;padding-left: 0.15em !important;padding-top: 0.10em !important;padding-bottom: 0.15em !important;overflow-x:scroll !important;flex: 1 1 auto !important;height:20vh !important;"></iframe>
                    @endif
                   </div>
            @endforeach
            </div>
        </div>

        </td>
    </tr>
</table>
