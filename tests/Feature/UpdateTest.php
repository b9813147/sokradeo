<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Azure\Blob;
use App\Libraries\Lang\Lang;
use App\Models\Resource;
use App\Models\Tba;
use App\Models\User;
use App\Services\Group\GroupService;
use App\Services\RatingService;
use App\Types\Src\SrcType;
use App\Types\Src\VodType;
use Carbon\Carbon;
use Faker\Provider\Barcode;
use Faker\Provider\Base;
use File;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testUpdateVideo()
    {
        $tbaVideoMaps = Tba::query()->findOrFail(8797)->videos()->first();
        dd($tbaVideoMaps->pivot->update(['tbavideo_order' => 1]));
        $tbaVideoMaps->pivot->save();
        dd($tbaVideoMaps);

        $file = public_path('HiEncoder_20191028_145421.mp4');

        $clientSize = File::size($file);
        $user       = auth()->loginUsingId(948);
        $fileName   = 'HiEncoder_20191028_145421';
        $group_id   = 7;

        $tba = Tba::query()->create([
            'user_id'                   => $user->id,
            'name'                      => $fileName,
            'teacher'                   => auth()->user()->name,
            'description'               => null,
            'thumbnail'                 => null,
            'playlisted'                => 0,
            'subject_field_id'          => null,
            'subject'                   => null,
            'educational_stage_id'      => null,
            'grade'                     => null,
            'lecture_type'              => 0,
            'lecture_date'              => Carbon::now()->format('Y-m-d'),
            'locale_id'                 => Lang::getConvertByLangString(\App::getLocale()),
            'mark'                      => null,
            'habook_id'                 => auth()->user()->habook,
            'double_green_light_status' => 0,
            'course_core'               => null,
            'observation_focus'         => null,
        ]);

        //Create Resource
        $resource = Resource::query()->create([
            'user_id'  => $user->id,
            'src_type' => SrcType::Vod,
            'name'     => $fileName,
            'status'   => 1
        ]);

        $rid   = $tba->id . '/' . $tba->id . '.mp4'; //ex 9000/9000.mp4
        $rdata = [
            'source'    => 'tw',
            'blob'      => $rid, //ex $rid
            'container' => getenv('BLOB_VIDEO_CONTAINER'),
            'file_size' => $clientSize, //ex 影片大小
            'duration'  => $this->getDuration($file), //ex 影片長度
        ];
        // Create Vod
        $resource->vod()->create([
            'type'    => VodType::AzureFile,
            'rid'     => $rid,
            'rstatus' => 'Normal',
            'rdata'   => json_encode($rdata),
        ]);


        // Create video
        $video = $tba->videos()->create([
            'user_id'        => $user->id,
            'resource_id'    => $resource->id,
            'name'           => $fileName,
            'description'    => null,
            'encoder'        => 'FileUpload',
            'tbavideo_order' => 1
        ]);

        // create tbaStatistics
        $tba->tbaStatistics()->create([
            'type' => 47,
            'idx'  => 0,
        ]);
        $tba->tbaStatistics()->create([
            'type' => 48,
            'idx'  => 0,
        ]);
        // Create tbaVideoMaps
        $tba->tbaVideoMaps()->create([
            'video_id'  => $video->id,
            'tba_start' => 0,
            'tba_end'   => $this->getDuration($file)
        ]);


        $rating    = app(RatingService::class);
        $groupServ = app(GroupService::class);

        $rating_id     = $rating->firstWhere(['groups_id' => $group_id, 'type' => 1])->id;
        $review_status = (int)$groupServ->findBy('id', $group_id)->review_status;
        $channelId     = GlobalPlatform::convertGroupIdToChannelId($group_id);

        $blob = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));

        $blob->update($rid, getenv('BLOB_VIDEO_CONTAINER'), $file);


        $tba->groupChannels()->attach($channelId, [
            'group_id'                => $group_id,
            'content_status'          => $review_status === 0 ? 1 : 2,
            'group_subject_fields_id' => null,
            'grades_id'               => null,
            'ratings_id'              => $rating_id,
            'author_id'               => $user->id,
            'share_status'            => 1,
        ]);


//
//        $tba = Tba::query()->create([
    }

    public function testGetShareGroup()
    {
        $videos = Tba::query()->findOrFail(8815)->videos;


//        $model = User::query()->with('groups')->findOrFail(948);
//        dd($model->groups()->where('school_upload_status', 1)->get());
    }

    public function testDuration()
    {
        $t = 'test';

        $tbaData = [
            'name'              => 'title',
            'description'       => 'desc',
            'course_core'       => 'courseCore',
            'observation_focus' => 'observationFocus'
        ];
        if ($t) {
            $tbaData['thumbnail'] = 'thumbnail';
        }

        dd($tbaData);
    }

}
