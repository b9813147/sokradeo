import Vue     from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const messages = {
	
	en: {
		
		app: {
			
			fields: {
				name       : 'name',
				description: 'description',
    			status     : 'status',
        		public     : 'public',
        		created_at : 'created at',
        		updated_at : 'updated at',
        		operators  : 'operator',
			},
			
		}
		
	},
	
	tw: {

		app: {
			
			fields: {
				name       : '名稱',
				description: '描述',
    			status     : '狀態',
        		public     : '公開',
        		created_at : '新增日期',
        		updated_at : '修改日期',
        		operators  : '操作',
			},
			
		}
		
	},
	
	cn: {

		app: {
			
			fields: {
				name       : '名称',
				description: '描述',
    			status     : '状态',
        		public     : '公开',
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
