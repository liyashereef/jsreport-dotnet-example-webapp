<table class="table table-bordered" id="mail-table">
    <thead>
        <th>Date</th>
        <th>Time</th>
        <th>Subject</th>
        <th>Roles</th>
        <th>Client</th>
        <th>Sender</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($tableContent as $contentData)
        <tr>
            <td data-sort="{{$contentData->id}}">
                {{date("d M Y",strtotime($contentData->created_at))}}
            </td>
            <td data-sort="{{$contentData->id}}">
                {{date("H:i",strtotime($contentData->created_at))}}
            </td>
            <td>
               {{$contentData->subject}} 
            </td>
            <td>
                @if ($contentData->email_roles_associated!=null)
                    @foreach ((array)$contentData->email_roles_associated as $roleAssoc)
                        {{$uRoles[$roleAssoc]  }}<br/>
                    @endforeach                    
                @endif

             
            </td>
            <td>
                @if ($contentData->email_clients!=null)
                        @foreach ((array)$contentData->email_clients as $clientAssoc)
                        @if (isset($customerArray[$clientAssoc]))
                                {{$customerArray[$clientAssoc]  }}<br/>
                        @endif
                    @endforeach
                @endif 
                @if ($contentData->email_clientgroups!=null)
                        @foreach ((array)$contentData->email_clientgroups as $clientGroupAssoc)
                            {{isset($allClientGroups[$clientGroupAssoc])?$allClientGroups[$clientGroupAssoc]:""  }}<br/>
                    @endforeach
                @endif             

            </td>
            <td>
                {{$contentData->users->getFullNameAttribute()}}
            </td>
            <td>
                <i class="fa fa-eye viewMessage" attr-id="{{$contentData->_id}}" style="cursor: pointer" ></i>
            </td>
        </tr>
            
        @endforeach
    </tbody>
</table>