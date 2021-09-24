<section class="candidate full-width">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Contact Log </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 form-panel">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group row styled-form">
                            <label for="inputEmail3" class="col-md-4  label-adjust control-label col-xs-3">Name</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" id="inputEmail3" placeholder="Name" name="candidate_name" value="{{$candidateJob->candidate->name}}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group row styled-form">
                            <label for="inputEmail3" class="col-md-4 label-adjust control-label col-xs-3">Address</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" id="inputEmail3" placeholder="Address" name="address" value="{{$candidateJob->candidate->address}}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group row styled-form">
                            <label for="inputEmail3" class="col-md-4 label-adjust control-label col-xs-3">Postal Code</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" id="inputEmail3" placeholder="Postal Code" name="postal_code" value="{{$candidateJob->candidate->postal_code}}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Transaction Log </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
        <!-- Data List container One-->
        @foreach($event_log as $key=>$event_log)
        <div class="data-list-container">
            <div class="data-list-head">
                <h2>{{date('F d, Y', strtotime(str_replace('-','/',$event_log->created_at)))}}</h2>
            </div>
            <div class="data-list-body">
                <div class="data-list-line row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Date
                    </div>
                    <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{date('F d, Y', strtotime(str_replace('-','/',$event_log->created_at)))}}

                    </div>
                </div>

                <div class="data-list-line  row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Time
                    </div>
                    <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{date('h:i A', strtotime($event_log->created_at))}}

                    </div>
                </div>
                <div class="data-list-line  row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Duty Officer
                    </div>
                    <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$event_log->requirement->user->full_name or '--'}}
                    </div>
                </div>
                <div class="data-list-line  row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Status
                    </div>
                    <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$event_log->status_log->status }}
                    </div>
                </div>
                <div style="display:{{ $event_log->status == 1 ? 'block' : 'none' }}">
                    <div class="data-list-line  row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Project Number
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{$event_log->requirement->customer->project_number or '--'}}
                        </div>
                    </div>

                    <div class="data-list-line  row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Type
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{$event_log->requirement->assignment_type->type or '--'}}
                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Client
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{$event_log->requirement->customer->client_name or '--'}}
                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Site Rate
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            ${{number_format((float) $event_log->requirement->site_rate, 2, '.', '')}}

                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Rate Accepted
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            ${{number_format((float) $event_log->accepted_rate, 2, '.', '')}}

                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Start Date
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{!empty($event_log->requirement->start_date) ? date('F d, Y', strtotime(str_replace('-','/',$event_log->requirement->start_date))):
                            '--'}}


                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            End Date
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{!empty($event_log->requirement->end_date) ? date('F d, Y', strtotime(str_replace('-','/',$event_log->requirement->end_date))):
                            '--'}}

                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Time Scheduled
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{!empty($event_log->requirement->time_scheduled) ? date('h:i A', strtotime($event_log->requirement->time_scheduled)): '--'}}

                        </div>
                    </div>
                    <div class="data-list-line row">
                        <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                            Length of Shift (Hrs)
                        </div>
                        <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                            {{$event_log->requirement->length_of_shift or '--'}}
                        </div>
                    </div>
                </div>
                <div class="data-list-line row">
                    <div class="col-md-2 col-xs-12 col-sm-12 data-list-label">
                        Notes
                    </div>
                    <div class="col-md-10 col-xs-12 col-sm-12  data-list-disc">
                        {{$event_log->requirement->notes or '--'}}
                    </div>
                </div>

            </div>
        </div>

        @endforeach

    </div>
</section>