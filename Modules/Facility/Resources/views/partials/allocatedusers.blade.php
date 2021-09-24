<table id="usertable" class="table table-bordered">
    <thead>
        <th>
            <input type="checkbox" id="selectall" />
        </th>
        <th>Name</th>
        <th>User Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Prerequisite</th>
        <th>Actions</th>
    </thead>
    <tbody>

    @foreach ($users as $user)
    <tr>
        <td>
        <input type="checkbox" name="{{$user->id}}"  class="alloccbox"

            @if(isset($user->FacilityServiceUserAllocation) || isset($user->ServiceFacilityUserAllocation))
                @if(($user->FacilityServiceUserAllocation->count()>0 && $item->single_service_facility==1) || ($user->ServiceFacilityUserAllocation->count()>0  && $item->single_service_facility==0))
                    attr-allocated="true"
                @else
                    attr-allocated="false"
                @endif
            @endif
            id="{{$user->id}}" /></td>
        <td>{{$user->first_name}} {{$user->last_name}}</td>
        <td>{{$user->username}}</td>
        <td>{{$user->email}}</td>
        <td>{{$user->phoneno}}</td>
        <td style="cursor:pointer;padding-left:40px" attr-userid="{{$user->id}}" class="prereqform"
        @if(count($user->FacilityServiceUserAllocation)>0)
            attr-allocated ="1"  attr-allocid="{{($user->FacilityServiceUserAllocation)[0]->id}}"
        @else
            attr-allocated="0"  attr-allocid="0"
        @endif>

            @if(count($user->FacilityServiceUserAllocation)>0)
                <span style="padding-left:15px">
                @if(($user->FacilityServiceUserAllocation)[0]->facilityuserprerequisiteanswer->count()>0)
                    <i class="fa fa-check"></i>
                @else
                    --
                @endif
                </span>
            @else
            <span style="padding-left:15px">
                --
            </span>
            @endif
        </td>
        <td>
        @if (isset($facilitydetails))
            @if ($facilitydetails->single_service_facility==1)
                @if ($chosenfacility>0)

                    @if ($user->FacilityServiceUserAllocation->count()<1)
                        @can('manage_user_allocation')
                        <button attr-add="addfacility" attr-id="{{$chosenfacility}}" attr-user="{{$user->id}}" id="add-{{$user->id}}-{{$chosenfacility}}" class="btn btn-primary adduseralloc">Add</button>
                        <button attr-add="removefacility" attr-id="{{$chosenfacility}}" attr-user="{{$user->id}}" id="rem-{{$user->id}}-{{$chosenfacility}}" class="btn btn-danger adduseralloc" style="display: none">Remove</button>
                        @endcan
                    @else
                        @php
                        if(isset($user->FacilityServiceUserAllocation)){
                            try{
                                $dayalloclist = ($user->FacilityServiceUserAllocation)[0]->facilityuserweekenddefinition->pluck('day_id');
                                if(in_array('6',$dayalloclist->toArray())){
                                    echo '<input type="checkbox" id="weekend-'.($user->FacilityServiceUserAllocation)[0]->id.'"  attr-user="'.$user->id.'" value="'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-daytype="weekend" class="weekendallocation" checked />&nbsp;&nbsp;';
                                    echo "Weekend&nbsp;";
                                }else{
                                    echo '<input type="checkbox" id="weekend-'.($user->FacilityServiceUserAllocation)[0]->id.'"  attr-user="'.$user->id.'"  value="'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-daytype="weekend" class="weekendallocation"  />&nbsp;&nbsp;';
                                    echo "Weekend&nbsp;";
                                }
                                echo "&nbsp;";
                                if(in_array('1',$dayalloclist->toArray())){
                                    echo '<input type="checkbox" id="weekday-'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-daytype="weekday" class="weekendallocation" checked />&nbsp;&nbsp;';
                                    echo "Weekday&nbsp;";
                                }else{
                                    echo '<input type="checkbox" id="weekday-'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->FacilityServiceUserAllocation)[0]->id.'" attr-daytype="weekday" class="weekendallocation"  />&nbsp;&nbsp;';
                                    echo "Weekday&nbsp;";
                                }

                            }catch(Exception $e){

                            }
                            echo "&nbsp;&nbsp;";

                        }
                        @endphp
                        @can('manage_user_allocation')
                        <button attr-add="addfacility" attr-id="{{$chosenfacility}}"  attr-user="{{$user->id}}" id="add-{{$user->id}}-{{$chosenfacility}}" class="btn btn-primary adduseralloc" style="display: none">Add</button>
                        <button attr-add="removefacility" attr-id="{{$chosenfacility}}"  attr-user="{{$user->id}}" id="rem-{{$user->id}}-{{$chosenfacility}}" class="btn btn-danger adduseralloc">Remove</button>
                        @endcan
                    @endif
                @else
                    Select a Facility
                @endif
            @else

                @if ($service->count()>0)

                        @php
                        if(isset($user->ServiceFacilityUserAllocation)){
                           try{
                                $dayalloclist = ($user->ServiceFacilityUserAllocation)[0]->facilityuserweekenddefinition->pluck('day_id');

                                if(in_array('6',$dayalloclist->toArray())){
                                    echo '<input type="checkbox" id="weekend-'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->ServiceFacilityUserAllocation)[0]->id.'"  attr-daytype="weekend" class="weekendallocation"  checked />&nbsp;&nbsp;';
                                    echo "Weekend&nbsp;";
                                }else{
                                    echo '<input type="checkbox" id="weekend-'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-daytype="weekend" class="weekendallocation" />&nbsp;&nbsp;';
                                    echo "Weekend&nbsp;";
                                }
                                echo "&nbsp;";
                                if(in_array('1',$dayalloclist->toArray())){
                                    echo '<input type="checkbox" id="weekday-'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->ServiceFacilityUserAllocation)[0]->id.'"  attr-daytype="weekday" class="weekendallocation" checked />&nbsp;&nbsp;';
                                    echo "Weekday&nbsp;";
                                }else{
                                    echo '<input type="checkbox" id="weekday-'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-user="'.$user->id.'" value="'.($user->ServiceFacilityUserAllocation)[0]->id.'" attr-daytype="weekday" class="weekendallocation"  />&nbsp;&nbsp;';
                                    echo "Weekday&nbsp;";
                                }
                           }catch(Exception $e){

                           }
                           echo "&nbsp;&nbsp;";
                        }
                    @endphp
                    @can('manage_user_allocation')
                    @if ($user->ServiceFacilityUserAllocation->count()<1)
                        <button attr-add="addservice" attr-id="{{$service->id}}"   attr-user="{{$user->id}}" id="add-{{$user->id}}-{{$service->id}}" class="btn btn-primary adduseralloc">Add</button>
                        <button attr-add="removeservice" attr-id="{{$service->id}}"  attr-user="{{$user->id}}" id="rem-{{$user->id}}-{{$service->id}}" class="btn btn-danger adduseralloc" style="display: none">Remove</button>
                    @else
                        <button attr-add="addservice" attr-id="{{$service->id}}"  attr-user="{{$user->id}}" id="add-{{$user->id}}-{{$service->id}}" class="btn btn-primary adduseralloc" style="display: none">Add</button>
                        <button attr-add="removeservice" attr-id="{{$service->id}}"  attr-user="{{$user->id}}" id="rem-{{$user->id}}-{{$service->id}}" class="btn btn-danger adduseralloc">Remove</button>
                    @endif
                    @endcan
                @else
                    Select a service
                @endif


            @endif
        @else
            Select Facility/Service
        @endif


        </td>

    </tr>
    @endforeach
    </tbody>
</table>
<input type="hidden" name="facilityorservice" id="facilityorservice"
@if (isset($facilitydetails))
 value="{{$facilityorservice}}"
 @else
 value="0"
 @endif
  />

