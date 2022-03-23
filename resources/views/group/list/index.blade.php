@extends('app.layouts.common')

@section('content')
<layout>
    <i-header>
    	Header
    </i-header>
    <layout>
    	<sider hide-trigger>
    		Sider
    	</sider>
    	<i-content>
			<section class="tools">
            	<span class="title">群組列表</span>
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
                	v-on:on-change="getPagination">
                </page>
            </section>
    	</i-content>
    </layout>
    
    @include('app.footers.common')
</layout>
@endsection
