<div id="content-div1" class="white-bg" style="padding: 0px !important">
        <!-- content area -->
        <div class="content-component px-3 py-3" style="padding: 2px !important">

{{---START--- Dashboard -- Title and Search Bar --}}
            <div class="row search-component">
            <div class="col-md-6 d-flex align-items-center table_title">
                @if(empty($hidePageHeading))
                    <h4 class="table_title font-bold color-primary heading-main mb-0">Training Management</h4>
                @endif
                </div>
                <div class=" col-md-6 d-flex justify-content-end">
                    <div class="dboard-search d-flex">
                        <input type="search" placeholder="Enter a Course Name" name="course_search" id="course_search" />
                        <button class="primary-bg font-bold" onclick="getCourseList()">Search Course</button>
                    </div>
                </div>
            </div>
{{---END--- Dashboard -- Title and Search Bar --}}

{{---START--- Dashboard -- Course Type counts --}}
            <div class="row mainlink-component">
                <div class="col-md-12 mt-4">
                    <div class="bshadow card-box-lg">
                        <div class="row flex-nowrap listline-link" id="CourseTypeCount">
                            <a href="#" class="d-flex mcard-grid justify-content-center align-items-center m-linkactive CourseTypeCount_a" data-id="1" >
                                <div class="round-icohold d-flex justify-content-center align-items-center todo-ico"></div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">To-Do</h2>
                                    <h3 class="mb-0 color-dark">{{$todo_count}}@if($todo_count>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                            <a href="#" class="d-flex mcard-grid justify-content-center align-items-center CourseTypeCount_a" data-id="2">
                                <div class="round-icohold d-flex justify-content-center align-items-center completed-ico">

                                </div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Completed</h2>
                                    <h3 class="mb-0 color-dark">{{$completed_count}} @if($completed_count>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                            <a href="#" class="d-flex mcard-grid justify-content-center align-items-center CourseTypeCount_a" data-id="3">
                                <div class="round-icohold d-flex justify-content-center align-items-center overdue-ico">

                                </div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Overdue</h2>
                                    <h3 class="mb-0 color-dark">{{$over_due_count}} @if($over_due_count>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                            <a href="#" class="d-flex mcard-grid justify-content-center align-items-center CourseTypeCount_a" data-id="4">
                                <div class="round-icohold d-flex justify-content-center align-items-center recommend-ico">

                                </div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Recommended</h2>
                                    <h3 class="mb-0 color-dark">{{$recommended_count}}  @if($recommended_count>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                            <a href="#" class="d-flex mcard-grid justify-content-center align-items-center CourseTypeCount_a" data-id="5">
                                <div class="round-icohold d-flex justify-content-center align-items-center courselib-ico">

                                </div>
                                <div class="training-title d-flex flex-column justify-content-center">
                                    <h2 class="font-bold mb-1 color-dark">Course Library</h2>
                                    <h3 class="mb-0 color-dark">{{$total_course_library}} @if($total_course_library>1) Courses @else Course  @endif</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
{{---END--- Dashboard -- Course Type counts --}}

{{---START--- Dashboard -- Course Lists --}}
            <div class="row bodylink-component flex-wrap">
                <div class="col-md-12 col-lg-9 dleft-column">
                    <div class="row mt-4" id="course_list" style="margin-left:0px !important;">



                    </div>
                </div>
                <div class="col-md-12 col-lg-3 dright-column">
                    <div class="row">
                        <div class="mt-4 d-flex col-md-12">
                            <div class="bshadow card-box dashboard-card-nrm d-flex flex-column w-100">
                                <div class="card-bodycontent" >
                                    <h2 class="color-high font-bold mb-4" >Recent Achievements</h2>

                                    <div id="recent_achievement_list">

                                    </div>
                                    <hr class="themehr mb-2" />
                                    <div class="row justify-content-center">
                                        <input type="hidden" id="recent_achievement_next_page" value="1">
                                        <a href="#" class="color-primary a-link" id="recent_achievement_view_more" onclick="getRecentAchievement()">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>






            </div>
{{---END--- Dashboard -- Course Lists --}}

        </div>
        <!-- content area END-->
    </div>