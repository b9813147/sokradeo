<script>
export default {

    methods: {

        ezStationOpts () {
            return {
                hiddens : ['starMark', 'hardMark'],
                handlers: {
                    markClick: function (player, type, time) {
                        var id = Math.floor(Date.now() / 1000);
                        player.ezStation.createMark({id: id, type: type, time: time});
                    },
                },
            }
        },

    }

}
</script>
