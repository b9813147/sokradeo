export default {

    methods: {

        getApi (cate, act) {

            let baseApi = ''
            switch (cate) {
                case 'GroupManage':
                    baseApi = '/group/' + this.groupId + '/manage/'
                    break
                case 'GroupManageChannel':
                    baseApi = '/group/' + this.groupId + '/manage/channel/' + this.channel.id + '/'
                    break
                case 'GroupWatchChannel':
                    baseApi = '/group/' + this.groupId + '/watch/channel/' + this.channel.id + '/'
                    break
            }
            return baseApi + act
        }

    }

}
