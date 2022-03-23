@extends('layouts.management.manage')

@section('app-content')
<section class="tools">
    <span class="title">角色管理</span>
    <div  class="items">
		<i-button v-on:click="modal.create=true"><icon type="plus"></icon></i-button>
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

<!-- create -->
<modal v-model="modal.create" title="角色新增" v-on:on-visible-change="getRole" v-on:on-ok="createRole">
	<i-form ref="createForm" v-bind:model="roleInfo" v-bind:label-width="80">
		<form-item label="類型">
            <i-input type="text" v-model="roleInfo.type" placeholder="請輸入..."></i-input>
        </form-item>
		<form-item label="名稱">
            <i-input type="text" v-model="roleInfo.name" placeholder="請輸入..."></i-input>
        </form-item>
        <form-item label="描述">
            <i-input type="text" v-model="roleInfo.description" placeholder="請輸入..."></i-input>
        </form-item>
	</i-form>
</modal>

<!-- edit -->
<modal v-model="modal.edit" title="角色編輯" v-on:on-visible-change="getRole" v-on:on-ok="">
	<i-form ref="createForm" v-bind:model="roleInfo" v-bind:label-width="80">
		<form-item label="類型">
			<p>@{{ roleInfo.type }}</p>
        </form-item>
		<form-item label="名稱">
			<p>@{{ roleInfo.name }}</p>
        </form-item>
        <form-item label="描述">
        	<p>@{{ roleInfo.description }}</p>
        </form-item>
        <form-item label="模組">
        	<p>待條列...</p>
        </form-item>
	</i-form>
</modal>
@endsection
