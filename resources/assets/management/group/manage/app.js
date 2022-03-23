import _         from 'lodash'
import axios     from 'axios'
import Vue       from 'vue'
import iView     from 'iview'
import VueSelect from 'vue-select'
import i18n      from './lang/index'

Vue.use(iView)
Vue.component('cpnt-select', VueSelect)

new Vue({

    el: '#app',

    i18n,

    data () {
        return {
        	list: {
        		fields: [
                    {key: 'name',        title: i18n.t('app.fields.name')       },
        			{key: 'description', title: i18n.t('app.fields.description')},
        			{key: 'status',      title: i18n.t('app.fields.status')     },
                    {key: 'public',      title: i18n.t('app.fields.public')     },
        			{key: 'created_at',  title: i18n.t('app.fields.created_at') },
        			{key: 'updated_at',  title: i18n.t('app.fields.updated_at') },
        			{key: 'operators',   title: i18n.t('app.fields.operators'), render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                on: {
                                    click: () => {
                                        this.modal.edit = true
                                        this.getGroup(params.row.id)
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
            userSelect: {
            	list: [],
            },
        	groupInfo: {
                id         : null,
                school_code: null,
                name       : '',
                description: '',
                status     : 0,
                public     : 0,
                created_at : '',
                updated_at : '',
                admins     : [],
            },
        }
    },

    methods: {

        init: function () {

        	this.getPagination()

        },

        getPagination: function(page = 1) {

            axios.get('/management/group/list', {
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

        getGroup: function(groupId = null) {

            this.groupInfo = {
        		id         : null,
                school_code: null,
                name       : '',
                description: '',
                public     : 0,
                status     : 0,
                created_at : '',
                updated_at : '',
                admins     : [],
            }

            if(groupId === null) {
                return
            }

            axios.get('/management/group/get-group', {
                params: {
                	groupId: groupId
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                data = data.data
                data.admins = data.users
                delete data.users
                this.groupInfo = data
            })
            .catch((e) => {
                console.log(e)
            })

        },

        setGroup: function() {
            let group = _.clone(this.groupInfo)
            group.admins = _.map(this.groupInfo.admins, 'id')

            axios.put('/management/group/set-group',
                group
            )
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                data = data.data
                data.admins = data.users
                delete data.users
                this.groupInfo = data
            })
            .catch((e) => {
                console.log(e)
            })

        },

        createGroup: function() {
            let group = _.clone(this.groupInfo)
            group.admins = _.map(this.groupInfo.admins, 'id')

        	axios.post('/management/group/create-group',
                group
            )
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.getPagination();
            })
            .catch((e) => {
                console.log(e)
            })

        },

        searchUsers: function(search, loading) {

        	loading(true)
        	axios.get('/management/group/search-users', {
                params: {name: search}
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.userSelect.list = data.data
                loading(false)
            })
            .catch((e) => {
            	loading(false)
                console.log(e)
            })
        },

    },

    mounted: function () {

        this.init()

    }

})
