@extends('layouts.management.manage')

@section('app-content')
<section class="tools">
    <span class="title">模組管理</span>
    <div  class="items">
		
    </div>
</section>
<section class="list">
	<i-table stripe border v-bind:columns="list.fields" v-bind:data="list.items"></i-table>
</section>
<section class="pager">
	<page 
    	v-bind:total="list.total"
    	v-bind:page-size="pager.per"
    	v-bind:current="pager.current"
    	v-on:on-change="getList">
    </page>
</section>
@endsection
