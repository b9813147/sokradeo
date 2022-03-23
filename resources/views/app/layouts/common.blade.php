<!DOCTYPE html>
@inject('browserLang','App\Libraries\Lang\Lang')
@inject('meta', 'App\Libraries\Meta\Meta')
<html lang="{{ $browserLang->getBrowserLang() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="keywords" content="{{ $browserLang->getMetaKeywordsByLang($browserLang->getBrowserLang()) }}">
    <meta name="description" content="{{ $browserLang->getMetaDescByLang($browserLang->getBrowserLang()) }}">

    <title>{{ $meta->getCustomMetaTitle() }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/base.css') }}" rel="stylesheet">
    <link href="{{ mix('assets/app/css/common.css') }}" rel="stylesheet">
    @isset($module)
        <link href="{{ mix('assets/'.$module.'/css/app.css') }}" rel="stylesheet">
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
<script src="{{ mix('js/manifest.js' )}}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/base.js') }}"></script>
@isset($imports['scripts'])
    @foreach ($imports['scripts'] as $v)
        <script src="{{ mix($v) }}"></script>
    @endforeach
@endisset
@isset($module)
    <script src="{{ mix('assets/'.$module.'/js/app.js') }}"></script>
@endisset

@yield('supplement')
<script>
  // console.log(    Echo.channel('laravel_database_events')
  //   .listen('PostRepliedMessage', e => {
  // console.log(e.message)
  // })
  // )
  // Echo.channel('laravel_database_user-channel')
  //   .listen('.UserEvent', e => {
  //     console.log(e.message)
  //   })

  // Echo.channel('events')
  //   .listen('.PostRepliedMessage', e => {
  //     console.log(e.message)
  //   })
</script>
</body>

@isset($imports['htmls'])
    @foreach ($imports['htmls'] as $v)
        @include($v)
    @endforeach
@endisset

</html>
