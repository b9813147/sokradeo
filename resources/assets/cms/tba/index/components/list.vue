<template>
<article>
	<section class="tools">
		<span class="title">教學行為分析管理</span>
		<div  class="items">
			<Button><Icon type="plus"></Icon></Button>
		</div>
	</section>
	<section class="list">
		<Table stripe border v-bind:columns="list.fields" v-bind:data="list.items"></Table>
	</section>
	<section class="pager">
		<Page
	    	v-bind:total="list.total"
	    	v-bind:page-size="pager.per"
	    	v-bind:current="pager.current"
	    	v-on:on-change="getPagination">
	    </Page>
	</section>
</article>
</template>

<script>
export default {

	data () {
		return {
			list: {
				fields: [
					{key: 'name',        title: this.$i18n.t('list.fields.name')       },
					{key: 'description', title: this.$i18n.t('list.fields.description')},
					{key: 'thumbnail',   title: this.$i18n.t('list.fields.thumbnail')  },
					{key: 'created_at',  title: this.$i18n.t('list.fields.created_at') },
					{key: 'updated_at',  title: this.$i18n.t('list.fields.updated_at') },
					{key: 'operators',   title: this.$i18n.t('list.fields.operators'), render: (h, params) => {
						return h('div', [
							h('Button', {
								on: {click: () => {this.$router.push({name: 'edit', params: {id: params.row.id}})}},
							}, [h('Icon', {props: {type: 'edit'}})]),
							h('Button', {
								on: {click: () => {window.open('tba/watch?id=' + params.row.id)}},
							}, [h('Icon', {props: {type: 'eye'}})])
						])
					}},
				],
				items: [],
				total: 0,
			},
			pager: {
				per    : 15,
				total  : 1,
				current: 1,
				prev   : 1,
				next   : 1,
			},
		}
	},

	methods: {

		init () {

        	this.getPagination()

        },

        getPagination (page) {

        	axios.get('/cms/tba/list', {
                params: {
                	page: page,
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                data = data.data

                this.list.items = data.data
                this.list.total = data.total
                this.pager = {
            		per    : data.per_page,
            		total  : data.last_page,
            		current: data.current_page,
                    prev   : data.current_page == 1 ? 1 : data.current_page - 1,
                    next   : data.current_page == data.last_page ? data.last_page : data.current_page + 1,
                }

            })
            .catch((e) => {
                console.log(e)
            })

        },

	},

	mounted () {

        this.init()

    }

}
</script>

<style scoped>

</style>
