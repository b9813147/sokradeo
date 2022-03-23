@extends('app.layouts.common')

@section('content')
<section>
	<cpnt-tbaplayer class="tbaplayer" id="tbaplayer" v-bind:options="tbaPlayer.options"></cpnt-tbaplayer>
</section>
@endsection
