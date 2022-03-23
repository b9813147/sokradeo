<template>
    <Card class="card flat">
        <div style="text-align: center;">
            <img v-bind:src="image('app', 'teammodel/institute-original-black-big.png', true)" style="width: 128px;">
            <br/><br/>
            <p style="font-size: 20px">{{ $t('message.login') }}</p>
            <br/>
            <i-button style="width: 80px;" v-on:click="loginAsHabook">{{ $t('login') }}</i-button>
        </div>
    </Card>
</template>

<script>
    import HelperUrlPublic from '../../../app/helpers/url/public.vue'

    export default {

        mixins: [HelperUrlPublic],

        props: ['to'],

        data() {
            return {}
        },

        methods: {

            loginAsHabook() {
                window.location.href = '/auth/login/login-as-habook' + (this.to === null
                    ? ''
                    : '?to=' + this.to)
            },

        },

    }
</script>

<style lang="scss" scoped>

</style>
