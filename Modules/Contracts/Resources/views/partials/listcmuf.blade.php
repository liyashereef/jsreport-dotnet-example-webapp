

        <thead>
                <tr>
                    <th>#</th>
                    <th>Prepared By</th>
                    <th>Project Number</th>
                    <th>Contract Name</th>
                    <th>Contract Start Date</th>
                    <th>Contract End Date</th>
                    <th>Regional Manager</th>
                    <th>Supervisor</th>
                    <th>Total Contract Billing Value</th>
        
                    
            @canany(['add_contracts','view_contracts'])
                    <th>Actions</th>
                    @endcan
                </tr>
            </thead>
        <tbody>
                @php
                // dd($cmuflist)
                $i=0;
             @endphp
            @foreach ($cmuflist as $key=>$list)
            @php
                // dd($cmuflist)
                $i++;
             @endphp
                <tr>
                     <td>{{$i}}</td>
                     <td>{{$list["preparedby"]}}</td>
                      <td>{{$list["contract_number"]}}</td>
                      <td>{{$list["contract_name"]}}</td>
                      <td>{{$list["contract_startdate"]}}</td>
                      <td>{{$list["contract_enddate"]}}</td>
                      <td>{{$list["regional_manager"]}}</td>
                      <td>
                          @if(!is_numeric($list["supervisor_name"]))
                          {{$list["supervisor_name"]}}
                          @endif
                        </td>
                      <td>{{$list["billing_value_formatted"]}}</td>
                     @can('edit_contract')
                     <td><a href="edit-cmuf-form/{{$list["id"]}}" class="edit fa fa-edit" data-id="{{$list["id"]}}"></a></td>
                     @endcan
                     
                    
                 </tr>
            @endforeach
        </tbody>
    



