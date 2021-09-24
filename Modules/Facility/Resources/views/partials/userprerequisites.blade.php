

    @foreach ($facprereq as $item)
        
        <div class="form-group" id="severity">
            <label for="severity" class="col-sm-12 control-label">{{$item->requisite}}</label>
            <div class="col-sm-12">
                <select attr-facilityid="{{$item->facility_id}}" 
                    attr-userid="{{$user_id}}" attr-requisite={{$item->id}}
                    attr-allocationid="{{$allocationid}}" class="form-control frmdata" required>
                    <option value="">Select Any</option>
                    @if($item->FacilityUserPrerequisiteAnswer->count()>0)
                        <option 
                        @if(($item->FacilityUserPrerequisiteAnswer)[0]->answer=="Yes")
                                    selected
                                @endif
                                value="Yes">Yes</option>
                        <option @if(($item->FacilityUserPrerequisiteAnswer)[0]->answer=="No")
                            selected
                        @endif
                        value="No">No</option>
                        <option @if(($item->FacilityUserPrerequisiteAnswer)[0]->answer=="Not Applicable")
                            selected
                        @endif
                        value="Not Applicable">Not Applicable</option>
                    @else
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                        <option value="Not Applicable">Not Applicable</option>
                    @endif
                </select>
            </div>
        </div>
        
    @endforeach
   
