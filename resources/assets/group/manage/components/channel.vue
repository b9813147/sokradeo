<i18n>
{
    "en": {
        "id"         : "id",
        "title"      : "channel content management",
        "name"       : "name",
        "description": "description",
        "status"     : "status",
        "operator"   : "operator",
        "invalid"    : "invalid",
        "valid"      : "valid",
        "pending"    : "pending",
        "share"      : "share",
        "editModal"  : {
            "title": "content editing"
        }
    },

    "tw": {
        "id"         : "序",
        "title"      : "頻道內容管理",
        "name"       : "名稱",
        "description": "描述",
        "status"     : "狀態",
        "operator"   : "操作",
        "invalid"    : "無效不顯示",
        "valid"      : "頻道內觀摩",
        "pending"    : "尚待審核中",
        "share"      : "全平台分享",
        "editModal"  : {
            "title": "內容編輯"
        }
    },

    "cn": {
        "id"         : "序",
        "title"      : "频道内容管理",
        "name"       : "名称",
        "description": "描述",
        "status"     : "状态",
        "operator"   : "操作",
        "invalid"    : "无效不显示",
        "valid"      : "频道内观摩",
        "pending"    : "尚待审核中",
        "share"      : "全平台分享",
        "editModal"  : {
            "title": "内容编辑"
        }
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

    <!-- edit -->
    <Modal v-model="modal.edit" v-bind:title="$t('editModal.title')" v-on:on-visible-change="getContent()" v-on:on-ok="setContent">
        <Form ref="editForm" v-bind:model="contentInfo" v-bind:label-width="80">
            <FormItem v-bind:label="$t('name')">
                <p>{{contentInfo.name}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('description')">
                <p>{{contentInfo.description}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('status')">
                <Select v-model="contentInfo.content_status">
                    <Option v-bind:value="1">{{ $t('invalid') }}</Option>
                    <Option v-bind:value="2">{{ $t('valid')   }}</Option>
                    <Option v-bind:value="3">{{ $t('share') }}</Option>
                    <Option v-bind:value="4">{{ $t('pending') }}</Option>
                </Select>
            </FormItem>
        </Form>
    </Modal>
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
                    {key: 'id',             title: this.$i18n.t('id')         },
                    {key: 'name',           title: this.$i18n.t('name')       },
                    {key: 'description',    title: this.$i18n.t('description')},
                    {key: 'status',          title: this.$i18n.t('status')     },
                    {key: 'operators',      title: this.$i18n.t('operator'), render: (h, params) => {
                        return h('div', [
            				h('Button', {
                                on: {
                                    click: () => {
                                        this.modal.edit = true
                                        this.getContent(params.row.id)
                                    }
                                },
            				}, [h('Icon', {props: {type: 'edit'}})]),
                            h('Button', {
                                on: {
                                    click: () => {
                                        // window.open(this.getApi('GroupWatchChannel', this.channel.cmsType.toLowerCase()) + '?contentId=' + params.row.id)
                                      let url =`/exhibition/${this.channel.cmsType.toLowerCase()}#/content/${params.row.id}?groupIds=${this.groupId}&channelId=${this.channel.id}`;
                                      window.open(url);
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
            modal: {
                edit: false,
            },
            contentInfo: {
                id            : null,
                name          : '',
                description   : '',
                content_status: 1,
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

            axios.get(this.getApi('GroupManageChannel', 'contents'), {
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

        getContent (contentId = null) {

            this.contentInfo = {
                id            : null,
                name          : '',
                description   : '',
                content_status: 1,
            }

            if(contentId === null) {
                return
            }

            axios.get(this.getApi('GroupManageChannel', 'get-content'), {
                params: {
                    contentId: contentId
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.contentInfo = data.data;
            })
            .catch((e) => {
                console.log(e)
            })

        },

        setContent () {

            let content = {
                contentId : this.contentInfo.id,
            }
            content = _.assign(content, this.contentInfo)

            axios.put(this.getApi('GroupManageChannel', 'set-content'), content)
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.contentInfo = data.data
                this.getPagination()
            })
            .catch((e) => {
                console.log(e)
            })

        },

        createContent () {

            let content = {
                contentId : this.contentInfo.id,
            }
            content = _.assign(content, this.contentInfo)

            axios.post(this.getApi('GroupManageChannel', 'create-content'), content)
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.getPagination()
            })
            .catch((e) => {
                console.log(e)
            })

        }

    },

    mounted () {

        this.channel = this.$route.params

        this.init()

    }

}
</script>

<style scoped>

</style>
