<i18n>
{
    "en": {
        "title"      : "channel management",
        "type"       : "type",
        "name"       : "name",
        "description": "description",
        "status"     : "status",
        "public"     : "public",
        "operator"   : "operator",
        "close"      : "close",
        "open"       : "open",
        "no"         : "no",
        "yes"        : "yes",
        "createModal": {
            "title": "channel creating"
        },
        "editModal"  : {
            "title": "channel editing"
        }
    },

    "tw": {
        "title"      : "頻道管理",
        "type"       : "類型",
        "name"       : "名稱",
        "description": "描述",
        "status"     : "狀態",
        "public"     : "公開",
        "operator"   : "操作",
        "close"      : "關閉",
        "open"       : "開啟",
        "no"         : "否",
        "yes"        : "是",
        "createModal": {
            "title": "頻道新增"
        },
        "editModal"  : {
            "title": "頻道編輯"
        }
    },

    "cn": {
        "title"      : "频道管理",
        "type"       : "类型",
        "name"       : "名称",
        "description": "描述",
        "status"     : "状态",
        "public"     : "公开",
        "operator"   : "操作",
        "close"      : "关闭",
        "open"       : "开启",
        "no"         : "否",
        "yes"        : "是",
        "createModal": {
            "title": "频道新增"
        },
        "editModal"  : {
            "title": "频道编辑"
        }
    }
}
</i18n>

<template>
<section>
    <section class="tools">
        <span class="title">{{ $t('title') }}</span>
        <div  class="items">
            <Button v-on:click="modal.create=true"><Icon type="plus"></Icon></Button>
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

    <!-- create -->
    <Modal v-model="modal.create" v-bind:title="$t('createModal.title')" v-on:on-visible-change="getChannel()" v-on:on-ok="createChannel">
        <Form ref="createForm" v-bind:model="channelInfo" v-bind:label-width="80">
            <FormItem v-bind:label="$t('type')">
                <Select v-model="channelInfo.cms_type">
                    <Option v-for="v in cmses" v-bind:value="v.value" v-bind:key="v.type">{{ v.text }}</Option>
                </Select>
            </FormItem>
            <FormItem v-bind:label="$t('name')">
                <Input v-model="channelInfo.name" placeholder="請輸入..."></Input>
            </FormItem>
            <FormItem v-bind:label="$t('description')">
                <Input v-model="channelInfo.description" placeholder="請輸入..."></Input>
            </FormItem>
            <FormItem v-bind:label="$t('status')">
                <Select v-model="channelInfo.status">
                    <Option v-bind:value="0">{{ $t('close') }}</Option>
                    <Option v-bind:value="1">{{ $t('open')  }}</Option>
                </Select>
            </FormItem>
            <FormItem v-bind:label="$t('public')">
                <Select v-model="channelInfo.public">
                    <Option v-bind:value="0">{{ $t('no')  }}</Option>
                    <Option v-bind:value="1">{{ $t('yes') }}</Option>
                </Select>
            </FormItem>
        </Form>
    </Modal>

	<!-- edit -->
    <Modal v-model="modal.edit" v-bind:title="$t('editModal.title')" v-on:on-visible-change="getChannel()" v-on:on-ok="setChannel">
        <Form ref="editForm" v-bind:model="channelInfo" v-bind:label-width="80">
            <FormItem v-bind:label="$t('type')">
                <p>{{channelInfo.cms_type}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('name')">
                <p>{{channelInfo.name}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('description')">
                <p>{{channelInfo.description}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('status')">
                <Select v-model="channelInfo.status">
                    <Option v-bind:value="0">{{ $t('close') }}</Option>
                    <Option v-bind:value="1">{{ $t('open')  }}</Option>
                </Select>
            </FormItem>
            <FormItem v-bind:label="$t('public')">
                <Select v-model="channelInfo.public">
                    <Option v-bind:value="0">{{ $t('no')  }}</Option>
                    <Option v-bind:value="1">{{ $t('yes') }}</Option>
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
            list: {
                fields: [
                    {key: 'cms_type',    title: this.$i18n.t('type')       },
                    {key: 'name',        title: this.$i18n.t('name')       },
                    {key: 'description', title: this.$i18n.t('description')},
                    {key: 'status',      title: this.$i18n.t('status')     },
                    {key: 'public',      title: this.$i18n.t('public')     },
                    {key: 'operators',   title: this.$i18n.t('operator'), render: (h, params) => {
                        return h('div', [
            				h('Button', {
                                on: {
                                    click: () => {
                                        this.modal.edit = true
                                        this.getChannel(params.row.id)
                                    }
                                },
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
            modal: {
                create: false,
                edit  : false,
            },
            channelInfo: {
				id         : null,
				group_id   : null,
                cms_type   : null,
                name       : '',
                description: '',
                status     : 0,
                public     : 0,
            },
        }
    },

    computed: _.merge(
        Vuex.mapState(['groupId', 'cmses']),
        Vuex.mapGetters([]),
    ),

    methods: {

        init () {

            this.getPagination()

        },

        getPagination (page = 1) {

            axios.get(this.getApi('GroupManage', 'channels'), {
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

        getChannel (channelId = null) {

            this.channelInfo = {
				id         : null,
				group_id   : null,
                cms_type   : null,
                name       : '',
                description: '',
                status     : 0,
                public     : 0,
            }

            if(channelId === null) {
                return
            }

            axios.get(this.getApi('GroupManage', 'get-channel'), {
                params: {
                    channelId: channelId
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.channelInfo = data.data
            })
            .catch((e) => {
                console.log(e)
            })

        },

        setChannel () {

            axios.put(this.getApi('GroupManage', 'set-channel'),
				this.channelInfo
			)
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.channelInfo = data.data
                this.getPagination()
            })
            .catch((e) => {
                console.log(e)
            })

        },

        createChannel () {

            axios.post(this.getApi('GroupManage', 'create-channel'),
				this.channelInfo
			)
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

        this.init()

    }

}
</script>

<style scoped>

</style>
