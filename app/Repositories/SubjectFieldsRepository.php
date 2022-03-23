<?php


namespace App\Repositories;


use App\Models\SubjectField;
use Yish\Generators\Foundation\Repository\Repository;

class SubjectFieldsRepository extends Repository
{
    protected $subjectFieldsRepository;

    /**
     * SubjectFieldsRepository constructor.
     * @param $subjectFieldsRepository
     */
    public function __construct(SubjectField $subjectFieldsRepository)
    {
        $this->subjectFieldsRepository = $subjectFieldsRepository;
    }
}
