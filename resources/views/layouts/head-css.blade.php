@yield('css')

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
	@vite(['resources/scss/bootstrap.scss', 'resources/scss/icons.scss', 'resources/scss/app.scss'])
@else
	<link href="{{ URL::asset('build/css/app.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endif
<!-- App js -->
<script src="{{ URL::asset('build/js/plugin.js') }}"></script>