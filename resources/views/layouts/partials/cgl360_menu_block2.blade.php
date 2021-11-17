

        <li attr-block="2"  class="pli">
            <a href="#" attr-block="2" class="mainhead mhead">
                <div attr-block="2" class="plihidden ">C </div>
                <span attr-block="2"   >Communications </span>
            </a>
        </li>
        @canany(["view_meeting_page","view_scheduled_meeting_page"])
        <li class="block2 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="CGL Meet " class="iconclass fa fa-phone fa-fw" aria-hidden="true"></i>
                <span class="singlelinespan">CGL Meet </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @can('view_meeting_page')
                    <li class="dropdown-submenu">
                            <a href="{{ route('jitsi.index') }}"> Meeting </a>
                    </li>
                @endcan
                @can('view_scheduled_meeting_page')
                    <li class="dropdown-submenu">
                        <a href="{{ route('jitsi.schedulemeeting') }}"> Schedule Meeting </a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcanany
        @canany(["create_blastcom_all_customers","create_blastcom_allocated_customers","view_blastcom_reports"])
        <li class="block2 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Mass communication " class="iconclass fa fa-envelope fa-fw" aria-hidden="true"></i>
                <span class="singlelinespan">Blastcom </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">

                @canany(["create_blastcom_all_customers","create_blastcom_allocated_customers"])
                <li class="dropdown-submenu">
                    <a href="{{ route('mailblast.index') }}"> BlastCom </a>
                </li>
                @endcanany
                @can('view_blastcom_reports')
                <li class="dropdown-submenu">
                    <a href="{{ route('mailblast.reports') }}"> BlastCom - Reports </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
        {{-- <li>
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Documents " class="fa fa-file fa-fw" aria-hidden="true"></i>
                <span class="">CGL Chat </span>
            </a>
        </li> --}}
        @canany(['motion_sensor_view'])
        <li class="block2 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Motion Sensors " class="iconclass fa fa-eye fa-fw" aria-hidden="true"></i>
                <span class="singlelinespan">Sensors </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                @canany(['motion_sensor_view'])
                    <li>
                        <a href="{{ route('sesors.triggers') }}">Sensor Triggers</a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcanany
        <li class="block2 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="IP Camera " class="iconclass fa fa-camera fa-fw" aria-hidden="true"></i>
                <span class="singlelinespan">Ip Camera </span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                <li>
                    <a href="{{ route('ip_camera.widget_view') }}">IP Camera</a>
                </li>
            </ul>
        </li>

        <li class="block2 accordclass">
            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Chat" class="iconclass fa fa-comment-alt fa-fw" aria-hidden="true"></i>
                <span class="singlelinespan">Chat</span>
            </a>
            <ul class="dropdown-menu menu-list" role="menu">
                <li>
                    <a href="{{ route('chat.viewchat') }}">Chat</a>
                </li>
            </ul>
        </li>
