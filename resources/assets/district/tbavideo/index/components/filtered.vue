<template>
    <article>
        <section class="channel-info" v-if="channel !== null">
            <h1 style="color: #fff;">{{channel.name}}</h1>
            <p style="color: #999; font-size: 1.5em">{{channel.description}}</p>
        </section>
        <section class="tools">
            <span style="font-size: 1.25em; padding-right: 20px">{{ $t('sorting') }} :</span>
            <RadioGroup v-model="tools.sort.selected" v-on:on-change="getPagination()">
                <Radio label="hits">{{ $t('sorts.hits') }}</Radio>
                <Radio label="added-time">{{ $t('sorts.addedTime') }}</Radio>
                <Radio label="tba-tech-interact-idx">{{ $t('sorts.tbaTechInteractIdx') }}</Radio>
                <Radio label="tba-method-anal">{{ $t('sorts.tbaMethodAnal') }}</Radio>
            </RadioGroup>
        </section>
        <section class="list">
            <Row>
                <Col v-bind:xs="24" v-bind:sm="12" v-bind:md="8" v-bind:lg="6" v-for="(v, i) in list.items"
                     v-bind:key="i">
                    <cpnt-thumb-content v-bind:item="v" v-on:execute="exeContent"></cpnt-thumb-content>
                </Col>
            </Row>
        </section>
        <section class="pager">
            <Page size="small"
                  v-bind:total="list.total"
                  v-bind:page-size="pager.per"
                  v-bind:current="pager.current"
                  v-on:on-change="getPagination">
            </Page>
        </section>
    </article>
</template>

<script>
    import _                from 'lodash'
    import Vuex             from 'vuex'
    import CpntThumbContent from '../../../app/components/thumb-content.vue'

    export default {

        components: {
            'cpnt-thumb-content': CpntThumbContent,
        },

        data() {
            return {
                list : {
                    items: [],
                    total: 0,
                },
                pager: {
                    per    : 12,
                    total  : 1,
                    current: 1,
                    prev   : 1,
                    next   : 1,
                },
                tools: {
                    sort: {
                        selected: 'hits',
                    },
                },
            }
        },

        computed: _.merge(
            Vuex.mapState(['path', 'searchTime', 'keyword', 'channel', 'filter']),
            Vuex.mapGetters(['logined']),
        ),

        watch: {

            searchTime(v) {
                this.getPagination()
            },

        },

        methods: _.merge(
            Vuex.mapActions(['initFilterSelected']),

            {

                init() {

                    this.getPagination()

                },

                getPagination(page = 1) {

                    let year        = null;
                    let conds       = {};
                    let tbaFeatures = [];
                    if (this.filter.years.selected !== 'none') {
                        year = this.filter.years.selected
                    }
                    if (this.filter.eduStages.selected !== 'none') {
                        conds.educational_stage_id = this.filter.eduStages.selected
                    }
                    if (this.filter.grades.selected !== 'none') {
                        conds.grade = this.filter.grades.selected
                    }
                    if (this.filter.subjectFields.selected !== 'none') {
                        conds.subject_field_id = this.filter.subjectFields.selected
                    }
                    if (this.filter.lectureTypes.selected !== 'none') {
                        conds.lecture_type = this.filter.lectureTypes.selected
                    }
                    if (this.filter.tbaFeatures.selected !== 'none') {
                        tbaFeatures.push({tba_features: this.filter.tbaFeatures.selected})
                    }

                    axios.post('/exhibition/tbavideo/filters', {
                        page       : page,
                        perPage    : this.pager.per,
                        order      : {
                            type: _.upperFirst(_.camelCase(this.tools.sort.selected)),
                            dir : 'desc'
                        },
                        conds      : conds,
                        tbaFeatures: tbaFeatures,
                        year       : year,
                        search     : this.keyword,
                        channelId  : (this.channel === null)
                            ? null
                            : this.channel.id,
                        abbr:this.$store.state.abbr
                    })
                        .then((data) => {
                            data = data.data;
                            if (!data.status) {
                                return
                            }
                            data = data.data;

                            this.list.items = data.data;
                            this.list.total = data.total;
                            this.pager      = {
                                per    : data.per_page,
                                total  : data.last_page,
                                current: data.current_page,
                                prev   : data.current_page == 1
                                    ? 1
                                    : data.current_page - 1,
                                next   : data.current_page == data.last_page
                                    ? data.last_page
                                    : data.current_page + 1,
                            }

                        })
                        .catch((e) => {
                            console.log(e)
                        })

                },

                exeContent(content) {
                    this.$emit('check-logined');
                    if (!this.logined) {
                        return
                    }

                    let groupIds  = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',');
                    let channelId = _.join(_.uniq(_.map(content.group_channels, 'id')), ',');
                    this.$router.push({
                        name  : 'content',
                        params: {contentId: content.id},
                        query : {
                            groupIds : groupIds,
                            channelId: channelId
                        },
                    })
                    //let groupIds = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',')
                    //window.open('tbavideo/watch?contentId=' + content.id + '&groupIds=' + groupIds)
                },

            }
        ),

        mounted() {

            this.initFilterSelected();
            this.init();

        }

    }
</script>

<style lang="scss" scoped>
    .ivu-radio-group {
        font-size : 1.25em;
    }

    .channel-info {
        padding-top    : 20px;
        padding-bottom : 32px;
    }

    .tools {
        color : #fff;
    }

    .pager {
        padding    : 20px 0px;
        text-align : center;
    }
</style>
