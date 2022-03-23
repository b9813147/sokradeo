<?php

namespace Tests\Feature;

use App\Helpers\Code\AlphaNumeric;
use App\Models\Group;
use App\Models\Role;
use App\Models\Tba;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\User;
use App\Types\App\RoleType;
use App\Types\Group\DutyType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GroupTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testShareVideo()
    {
//        dd(auth()->id());
//        try {
        $id         = 948;
        $attributes = [
            'member_duty'   => 'General',
            'member_status' => 1
        ];

//            $model      = User::query()->findOrFail($id)->groups()->where('group_id', $attributes['group_id']);
//            dd($model->exists());
        $model = User::query()->findOrFail($id)->groups()->attach(10, $attributes);
//        } catch (\Exception $exception) {
//            dd($exception->getMessage());
//        }

//
//        dd($model);
    }

    public function testDebug()
    {
        $user = User::query()->with('roles')->findOrFail(12839);
//        dd(empty($user->toArray()['roles']));
        dd($user->roles()->exists());
    }

    public function testExampleData()
    {

        $data  = [
            "eventType"      => "training",
            "startDate"      => "2021-06-15",
            "endDate"        => "2021-08-31",
            "maxParticipant" => 50,
            "stageCount"     => 3,
            "enableTrial"    => 1,
            "trialDeadline"  => "2021-07-31",
            "eventStage"     => [
                "registration" => [
                    "stageOrder" => 1,
                    "startDate"  => "2021-07-01",
                    "endDate"    => "2021-07-05"
                ],
                "reviewing"    => [
                    "stageOrder" => 0,
                    "startDate"  => 0,
                    "endDate"    => 0
                ],

                "submission"    => [
                    "stageOrder"  => 2,
                    "startDate"   => "2021-07-06",
                    "endDate"     => "2021-07-21",
                    "isMultiTask" => 1,
                    "requirement" => [
                        "isDoubleGreen" => 0,
                        "hasMaterial"   => 1,
                        "hasTPC"        => 1
                    ]
                ],
                "certification" => [
                    "stageOrder" => 3,
                    "startDate"  => "2021-07-22",
                    "endDate"    => "2021-07-31"
                ]
            ]
        ];
        $event = [
            "eventInfo"  => [
                "startDate"     => "2021-06-15",
                "endDate"       => "2021-08-31",
                "stageCount"    => "3",
                "enableTrial"   => "1",
                "trialDeadline" => "2021-07-31"
            ],
            "eventStage" => [
                [
                    "stageOrder" => 1,
                    "stageType"  => "registration",
                    "startDate"  => "2021-06-15",
                    "endDate"    => "2021-06-31"
                ],
                [
                    "stageOrder" => 0,
                    "stageType"  => "reviewing",
                    "startDate"  => "2021-06-15",
                    "endDate"    => "2021-06-31"
                ],
                [
                    "stageOrder" => 2,
                    "stageType"  => "submission",
                    "startDate"  => "2021-07-01",
                    "endDate"    => "2021-07-31",
                    "multiTask"  => "1"
                ],
                [
                    "stageOrder" => 3,
                    "stageType"  => "certification",
                    "startDate"  => "2021-08-01",
                    "endDate"    => 'null'
                ]
            ]
        ];

        // 申請截止時間
        // 實作任務開始時間
        // 考核認證開始時間
        // 授權時間
        $data = json_encode($data);
        Group::query()->whereIn('public', [2, 1])->update(['event_data' => null]);
        Group::query()->whereIn('public', [2, 1])->update([
            'event_data' => $data,
        ]);
    }

    public function testTbaRelatedData()
    {
        $model = Tba::with('tbaAnnexs', 'tbaEvaluateUsers')->first();
        dd($model->toArray());
    }

    public function testCrontabRunSetup()
    {
        $model    = Group::query()->where('public', 2)->first();
        $trialDay = Carbon::create('2021-06-03');
        $current  = Carbon::now();
        $d        = $trialDay->diff($current)->days;
        dd($d);
        // 預設進度狀態
        $stageTypes = collect(['registration', 'submission', 'certification']);
        // 當前時間
        $current = Carbon::now()->format('Y-m-d');
        $groups  = Group::query()->select('event_data->eventInfo as eventInfo', 'event_data->eventStage as eventStage', 'id')->with('channels')->whereNotNull('event_data')->get();
        $stageTypes->each(function ($stageType) use ($current, $groups) {
            $groups->filter(function ($group) use ($current, $stageType) {
                if (collect(json_decode($group->eventStage))->where('endDate', '!=', '')->where('endDate', '<', $current)->contains('stageType', $stageType)) {
                    return $group;
                }
            })->each(function ($q) use ($stageType) {
                switch ($stageType) {
                    case 'registration':
                        $q->channels()->where('status', 1)->update(['status' => 2]);
                        break;
                    case 'submission':
                        $q->channels()->where('status', 2)->update(['status' => 3]);
                        break;
                    case'certification':
                        $q->channels()->where('status', 3)->update(['status' => 4]);
                }
            });
        });


//        $current = Carbon::now()->format('Y - m - d');
//        // 申請截止時間
//        $groups = Group::query()->with('channels')->where('event_data->deadline', ' < ', $current)->get();
//        $groups->each(function ($q) {
//            $q->channels()->where('status', 1)->update(['status' => 2]);
//        });
//        // 實作任務開始時間
//        $groups = Group::query()->with('channels')->where('event_data->taskDeadline', ' < ', $current)->get();
//        $groups->each(function ($q) {
//            $q->channels()->where('status', 2)->update(['status' => 3]);
//        });
//        // 考核認證開始時間
//        $groups = Group::query()->with('channels')->where('event_data->assessDeadline', ' < ', $current)->get();
//        $groups->each(function ($q) {
//            $q->channels()->where('status', 3)->update(['status' => 4]);
//        });
    }

    public function testGroupUserCount()
    {
//        $group     = Group::query()->find(582);
//        $groups    = User::query()->find(948)->groups()->where('group_id', 3);
        auth()->loginUsingId(948);
//        auth()->user()->groups()->attach(582, ['member_duty' => 'Admin', 'member_status' => 1]);
        auth()->user()->groups()->updateExistingPivot(582, ['user_data' => json_encode(['license' => 'test'])]);
//        $user_data = json_encode(['license' => 'test']);
//        $groups->updateExistingPivot(7, ['user_data' => $user_data]);
//        dd(1);
//        $json_decode = json_decode($group->event_data);
//
//        if ($json_decode->maxParticipant < $group->users->where('member_duty', DutyType::General)->count()) {
//
//        }
//        dd($json_decode->maxParticipant);
    }

    public function test_add_abbr_to_group()
    {
        $data = collect(json_decode(file_get_contents(public_path('school_code.json'))));
        $data->each(function ($q) {
            Group::query()->where('school_code', $q->SchoolCode)->update(['abbr' => $q->Abbr]);
//            dd($q->SchoolCode);
        });
        $this->assertTrue(true);
    }

    public function test_add_exno_and_serialnumber_to_tbas()
    {
        $result = [
            'exno_serialnumber_cn1.json',
            'exno_serialnumber_cn2.json',
            'exno_serialnumber_cn3.json',
            'exno_serialnumber_cn4.json',
            'exno_serialnumber_cn5.json',
            'exno_serialnumber_cn6.json',
            'exno_serialnumber_cn7.json',
            'exno_serialnumber_org.json',
        ];
        foreach ($result as $item) {
            $data = collect(json_decode(file_get_contents(public_path($item))));
            $data->each(function ($q) {
                Tba::query()->where(['mark' => $q->ExNO, 'locale_id' => $q->locale])->update(['binding_number' => $q->SERIALNUMBER]);
            });
        }


        $this->assertTrue(true);
    }
}
