   {{ Form::submit('Enter New Record', array('class' => 'btn submit add-new','onclick'=>'addRating()')) }}
          <br><br>
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
                        <!-- Data List container One-->
                        @foreach($employee_rating as $key=>$employee_rating)
                          <div class="data-list-line row">
                            <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                              Subject
                            </div>
                            <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                              {{$employee_rating->subject}}
                            </div>
                          </div>

                          <div class="data-list-container">
                            <div class="data-list-head">
                              <h2></h2>
                            </div>
                              <div class="data-list-body">
                                <div class="data-list-line row">
                                  <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                    Date
                                  </div>
                                  <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                    @php
                                        //dump($employee_rating->created_at);
                                    @endphp
                                    {{date('F d, Y', strtotime(str_replace('-','/',$employee_rating->created_at)))}}

                                  </div>
                                </div>

                                <div class="data-list-line  row">
                                  <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                    Time
                                  </div>
                                  <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                   {{date('h:i A', strtotime($employee_rating->created_at))}}
                                 </div>
                               </div>

                                 <div class="data-list-line  row">
                                  <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                    Manager Name
                                  </div>
                                  <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                   {{$employee_rating->user->full_name}}
                                 </div>
                               </div>

                                <div>

                                <div class="data-list-line  row">
                                  <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                   Manager Employee Id
                                 </div>
                                 <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                   {{$employee_rating->user->trashedEmployee->employee_no}}
                                 </div>
                               </div>

                                 <div class="data-list-line  row">
                                  <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                   Supporting Facts
                                 </div>
                                 <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                  {{$employee_rating->supporting_facts}}
                                </div>
                              </div>

                              <div class="data-list-line row">
                                <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                                  Rating
                                </div>
                                <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                                 {{(null!=$employee_rating->userRating)?$employee_rating->userRating->rating:'--'}}
                               </div>
                              </div>
                            </div>
                         </div>
                       </div>
                      @endforeach
                    </div>
