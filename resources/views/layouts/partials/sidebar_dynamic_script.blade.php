<!-- Sidebar -->
<script>
    function removeDyns() {
        $('#sidebar').removeClass('dyn-sidebar');
        $('#sidebar ul.components').removeClass('dyn-side-ul')
    }

    $(document).ready(function() {
        //Sidebar item press
        $('#sidebar .components > li > a').on('click', function(e) {
            let target = $(e.target);
            if (target.hasClass('sidebarToggleImg') || target.hasClass('sidebarCollapseEl')) {
                return;
            }
            $('#sidebar').addClass('dyn-sidebar');
            $('#sidebar ul.components').addClass('dyn-side-ul');
        });

        //click outside sidebar
        $('#sidebar .components').on('click', function(e) {
            let target = $(e.target);
            if (target.hasClass("components")) {
                console.log('ul mouseover init remove')
                removeDyns();
            }
        });
    });
</script>