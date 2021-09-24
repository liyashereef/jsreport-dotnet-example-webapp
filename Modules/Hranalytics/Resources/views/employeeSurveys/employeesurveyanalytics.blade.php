<div class="scrollmenunav">
  @foreach ($templatenames as $key=>$value)

  <a id="tab_{{$loop->iteration}}" attr-templateid="{{$value["id"]}}" href="#template_{{$value["id"]}}" class="templatetab">{{$value["name"]}}</a>

  @endforeach
  {{-- <a href="#home">Home</a>
    <a href="#news">News</a>
    <a href="#contact">Contact</a>
    <a href="#about">About</a>
    <a href="#support">Support</a>
    <a href="#blog">Blog</a>
    <a href="#tools">Tools</a>
    <a href="#base">Base</a>
    <a href="#custom">Custom</a>
    <a href="#more">More</a>
    <a href="#logo">Logo</a>
    <a href="#friends">Friends</a>
    <a href="#partners">Partners</a>
    <a href="#people">People</a>
    <a href="#work">Work</a> --}}
</div>


<div class="tab-content">
  @foreach ($templatenames as $key=>$value)
  <div id="template_{{$value["id"]}}" class="tab-pane fade" >
    {{-- <h3>{{$value["name"]}}</h3> --}}
    <div class="container_fluid" style="background: white !important">


      <table id="tableid"  style="table-layout: fixed; 
      margin-bottom: 0px !important; width: 100% !important; height: 100% !important;">
        <thead>
          <tr>
            <td>
              <table class="table_row" style="width: 100%;table-layout: fixed !important;">
                <tbody>
                  <tr>
                    @if(isset($templatequestion[$value["id"]]))
                    @foreach ($templatequestion[$value["id"]] as $tquestions)
                    @if($loop->last==1 && $loop->iteration%2!=0)
                    <td class="custom-dashboard-th" id="dashboard_th" data-width="50%" style="display:inline-block;width:50% !important;padding-right: 10px !important;vertical-align:middle !important;position:relative;border: none !important;padding-top: 10px !important;padding-left: 0px !important;" rowspan="1" colspan="1">

                    @else

                    <td class="custom-dashboard-th" id="dashboard_th" data-width="50%" style="display:inline-block;width:50% !important;padding-right: 10px !important;vertical-align:middle !important;position:relative;border: none !important;padding-top: 10px !important;padding-left: 0px !important;" rowspan="1" colspan="1">

                      @endif
                      <div class="card-table js-widget" style="padding-left: 0px !important;padding-right: 0px !important;">
                        <div class="card-headers" style="background: white !important">
                          <span class=" widget-title-selector" style="" id="h_span_span-215-position-by-reasons">
                            <p style="background:white;border-bottom:white" class="card-header">{{$tquestions["question"]}} </p>
                          </span><span class="span-site-schedule filter-content" id="span-215-position-by-reasons" style="text-align:center;width: 40%;"></span>
                        </div>
                        <div id="tbl_responsive" data-parent-tbl="215" class="table-responsive widget-div dasboard-card-body" style="overflow-x:scroll !important;flex: 1 1 auto !important;height:340px !important;overflow-y:hidden">
                          <div style="padding: 20px;">
                            <div class="chartjs-size-monitor">
                              <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                              </div>
                              <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                              </div>
                            </div>
                            <figure class="highcharts-figure" style="overflow: none;">

                              <div style="min-height: 150px !important" id="chartblock_{{$tquestions["id"]}}">

                              </div>
                            </figure>
                          </div>
                        </div>
                      </div>


                    </td>


                    @if ($loop->iteration%2==0 )
                  </tr>
                
                  <tr>

                    @endif


                    @endforeach @else
                    No questions defined
                    @endif
                  </tr>
                </tbody>
              </table>

              {{-- </div> --}}

          </tr>
        </thead>
      </table>
    </div>
  </div>

  @endforeach
</div>



<style>
  div.scrollmenunav {
    background-color: #f26321;
    overflow: auto;
    white-space: nowrap;
  }

  div.scrollmenunav a {
    display: inline-block;
    color: white;
    text-align: center;
    padding: 14px;
    text-decoration: none;
    border-right: solid 1px #fff;
  }

  div.scrollmenunav a:hover {
    background-color: #777;
  }

  /***scroll bar***/
  ::-webkit-scrollbar {
    width: 5px;
    height: 5px;
  }

  /* Track */
  ::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    -webkit-border-radius: 10px;
    border-radius: 10px;
  }

  /* Handle */
  ::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    border-radius: 10px;
    /* background: #f26321; */
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
  }

  ::-webkit-scrollbar-thumb:window-inactive {
    /*background: #f26321; */
  }

  .card-table {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
  }

  .card-table-cbsa {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
  }
</style>
