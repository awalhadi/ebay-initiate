<meta charset="utf-8" />
<title>{{get_page_meta()}} {{ config('settings.site_title') ?? config('app.name') }}</title>
<meta content="Admin Dashboard" name="description" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<!-- App favicon -->
{{-- <link rel="shortcut icon" href="{{ \Illuminate\Support\Facades\Storage::url(config('settings.site_favicon') ?? '' )}}">
--}}
<!-- App css -->
<link href="{{ URL::asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/css/metismenu.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('admin/css/icons.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/css/style.css') }}" rel="stylesheet" type="text/css" />
<!-- Sweet Alert -->
<link href="{{ URL::asset('admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
<!-- DataTables -->
<link href="{{ URL::asset('admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<!-- Responsive datatable -->
<link href="{{ URL::asset('admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
{{-- Datepicker --}}
<link href="{{ URL::asset('admin/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}"
    rel="stylesheet">
{{-- Select2 --}}
<link href="{{ URL::asset('admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- Custom css --}}
<link rel="stylesheet" href="{{ asset('admin') . '/css/custom.css' }}">
<link rel="stylesheet" href="{{ asset('common') . '/css/custom-dev.css' }}">
<link rel="stylesheet" href="{{ asset('admin') . '/css/custom-dev.css' }}">
{{-- Load style form view --}}
@stack('style')