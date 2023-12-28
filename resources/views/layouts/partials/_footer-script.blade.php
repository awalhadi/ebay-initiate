<!-- Vue -->
<script src="{{ URL::asset('js/app.js') }}"></script>
<script src="{{ URL::asset('js/header.js') }}"></script>
<!-- App's Basic Js  -->
<script src="{{ URL::asset('admin/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('admin/js/jquery.slimscroll.js') }}"></script>
<script src="{{ URL::asset('admin/js/waves.min.js') }}"></script>
<!-- App js-->
<script src="{{ URL::asset('admin/js/app.js') }}"></script>
<!-- Parsley js-->
<script src="{{ URL::asset('admin/js/parsley.min.js') }}"></script>
<!-- Sweet-Alert  -->
<script src="{{ URL::asset('admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('admin/pages/sweet-alert.init.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ URL::asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ URL::asset('admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
{{-- Datepicker --}}
<script src="{{ URL::asset('admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
{{-- Select2 --}}
<script src="{{ URL::asset('admin/plugins/select2/js/select2.min.js') }}"></script>
<script>
    $('.datatable').DataTable();
    jQuery('.datepicker-autoclose').datepicker({
        viewMode: "months",
        minViewMode: "months",
        format: "M yyyy",
        autoclose: true,
        todayHighlight: true
    });
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('.form-validate').parsley();
    $('.select2').select2();
</script>
{{-- Custom js --}}
<script src="{{ asset('admin') . '/js/custom.js' }}"></script>
<script src="{{ asset('common') . '/js/custom-dev.js' }}"></script>
<script src="{{ asset('admin') . '/js/custom-dev.js' }}"></script>
{{-- Load script form view --}}
@stack('script')