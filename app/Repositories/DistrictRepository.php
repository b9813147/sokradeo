<?php


namespace App\Repositories;


use App\Helpers\Custom\GlobalPlatform;
use App\Models\DistrictLang;
use App\Models\Districts;
use App\Models\Grade;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectFields;
use App\Models\SubjectField;
use App\Models\Tba;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Yish\Generators\Foundation\Repository\Repository;

class DistrictRepository extends Repository
{


    /**
     * @var Districts
     */
    protected $model;

    public function __construct(Districts $districts)
    {
        $this->model = $districts;
    }

    /**
     * 統計學區
     * @param string $abbr
     * @param string $lang
     * @param object $groupIds
     * @return array
     * @throws \Exception
     */
    public function getDistrictCount(string $abbr, string $lang, $groupIds): array
    {
        $districtInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);
        // 判斷語系
        $existsLang   = $this->model->with('districtLang')->where('abbr', $abbr)->first();
        $districtLang = $existsLang->districtLang->where('locales_id', $lang)->first();
        // 語系不存在則拿第一筆來使用
        $districts = $this->model->with([
            'districtLang' => function ($q) use ($lang, $districtLang) {
                if ($districtLang instanceof DistrictLang) {
                    return $q->where('locales_id', $lang);
                }
                return $q->first();
            },
            'districtGroups'
        ])->where('abbr', $abbr)->first();

        // 全部影片
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1, 2])
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->whereIn('group_channel_contents.group_id', $groupIds);

        // 全平台公開
        $publicTotal = DB::table('district_channel_contents')
            ->join('groups', 'district_channel_contents.groups_id', '=', 'groups.id')
            ->where('district_channel_contents.districts_id', $districtInfo->id);

        $contentIds = $publicTotal->pluck('content_id');

        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');

        $doubleGreenLightTotal = $mapTba->get()->filter(function ($tba) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    return $tba->tbaStatistics;
                }
            }
        })->count();

//        // 學科領域統計
//        $subjectField          = SubjectField::all()->flatMap(function ($q) use ($groupIds, $contentIds) {
//            return [$q->type => $this->getSubjectCount($groupIds, $contentIds, $q->id)];
//        })->toArray();
//        $subjectField['Other'] = $this->getSubjectCount($groupIds, $contentIds, 0);
//
//        // 年級統計
//        $gradeFields   = Grade::query()->get()->map(function ($q) use ($groupIds, $contentIds) {
//            return $this->getGradeCount($groupIds, $contentIds, $q->id);
//        });
//        $gradeFields[] = $this->getGradeCount($groupIds, $contentIds, 0);

        return [
            'id'                     => $districts->id,
            'thumbnail'              => $districts->thumbnail,
            'name'                   => $districts->districtLang->first()->name,
            'description'            => $districts->districtLang->first()->description,
            'public_total'           => number_format($publicTotal->count()),
            'hits_total'             => number_format((int)$mapTba->sum('hits')),
            'all_total'              => number_format($query->count()),
            'doubleGreenLight_total' => number_format($doubleGreenLightTotal),
//            'subject_fields'         => $subjectField,
//            'grade_fields'           => $gradeFields
        ];
    }

    /**
     * 取得學區groupId
     * @param string $abbr
     * @return array
     */
    public function getDistrictByGroupIds($abbr)
    {
        $groupIds = [];

        $districts = $this->model->with('districtLang', 'districtGroups')->where('abbr', $abbr)->get();

        $districts->first()->districtGroups->each(function ($group) use (&$groupIds) {
            array_push($groupIds, $group->groups_id);
        });

        return $groupIds;
    }

    /**
     * 統計學科
     * @param $groupIds
     * @param $tba_id
     * @param int $subject_fields_id
     * @return mixed
     */
    private function getSubjectCount($groupIds, $tba_id, $subject_fields_id)
    {
        // 取出學區的頻道
        if ($subject_fields_id === 0) {
            return GroupChannelContent::leftJoin('group_subject_fields', 'group_subject_fields_id', 'group_subject_fields.id')
                ->whereIn('content_id', $tba_id)
                ->whereIn('group_id', $groupIds)
//                ->where('groups.status', 1)
                ->where('content_status', 1)
                ->where('group_channel_contents.content_public', 1)
                ->whereNull('group_subject_fields.subject_fields_id')
                ->count();
        }


        return GroupChannelContent::join('group_subject_fields', 'group_subject_fields_id', 'group_subject_fields.id')
            ->whereIn('content_id', $tba_id)
            ->whereIn('group_id', $groupIds)
//            ->where('groups.status', 1)
            ->where('content_status', 1)
            ->where('group_channel_contents.content_public', 1)
            ->where('group_subject_fields.subject_fields_id', $subject_fields_id)
            ->count();
    }

    /**
     * 統計年級
     * @param array $groupIds
     * @param array $tba_id
     * @param int $grade_id
     * @return mixed
     */
    private function getGradeCount($groupIds, $tba_id, int $grade_id)
    {
        // 取出學區的頻道
        if ($grade_id === 0) {
            return GroupChannelContent::whereIn('content_id', $tba_id)
                ->whereIn('group_id', $groupIds)
//                ->where('groups.status', 1)
                ->where('content_status', 1)
                ->where('group_channel_contents.content_public', 1)
                ->whereNull('grades_id')
                ->count();
        }

        return GroupChannelContent::whereIn('content_id', $tba_id)
            ->whereIn('group_id', $groupIds)
            ->where('grades_id', $grade_id)
//            ->where('groups.status', 1)
            ->where('content_status', 1)
            ->where('group_channel_contents.content_public', 1)
            ->count();
    }

}
