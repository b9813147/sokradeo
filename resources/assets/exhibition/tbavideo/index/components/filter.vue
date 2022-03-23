<template>
    <article style="line-height: 40px; background-color: #333;">
        <dl class="filter">
            <dt><span>{{ $t('filters.eduStage') }}</span></dt>
            <dd>|
                <RadioGroup type="button" v-bind:value="filter.eduStages.selected" v-on:on-change="selectEduStages">
                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>
                    <Radio v-for="v in filter.eduStages.list" v-bind:key="v.type" v-bind:label="v.value"><span>{{v.text}}</span>
                    </Radio>
                </RadioGroup>
            </dd>
        </dl>
        <dl class="filter">
            <dt><span>{{ $t('filters.grade') }}</span></dt>
            <dd>|
                <RadioGroup type="button" v-bind:value="filter.grades.selected" v-on:on-change="selectGrades">
                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>
                    <Radio v-for="v in filter.grades.list" v-bind:key="v.type" v-bind:label="v.value">
                        <span>{{v.text}}</span></Radio>
                </RadioGroup>
            </dd>
        </dl>
        <dl class="filter">
            <dt><span>{{ $t('filters.subjectField') }}</span></dt>
            <dd>|
                <RadioGroup type="button" v-bind:value="filter.subjectFields.selected"
                            v-on:on-change="selectSubjectFields">
                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>
                    <Radio v-for="v in filter.subjectFields.list" v-bind:key="v.type" v-bind:label="v.value"><span>{{v.text}}</span>
                    </Radio>
                </RadioGroup>
            </dd>
        </dl>
<!--        <dl class="filter">-->
<!--            <dt><span>{{ $t('filters.lectureType') }}</span></dt>-->
<!--            <dd>|-->
<!--                <RadioGroup type="button" v-bind:value="filter.lectureTypes.selected"-->
<!--                            v-on:on-change="selectLectureTypes">-->
<!--                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>-->
<!--                    <Radio v-for="v in filter.lectureTypes.list" v-bind:key="v.type" v-bind:label="v.value"><span>{{v.text}}</span>-->
<!--                    </Radio>-->
<!--                </RadioGroup>-->
<!--            </dd>-->
<!--        </dl>-->
<!--        <dl class="filter">-->
<!--            <dt><span>{{ $t('filters.tbaFeature') }}</span></dt>-->
<!--            <dd>|-->
<!--                <RadioGroup type="button" v-bind:value="filter.tbaFeatures.selected" v-on:on-change="selectTbaFeatures">-->
<!--                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>-->
<!--                    <Radio v-for="v in filter.tbaFeatures.list" v-bind:key="v.type" v-bind:label="v.value"><span>{{v.text}}</span>-->
<!--                    </Radio>-->
<!--                </RadioGroup>-->
<!--            </dd>-->
<!--        </dl>-->
        <dl class="filter">
            <dt><span>{{ $t('filters.year') }}</span></dt>
            <dd>|
                <RadioGroup type="button" v-bind:value="filter.years.selected" v-on:on-change="selectYears">
                    <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>
                    <Radio v-for="v in filter.years.list" v-bind:key="v.type" v-bind:label="v.value">
                        <span>{{v.text}}</span></Radio>
                </RadioGroup>
            </dd>
        </dl>
    </article>
</template>

<script>
    import Vuex from 'vuex'

    export default {

        data() {
            return {}
        },

        computed: _.merge(
            Vuex.mapState(['filter']),
        ),

        methods: _.merge(
            Vuex.mapActions(['selectFilter']),

            {
                selectEduStages(v) {
                    this.selectFilter({
                        type: 'eduStages',
                        value: v
                    })
                },
                selectGrades(v) {
                    this.selectFilter({
                        type: 'grades',
                        value: v
                    })
                },
                selectSubjectFields(v) {
                    this.selectFilter({
                        type: 'subjectFields',
                        value: v
                    })
                },
                selectLectureTypes(v) {
                    this.selectFilter({
                        type: 'lectureTypes',
                        value: v
                    })
                },
                selectTbaFeatures(v) {
                    this.selectFilter({
                        type: 'tbaFeatures',
                        value: v
                    })
                },
                selectYears(v) {
                    this.selectFilter({
                        type: 'years',
                        value: v
                    })
                },
            }
        ),

        mounted() {

        }

    }
</script>

<style lang="scss" scoped>
    dl {
        display   : table;
        color     : #fff;
        font-size : 1.2em;

        dt,
        dd {
            display : table-cell;
        }

        dt {
            width      : 180px;
            text-align : right;

            span {
                padding-right : 15px;
            }
        }

        dd a {
            display : inline-block;
            padding : 0px 6px;
            color   : #fff;
        }

        dd a.active {
            color            : #00d9d9;
            background-color : #000;
            border-radius    : 5px;
        }
    }

    .filter {
        margin : 16px 0px;

        .ivu-radio-group-item {
            margin           : 0 10px;
            color            : #fff;
            background-color : transparent;
            border-color     : transparent;
            border-radius    : 4px;
        }

        .ivu-radio-group-item.ivu-radio-wrapper-checked {
            color            : #32C2F2;
            background-color : #000;
        }

        .ivu-radio-group-item::before {
            background : transparent;
        }
    }
</style>
