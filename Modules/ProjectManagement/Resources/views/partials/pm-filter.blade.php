<div class="pm-report-filter border">
    <form id="pm-filter-form">
        <div class="row ">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-6">
                        <label>Start Date</label>
                        <input type="text" name="startdate" id="startdate" placeholder="Start Date" value="" class="form-control datepicker" style="font-size:12px" />
                    </div>
                    <div class="col-md-6">
                        <label>End Date</label>
                        <input type="text" name="enddate" id="enddate" placeholder="End Date" value="" class="form-control datepicker" style="font-size:12px" />
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label>Site</label>
                <select class="form-control option-adjust pm-select2" name="client_id">
                    <option value="" selected>All</option>
                    @foreach($customers as $key => $value)
                    <option value="{{$key}}" {{ isset($site_id) &&($site_id == $key) ? 'selected="selected"' : '' }}>{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Project</label>
                <select class="form-control option-adjust pm-select2" name="project_id">
                    <option value="" selected>All</option>
                    @foreach($projects as $key => $value)
                    <option value="{{$key}}" {{ isset($project_id) &&($project_id == $key) ? 'selected="selected"' : '' }}>{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Employee</label>
                <select class="form-control option-adjust pm-select2" name="user_id" id="user_id">
                    <option value="" selected>All</option>
                    @foreach($employees as $key => $value)
                    <option value="{{$key}}" {{ isset($user_id) &&($user_id == $key) ? 'selected="selected"' : '' }}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Status (From - To)</label>
                <div class="row">
                    <div class="col-md-4">
                        <input type="number" name="status_from" placeholder="Low" value="" max="100" min="0" step="1" class="form-control stat-field" />

                    </div>
                    <div class="col-md-4">
                        <input type="number" name="status_to" placeholder="High" value="" max="100" min="0" step="1" class="form-control stat-field" />
                    </div>
                    <div class="col-md-4">
                        <button id="pm-filter-search" type="button" class="btn pm-filter-btn"><i class="fa fa-search"></i></button>
                        <button id="pm-filter-reset" type="button" class="btn pm-filter-btn"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>