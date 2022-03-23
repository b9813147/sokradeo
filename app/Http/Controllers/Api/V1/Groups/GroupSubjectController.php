<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Custom\GlobalPlatform;
use App\Services\Group\GroupService;
use App\Services\GroupSubjectFieldsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class GroupSubjectController extends Controller
{

    protected $groupSubjectFieldsService;
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * GroupSubjectController constructor.
     * @param GroupSubjectFieldsService $groupSubjectFieldsService
     * @param GroupService $groupService
     */
    public function __construct(GroupSubjectFieldsService $groupSubjectFieldsService, GroupService $groupService)
    {
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->groupService              = $groupService;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subjects = collect($request->input('subjects'));
        $group    = $this->groupService->getSchoolCodeByGroup($request->input('school_code'));
        //  group
        if (is_null($group)) {
            return $this->fail($request->input('school_code') . ' does not exist');
        }
        // subjects
        if (!is_array($subjects->toArray())) {
            return $this->fail('Data format is incorrect');
        }

        try {
            foreach ($subjects as $subject) {
                $this->groupSubjectFieldsService->updateOrCreate(
                    ['groups_id' => $group->id, 'subject' => $subject],
                    ['subject' => $subject, 'alias' => $subject]
                );
            }
            return $this->success('success');
        } catch (\Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function import(Request $request)
    {
        foreach ($request->all() as $item) {
            $groupId = GlobalPlatform::convertSchoolCodeToGroupId($item['school_code']);
            if (!is_null($groupId)) {
                $this->groupSubjectFieldsService->updateOrCreate(
                    ['groups_id' => $groupId, 'subject' => $item['subjects']],
                    ['subject' => $item['subjects'], 'subject_fields_id' => $item['subject_fields_id']]
                );
            }
        }
        return $this->success('success');
    }
}
