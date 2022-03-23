<!DOCTYPE html>
@inject('browserLang','App\Libraries\Lang\Lang')
<html lang="{{ $browserLang->getBrowserLang() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="keywords" content="教師專業發展,公開授課,智慧教育,教育大數據,醍摩豆">
    <meta name="description" content="蘇格拉底平台是由全球醍摩豆智慧教育研究院 (Global TEAM Model Education Research Institute，簡稱GTERI) 所提供，匯流來自智慧教室錄製的蘇格拉底課例，便於研究醍摩豆智慧教育，以建立全球性的智慧教育研究平台與教學大數據研究中心">
    <meta name="keywords" content="教师专业发展,公开授课,智慧教育,教育大数据,醍摩豆">
    <meta name="description" content="苏格拉底平台是由全球醍摩豆智慧教育研究院(Global TEAM Model Education Research Institute，简称GTERI) 所提供，汇流来自智慧教室录制的苏格拉底课例，便于研究醍摩豆智慧教育，以建立全球性的智慧教育研究平台与教学大数据研究中心">
    <meta name="keywords" content="Teacher Professional Development,lesson study,open classroom observation,smart education,TEAM Model">

    <title>{{ config('app.name', 'Sokradeo') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/base.css'.'?ver='.config('app.version')) }}" rel="stylesheet">
    @isset($module)
    	<link href="{{ asset('assets/'.$module.'/css/app.css'.'?ver='.config('app.version')) }}" rel="stylesheet">
    @endisset
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
		@yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/manifest.js'.'?ver='.config('app.version')) }}"></script>
    <script src="{{ asset('js/vendor.js'  .'?ver='.config('app.version')) }}"></script>
    <script src="{{ asset('js/base.js'    .'?ver='.config('app.version')) }}"></script>

    @isset($imports['scripts'])
        @foreach ($imports['scripts'] as $v)
        	<script src="{{ asset($v.'?ver='.config('app.version')) }}"></script>
        @endforeach
    @endisset

    @isset($module)
    	<script src="{{ asset('assets/'.$module.'/js/app.js.'?ver='.config('app.version')') }}"></script>
    @endisset

    @yield('supplement')

</body>

@isset($imports['htmls'])
    @foreach ($imports['htmls'] as $v)
		@include($v)
    @endforeach
@endisset

</html>
