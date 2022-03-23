import Vue     from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const messages = {
	
	en: {
		
		app: {
			
			fields: {
				type       : 'type',
				name       : 'name',
				description: 'description',
    			created_at : 'created at',
        		updated_at : 'updated at',
        		operators  : 'operator',
			},
			
		}
		
	},
	
	tw: {

		app: {
			
			fields: {
				type       : '類型',
				name       : '名稱',
				description: '描述',
    			created_at : '新增日期',
        		updated_at : '修改日期',
        		operators  : '操作',
			},
			
		}
		
	},
	
	cn: {

		app: {
			
			fields: {
				type       : '类型',
				name       : '名称',
				description: '描述',
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
