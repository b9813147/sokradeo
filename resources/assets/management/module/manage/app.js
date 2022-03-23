import _     from 'lodash'
import axios from 'axios'
import Vue   from 'vue'
import iView from 'iview'
import i18n  from './lang/index'

Vue.use(iView)

new Vue({

    el: '#app',

    i18n,

    data () {
        return {
            list: {
        		fields: [
                    {key: 'cate',      title: i18n.t('app.fields.cate')  },
        			{key: 'app',       title: i18n.t('app.fields.app')   },
        			{key: 'enable',    title: i18n.t('app.fields.enable')},
                    {key: 'operators', title: i18n.t('app.fields.operators'), render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                on: {
                                    click: () => {

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
        }
    },

    methods: {

        init: function () {

            this.getList()

        },

        getList: function () {

            axios.get('/management/module/list', {
                params: {}
            })
            .then((data) => {
                data = data.data
                if (! data.status) {
                    return
                }
                data = data.data
                this.list.items = []
                _.forIn(data, (apps, cate) => {
                    _.forIn(apps, (enable, app) => {
                        this.list.items.push({cate: cate, app: app, enable: enable})
                    })
                })
                this.list.total = this.list.items.length
            })
            .catch(function (e) {
                console.log(e)
            })

        }

    },

    mounted: function () {

        this.init()

    }

})
