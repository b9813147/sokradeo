<?php


namespace App\Repositories;


use App\Models\SubjectFieldLang;
use LaravelIdea\Helper\App\Models\_SubjectFieldLangCollection;
use Yish\Generators\Foundation\Repository\Repository;

class SubjectFieldLangRepository extends Repository
{
    protected $model;

    /**
     * SubjectFieldLangRepository constructor.
     * @param SubjectFieldLang $subjectFieldLang
     */
    public function __construct(SubjectFieldLang $subjectFieldLang)
    {
        $this->model = $subjectFieldLang;
    }

    /**
     * 取得指定語系的學科欄位
     * @param $locales_id
     * @param null $districts_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSubject($locales_id, $districts_id = null)
    {

        $query = $this->model->query()->select('subject_fields.id', 'name', 'type')
            ->join('subject_fields', 'subject_fields.id', 'subject_field_langs.subject_fields_id');

        if ($districts_id) {
            $query->join('district_subject_fields', 'district_subject_fields.subject_fields_id', 'subject_fields.id');
            $query->where('district_subject_fields.districts_id', $districts_id);
        }

        return $query->where('locales_id', $locales_id)->distinct('subject_fields.id')->get();
    }
}
