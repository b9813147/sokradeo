<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sokradeo by CDN</title>

    <!-- Styles -->
    <link href="{{ asset('lib/tbaplayer/css/tbaplayer.css')       }}" rel="stylesheet">
    <link href="{{ asset('lib/tbaplayer/css/skins/dark-blue.css') }}" rel="stylesheet">
    
</head>
<body>
	<script>
		@if (isset($globals))
			var Globals = @json($globals);
		@else
			var Globals = {};
		@endif
	</script>
	
    <div id="app">
		<section>
			<cpnt-tbaplayer class="tbaplayer" id="tbaplayer" v-bind:options="tbaPlayer.options"></cpnt-tbaplayer>
		</section>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('lib/tbaplayer/js/tbaplayer.rice.js') }}"></script>
    <script src="{{ asset('lib/tbaplayer/locale/vendor/iview/en-US.js') }}"></script>
    <script src="{{ asset('lib/tbaplayer/locale/vendor/iview/zh-TW.js') }}"></script>
    <script src="{{ asset('lib/tbaplayer/locale/vendor/iview/zh-CN.js') }}"></script>
    <script src="{{ asset('lib/tbaplayer/lang/vendor/video.js/en.js')   }}"></script>
    <script src="{{ asset('lib/tbaplayer/lang/vendor/video.js/tw.js')   }}"></script>
    <script src="{{ asset('lib/tbaplayer/lang/vendor/video.js/cn.js')   }}"></script>
    <script src="{{ asset('lib/axios/axios.min.js') }}"></script>
    
    @include('cms.tbavideo.watchbycdn.service')
    @include('cms.tbavideo.watchbycdn.store'  )
    @include('cms.tbavideo.watchbycdn.lang'   )
    @include('cms.tbavideo.watchbycdn.main'   )
    
</body>

</html>
