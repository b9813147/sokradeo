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
                    {key: 'email',      title: i18n.t('app.fields.email')      },
                    {key: 'name',       title: i18n.t('app.fields.name')       },
        			{key: 'created_at', title: i18n.t('app.fields.created_at') },
        			{key: 'updated_at', title: i18n.t('app.fields.updated_at') },
        			{key: 'operators',  title: i18n.t('app.fields.operators'), render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                on: {
                                    click: () => {
                                        this.modal.edit = true
                                        this.getUser(params.row.id)
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
            userInfo: {
                id        : null,
                email     : '',
                name      : '',
                created_at: '',
                updated_at: '',
                roles     : [],
            },
        }
    },

    methods: {

        init: function () {

        	this.getPagination()

        },

        getPagination: function(page = 1) {

        	axios.get('/management/user/list', {
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

        getUser: function(userId = null) {

            this.userInfo = {
                id        : null,
                email     : '',
                name      : '',
                created_at: '',
                updated_at: '',
                roles     : [],
            }

            if(userId === null) {
                return
            }

            axios.get('/management/user/get-user', {
                params: {
                    userId: userId
                }
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.userInfo = data.data
            })
            .catch((e) => {
                console.log(e)
            })

        },

        setUser: function() {
            let user = _.clone(this.userInfo)
            user.roles = _.map(this.userInfo.roles, 'id')

            axios.put('/management/user/set-user',
                user
            )
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                this.userInfo = data.data
            })
            .catch((e) => {
                console.log(e)
            })

        }
    },

    mounted: function () {

        this.init()

    }

})
