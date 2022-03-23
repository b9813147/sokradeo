@extends('app.layouts.common')

@section('content')
<layout>
    <i-header>
    	Header
    </i-header>
    <i-content>
    	@yield('app-content')
    </i-content>
    
    @include('app.footers.common')
</layout>
@endsection

@section('supplement')
<iframe src="{{route('auth.habook.logout')}}" style="display: none"></iframe>
@endsection