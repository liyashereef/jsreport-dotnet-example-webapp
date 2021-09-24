<script>
    super_admin = {!! (int)(Auth::user()->can('super_admin')) !!};
    admin = {!! (int)(Auth::user()->hasAnyPermission(['admin', 'super_admin'])) !!};
    //todo:: later replace below code with permissioons
    coo = {!! (int)(Auth::user()->hasAnyPermission(['admin', 'super_admin', 'coo'])) !!};
    hr = {!! (int)(Auth::user()->hasAnyPermission(['admin', 'super_admin', 'hr_representative'])) !!};
    am = {!! (int)(Auth::user()->hasAnyPermission(['admin', 'super_admin', 'area_manager'])) !!};
</script>
    