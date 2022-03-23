<?php

namespace Tests\Feature;

use App\Factories\Src\SrcServiceFactory;
use App\Models\GroupChannelContent;
use App\Models\Tba;
use App\Models\User;
use App\Policies\GroupChannelContentPolicy;
use App\Repositories\ConfigRepository;
use App\Repositories\Exhibition\TbaVideoContentRepository;
use App\Repositories\Tba\AnnexRepository as TbaAnnexRepository;
use App\Services\Cms\TbaService;
use App\Services\Exhibition\ExhibitionService;
use App\Types\Cms\CmsType;
use App\Types\Exhibition\SetType;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;

class AnnexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $req = (object)[
            'annexId'  => 12132,
            'groupIds' => 7,
        ];

        $tbaSrv        = app(TbaService::class);
        $exhibitionSrv = app(ExhibitionService::class);
        $annexId       = $req->annexId;
        $annex         = $tbaSrv->getTbaAnnex($annexId);
        $contentId     = $annex->tba_id;
        $groupIds      = $req->groupIds;
        $groupIds      = is_null($groupIds) ? null : explode(',', $groupIds);


        if (is_null($groupIds)) {
            try {
                $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));
            } catch (Exception $e) {
                if (!$exhibitionSrv->checkCmsSet($contentId, CmsType::TbaVideo, [SetType::Top])) {
                    throw $e;
                }
            }
        } else {
            $this->authorize(
                'viewByGroupIds',
                [new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]), $groupIds]
            );
        }

        $resrc  = $annex->resource;
        $srcSrv = (new SrcServiceFactory(new ConfigRepository()))->create($resrc->src_type);

//        return $srcSrv->getExecuting($resrc->src());
        $this->assertTrue(true);
    }

    public function testContentCount()
    {
        $cmsType     = CmsType::TbaVideo;
        $setType     = SetType::Excellent;
        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;

        $select = ['MAX(exhibition_group_channel_sets.order) AS order_set', 'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail', 'COUNT(group_channel_contents.content_id) AS total_content'];
        $select = DB::raw(implode(',', $select));

        $var = DB::table('exhibition_group_channel_sets')
            ->where('type', $setType)
            ->join('group_channels', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channels.id')
            ->where('cms_type', $cmsType)
            ->where('status', 1)
            ->where('public', 1)
            ->join('group_channel_contents', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channel_contents.group_channel_id')
            ->where('content_type', $contentType)
            ->where('content_status', 1)
            ->where('content_public', 1)
            ->select($select)
            ->groupBy('group_channels.id')
            ->orderBy('order_set', 'asc')
            ->get();


        $var2 = DB::table('exhibition_group_channel_sets')
            ->where('type', $setType)
            ->join('group_channels', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channels.id')
            ->where('cms_type', $cmsType)
            ->where('status', 1)
            ->where('public', 1)
            ->join('group_channel_contents', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channel_contents.group_channel_id')
            ->where('content_type', $contentType)
            ->whereIn('content_status', [1, 2])
            ->whereIn('content_public', [0, 1, 2])
            ->selectRaw("COUNT(group_channel_contents.content_id) AS total_content_all,group_channels.id")
            ->groupBy('group_channels.id')
            ->get();

//        dd($var);
//        $var = $var->keyBy('id');
//
//        $var->map(function ($q) use ($var2) {
//            if ($var2->has($q->id)) {
//                $q->total_content_all = $var2[$q->id]->total_content_all;
//            }
//            return $q;
//        });
        dd(array_keys($var->toArray()), $var);
        foreach ($var->toArray() as $item) {
            dd(array_keys($item));
        }


        $var->map(function ($q) {
            dd($q);
        });
    }

    public function testCollection()
    {
        $gates  = [
            'BaiYaun_A_A17',
            'Beijing_J7',
            'ShuabgLiu_K203',
            'HongQiao_A157',
            'A2',
            'BaiYaun_B_B230'
        ];
        $boards = collect($gates)->map(function ($item) {
//            if (strrpos($item, '_') === false) {
//                return $item;
//            }
//            $underscorePosition = strrpos($item, '_');
//            $offset             = $underscorePosition + 1;
//            return mb_substr($item, $offset);
            return collect(explode('_', $item))->last();
        });

        dd($boards);
        $boards = [
            'A17',
            'J7',
            'K203',
            'A157',
            'A2',
            'B230',
        ];
    }
    use AuthorizesRequests;
    public function testAnnex()
    {
//        dd(app(TbaVideoContentRepository::class)->getContent(3273));
        $annexes = (new TbaAnnexRepository())->getAnnexResrcs(3273);
        dd($annexes);
//        $content = $this->tbaVideoContentRepo->getContent($contentId)

        $groupIds = 1;
        $tbaSrv=app(TbaService::class);
        $annexId   = 8335;
        $annex     = $tbaSrv->getTbaAnnex($annexId);
//        dd($annex->toArray());
        $contentId = $annex->tba_id;
//        $groupIds  = $req->groupIds;
        $groupIds  = is_null($groupIds) ? null : explode(',', $groupIds);

       $r= $this->authorize('viewByContentId', new GroupChannelContent(['content_id' => $contentId, 'content_type' => CmsType::Tba]));

       dd($r);

    }
}
