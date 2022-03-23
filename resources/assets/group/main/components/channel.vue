<i18n>
{
    "en": {
        "title"      : "channel content",
        "name"       : "name",
        "description": "description",
        "status"     : "status",
        "operator"   : "operator"
    },

    "tw": {
        "title"      : "頻道內容",
        "name"       : "名稱",
        "description": "描述",
        "status"     : "狀態",
        "operator"   : "操作"
    },

    "cn": {
        "title"      : "频道内容",
        "name"       : "名称",
        "description": "描述",
        "status"     : "状态",
        "operator"   : "操作"
    }
}
</i18n>

<template>
<section>
    <section class="tools">
        <span class="title">{{ $t('title') }}</span>
        <div  class="items">

        </div>
    </section>
    <section class="filter">

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
</section>
</template>

<script>
import Vuex      from 'vuex'
import CpntMixin from './mixin'

export default {

    mixins: [CpntMixin],

    data () {
        return {
            channel: {
                id     : null,
                cmsType: null,
            },
            list: {
                fields: [
                    {key: 'name',           title: this.$i18n.t('name')       },
                    {key: 'description',    title: this.$i18n.t('description')},
                    {key: 'content_status', title: this.$i18n.t('status')     },
                    {key: 'operators',      title: this.$i18n.t('operator'), render: (h, params) => {
                        return h('div', [
            				h('Button', {
                                on: {
                                    click: () => {

                                      // let url =`/exhibition/${this.channel.cmsType.toLowerCase()}#/content/${params.row.id}?groupIds=${this.groupId}`;
                                      // window.open(url);
                                      //
                                        window.open(this.getApi('GroupWatchChannel', this.channel.cmsType.toLowerCase()) + '?contentId=' + params.row.id)
                                    }
                                },
            				}, [h('Icon', {props: {type: 'eye'}})]),
    					])
            		}},
                ],
                items: [],
                total: 0,
            },
            filter: {

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

    computed: _.merge(
        Vuex.mapState(['groupId']),
        Vuex.mapGetters([]),
    ),

    watch: {

        '$route' (v) {

            this.channel = v.params
            this.getPagination()

        },

    },

    methods: {

        init () {

            this.getPagination()

        },

        getPagination (page = 1) {

            let conds = {} // 待加入過濾條件

            axios.get(this.getApi('GroupMainChannel', 'contents'), {
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

        this.channel = this.$route.params

        this.init()

    }

}
</script>

<style scoped>

</style>
