<script>

        $(document).ready(function() {


            $('#course_search').val('');

            /***START** Add search_key and  course_list_type default value to local storage  */
                localStorage.setItem('course_list_type', 1);
                localStorage.setItem('search_key', '');
            /***END** Add search_key and  course_list_type default value to local storage  */

            $('#course_list_type').text('To–Do Course List')

            getCourseList();
            getRecentAchievement();

            /***START** On clicking count showing card, list Course data   */
            $('#CourseTypeCount').on('click','.CourseTypeCount_a', function (e) {

        /***START** on click change widget color */
                $(".CourseTypeCount_a").removeClass("m-linkactive");
                $($(this)).addClass('m-linkactive');
        /***END** on click change widget color */

                /*****
                 * To – Do = 1
                 * Completed = 2
                 * Overdue = 3
                 * Recommended = 4
                 * Course Library = 5
                 */


                var id = $(this).data('id');
                localStorage.setItem('course_list_type', id);

                // if(id == 1){
                //     $('#course_list_type').text('To–Do Course List')
                // }else if(id == 2){
                //     $('#course_list_type').text('Completed Course List')
                // }else if(id == 3){
                //     $('#course_list_type').text('Overdue Course List')
                // }else if(id == 4){
                //     $('#course_list_type').text('Recommended Course List')
                // }else if(id == 5){
                //     $('#course_list_type').text('Course Library')
                // }
                getCourseList();



            });
            /***END** On clicking count showing card, list Course data   */

            /***START** On typing search input add value to local storage and get course list based on that*/
            $('#course_search').on('keyup', function (e) {
                // localStorage.setItem('search_key', $('#course_search').val());
                getCourseList();
            });
            /***END** On typing search input add value to local storage and get course list based on that   */

        });

        function getCourseList() {
            localStorage.setItem('search_key', $('#course_search').val());
            var base_url = "{{ route('learning.dashboard.course-list',':id') }}";
            var url = base_url.replace(':id', localStorage.getItem('course_list_type'));
            var search_key = localStorage.getItem('search_key');
            $.ajax({
                url: url,
                type: 'GET',
                data: 'search_key='+search_key,
                success: function (data) {
                    // console.log(data);
                    $('#course_list').html(data);
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
                contentType: false,
                processData: false,
            });
        }
        function getRecentAchievement() {
            var completed_course_url = "{{ route('learning.dashboard.completed-course-list') }}";
            var recent_achievement_next_page = $('#recent_achievement_next_page').val()
            $.ajax({
                url: completed_course_url,
                type: 'GET',
                data: 'page='+recent_achievement_next_page,
                success: function (data) {
                    // console.log(data);
                    $('#recent_achievement_list').append(data.html_view);
                     $('#recent_achievement_next_page').val(data.nextPage);
                     if(data.nextPage == ''){
                         $('#recent_achievement_view_more').hide();
                     }


                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
                contentType: false,
                processData: false,
            });
        }
    </script>