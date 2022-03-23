<?php


namespace App\Services;


use App\Libraries\Lang\Lang;
use App\Models\DistrictSubjectField;
use App\Repositories\DistrictRepository;
use App\Repositories\SubjectFieldLangRepository;

class DistrictServices extends BaseService
{
    protected $repository;
    protected $districtSubjectField;
    protected $subjectFieldLangRepository;
    protected $gradeLangService;

    public function __construct(DistrictRepository $districtRepository, DistrictSubjectField $districtSubjectField, SubjectFieldLangRepository $subjectFieldLangRepository, GradeLangService $gradeLangService)
    {
        $this->repository                 = $districtRepository;
        $this->districtSubjectField       = $districtSubjectField;
        $this->subjectFieldLangRepository = $subjectFieldLangRepository;
        $this->gradeLangService           = $gradeLangService;
    }

    /**
     * 取得學區統計
     * @param string $abbr
     * @param object $groupIds
     * @return array
     * @throws \Exception
     */
    public function getDistrictCount(string $abbr, $groupIds)
    {
        $parsing = new Lang();
        $lang    = $parsing->getConvertByLangString(\App::getLocale());

        return $this->repository->getDistrictCount($abbr, $lang, $groupIds);
    }

    public function getDistrictByGroupIds($abbr)
    {
        return $this->repository->getDistrictByGroupIds($abbr);
    }

    /**
     * 取得年級欄位
     * @return GradeService[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getGrade()
    {
        $lang       = new Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());
        $fields     = $this->gradeLangService->getBy('locales_id', $locales_id);

        $result = $fields->map(function ($v) {
            return ['type' => $v->grades_id, 'value' => $v->grades_id, 'text' => $v->name];
        });

        $result->push(['value' => $fields->count() + 1, 'type' => 'Other', 'text' => __('app/subject-field.Other')]);

        return $result;
    }

    /**
     * 取得學科欄位
     * @param string $abbr
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getSubjectField($abbr)
    {
        $lang         = new Lang();
        $locales_id   = $lang->getConvertByLangString(\App::getLocale());
        $districts_id = $this->repository->firstBy('abbr', $abbr)->id ?? null;

        $fields = $this->subjectFieldLangRepository->getSubject($locales_id, $districts_id);

        $result = $fields->map(function ($v) {
            return ['type' => $v->type, 'value' => $v->id, 'text' => $v->name];
        });

        $result->push(['type' => 'Other', 'value' => $fields->count() + 1, 'text' => __('app/subject-field.Other')]);

        return $result;

    }
}
