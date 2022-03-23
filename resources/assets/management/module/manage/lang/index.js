import Vue     from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const messages = {

	en: {

		app: {

			fields: {
				cate     : 'cate',
				app      : 'app',
    			enable   : 'enable',
        		operators: 'operator',
			},

		}

	},

	tw: {

		app: {

			fields: {
				cate     : '分類',
				app      : '應用',
    			enable   : '啟用',
        		operators: '操作',
			},

		}

	},

	cn: {

		app: {

			fields: {
				cate     : '分类',
				app      : '应用',
    			enable   : '启用',
        		operators: '操作',
			},

		}

	},

}

export default new VueI18n({

	locale: document.documentElement.lang,

	messages,

})
