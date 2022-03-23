<script>
export default {

    methods: {

        image (module, path, lang = false) {
            return '/images/' + module + '/' + (lang ? document.documentElement.lang+'/' : '') + path
        },

    }

}
</script>
