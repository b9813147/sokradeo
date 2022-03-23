@extends('layouts.home.home')

@section('app-content')
<h1 class="title">{{ __('home/about.title') }}</h1>

@foreach (__('home/about.contexts') as $ctx)
    <section class="paragraph">
    	{{ $ctx }}
    </section>
@endforeach

<br/>

<section class="paragraph" style="color: #fff; text-align: center;">
	<h3>{{ __('home/about.conclusion') }}</h3>
	<br/>
	<i>{{ __('home/about.signature') }}</i>
</section>
@endsection
