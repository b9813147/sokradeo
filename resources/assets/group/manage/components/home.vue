<template>
    <!--<p>Home</p>-->
    <label>影片審核
        <i-switch v-model="switchContent" @on-change="change"></i-switch>
    </label>


</template>

<script>
    import Vuex      from 'vuex'
    import CpntMixin from './mixin'

    export default {
        mixins: [CpntMixin],
        data() {
            return {
                switchContent: false,
                test: ''
            }
        },
        computed: _.merge(
            Vuex.mapState(['groupId']),
        ),
        methods : {
            change(status) {
                this.$Message.info('开关状态：' + status);
                let groupId       = this.groupId;
                let review_status = (this.switchContent)
                    ? 1
                    : 0;

                this.setReviewStatus(groupId, review_status)
            },

            setReviewStatus(groupId, review_status) {
                let url = '/management/group/set-review-status';
                axios.get(url, {
                    params: {
                        review_status: review_status,
                        id           : groupId
                    }
                }).then((response) => {
                    console.log(response);
                }).catch((error) => {
                    console.log(error);
                })
            },
            getGroup() {
                let _this = this;
                let url   = '/management/group/get-group';

                axios.get(url, {
                    params: {
                        groupId: this.groupId
                    }
                })
                    .then((response) => {
                        let _this     = this;
                        _this.switchContent = (response.data.data.review_status == 1) ? true : false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.getGroup();
        }

    }
</script>

<style scoped>

</style>
