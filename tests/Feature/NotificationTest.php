<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\Tba;
use App\Models\User;
use App\Notifications\EventChannel;
use App\Repositories\UserRepository;
use App\Services\App\UserService;
use App\Services\Cms\TbaService;
use App\Services\NotificationMessageService;
use Carbon\Carbon;
use Faker\Provider\Base;
use Faker\Provider\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\Miscellaneous;
use Illuminate\Notifications\Notification;
use jaq\Request\V20161123\AfsAppCheckRequest;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testSendExample()
    {
//        $user = User::find(4);


//        dd($user);
        User::query()->whereNotNull('habook')->get()->each(function ($q) {

//            $message = [
//                'title'    => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
//                'content'  => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
//                'group_id' => 8,
//                'channel_id' => 8,
//                'thumbnail' => 'thum.png',
//                'top'      => false,
//                'link'     => '/activity-channel/' . 8,
//            ];
//
//            $q->notify(new EventChannel($message));
//        });
//        for ($i = 0; $i <= 20; $i++) {
//            $message = [
//                'title'    => Lorem::sentence(),
//                'content'  => Lorem::text(),
//                'group_id' => Base::randomDigitNotNull(),
//                'channel_id' => Base::randomDigitNotNull(),
//                'thumbnail' => 'thum.png',
//
//                'top'      => Miscellaneous::boolean(),
//                'link'     => '/activity-channel/' . Base::randomDigitNotNull(),
//            ];

            $message = [
                'title'      => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
                'content'    => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
                'group_id'   => 8,
                'channel_id' => 8,
                'thumbnail'  => 'thum.png',
                'top'        => false,
                'link'       => '/activity-channel/' . 8,
            ];

            $q->notify(new EventChannel($message));
        });

    }

    public function testMarkAsRead()
    {

        $user = User::find(948);
        $map  = $user->notifications->flatMap(function ($q) {
            return [
                $q->data
            ];
        })->sortByDesc('top');
        dd($map);
//            ->where('content', 'Vel molestiae blanditiis rem sit et.');
//        dd($map);

        $user->markAsRead();
//        $user->notifications->each(function ($q) {
//            dd($q);
//        });
//        dd($user->notifications->first()->data);
//        $user->markAsRead();

    }

    public function testGetUserNotification()
    {
        $user = User::with('notifications')->find(948);
        $map  = $user->notifications->flatMap(function ($q) {
            return [
                $q->data
            ];
        });

        dd($map);
    }

    public function testUnread()
    {

        $item                      = Tba::query()->find(1);
        $group_id                  = 1;
        $channel_id                = GlobalPlatform::convertGroupIdToChannelId($group_id);
        $itemInfoAndEvaluateEvents = app(TbaService::class)->getTbaInfoAndEvaluateEvents($channel_id, $item->id);
        $message                   = [];
        $observerPeopleIds         = [];
        $t                         = $itemInfoAndEvaluateEvents->flatMap(function ($item) use ($group_id, $channel_id, &$observerPeopleIds) {
            $observerPeople    = $item->tbaEvaluateEvents->map(function ($itemEvaluateEvent) {
                return [
                    'id'     => $itemEvaluateEvent->tbaEvaluateUser->user->id ?? null,
                    'name'   => $itemEvaluateEvent->tbaEvaluateUser->user->name ?? null,
                    'habook' => $itemEvaluateEvent->tbaEvaluateUser->user->habook ?? null,
                ];
            })->where('id', '!=', null);
            $observerPeopleIds = $observerPeople->groupBy('id')->keys();
            dd($item->tbaEvaluateEvents);
            return [
                'channel_id'  => $channel_id,
                'title'       => __('app/license.title'),
                'content'     => "課例名稱：$item->name 
                授課教師：" . $item->teacher . ($item->user->habook) . "
                課例學科：$item->subject
                課例年級：$item->grade
                學生人數：$item->student_count
                反饋次數：$item->irs_count (" . number_format(($item->irs_count / $item->student_count), 1) . "  次/人)
                授課日期：$item->lecture_date
                觀議課數據：
                觀課人數：" . $observerPeople->groupBy('id')->count() . "人
                標記數：" . $item->tbaEvaluateEvents->count() . "
                $item->teacher (" . $item->user->habook . ") $item->name 之蘇格拉底觀議課紀錄表鏈接如下：",
                'url'         => url("/group/$group_id/watch/channel/$channel_id/tbavideo?contentId=$item->id&groupIds=$group_id&channelId=$channel_id"),
                'isOperating' => false,
            ];
        })->toArray();
        dd($t,$observerPeopleIds);

        dd($message);


        $user = User::find(948);
        $user = User::all()->take(20);

        dd($user->toArray());

        foreach ($user->unreadNotifications as $notification) {
            dump($notification->type);
//            echo $notification->type;
        }
    }

    public function testRunNotification()
    {
        $all = \DB::table('notifications')->where('type', 'App\Notifications\EventChannel')
            ->where('notification_id', '')->data;
        dd($all);
    }

}
