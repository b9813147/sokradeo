import _     from 'lodash'
import axios from 'axios'
import Vue   from 'vue'
import iView from 'iview'
import i18n  from './lang/index'

Vue.use(iView)

new Vue({

    el: '#app',

    i18n,

    data: {
    	list: {
            fields: [
                {key: 'name',        title: i18n.t('group.name')       },
    			{key: 'description', title: i18n.t('group.description')},
    			{key: 'status',      title: i18n.t('group.status')     },
                {key: 'public',      title: i18n.t('group.public')     },
    			{key: 'created_at',  title: i18n.t('group.created_at') },
    			{key: 'updated_at',  title: i18n.t('group.updated_at') },
    			{key: 'operators',   title: i18n.t('base.operators'), render: (h, params) => {
                    return h('div', [
        				h('Button', {
                            on: {click: () => {window.open('/group/'+params.row.id+'/main')}},
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
    },

    methods: {

        init: function () {

        	this.getPagination()

        },

        getPagination: function(page) {

        	axios.get('/group/list/list', {
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

    mounted: function () {

        this.init()

    }

})
