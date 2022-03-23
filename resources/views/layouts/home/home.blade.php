@extends('app.layouts.common')

@inject('publicUrlPres', 'App\Presenters\App\Url\PublicPresenter')

@section('content')
<layout>
    <layout>
    	<i-content>
    		<card v-bind:bordered="false" style="background-color: #000;">
            	<div style="text-align: center">
                	<img src="{{$publicUrlPres->image('app', 'teammodel/original-black-small.png', true)}}" style="width: 200px;">
                	<br/><br/>
                </div>
            </card>
            <article class="main">
				@yield('app-content')
			</article>
    	</i-content>
    </layout>

    @include('app.footers.common')
</layout>
@endsection
