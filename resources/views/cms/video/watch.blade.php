@extends('app.layouts.common')

@section('content')
<section class="container-player">
    <video id="player" class="video-js vjs-default-skin vjs-big-play-centered"  
    	width="640" height="480" controls preload="auto" 
    	poster="{{$exeInfo['thumbnail']}}"
    	data-setup='{"fluid": true, "language": "{{app()->getLocale()}}"}'>
    	@foreach ($exeInfo['list'] as $v)
        	<source src="{{$v['url']}}" type="{{$v['mime']}}" label="{{$v['label']}}">
        @endforeach
    </video>
</section>
@endsection
