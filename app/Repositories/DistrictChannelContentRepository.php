<?php

namespace App\Repositories;

use App\Models\DistrictChannelContent;
use App\Models\GradeLang;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictChannelContentRepository extends Repository
{
    protected $model;

    /**
     * DistrictChannelContentRepository constructor.
     * @param $model
     */
    public function __construct(DistrictChannelContent $model)
    {
        $this->model = $model;
    }

    /**
     * @param $groupIds
     * @param $ratingId
     * @param $district_id
     * @return array
     */
    public function getSubjectCount($groupIds, $ratingId, $district_id): array
    {
        $districtChannelContent = $this->model->query()
            ->selectRaw("count(*) as total, district_channel_contents.group_subject_fields_id , district_subjects.subject ,district_subjects.id,`order`")
            ->leftJoin('district_group_subjects', 'district_group_subjects.group_subject_fields_id', 'district_channel_contents.group_subject_fields_id')
            ->leftJoin('district_subjects', 'district_subjects.id', 'district_group_subjects.district_subjects_id')
            ->where('district_channel_contents.districts_id', $district_id)
            ->where('district_subjects.districts_id', $district_id)
//            ->whereIn('district_channel_contents.groups_id', $groupIds)
            ->groupBy('district_subjects.id')
            ->orderBy('district_subjects.order', 'desc')
            ->orderByRaw("ISNULL(district_subjects_id),district_subjects_id asc");

        // 增加 $ratingId 條件連動
        if (is_numeric($ratingId)) {
            $districtChannelContent->where('ratings_id', $ratingId);
        }


        return $districtChannelContent->get()->map(function ($districtChannelContent) {
            $data = [];
            if ($districtChannelContent->group_subject_fields_id === null) {
                return $data = ['text' => __('app/subject-field.Other'), 'value' => $districtChannelContent->total, 'id' => 'Other'];
            }

            return $data = [
                'text'  => $districtChannelContent->subject ?? __('app/subject-field.Other'),
                'value' => $districtChannelContent->total,
                'id'    => $districtChannelContent->id ?? 'Other',
            ];

        })->toArray();
    }


    /**
     * @param $groupIds
     * @param  $group_subject_fields_id
     * @param  $ratingId
     * @param  $district_id
     * @return array
     */
    public function getGradeCount($groupIds, $group_subject_fields_id, $ratingId, $district_id): array
    {
        $lang       = new \App\Libraries\Lang\Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());


        $districtChannelContent = $this->model->query()
            ->selectRaw("count(*) as total, district_channel_contents.grades_id")
            ->leftJoin('district_group_subjects', 'district_group_subjects.group_subject_fields_id', 'district_channel_contents.group_subject_fields_id')
            ->leftJoin('district_subjects', 'district_subjects.id', 'district_group_subjects.district_subjects_id')
            ->whereIn('district_channel_contents.groups_id', $groupIds)
            ->where('district_channel_contents.districts_id', $district_id)
            ->groupBy('district_channel_contents.grades_id')
            ->orderByRaw("ISNULL(district_channel_contents.grades_id),district_channel_contents.grades_id ASC");

        // 增加 $group_subject_fields_id 條件連動
        if (is_numeric($group_subject_fields_id)) {
            $districtChannelContent->where('district_subjects.id', $group_subject_fields_id);
        }
        // 增加 $ratingId 條件連動
        if (is_numeric($ratingId)) {
            $districtChannelContent->where('ratings_id', $ratingId);
        }
        // 增加 $group_subject_fields_id 條件連動
        if ($group_subject_fields_id === 'Other') {
            $districtChannelContent->whereNull('district_subjects.id');
        }

        $gradeLangs = GradeLang::query()->where('locales_id', $locales_id)->get();


        $result = collect();
        $districtChannelContent->get()->map(function ($districtChannelContent) use ($gradeLangs, $result) {
            $gradeLangs->where('grades_id', $districtChannelContent->grades_id)->map(function ($gradeLang) use ($districtChannelContent, $result) {
                $result->push(['text' => $gradeLang->name, 'value' => $districtChannelContent->total, 'id' => $districtChannelContent->grades_id]);
            });

            if ($districtChannelContent->grades_id === null) {
                $result->push(['text' => __('app/subject-field.Other'), 'value' => $districtChannelContent->total, 'id' => 'Other']);
            }
        });

        return $result->toArray();
    }

    /**
     * @param $groupIds
     * @param  $district_id
     * @return array
     */
    public function getRatingCount($groupIds, int $district_id): array
    {
        $districtChannelContents = $this->model->query()
            ->with([
                'groupRatingFields' => function ($q) {
                    $q->select('id', 'type', 'name');
                }
            ])
            ->selectRaw("count(*) as total, district_channel_contents.ratings_id")
            ->where('district_channel_contents.districts_id', $district_id)
            ->whereIn('district_channel_contents.groups_id', $groupIds)
            ->groupBy('district_channel_contents.ratings_id')
            ->orderBy('district_channel_contents.ratings_id', 'ASC')
            ->whereNotNull('district_channel_contents.ratings_id')
            ->get();


        $result = $districtChannelContents->map(function ($q) {
            $rating = $q->groupRatingFields->first();
            return $data = ['text' => $rating->name, 'value' => $q->total, 'id' => $q->ratings_id, 'type' => $rating->type];
        });

        return $result->toArray();
    }
}
