<i18n>
{
    "en": {
        "act": {
			"update": {
				"suce": "update success",
				"fail": "update fail"
			}
		}
    },

    "tw": {
        "act": {
            "update": {
                "suce": "更新成功",
                "fail": "更新失敗"
            }
        }
    },

    "cn": {
        "act": {
            "update": {
                "suce": "更新成功",
                "fail": "更新失败"
            }
        }
    }
}
</i18n>

<script>
import Vuex from 'vuex'

export default {

    template: '<div style="display: none"></div>',

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            msgInfo: state => state.msgInfo,
        }),
    ),

    watch: {

        msgInfo (v) {
            this.$Message[v.type](this.$i18n.t(v.value))
        },
    },

}
</script>
