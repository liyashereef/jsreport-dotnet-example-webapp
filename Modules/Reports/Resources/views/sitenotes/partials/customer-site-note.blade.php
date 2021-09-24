@if ($siteNotes)
    <table id="resulttable" class="table table-bordered dataTable no-footer dtr-inline" style="width:100%">
        <thead>
        <tr>
            <th >Project </th>

            <th >Date</th>
            <th >Time</th>
            <th >Subject</th>
            <th >Attendees</th>
            <th >Location</th>
            <th >Task</th>
            <th >Assigned To</th>
            <th >Due Date</th>
            <th >Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($siteNotes as $key=>$cust)
            @foreach($cust as $k=>$item)
                <tr s="22">
                    <td class="pointer" style="text-align:left" onclick="getSiteNotesDetails({{ $item['customer_id']}}, {{ $item['site_notes_id']}})">{{$item['project_number'] }}-{{$item['project_name'] }}</td>

                    <td >{{$item['date'] }}</td>
                    <td >{{$item['time'] }}</td>
                    <td >{{$item['subject'] }}</td>
                    <td >{{$item['attendees'] }}</td>
                    <td >{{$item['location'] }}</td>
                    <td >{{$item['task_name'] }}</td>
                    <td >{{$item['assigned_to'] }}</td>
                    <td >{{$item['due_date'] }}</td>
                    <td >{{$item['status'] }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endif
<style>
    td.pointer {
        cursor: pointer;
    }
    td.pointer:hover {
        color: #F2351F !important;
    }
</style>
<script type="text/javascript">
    function getSiteNotesDetails(customerId, siteNotesid) {
        var url = "{{ route('customer.sitenotesonly',[':id',':siteNotesId']) }}";
        url = url.replace(':id', customerId);
        url = url.replace(':siteNotesId', siteNotesid);
        window.open(url, "_blank");
    }
</script>
