export default {

    methods: {

        getApi (cate, act) {

            let baseApi = ''
            switch (cate) {
                case 'GroupMain':
                    baseApi = '/group/' + this.groupId + '/main/'
                    break
                case 'GroupMainChannel':
                    baseApi = '/group/' + this.groupId + '/main/channel/' + this.channel.id + '/'
                    break
                case 'GroupWatchChannel':
                    baseApi = '/group/' + this.groupId + '/watch/channel/' + this.channel.id + '/'
                    break
            }
            return baseApi + act
        }

    }

}
