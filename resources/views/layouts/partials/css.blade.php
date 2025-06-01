
<link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">

<!-- Bootstrap 3.3.6 -->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" rel="stylesheet">

<!-- bootstrap toggle -->
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<!-- bootstrap datepicker -->
{{-- <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.css?v='.$asset_v) }}"> --}}

<!-- Bootstrap file input -->
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-fileinput/fileinput.min.css?v='.$asset_v) }}">

<link rel="stylesheet" href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css?v='.$asset_v) }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.css" integrity="sha512-X6069m1NoT+wlVHgkxeWv/W7YzlrJeUhobSzk4J09CWxlplhUzJbiJVvS9mX1GGVYf5LA3N9yQW5Tgnu9P4C7Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.rtl.min.css?v='.$asset_v) }}">
@endif

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />


<link href="{{ asset('v2/css/sidebar.min.css?v=8') }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('v2/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/metisMenu.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/slicknav.min.css') }}">

<link rel="stylesheet" href="{{ asset('v2/css/typography.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/default-css.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('v2/css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css?v='.$asset_v) }}">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<!-- Styles -->
<link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.min.css?v='.$asset_v) }}">

{{-- fonts --}}
<link rel="stylesheet" href="{{url('public/fonts/google-fonts/google-fonts.css')}}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{ asset('plugins/ionicons/css/ionicons.min.css?v='.$asset_v) }}">
 <!-- Select2 -->
<link rel="stylesheet" href="{{ asset('AdminLTE/plugins/select2/css/select2.min.css?v='.$asset_v) }}">


<!-- iCheck -->
 <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/iCheck/square/blue.css?v='.$asset_v) }}"> 
 
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('AdminLTE/plugins/DataTables/datatables.min.css?v='.$asset_v) }}">

<!-- Toastr -->
{{-- <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css?v='.$asset_v) }}"> --}}

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('AdminLTE/css/AdminLTE.rtl.min.css?v='.$asset_v) }}">
@endif


{{-- <link rel="stylesheet" href="{{ asset('css/Colorpicker.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('css/pickr.min.css') }}"/>

@yield('css')

<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
