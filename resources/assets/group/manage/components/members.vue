<i18n>
{
    "en": {
        "title"      : "member management",
        "role"       : "role",
        "duty"       : "duty",
        "email"      : "E-mail",
        "habook"     : "HabookID",
        "name"       : "name",
        "status"     : "status",
        "operator"   : "operator",
        "invalid"    : "invalid",
        "valid"      : "valid",
        "pending"    : "pending",
        "memberModal": {
            "title": "member editing"
        }
    },

    "tw": {
        "title"      : "成員管理",
        "role"       : "角色",
        "duty"       : "職務",
        "email"      : "電子信箱",
        "habook"     : "HabookID",
        "name"       : "名稱",
        "status"     : "狀態",
        "operator"   : "操作",
        "invalid"    : "無效",
        "valid"      : "有效",
        "pending"    : "待審核",
        "memberModal": {
            "title": "成員編輯"
        }
    },

    "cn": {
        "title"      : "成员管理",
        "role"       : "角色",
        "duty"       : "职务",
        "email"      : "电子信箱",
        "habook"     : "HabookID",
        "name"       : "名称",
        "status"     : "状态",
        "operator"   : "操作",
        "invalid"    : "无效",
        "valid"      : "有效",
        "pending"    : "待审核",
        "memberModal": {
            "title": "成员编辑"
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
    <p>
        <Button @click="modal.create=true">Add</Button>
    </p>
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

    <!-- create member to group -->
    <!--<Modal v-model="modal.create" draggable scrollable title="create member to group" v-on:on-ok="createMemberToGroup">-->
    <Modal v-model="modal.create" draggable scrollable title="create member to group" >
        <Form  label-position="left" :label-width="100">
            <FormItem label="TeamModelID">
                <Input v-model="teamModelId"></Input>
                <br>
                <Alert v-if="isTeamModelID === 3" type="error" show-icon>
                    帳號不存在
                </Alert>
                <Alert v-if="isTeamModelID === 2" type="warning" show-icon>
                    使用者已是頻道成員
                </Alert>
                <Alert v-if="isTeamModelID === 1" type="success" show-icon>
                    此帳號可加入
                </Alert>

            </FormItem>
        </Form>
        <div slot="footer">
            <Button  size="large" @click="modal.create=false">取消</Button>
            <Button  type="primary" v-if="isTeamModelID === 1"  @click="createMemberToGroup">確定</Button>
            <!--<Button  size="large" v-if="isTeamModelID = true" >確定</Button>-->

        </div>
    </Modal>
    <!-- member -->
    <Modal v-model="modal.member" v-bind:title="$t('memberModal.title')" v-on:on-visible-change="getMember()" v-on:on-ok="setMember">
        <Form ref="memberForm" v-bind:model="memberInfo" v-bind:label-width="80">
            <FormItem v-bind:label="$t('habook')">
                <p class="form-control">{{memberInfo.habook}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('name')">
                <p class="form-control">{{memberInfo.name}}</p>
            </FormItem>
            <FormItem v-bind:label="$t('status')">
                <Select v-model="memberInfo.member_status">
                    <Option v-bind:value="0">{{ $t('invalid') }}</Option>
                    <Option v-bind:value="1">{{ $t('valid')   }}</Option>
                    <Option v-bind:value="2">{{ $t('pending') }}</Option>
                </Select>
            </FormItem>
            <FormItem v-bind:label="$t('duty')">
                <Select v-model="memberInfo.member_duty">
                    <Option v-for="v in duties" v-bind:value="v.value" v-bind:key="v.type">{{ v.text }}</Option>
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
            teamModelId: '',
            isTeamModelID: 0,
            fun : null,
            list: {
                fields: [
                    {key: 'habook',       title: this.$i18n.t('habook')},
                    {key: 'name',        title: this.$i18n.t('name') },
                    {key: 'member_duty', title: this.$i18n.t('duty') },
                    {key: 'operators',   title: this.$i18n.t('operator'), render: (h, params) => {
                        return h('div', [
            				h('Button', {
                                on: {
                                    click: () => {
                                        this.modal.member = true
                                        this.getMember(params.row.id)
                                    }
                                },
            				}, [h('Icon', {props: {type: 'eye'}})])
    					])
            		}},
                ],
                items: [],
                total: 0,
            },
            filter: {
                role: {
                    selected: [],
                },
                duty: {
                    selected: [],
                },
            },
            pager: {
                per    : 15,
                total  : 1,
                current: 1,
                prev   : 1,
                next   : 1,
            },
            modal: {
                member: false,
                create:false,
            },
            memberInfo: {
                id           : null,
                email        : '',
                habook       : '',
                name         : '',
                member_status: 0,
                member_duty  : null,
            },
        }
    },

    computed: _.merge(
        Vuex.mapState(['groupId', 'roles', 'duties']),
        Vuex.mapGetters([]),
    ),

    watch: {

        '$route' (v) {

            this.fun = v.params.fun
            this.getPagination()

        },
      /* 這需要加一隻API驗證teamModelID 存不存在的 */
      teamModelId() {

        this.isMember();

      },

    },

    methods: {

        init () {

            this.getPagination()

        },

        getPagination (page = 1) {

            let conds = {} // 待加入過濾條件
            switch(this.fun) {
                case 'valid':
                    conds.member_status = 1
                    break
                case 'applied':
                    conds.member_status = 2
                    break
                default:
                    return
            }

            axios.get(this.getApi('GroupManage', 'members'), {
                params: {
                    member_status: conds.member_status,
                    page         : page,
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

        getMember (userId = null) {

            this.memberInfo = {
                id           : null,
                email        : '',
                habook       : '',
                name         : '',
                member_status: 0,
                member_duty  : null,
            }

            if(userId === null) {
                return
            }

            axios.get(this.getApi('GroupManage', 'get-member'), {
                params: {
                    userId: userId
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.memberInfo = data.data
            })
            .catch((e) => {
                console.log(e)
            })

        },

        setMember () {

            let member = {
                userId : this.memberInfo.id,
            }
            member = _.assign(member, this.memberInfo)

            axios.put(this.getApi('GroupManage', 'set-member'), member)
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.memberInfo = data.data
                this.getPagination()
            })
            .catch((e) => {
                console.log(e)
            })

        },

        createMember (e) {

            e.preventDefault()

            let member = {
                userId : this.memberInfo.id,
            }
            member = _.assign(member, this.memberInfo)

            axios.post(this.getApi('GroupManage', 'create-member'), member)
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

        },
        /* 這需要增加一增 API 來提梗可以新增member 到當前的group*/
      createMemberToGroup() {
        let groupId     = this.groupId;
        let teamModelId = this.teamModelId;

        axios.post('/management/group/joinMemberGroup', {
            groupId: groupId,
            teamModelId: teamModelId,
        }).then((response) => {
          if (response.data.data === true){
            this.modal.create = false;
            this.getPagination();
          }

        }).catch((e) => {
            console.log(e)
        });

      },
      // 驗證teamModelID 存不存在的
      isMember() {
        let _this       = this;
        let groupId     = this.groupId;
        let teamModelId = this.teamModelId;
        // console.log(groupId, teamModelId);
        axios.get('/management/group/isMember', {
          params: {
            groupId: groupId,
            teamModelId: teamModelId,
          },
        }).then((response) => {
          _this.isTeamModelID = '';

          if (response.data.data.isTeamModelId === true && response.data.data.isGroupUser === false) {
            //可加入
            _this.isTeamModelID = 1;

            // console.log(_this.isTeamModelID);
            // console.log(response.data.data);
          }
          else if (response.data.data.isTeamModelId === true && response.data.data.isGroupUser === true) {
            //已經在此群組
            _this.isTeamModelID = 2;
            // console.log(_this.isTeamModelID);
            // console.log(response.data.data);

          }
          else {
            //teamModelId不存在
            _this.isTeamModelID = 3;
            // console.log(_this.isTeamModelID);
            // console.log(response.data.data);
          }

        }).catch((e) => {
          console.log(e);
        });
      },

    },

    mounted () {

        this.fun = this.$route.params.fun

        this.init()

    }

}
</script>

<style scoped>

</style>
