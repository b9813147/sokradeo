import Vue     from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const messages = {
	
	en: {
		
		app: {
			
			fields: {
				email      : 'E-mail',
				name       : 'name',
				created_at : 'created at',
        		updated_at : 'updated at',
        		operators  : 'operator',
			},
			
		}
		
	},
	
	tw: {

		app: {
			
			fields: {
				email      : '電子信箱',
				name       : '姓名',
				created_at : '新增日期',
        		updated_at : '修改日期',
        		operators  : '操作',
			},
			
		}
		
	},
	
	cn: {

		app: {
			
			fields: {
				email      : '电子信箱',
				name       : '姓名',
    			created_at : '新增日期',
        		updated_at : '修改日期',
        		operators  : '操作',
			},
			
		}
		
	},
	
}

export default new VueI18n({
	
	locale: document.documentElement.lang,
	
	messages,
	
})
