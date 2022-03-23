@extends('app.layouts.common')

@section('content')
<layout>
    <i-header>
    	Header
    </i-header>
    <layout>
    	<sider hide-trigger>
    		@yield('app-sider')
    	</sider>
    	<i-content>
			@yield('app-content')
    	</i-content>
    </layout>
    
    @include('app.footers.common')
</layout>
@endsection
