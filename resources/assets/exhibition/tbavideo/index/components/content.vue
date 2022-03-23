<template>
  <article class="content">
    <section v-if="info === null" style="text-align: center;">
      <!--            <h2>{{$t('messages.no_content')}}</h2>-->
    </section>
    <section v-else>
      <Row v-bind:gutter="16">
        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8" class="thumb">
          <img
              style="width: 100%; height: 100%; object-fit: cover"
              :src="thumbnailUrl"
          />
        </Col>
        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
          <!-- Title -->
          <div>
            <h2>
              {{ info.tba.name }}
              <!-- Private status -->
              <Icon
                :title="$t('notReady')"
                type="ios-locked-outline"
                style="color:yellow;font-size: 25px;"
                v-if="isContentPrivate"
              ></Icon>
              <!-- My Fav -->
              <span @click="toggleFavVideo" class="favorite-container">
                <Icon type="heart" v-if="contentState.isMyFav === true" style="color: #ef7c8e"></Icon>
                <Icon type="ios-heart-outline" v-else></Icon>
              </span>
              <!-- Edit content info -->
              <Icon
                type="edit"
                class="icon-container"
                v-if="userState.isOwner"
                @click="displayContentEditor"
              ></Icon>
              <!-- Edit tba video map -->
              <Icon
                type="clock"
                class="icon-container"
                v-if="userState.isOwner && observation.btnDisplay"
                @click="tbaVideoTimePointModal = true"
              ></Icon>
              <!-- Edit tba video map modal -->
              <Modal
                class="tba-video-map-modal-container"
                v-model="tbaVideoTimePointModal"
                width="350"
              >
                <section slot="header">
                  <h4>{{ $t("tbaVideoTimePointModal.title") }}</h4>
                </section>

                <section>
                  <!-- Sub Title -->
                  <Row>
                    <Col span="24">
                      <p>{{ $t("tbaVideoTimePointModal.subTitle") }}</p>
                    </Col>
                  </Row>
                  <!-- Mode selection -->
                  <Row style="padding: 10px 0">
                    <Col span="24">
                      <RadioGroup v-model="tbaVideoTimePointData.curMode">
                        <Radio
                          v-for="mode in tbaVideoTimePointData.modes"
                          :key="mode.value"
                          :label="mode.value"
                        >
                          {{ mode.label }}
                          (<Icon :type="mode.icon"></Icon>)
                        </Radio>
                      </RadioGroup>
                    </Col>
                  </Row>
                  <!-- Value in seconds -->
                  <Row
                    type="flex"
                    justify="center"
                    align="middle"
                    style="padding: 10px 0"
                  >
                    <Col span="6" style="display: contents">
                      <InputNumber
                        :min="0"
                        :precision="0"
                        v-model="tbaVideoTimePointData.sec"
                      ></InputNumber>
                      <span style="padding-left: 10px">
                        {{ $t("tbaVideoTimePointModal.seconds") }}
                      </span>
                    </Col>
                  </Row>
                </section>

                <section slot="footer">
                  <Button
                    type="text"
                    @click="tbaVideoTimePointModal = false"
                  >
                    {{ $t("tbaVideoTimePointModal.cancel") }}
                  </Button>
                  <Button
                    type="primary"
                    :loading="isLoading"
                    :disabled="!updateTbaTimePointsAllowed"
                    @click="updateTbaTimePoints"
                  >
                    {{ $t("tbaVideoTimePointModal.submit") }}
                  </Button>
                </section>
              </Modal>
              <!-- Re-upload a video -->
              <Icon
                type="upload"
                class="icon-container"
                v-if="updateVideoStatus"
                @click="videoModal = true"
              ></Icon>
              <!-- Re-upload modal -->
              <Modal
                  v-model="videoModal"
                  title="Common Modal dialog box title"
                  @on-ok="updateVide"
                  @on-cancel="cancel">
                <Row class="editor-item" style="padding-top: 15px">
                  <Col span="4">
                    <span>{{ $t('editorVideoModal.video') }} : </span>
                  </Col>
                  <Col span="20">
                    <input type="file" @change="checkFileInfo">
                  </Col>
                </Row>
              </Modal>
              <!-- Delete Content -->
              <Icon
                type="trash-a"
                class="icon-container red"
                v-if="isContentPrivate"
                @click="contentDeleteModal = true"
              ></Icon>
              <!-- Delete Content Modal -->
              <Modal v-model="contentDeleteModal" width="360">
                <p slot="header" style="color: #f60; text-align: center">
                  <Icon type="information-circled"></Icon>
                  <span>{{ $t("baseModal.delBtn") }}</span>
                </p>
                <div style="text-align: center">
                  <p>{{ $t("baseModal.delConfirm") }}</p>
                </div>
                <div slot="footer">
                  <Button
                    type="error"
                    icon="trash-a"
                    :loading="isLoading"
                    @click="deleteContent"
                    long
                  >
                    {{ $t("baseModal.delBtn") }}
                  </Button>
                </div>
              </Modal>
            </h2>
          </div>

          <div v-if="info.tba.playlisted === 1">
            <Icon type="ios-list-outline"></Icon>
            <span>{{ info.tba.tba_playlist_tracks.length }}</span>
          </div>
          <div style="position: relative; color: #32C2F2;" v-if="channel.info !== null">
            <router-link :to="'/myChannel/'+ channel.info.data.id" style="color: #32C2F2;">
              {{ channel.info.data.name }}
            </router-link>
          </div>
          <div>
            <span class="sub-title">{{ $t('teacher') }}</span>:
            <span v-if="info.tba.teacher === null">{{ info.tba.user.name }}
              <span v-if="info.tba.habook_id">({{ info.tba.habook_id }})
                  <Icon @click="toFilterHaBookId(info.tba.habook_id)" style="color: #32C2F2; cursor:pointer;" type="search"></Icon>
              </span>
            </span>
            <span v-else>{{ info.tba.teacher }}
              <span v-if="info.tba.habook_id">({{ info.tba.habook_id }})
                <Icon @click="toFilterHaBookId(info.tba.habook_id)" style="color: #32C2F2; cursor:pointer;" type="search"></Icon>
              </span>
            </span>
          </div>

          <!--          <div>-->
          <!--            <span>{{ $t('subjectField') }}</span>:-->
          <!--            <span v-if="info.tba.field === null">{{ $t('annexes.other') }}</span>-->
          <!--            <span v-else>{{ info.tba.field }}</span>-->
          <!--          </div>-->
          <div>
            <span class="sub-title">{{ $t('subject') }}</span>:
            <span v-if="info.tba.subject === null">{{ $t('annexes.other') }}</span>
            <span v-else>{{ info.tba.subject }}</span>
          </div>
          <!--                    <div>-->
          <!--                        <span>{{$t('eduStage')}}</span>:-->
          <!--                        <span v-if="info.tba.educational_stage === null">{{$t('none')}}</span>-->
          <!--                        <span v-else>{{info.tba.educational_stage.text}}</span>-->
          <!--                    </div>-->
          <div>
            <span class="sub-title">{{ $t('grade') }}</span>:
            <span v-if="info.tba.grade === null">{{ $t('annexes.other') }}</span>
            <span v-else>{{ info.tba.grade }}</span>
          </div>
          <!-- <div>
            <span class="sub-title">{{ $t('lecture_type') }}</span>:
            <span v-if="info.tba.lecture_type === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.lecture_type }}</span>
          </div> -->
          <div>
            <span class="sub-title">{{ $t('lecture_date') }}</span>:
            <span v-if="info.tba.lecture_date === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.lecture_date }}</span>
          </div>
          <div>
            <span class="sub-title">{{ $t('locale') }}</span>:
            <span v-if="info.tba.locale === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.locale.text }}</span>
          </div>
          <div>
            <p class="sub-title">{{ $t('sokrates') }}</p>
            <Row class="annexes" v-bind:gutter="16">
              <!-- Watch Video -->
              <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button type="primary" icon="ios-eye" long v-on:click="exeContent">{{ $t('watch') }}
                </Button>
              </Col>
              <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8" v-if="report">
                <Button class="resrc" long @click="modal = true">{{ $t('report') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.hiTeachNote" v-bind:key="'hiteachnote'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.hiTeachNote') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.lessonPlan" v-bind:key="'lessonplan'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.lessonPlan') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.material" v-bind:key="'material'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.material') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.other" v-bind:key="'other'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.other') }}</Button>
              </Col>
              <Col v-for="(v, i) in annexes.link" v-bind:key="'link'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id, true)">{{ $t('annexes.link') }}</Button>
              </Col>
              <Col
                  :xs="24"
                  :sm="8"
                  :md="8"
                  :lg="8"
              >
                <Button
                    class="resrc"
                    v-if="observation.btnDisplay"
                    @click="observation.formDisplay = !observation.formDisplay"
                    long
                >
                  {{ $t("observation.observation_24") }}
                </Button>
              </Col>
            </Row>
          </div>
          <!-- 暫時註解
          <div style="margin: 8px 0;">
              <Button type="primary" icon="ios-eye" v-on:click="exeContent">{{$t('watch')}}</Button>
          </div>
          -->
        </Col>
        <Col
          v-if="!isNarrowScreen"
          v-bind:xs="24"
          v-bind:sm="7"
          v-bind:md="7"
          v-bind:lg="7"
        >
          <div style="text-align: center; padding: 2px 0; background-color: white ;min-width:210px;max-width: 210px; ">
            <qrcode-vue :value="currentUrl" size="200" level="H"/>
            <span> {{ $t('qrcode.shares') }} </span>
          </div>
        </Col>
        <!--        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">-->
        <!--          <span v-html="info.tba.description"></span>-->
        <!--        </Col>-->
      </Row>
      <!-- Observation Form -->
      <Row
          type="flex"
          justify="center"
          align="middle"
          style="padding-top: 10px;"
          v-show="observation.formDisplay"
      >
        <Col
          :lg="18"
          :md="18"
          :sm="24"
          :xs="24" 
          v-if="channelId"
        >
          <div
            v-if="!isNarrowScreen"
            class="print-btn-container"
          >
            <Button
                type="success"
                icon="printer"
                id="no-print"
                @click="execPrint"
            >
              {{ $t("observation.observation") }}
            </Button>
          </div>
          <thumb-observation
            @obsrvDisplayCtrl="obsrvDisplayCtrl"
            :content-id="info.tba.id"
            :channel-id="channelId"
            :report="report"
            :is-mobile="isNarrowScreen"
          >
          </thumb-observation>
        </Col>
      </Row>
      <!-- 暫時註解
      <Row v-bind:gutter="16">
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
              <div class="annexes">
                  <Button v-for="(v, i) in annexes.hiTeachNote" v-bind:key="'hiteachnote'+i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.hiTeachNote')}}</Button>
                  <Button v-for="(v, i) in annexes.lessonPlan"  v-bind:key="'lessonplan' +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.lessonPlan' )}}</Button>
                  <Button v-for="(v, i) in annexes.material"    v-bind:key="'material'   +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.material'   ) + ':' + v.name}}</Button>
                  <Button v-for="(v, i) in annexes.other"       v-bind:key="'other'      +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.other'      ) + ':' + v.name}}</Button>
                  <Button v-for="(v, i) in annexes.link"        v-bind:key="'link'       +i" v-on:click="exeAnnex(v.id, true)">{{$t('annexes.link'       ) + ':' + v.name}}</Button>
              </div>
          </Col>
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8"></Col>
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8"></Col>
      </Row>
      -->
      <Modal
          v-model="modal"
          :footer-hide=true
          width="1024"
      >
        <img style="width: 100%; height: 100%; object-fit: cover;"
             :src="path.tba + info.tba.id + '/report.png'"
             alt="Report"
        >
      </Modal>
      <!-- Editor Modal -->
      <Modal
          class="editor-modal"
          v-model="displayContentEditorModal"
          @on-cancel="closeContentEditor"
      >
        <!-- Editor Title -->
        <section slot="header">
          <Icon type="edit"></Icon>
          <span style="font-size: 20px; padding: 5px;"> {{ $t('editorModal.title') }} </span>
        </section>

        <!-- Editor Body -->
        <Form id="content-editor">
          <!-- Content Title -->
          <Row class="editor-item">
            <Col span="4">
              <span>{{ $t('editorModal.contentTitle') }} : </span>
            </Col>
            <Col span="20">
              <Input v-model="contentEditorData.title" class="editor-input"></Input>
            </Col>
          </Row>
          <!-- Description -->
          <Row style="padding-top: 15px">
            <span>{{ $t('editorModal.desc') }} : </span>
            <Input
                type="textarea"
                :rows="4"
                v-model="contentEditorData.desc"
                class="editor-input editor-item-input"
            >
            </Input>
          </Row>
          <!-- Subject and Grade -->
          <Row style="padding-top: 15px">
            <Col span="12" class="editor-item">
              <Col span="8">
                <span>{{ $t('editorModal.subject') }} : </span>
              </Col>
              <Col span="14">
                <Select v-model="contentEditorData.subjectId" class="editor-input">
                  <Option
                      v-for="v in info.tba.subject_choices"
                      :value="v.id"
                      :key="v.id"
                  >
                    {{ v.subject }}
                  </Option>
                </Select>
              </Col>
            </Col>
            <Col span="12" class="editor-item">
              <Col span="8">
                <span>{{ $t('editorModal.grade') }} : </span>
              </Col>
              <Col span="14">
                <Select v-model="contentEditorData.grade" class="editor-input">
                  <Option
                      v-for="i in 12"
                      :value="i"
                      :key="i"
                  >
                    {{ i }}
                  </Option>
                </Select>
              </Col>
            </Col>
          </Row>
          <!-- Course Core -->
          <Row class="editor-item" style="padding-top: 15px">
            <Col span="4">
              <span>{{ $t('editorModal.courseCore') }} : </span>
            </Col>
            <Col span="20">
              <Input v-model="contentEditorData.courseCore" class="editor-input"></Input>
            </Col>
          </Row>
          <!-- Observation Focus -->
          <Row class="editor-item" style="padding-top: 15px">
            <Col span="4">
              <span>{{ $t('editorModal.observationFocus') }} : </span>
            </Col>
            <Col span="20">
              <Input v-model="contentEditorData.observationFocus" class="editor-input"></Input>
            </Col>
          </Row>
          <!-- Thumbnail -->
          <Row class="editor-item" style="padding-top: 15px">
            <Col span="4">
              <span>{{ $t('editorModal.thumbnail') }} : </span>
            </Col>
            <Col span="20">
              <input type="file" id="thumbnailUpdatedFile" name="thumbnailUpdatedFile" ref="thumbnailUpdatedFile" @change="handleContentFileUpload('thumbnailUpdatedFile')"/>
            </Col>
          </Row>
          <!-- E-Note -->
          <!--          <Row class="editor-item" style="padding-top: 15px">-->
          <!--            <Col span="4">-->
          <!--              <span>{{ $t('editorModal.hiTeachNote') }} : </span>-->
          <!--            </Col>-->
          <!--            <Col span="20">-->
          <!--              <input type="file" id="hiTeachNoteUpdatedFile" name="hiTeachNoteUpdatedFile" ref="hiTeachNoteUpdatedFile" @change="handleContentFileUpload('hiTeachNoteUpdatedFile')"/>-->
          <!--              <div v-if="annexes.hiTeachNote.length > 0">-->
          <!--                <span v-for="(v, i) in annexes.hiTeachNote" v-bind:key="'hiteachnote'+i">{{ v.name }}</span>-->
          <!--              </div>-->
          <!--            </Col>-->
          <!--          </Row>-->
          <!-- TPC Plan -->
          <Row class="editor-item" style="padding-top: 15px">
            <Col span="4">
              <span>{{ $t('editorModal.lessonPlan') }} : </span>
            </Col>
            <Col span="20">
              <input type="file" id="lessonPlanUpdatedFile" name="lessonPlanUpdatedFile" ref="lessonPlanUpdatedFile" @change="handleContentFileUpload('lessonPlanUpdatedFile')"/>
              <div v-if="annexes.lessonPlan.length > 0">
                <span v-for="(v, i) in annexes.lessonPlan" v-bind:key="'lessonPlan'+i">{{ v.name }}</span>
              </div>
            </Col>
          </Row>
          <!-- Material -->
          <Row class="editor-item" style="padding-top: 15px">
            <Col span="4">
              <span>{{ $t('editorModal.material') }} : </span>
            </Col>
            <Col span="20">
              <input type="file" id="materialUpdatedFile" name="materialUpdatedFile" ref="materialUpdatedFile" @change="handleContentFileUpload('materialUpdatedFile')"/>
              <div v-if="annexes.material.length > 0">
                <span v-for="(v, i) in annexes.material" v-bind:key="'material'+i">{{ v.name }}</span>
              </div>
            </Col>
          </Row>
        </Form>

        <!-- Editor Footer -->
        <section slot="footer">
          <Button @click="closeContentEditor">{{ $t('editorModal.cancel') }}</Button>
          <Button type="primary" @click="updateContentInfo">{{ $t('editorModal.submit') }}</Button>
        </section>
      </Modal>
    </section>
  </article>
</template>

<script>
import _                from "lodash";
import Vuex             from "vuex";
import ThumbObservation from "../../../app/components/thumb-observation";
import QrcodeVue        from "qrcode.vue";
import VideoService     from "../../../../../services/videoService.js"
import ContentService   from "../../../../../services/content.js";

export default {
  components: {
    "thumb-observation": ThumbObservation,
    "qrcode-vue"       : QrcodeVue,
  },
  data() {
    return {
      service    : {
        videoService: VideoService,
        content: ContentService,
      },
      userId     : null,
      info       : null,
      annexes    : {
        hiTeachNote: [],
        lessonPlan : [],
        material   : [],
        link       : [],
        other      : [],
      },
      channel    : {
        info: [],
      },
      observation: {
        btnDisplay : true,
        formDisplay: true,
      },
      modal      : false,
      report     : false,
      print      : false,
      // Content State
      contentState: {
        isMyFav: false,
      },
      // User State
      userState: {
        isOwner: false,
      },
      // Editor Modal
      displayContentEditorModal: false,
      contentEditorData        : {
        title           : null,
        teacher         : null,
        habookId        : null,
        desc            : null,
        subjectId       : null,
        grade           : null,
        courseCore      : null,
        observationFocus: null,
      },
      contentEditorDataAnnexes : {
        thumbnail  : null,
        hiTeachNote: null,
        lessonPlan : null,
        material   : null,
      },
      // Tba Video Map Modal
      tbaVideoTimePointModal: false,
      tbaVideoTimePointData: {
        modes: [
          {
            label: this.$t("tbaVideoTimePointModal.mode.inc"),
            value: "inc",
            icon: "plus",
          },
          {
            label: this.$t("tbaVideoTimePointModal.mode.dec"),
            value: "dec",
            icon: "minus",
          },
        ],
        curMode: "inc", // inc: increase, dec: decrease
        sec: 1,
      },
      // Content Deletion Modal
      contentDeleteModal: false,
      // Misc.
      currentUrl       : null,
      updateVideoStatus: false,
      videoFile        : null,
      videoModal       : false,
      isLoading        : false,
    };
  },
  computed: _.merge(
      Vuex.mapState(["path", "user"]),
      Vuex.mapGetters(["logined", "isNarrowScreen"]),
      {
        contentId() {
          return parseInt(this.$route.params.contentId);
        },
        groupIds() {
          let groupIds = null;
          if (typeof this.$route.query.groupIds !== "undefined")
            groupIds = this.$route.query.groupIds.split(",");
          return groupIds;
        },
        channelId() {
          let channelId = null;
          switch (typeof this.$route.query.channelId) {
            case "number":
              channelId = parseInt(this.$route.query.channelId);
              break;
            case "string":
              channelId = parseInt(this.$route.query.channelId.split(",")[0]);
              break;
            default:
              channelId = null;
          }
          return channelId;
        },
        thumbnailUrl() {
          let url = "/storage/default.png";
          if (this.info.tba.thumbnail)
            url =
                this.path.tba +
                this.info.tba.id +
                "/" +
                this.info.tba.thumbnail +
                "?" +
                Math.random();
          return url;
        },
        updateTbaTimePointsAllowed() {
          return (
            this.userState.isOwner &&
            this.isNumber(this.tbaVideoTimePointData.sec) &&
            this.tbaVideoTimePointData.sec > 0
          );
        },
        isContentPrivate() {
          return this.info.tba.content_status === 2;
        },
      }
  ),
  watch   : {
    $route(to, from) {
      this.init();
    },
  },
  methods : {
    init() {
      this.checkPolicy(this.contentId);
      this.getContentInfo(this.contentId);
      this.getChannelName();
      this.isReport();
      this.currentUrl = window.location.href;
    },
    checkPolicy(contentId) {
      axios
          .get("/exhibition/tbavideo/check-policy", {
            params: {
              contentId: contentId,
            },
          })
          .then((data) => {
            data = data.data;
            if (!data.status) {
              this.$emit("check-logined", true, false, false, false);
              return;
            }
            if (!data.data) {
              this.$emit("check-logined", true, false, false, false);
            }
          })
          .catch((e) => {
            console.log(e);
          });
    },
    getContentInfo(contentId) {
      this.info = null;
      this.annexes.hiTeachNote = [];
      this.annexes.lessonPlan = [];
      this.annexes.material = [];
      this.annexes.link = [];
      this.annexes.other = [];

      axios
          .get("/exhibition/tbavideo/get-content-info", {
            params: {
              contentId: contentId,
              groupIds : _.isNull(this.groupIds)
                         ? null
                         : _.join(this.groupIds, ","),
            },
          })
          .then((data) => {
            data = data.data;
            if (!data.status) return;

            this.userId = data.data.tba.user_id;
            this.info = {
              tba   : data.data.tba,
              videos: data.data.videos,
            };

            this.annexes.hiTeachNote = data.data.annexes.hiTeachNote;
            this.annexes.lessonPlan = data.data.annexes.lessonPlan;
            this.annexes.material = data.data.annexes.material;
            this.annexes.link = data.data.annexes.link;
            this.annexes.other = data.data.annexes.other;

            // Content State
            this.updateContentState();

            // Set Editor (tba data has to be present)
            this.updateUserState();
            if (this.userState.isOwner) this.setContentEditorData();
          })
          .catch((e) => {
            console.log(e);
          });
    },
    updateContentInfo() {
      let _this = this;
      let payload = {
        contentId  : this.contentId,
        groupIds   : this.groupIds,
        userId     : this.userId,
        contentData: this.contentEditorData,
      };

      let formData = new FormData();
      if (_this.contentEditorDataAnnexes.thumbnail) {
        formData.append("thumbnail", _this.contentEditorDataAnnexes.thumbnail);
      }
      if (_this.contentEditorDataAnnexes.hiTeachNote) {
        formData.append(
            "HiTeachNote",
            _this.contentEditorDataAnnexes.hiTeachNote
        );
      }
      if (_this.contentEditorDataAnnexes.lessonPlan) {
        formData.append(
            "LessonPlan",
            _this.contentEditorDataAnnexes.lessonPlan
        );
      }
      if (_this.contentEditorDataAnnexes.material) {
        formData.append("Material", _this.contentEditorDataAnnexes.material);
      }

      // Annexes need fromData to be sent
      axios
          .post("/exhibition/tbavideo/set-content-info", formData, {
            params: payload,
          })
          .then((data) => {
            data = data.data;
            if (!data.status) throw this.$t("editorModal.error");
            this.$Message.success(this.$t("editorModal.success"));
            location.reload();
          })
          .catch((e) => {
            this.$Message.error(e);
          })
          .finally(() => {
            this.closeContentEditor();
          });
    },
    exeContent() {
      if (this.logined) {
        this.$emit("check-logined", true, false, false, false);
        if (!document.cookie) {
          location.reload();
          return;
        }

        let groupIds = this.groupIds;
        //channel ID
        let channelId = this.channelId;
        let url = groupIds
                  ? `${process.env.MIX_APP_URL}/group/${_.join(
                this.groupIds,
                ","
            )}/watch/channel/${channelId}/tbavideo?contentId=${
                this.info.tba.id
            }&groupIds=${_.join(this.groupIds, ",")}&channelId=${
                this.channelId
            }`
                  : //暫時註解
                  `${process.env.MIX_APP_EXHIBITION_URL}tbavideo/watch-as-open?contentId=${this.info.tba.id}`;
        window.open(url);
      } else {
        let url = !this.logined
                  ? `${process.env.MIX_APP_EXHIBITION_URL}tbavideo/watch-as-open?contentId=${this.info.tba.id}`
                  : //暫時註解
                  `${process.env.MIX_APP_EXHIBITION_URL}tbavideo/watch?contentId=` +
                      this.info.tba.id +
                      (_.isnull(this.groupIds)
                       ? ""
                       : "&groupIds=" + _.join(this.groupids, ",")) +
                      "&channelId=" +
                      this.channelId;
        window.open(url);
      }
    },
    exeAnnex(annexId, blank = false) {
      let url =
              "/exhibition/tbavideo/exe-content-annex?annexId=" +
              annexId +
              (_.isNull(this.groupIds)
               ? ""
               : "&groupIds=" + _.join(this.groupIds, ","));
      if (blank) {
        window.open(url);
      } else {
        window.location.href = url;
      }
    },
    getChannelName() {
      let channelId = this.channelId;
      let url = `/exhibition/tbavideo/get-channel-info/`;
      if (channelId) {
        axios
            .get(url, {
              params: {channelId: channelId},
            })
            .then((response) => {
              let data = response.data;
              if (!data.status) {
                // this.$emit('check-logined', true, false, false, false);
                return;
              }
              this.channel.info = data;
            })
            .catch((e) => {
              this.channel.info = null;
            });
      }
      this.channel.info = null;
    },
    isReport() {
      let url = `/exists/${this.contentId}`;

      axios
          .get(url)
          .then((response) => {
            this.report = response.data.status;
          })
          .catch((error) => {
            console.log(error);
          });
    },

    toFilterHaBookId(habookId) {
      this.$store.state.keyword = habookId;
      return this.$router.push("/filtered");
    },
    cancel() {
      this.$Message.info("Clicked cancel");
    },
    execPrint() {
      return this.$router.push({
        name  : "observation",
        params: {
          printSwitch: true,
          contentId  : this.info.tba.id,
          channelId  : this.channelId,
        },
      });
    },
    updateContentState() {
      let favData = _.find(this.info.tba.tba_favorites, {
        user_id   : parseInt(this.user.id),
        channel_id: parseInt(this.channelId),
      });
      this.contentState.isMyFav = favData !== undefined ? true : false;
    },
    updateUserState() {
      this.userState.isOwner = this.user.habook === this.info.tba.habook_id;
    },
    setContentEditorData() {
      // tba
      let subjectData = this.info.tba.subject_choices.filter(
          (v) => v.subject === this.info.tba.subject
      );
      this.contentEditorData.title = this.info.tba.name;
      this.contentEditorData.teacher = this.info.tba.teacher;
      this.contentEditorData.habookId = this.info.tba.habook_id;
      this.contentEditorData.desc = this.info.tba.description;
      this.contentEditorData.subjectId =
          subjectData.length > 0 ? subjectData[0].id : null;
      this.contentEditorData.grade = this.info.tba.grade;
      this.contentEditorData.courseCore = this.info.tba.course_core;
      this.contentEditorData.observationFocus = this.info.tba.observation_focus;
    },
    handleContentFileUpload(ref) {
      let files = this.$refs[ref].files[0];
      if (files !== undefined) {
        if (ref === "thumbnailUpdatedFile")
          this.contentEditorDataAnnexes.thumbnail = files;
        if (ref === "hiTeachNoteUpdatedFile")
          this.contentEditorDataAnnexes.hiTeachNote = files;
        if (ref === "lessonPlanUpdatedFile")
          this.contentEditorDataAnnexes.lessonPlan = files;
        if (ref === "materialUpdatedFile")
          this.contentEditorDataAnnexes.material = files;
      } else {
        this.$refs[ref].value = null;
        this.$Message.error(this.$t("editorModal.error"));
      }
    },
    displayContentEditor() {
      this.setContentEditorData();
      this.displayContentEditorModal = true;
    },
    closeContentEditor() {
      this.displayContentEditorModal = false;
    },
    obsrvDisplayCtrl(v) {
      this.observation.btnDisplay = v;
      this.observation.formDisplay = v;
    },
    toggleFavVideo() {
      let favFunc = this.contentState.isMyFav
                    ? this.removeVideoFromFav()
                    : this.addVideoToFav();
      _.debounce(() => favFunc(), 300);
    },
    addVideoToFav() {
      if (!this.contentId || !this.channelId) return;
      let url = "/favorite/" + this.channelId;
      axios
          .put(url, {
            contentId: this.contentId,
          })
          .then((res) => {
            if (res.data.status !== true) throw new Error(res.data.message);
            this.$Notice.success({
              title: this.$t("messages.favAddSuccess"),
            });
            this.contentState.isMyFav = true;
          })
          .catch((e) => {
            console.log(e);
            this.$Notice.error({
              title: this.$t("messages.error"),
            });
          });
    },
    removeVideoFromFav() {
      if (!this.contentId || !this.channelId) return;
      let url = "/favorite/" + this.channelId;
      axios
          .delete(url, {
            data: {
              contentId: this.contentId,
            },
          })
          .then((res) => {
            if (res.data.status !== true) throw new Error();
            this.$Notice.success({
              title: this.$t("messages.favRemoveSuccess"),
            });
            this.contentState.isMyFav = false;
          })
          .catch((e) => {
            console.log(e);
            this.$Notice.error({
              title: this.$t("messages.error"),
            });
          });
    },
    async checkVideoEncoderType() {
      let result = await this.service.videoService.getVideo(this.contentId);
      _.find(result.videos).encoder === 'FileUpload' ? this.updateVideoStatus = true : this.updateVideoStatus = false;
    },

    async updateVide() {
      let _this = this;
      if (_this.videoFile === null) {
        _this.generalObsrvModal = false
        _this.$Message.info(this.$t('editorVideoModal.error'));
        return;
      }

      let config = {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }

      let formData = new FormData();
      if (_this.videoFile) {
        formData.append('video', _this.videoFile);
        formData.append('_method', 'PUT');
      }

      // POST video
      this.$Loading.start();
      this.loading = true;

      await this.service.videoService.updateVideo(this.contentId, formData, config)

      this.$Loading.finish();
      this.loading = false;
      this.videoFile = null;
      location.reload();

    },
    checkFileInfo(event) {
      let _this = this;
      let videoFile = event.target.files[0];
      let allowedVideoTypes = ['video/mp4']; // mp4
      let allowedVideoExts = ['mp4'];
      let limiterSize = 1610611911; // 1,610,611,911 (B) -> 1.5 (GB)

      // Check file existence
      if (videoFile === undefined) return;

      // Check video file size
      if (videoFile.size > limiterSize) {
        _this.$Message.error(this.$t('editorVideoModal.file_limit'));
        this.$refs.videoUploadFile.value = null;
        return;
      }

      // Check video file extensions and types
      let ext = videoFile.name.split('.').pop().toLowerCase();
      if (!allowedVideoTypes.includes(videoFile.type) || !allowedVideoExts.includes(ext)) {
        _this.$Message.error(this.$t('editorVideoModal.format_error'));
        this.$refs.videoUploadFile.value = null;
        return;
      }

      // Assign video file to video editor modal
      _this.videoFile = videoFile;
    },
    async updateTbaTimePoints() {
      if (!this.updateTbaTimePointsAllowed) return;
      let params = [
        this.contentId,
        this.tbaVideoTimePointData.sec,
        this.tbaVideoTimePointData.curMode,
      ];
      this.isLoading = true;
      await this.service.content
        .updateTbaTimePoints(...params)
        .then((res) => {
          if (res.data.status !== true) throw new Error(res.data.message);
          this.$Notice.success({
            title: this.$t("messages.success"),
          });
          this.tbaVideoTimePointModal = false;
        })
        .catch((e) => {
          console.log(e);
          this.$Notice.error({
            title: this.$t("messages.error"),
          });
          this.tbaVideoTimePointData.sec = 0;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    async deleteContent() {
      if (!this.channelId || !this.contentId) return;
      this.isLoading = true;
      await this.service.content
        .deleteContent(this.channelId, this.contentId)
        .then((res) => {
          if (res.data.status !== true) throw new Error(res.data.message);
          this.$Notice.success({
            title: this.$t("messages.success"),
          });
          this.contentDeleteModal = false;
          this.$router.push({
            name: "movie",
          });
        })
        .catch((e) => {
          console.log(e);
          this.$Notice.error({
            title: this.$t("messages.error"),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    isNumber(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },
  },
  mounted() {
    this.checkVideoEncoderType();
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.content {
  h2 {
    color: #fff;
  }

  .favorite-container {
    cursor: pointer;
  }

  .icon-container {
    color: #32C2F2;
    cursor: pointer;

    &.red {
      color: #FF4D4D;
    }
  }

  .tba-video-map-modal-container {
    color: #f5f5f5;
  }

  .sub-title {
    font-weight: bold;
  }

  .annexes {
    button {
      margin: 2px;
    }

    button.resrc {
      color: #bcbcbc;
      background-color: #333;
      overflow: hidden;
    }
  }

  .print-btn-container {
    text-align: right;
    padding: 5px;
  }
}
</style>
