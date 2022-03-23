<?php

namespace App\Services\Exhibition;

use App\Models\GroupSubjectFields;
use App\Repositories\GroupChannelContentRepository;
use Illuminate\Support\Facades\Lang;
use App\Repositories\KeywordRepository;
use App\Repositories\Exhibition\TbaVideoContentRepository;
use App\Repositories\Tba\AnnexRepository as TbaAnnexRepository;
use App\Types\Tba\AnnexType as TbaAnnexType;

class TbaVideoService
{
    private $dateFormat = 'Y-m-d';
    protected $tbaVideoContentRepo = null;

    //
    public function __construct(TbaVideoContentRepository $tbaVideoContentRepo)
    {
        $this->tbaVideoContentRepo = $tbaVideoContentRepo;
    }

    //
    public function filters(
        $page,
        $perPage,
        $userId = null,
        $order = null,
        $conds = [],
        $year = null,
        $search = '',
        $tbaFeatures = [],
        $channelId = null,
        $abbr = null
    )
    {
        $conds = collect($conds)->map(function ($v, $k) {
            return ['col' => $k, 'op' => '=', 'val' => $v];
        })->toArray();

        if (!is_null($year)) {
            $dateStart = Date($this->dateFormat, mktime(0, 0, 0, 1, 1, $year));
            $dateEnd   = Date($this->dateFormat, mktime(0, 0, 0, 12, 31, $year));
            array_push($conds, ['col' => 'tbas.lecture_date', 'op' => '>=', 'val' => $dateStart]);
            array_push($conds, ['col' => 'tbas.lecture_date', 'op' => '<=', 'val' => $dateEnd]);
        }

        if (!is_null($channelId)) {
            array_push($conds, ['col' => 'group_channel_contents.group_channel_id', 'op' => '=', 'val' => $channelId]);
        }

        $opts = [];
        if (!empty($search)) {
            $opts = [
                ['col' => 'tbas.name', 'op' => 'like', 'val' => '%' . $search . '%'],
                ['col' => 'tbas.teacher', 'op' => 'like', 'val' => '%' . $search . '%'],
                ['col' => 'groups.name', 'op' => 'like', 'val' => '%' . $search . '%'],
                ['col' => 'tbas.habook_id', 'op' => 'like', 'val' => '%' . $search . '%'],
//                ['col' => 'group_subject_fields.subject', 'op' => 'like', 'val' => '%' . $search . '%'],
//                ['col' => 'group_channel_contents.grades_id', 'op' => 'like', 'val' => '%' . $search . '%'],
//                ['col' => 'tbas.description', 'op' => 'like', 'val' => '%' . $search . '%'],
            ];

            (new KeywordRepository())->createKeyword($search);
        }

        return $this->tbaVideoContentRepo->listWithOrderType($page, $perPage, $userId, $conds, $opts, $tbaFeatures, $order, $abbr);
    }

    //
    public function getRanks($limit, $orders, $groupIds = null)
    {
        foreach ($orders as $i => $order) {
            $orders[$i]['col'] = 'tbas.' . $order['col'];
        }

        $contentIds = $this->tbaVideoContentRepo->getRankContentIds($limit, $orders, $groupIds);

        return $this->getContentsByIds($contentIds, $orders);
    }

    //
    public function getContentInfo($contentId, $groupIds): ?array
    {
        $lang                         = new \App\Libraries\Lang\Lang();
        $locales_id                   = $lang->getConvertByLangString(\App::getLocale());
        $mapEduStage    = Lang::get('app/edu-stage');
        $mapLectureType = Lang::get('app/lecture-type');
        $mapLocale      = Lang::get('app/locale');

        $content = $this->tbaVideoContentRepo->getContent($contentId);

        if (is_null($content)) {
            return null;
        }

        if (!is_null($content['tba']->educationalStage)) {
            $content['tba']->educationalStage->text = $mapEduStage[$content['tba']->educationalStage->type];
        }

        if (!is_null($content['tba']->lecture_type)) {
            $lectureType = null;
            switch ($content['tba']->lecture_type) {
                case 0:
                    $lectureType = $mapLectureType['new'];
                    break;
                case 1:
                    $lectureType = $mapLectureType['review'];
                    break;
            }
            $content['tba']->lecture_type = $lectureType;
        }

        // 取代Tba Subject
        if (is_array($groupIds)) {
            $ContentAndGroupSubjectFields = app(GroupChannelContentRepository::class)->getContentAndGroupSubjectFields($contentId, $groupIds[0])->first();
            $content['tba']->subject = $ContentAndGroupSubjectFields->groupSubjectFields instanceof GroupSubjectFields ? $ContentAndGroupSubjectFields->groupSubjectFields->alias : null;
            $content['tba']->field   = $ContentAndGroupSubjectFields->groupSubjectFields instanceof GroupSubjectFields ? ($ContentAndGroupSubjectFields->groupSubjectFields->subjectFields->first() ?
                ($ContentAndGroupSubjectFields->groupSubjectFields->subjectFields->isNotEmpty() ?
                    $ContentAndGroupSubjectFields->groupSubjectFields->subjectFields->firstWhere('locales_id', $locales_id)->name
                    : null)
                : null)
                : null;
        }

        if (!is_null($content['tba']->locale)) {
            if (!empty($mapLocale[$content['tba']->locale->type])) {
                $content['tba']->locale->text = $mapLocale[$content['tba']->locale->type];
            }
        }

        // 增加 影片狀態 及 年級
        if (is_array($groupIds)) {
            $contentInfo = $this->tbaVideoContentRepo->getContentInfo($contentId, $groupIds[0]);
            $content['tba']->content_status = $contentInfo->content_status;
            $content['tba']->grade          = $contentInfo->grades_id;
        }

        // Get all subject choices from a given group ids (the fist one is used)
        // This will be used by ContentEditor -> subject_choices
        if (is_array($groupIds)) {
            $subjectChoices = app(ExhibitionService::class)->getSubjectChoices($groupIds);
            $content['tba']->subject_choices = $subjectChoices ? $subjectChoices : [];
        }


        $annexes            = (new TbaAnnexRepository())->getAnnexResrcs($content['tba']->id, ['resources.status' => 1]);
        $content['annexes'] = [
            'hiTeachNote' => [],
            'lessonPlan'  => [],
            'material'    => [],
            'link'        => [],
            'other'       => [],
        ];
        $annexes->each(function ($v) use (& $content) {
            array_push($content['annexes'][lcfirst($v->type)], $v);
        });

        return $content;
    }

    public function setContentInfo(string $contentId, array $groupIds, array $contentData): ?array
    {
        return [
            'status' => $this->tbaVideoContentRepo->setContentInfo($contentId, $groupIds[0], $contentData),
            'data' => $contentData
        ];
    }

    public function setContentInfoAnnex($contentId, $groupIds, $userId, $contentDataAnnexes)
    {
        // To be written
    }

    //
    public function getContentsByIds($contentIds, $orders = [])
    {
        return $this->tbaVideoContentRepo->getContentsByIds($contentIds, $orders);
    }
}
