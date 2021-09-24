@if(!empty($tabDetails))

@if(empty($selectedCustomer))
   @php($selectedCustomer[0] = 0)
@endif

<div id="dashboard-tab-area" class="nav nav-tabs expense dashboard-tabs" role="tablist" style="width: 99%;float: right;">
    @php($index = 0)
    <a class="nav-item nav-link arrow prev-arrow" data-tab-index="{{$index}}" href="#">
        <span style="color:white;">&#10229;</span>
    </a>

    @foreach($tabDetails as $tab)
        @php($tabName = ucwords(strtolower($tab->tab_name)))
        @php($index += 1)
        <a data-tab="{{$tab->id}}" data-tab-index="{{$index}}"  class="tab_{{$index}} nav-item nav-link expense tab-config {{($index == 1)? 'dashboard-default-selected-tab': ''}}" title="{{$tabName}}" id="tab_{{$tab->id}}" href="#">{{$tabName}}</a>
    @endforeach

    <a class="nav-item nav-link arrow next-arrow" data-tab-index="{{$index}}" href="#">
        <span style="color:white;">&#10230;</span>
    </a>
</div>
@endif


<script>

    $(function(){
        $('.arrow').hide();
        var tabs_count = $('.dashboard-tabs > .tab-config').length;
        if(tabs_count > 0) {
            load_tab_structure(1, 3, tabs_count);
            load_first_tab_content();
        }
    });

    function load_tab_structure(minIndex, maxIndex, tabs_count) {
        var arrow_width = 0;
        $('.arrow').hide();
        $('.tab-config').hide();
        $('.arrow').addClass('hidden');
        $('.arrow').removeClass('show');
        $('.tab-config').removeClass('tab-end');
        $('.tab-config').removeClass('tab-start');

        var index = 1;
        var active_index = 0;
        $('.tab-config').each(function(){
            if(index <= maxIndex && index >= minIndex) {
                $('.tab_' + index).show();
                active_index++;
            }else{
                $('.tab_' + index).hide();
            }

            if(index > maxIndex) {
                tabs_count = 3;
                $('.next-arrow').show();
                $('.next-arrow').removeClass('hidden');
                $('.next-arrow').addClass('show');
            }

            if(index < minIndex) {
                tabs_count = active_index;
                $('.prev-arrow').show();
                $('.prev-arrow').removeClass('hidden');
                $('.prev-arrow').addClass('show');
            }

            if(index == maxIndex) {
                $('.tab_' + index).addClass('tab-end');
            }

            if(index == minIndex) {
                $('.tab_' + index).addClass('tab-start');
            }
            index++;
        });

        if($('.arrow.show').length > 0) {
            $('.arrow.show').each(function(){
                arrow_width += 7;
            });
        }

        var tab_parent_width = (99 - arrow_width);
        var individual_width = (tab_parent_width / tabs_count);
        $('.tab-config').css('width',individual_width + '%');

        $('.tab-config').each(function(){
            var lenth_tab = $('.tab-config').length;
            var tab_selected = $(this);
            var calculated_character_length = (80/lenth_tab);
            if(tab_selected.text().length > calculated_character_length) {
                tab_selected.text(tab_selected.text().substring(0,calculated_character_length) + '..');
            }
        });
    }

    function load_first_tab_content() {
        var first_tab_id = $('.tab-config.tab-start').attr('data-tab');
        if(first_tab_id !== "") {
            dashboard.loadTabDetails(first_tab_id);
        }
    }

    $('.tab-config').on('click', function(){
        var selected_tab = $(this).attr('data-tab');
        var tabs_count = $('.dashboard-tabs > .tab-config').length;
        var end_tab= $(this).hasClass('tab-end');
        var start_tab= $(this).hasClass('tab-start');
        var tab_index= $(this).attr('data-tab-index');
        if(end_tab && tabs_count != tab_index) {
            var minIndex = Number(tab_index) - 1;
            var maxIndex = Number(tab_index) + 1;
            load_tab_structure(minIndex, maxIndex, tabs_count);
            dashboard.loadTabDetails(selected_tab);
        }else if(start_tab && tab_index != 1) {
            var minIndex = Number(tab_index) - 1;
            var maxIndex = Number(tab_index) + 1;
            load_tab_structure(minIndex, maxIndex, tabs_count);
            dashboard.loadTabDetails(selected_tab);
        }else{
            dashboard.loadTabDetails(selected_tab);
        }
    });

    $('.next-arrow').on('click', function(){
        var tabs_count = $('.dashboard-tabs > .tab-config').length;
        var tab_index= $('.tab-end').attr('data-tab-index');
        var minIndex = Number(tab_index) + 1;
        var maxIndex = Number(tab_index) + 3;
        load_tab_structure(minIndex, maxIndex, tabs_count);
        load_first_tab_content();
    });

    $('.prev-arrow').on('click', function(){
        var tabs_count = $('.dashboard-tabs > .tab-config').length;
        var tab_index= $('.tab-start').attr('data-tab-index');
        if(tab_index < 4) {
            console.log('in');
            var minIndex = 1;
            var maxIndex = 3;
        }else {
            var minIndex = Number(tab_index) - 3;
            var maxIndex = Number(tab_index) - 1;
        }
        load_tab_structure(minIndex, maxIndex, tabs_count);
        load_first_tab_content();
    });
</script>
