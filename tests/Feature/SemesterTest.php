<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\Semester;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGetSchoolSemester()
    {
        $model = GroupChannel::query()->select()->findOrFail(1);
        dd($model->semesters()->get()->toArray());
    }

    // 建立 各學校 預設 學期資料
    public function testRunMakeData()
    {
        Semester::query()->truncate();

        Group::select('id')->get()->each(function ($q) {
            $i = 1;
            while ($i <= 2) {
                Semester::query()->create([
                    'semester_id' => $i,
                    'group_id'    => $q->id,
                    'year'        => $i === 1 ? 2020 : 2021,
                    'month'       => $i === 1 ? 8 : 2,
                    'day'         => 1,
                ]);
                $i++;
            }
        });
    }

    public function testGetCurrentSemester()
    {
        $semester = Semester::query()->select('start_date')->where('group_id', 3)->get()->toArray();
        dd($semester);
    }
}
