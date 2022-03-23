<?php

namespace App\Services\Exhibition;

use App\Repositories\GradeLangRepository;
use App\Repositories\GroupSubjectFieldsRepository;
use App\Repositories\SubjectFieldLangRepository;
use Illuminate\Support\Facades\Lang;
use App\Repositories\EducationalStageRepository;
use App\Repositories\ExhibitionRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\Tba\FeatureRepository;

use App\Helpers\Custom\GlobalPlatform;

class ExhibitionService
{
    private $exhibitionRepo = null;
    protected $subjectFieldLangRepo;

    /**
     * @var GroupSubjectFieldsRepository
     */
    protected $groupSubjectFieldsRepository;
    /**
     * @var GradeLangRepository
     */
    protected $gradeLangRepository;

    public function __construct(ExhibitionRepository $exhibitionRepo, SubjectFieldLangRepository $subjectFieldLangRepo, GroupSubjectFieldsRepository $groupSubjectFieldsRepository, GradeLangRepository $gradeLangRepository)
    {
        $this->exhibitionRepo               = $exhibitionRepo;
        $this->subjectFieldLangRepo         = $subjectFieldLangRepo;
        $this->groupSubjectFieldsRepository = $groupSubjectFieldsRepository;
        $this->gradeLangRepository          = $gradeLangRepository;
    }

    //
    public function checkPolicy($cmsId, $cmsType, $setTypes = [])
    {
        return (auth()->check() || $this->checkCmsSet($cmsId, $cmsType, $setTypes));
    }

    //
    public function checkCmsSet($cmsId, $cmsType, $setTypes = [])
    {
        return $this->exhibitionRepo->checkCmsSet($cmsId, $cmsType, $setTypes);
    }

    //
    public function getCmsSets($cmsType, $setType)
    {
        return $this->exhibitionRepo->getCmsSets($cmsType, $setType);
    }

    //
    public function getGroupChannelSets($cmsType): \Illuminate\Support\Collection
    {
        return $this->exhibitionRepo->getGroupChannelSets($cmsType);
    }

    // 僅限個人使用
    public function getGroupChannelSetsByUser($cmsType, $setType, $groupIds): \Illuminate\Support\Collection
    {
        return $this->exhibitionRepo->getGroupChannelSetsByUser($cmsType, $setType, $groupIds);
    }

    /**
     * 個人影片計算
     * @param int $userId
     * @return array
     */
    public function getUserVideoCount(int $userId): array
    {
        return $this->exhibitionRepo->getUserVideoCount($userId);
    }

    /**
     * ge User Comments
     * @param int $userId
     * @param string $mode
     * @param $filter
     * @param int $size
     * @return Object
     */
    public function getUserComments(int $userId, string $mode, $filter = "", int $size): object
    {
        return $this->exhibitionRepo->getUserComments($userId, $mode, $filter, $size);
    }

    // 僅限學區使用
    public function getGroupChannelSetsByDistrict($cmsType, $setType, $groupIds = null)
    {
        return $this->exhibitionRepo->getGroupChannelSetsByDistrict($cmsType, $setType, $groupIds);
    }

    //
    public function searchKeywords($name)
    {
        return (new KeywordRepository())->getKeywords($name);
    }

    //
    public function getEduStageFilter()
    {
        $mapEduStage = Lang::get('app/edu-stage');
        return (new EducationalStageRepository())->getEduStages()->map(function ($v) use ($mapEduStage) {
            return ['type' => $v->type, 'value' => $v->id, 'text' => $mapEduStage[$v->type]];
        });
    }

    //
    public function getGradeFilter()
    {
        return collect(Lang::get('app/grade'))->map(function ($v, $k) {
            return ['type' => $k, 'value' => $k, 'text' => $v];
        });
    }

    /**
     * 取得年級欄位 (新制)
     *
     * @return GradeService[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getGrade()
    {
        $lang       = new \App\Libraries\Lang\Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());
        $fields     = $this->gradeLangRepository->getBy('locales_id', $locales_id);

        $result = $fields->map(function ($v) {
            return ['type' => $v->grades_id, 'value' => $v->grades_id, 'text' => $v->name];
        });

        $result->push(['value' => 'Other', 'type' => 'Other', 'text' => __('app/subject-field.Other')]);

        return $result;
    }

    // Get submission choices
    public function getSubmissionChoices(int $channelId): array
    {
        $groupId = GlobalPlatform::convertChannelIdToGroupId($channelId);

        // Get Categorial choices
        $ratings        = $this->exhibitionRepo->getCategoricalChoices($groupId);
        $ratings_result = $ratings->map(function ($v) {
            return [
                'id'          => $v->id,
                'group_id'    => $v->groups_id,
                'district_id' => $v->districts_id,
                'type'        => $v->type,
                'name'        => $v->name
            ];
        });

        // Get Subject choices
        $group_subject_fields        = $this->exhibitionRepo->getSubjectChoices($groupId);
        $group_subject_fields_result = $group_subject_fields->map(function ($v) {
            return ['id' => $v->id, 'subject' => $v->subject, 'alias' => $v->alias, 'group_id' => $v->groups_id];
        });

        return [
            'ratings'              => $ratings_result,
            'group_subject_fields' => $group_subject_fields_result
        ];
    }

    // Get subject choices
    public function getSubjectChoices(array $groupIds): object
    {
        $groupId = $groupIds[0];

        $group_subject_fields        = $this->exhibitionRepo->getSubjectChoices($groupId);
        $group_subject_fields_result = $group_subject_fields->map(function ($v) {
            return ['id' => $v->id, 'subject' => $v->subject, 'alias' => $v->alias, 'group_id' => $v->groups_id];
        });
        return $group_subject_fields_result;
    }

    //
    public function getSubjectFieldFilter()
    {
        $lang       = new \App\Libraries\Lang\Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());
        $fields     = $this->subjectFieldLangRepo->getSubject($locales_id);

        return $fields->map(function ($v) {
            return ['type' => $v->type, 'value' => $v->id, 'text' => $v->name];
        });

//        $mapSubjectField = Lang::get('app/subject-field');
//        return (new SubjectFieldRepository())->getSubjectFields()->map(function ($v) use ($mapSubjectField) {
//            return ['type' => $v->type, 'value' => $v->id, 'text' => $mapSubjectField[$v->type]];
//        });
    }

    /**
     * 謹獻頻道內部使用
     *
     * @param $channelId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getChanelBySubjectFieldFilter($channelId)
    {

        $fields = $this->groupSubjectFieldsRepository->getChannelBySubject($channelId);


        $result = $fields->map(function ($v) {
            return ['type' => null, 'value' => $v->id, 'text' => $v->subject];
        });

        return $result->push(['type' => null, 'value' => 'Other', 'text' => __('app/subject-field.Other')]);
    }

    //
    public function getLectureTypeFilter()
    {
        $mapLectureType = Lang::get('app/lecture-type');
        return [
            ['type' => 'new', 'value' => 0, 'text' => $mapLectureType['new']],
            ['type' => 'review', 'value' => 1, 'text' => $mapLectureType['review']],
        ];
    }

    //
    public function getTbaFeatureFilter()
    {
        $mapTbaFeature = Lang::get('app/tba/feature');
        return (new FeatureRepository())->getFeatures()->map(function ($v) use ($mapTbaFeature) {
            return ['type' => $v->type, 'value' => $v->id, 'text' => $mapTbaFeature[$v->type]];
        });
    }

    //
    public function getYearFilter()
    {
        $filter  = [];
        $current = intval(date('Y'));
        for ($i = 0; $i < 5; $i++) {
            $tmp = $current - $i;
            array_push($filter, ['type' => $tmp, 'value' => $tmp, 'text' => $tmp]);
        }
        return $filter;
    }
}
