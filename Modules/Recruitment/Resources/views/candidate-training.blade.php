@extends('layouts.app')
@section('content')
<style>
    .profileImage {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
    }

    .candidate-image-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .candidate-image-div:hover img {
        z-index: 9999999;
        transform:scale(5);
        -ms-transform:scale(5); /* IE 9 */
        -moz-transform:scale(5); /* Firefox */
        -webkit-transform:scale(5); /* Safari and Chrome */
        -o-transform:scale(5); /* Opera */
        position: relative;
    }
</style>
<div class="table_title">
    <h4> Candidate Training
    </h4>
</div>
<table class="table table-bordered" id="candidates-table">
    <thead>
        <tr>
            <th class="sorting">Candidate Name</th>
            <th class="sorting">Image</th>
            <th class="sorting">City</th>
            <th class="sorting">Postal Code</th>
            <th class="sorting">Year of Security Experience</th>
            <th class="sorting">Last Wage</th>
            <th class="sorting">Application Date</th>
            <th class="sorting"></th>
            <th class="sorting">Cycle Time </th>
            <th class="sorting">Email Address</th>
            <th nowrap class="sorting">Phone</th>
            <th class="sorting">Course</th>
            <th class="sorting">Status</th> 
        </tr>
    </thead>
</table>

@stop
@section('scripts')
<script>

    $(function() {
        var table = $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            dom: 'Blfrtip',
            ajax: "{{ route('recruitment.candidate-traininglist') }}",
            buttons: [
                {
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    // exportOptions: {
                    //     @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    //     columns: 'th:not(:last-child)',
                    //     @endcan
                    // }
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                /*{
                    extend: 'excelHtml5',
                    exportOptions: {
                        @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                        columns: 'th:not(:last-child)',
                        @endcan
                    }
                },*/
                {
                    extend: 'print',
                    pageSize: 'A2',
                    // exportOptions: {
                    //     @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    //     columns: 'th:not(:last-child)',
                    //     @endcan
                    //     stripHtml: false,
                    // }
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            "order": [[0, "asc"]],
        
            columns: [                {
                    extend: 'pdf',
                    pageSize: 'A2',
                    exportOptions: {
                      
                        columns: [ 0, 2, 3,4,5,6,7,9,10,11,12,13,14 ],

                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                       
                        columns: [ 0,1, 2, 3,4,5,6,7,9,10,11,12,13,14 ],

                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [
            { width: 50, targets: 4 }
            ],
        
            columns: [
              
                 {
                    data: null,
                    name: 'name',
                    render: function (row) {
                        actions = '';
                        var url = '{{ route("recruitment.candidate.view", [":candidate_id"]) }}';
                        url = url.replace(':candidate_id', row.id);
                        actions += '<a title="View application" href="' + url + '">' + row.name + '</a>';
                        return actions;
                    }
                },

                {
                    data: null,
                    name:'profile_image',
                    render: function (row) {
                        if(row.profile_image != null && row.profile_image != "") {
                            let image = "{{asset('images/uploads/') }}/" + row.profile_image;
                            return '<div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;"><img name="image" src="'+image+'"  class="profileImage"></div>';
                        }else{
                            let image = "{{asset('images/uploads/') }}/{{ config('globals.noAvatarImg') }}";
                            return '<div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;"><img name="image" src="'+image+'"  class="profileImage"></div>';
                        }

                    }
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'postal_code',
                    name: 'postal_code'
                },
                {
                    data: 'years_security_experience',
                    name: 'years_security_experience',
                    defaultContent:'--',

                },
                 {
                    data: null,
                    name: 'last_wage',
                    defaultContent:'--',
                    render: function (row) {
                        if(row.last_wage != null){
                        return '$' + parseFloat(row.last_wage).toFixed(2);
                        }
                    },
                },

                 {
                    data: 'application_date',
                    name: 'application_date_unformatted'
                },
                {
                    data: 'application_date',
                    name: 'application_date',
                    visible:false
                },
                 {
                    data: 'cycle_time',
                    name: 'cycle_time'
                },

                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: null,
                    name: 'phone',
                    render: function (row) {
                        phone_home = row.phone;
                        phone_cellular = row.phone_cellular;
                        phone_home = (null != phone_home) ? (phone_home.split(')').join(') ')) :'';
                        phone_cellular = (null != phone_cellular) ? phone_cellular.split(')').join(') ') : '';
                        return phone_home + '\r\n<br/>' + phone_cellular;
                    }
                },
                {
                    data: null,
                    name: 'course',
                    defaultContent:'--',
                    render: function (row) {
                        if(row.course != null){
                        return row.course.split("#").join("<br/>");
                        }
                    },

                },
                {
                    data: null,
                    name: 'completed_percentage',
                    defaultContent:'--',
                    render: function (row) {
                        if(row.completed_percentage != null){
                        return row.completed_percentage.split("#").join("<br/>");
                        }
                    },
                }


            ]
        });


    });
    </script>
@stop
