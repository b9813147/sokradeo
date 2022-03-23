<?php

namespace App\Http\Controllers\Api\V1\Videos;

use App\Services\Cms\TbaService;
use App\Types\Src\SrcType;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\VideoResource;
use App\Http\Transformers\Api\V1\VideoTransformer;
use App\Models\Video;
use App\Services\Cms\VideoService;

class VideoController extends Controller
{
    private $videoSrv = null;

    //
    protected $tbaSrv;

    public function __construct(VideoService $videoSrv, TbaService $tbaService)
    {
        $this->module   = ['cate' => 'Videos', 'app' => 'Video'];
        $this->videoSrv = $videoSrv;
        $this->tbaSrv   = $tbaService;
    }

    //
    public function index()
    {

        return $this->success(['message' => 'index']);

    }

    //
    public function store(Request $req)
    {
        $this->validate($req, [
            'info'    => 'required|file', // json
            'thum'    => 'file', // img
            'idxThum' => 'file', // zip
            'vodType' => 'required'
        ]);

        $this->authorize('create', Video::class);

        $info = $req->file('info')->path();
        $info = json_decode(file_get_contents($info), true);
        $info = collect($info);

        $video = $info->only(['name', 'description', 'author', 'copyright', 'encoder'])->toArray();
        $req->merge([
            'video'    => $video,
            'type'     => $info->get('type'),
            'data'     => $info->get('data'),
            'idxes'    => $info->get('idxes'),
            'markStat' => $info->get('markStat'),
        ]);

        (new VideoTransformer($req))->execute();

        $userId       = $req->user()->id;
        $video        = $req->input('video');
        $resrc        = $req->input('resource');
        $idxes        = $req->input('idxes');
        $markStatInfo = $req->input('markStat');
        $vodType      = $req->input('vodType');

        $idxInfo = [
            'list' => $idxes,
        ];

        if ($req->hasFile('thum')) {
            $video['thum'] = $req->file('thum');
        }

        if ($req->hasFile('idxThum')) {
            $idxInfo['thumFile'] = $req->file('idxThum');
        }

        $video = $this->videoSrv->createVideo($userId, $video, $resrc, $idxInfo, $markStatInfo, $vodType);

        return $this->success(new VideoResource($video));

    }

    /**
     * @param int $tba_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $tba_id): \Illuminate\Http\JsonResponse
    {
        try {
            $model = $this->tbaSrv->getTba($tba_id);
            return $this->success($model);
        } catch (\Exception $exception) {
            return $this->setStatus(422)->respond(['message' => $exception->getMessage()]);
        }
    }

    /**
     * 更新傳統片
     * @param Request $request
     * @param $tba_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $tba_id): \Illuminate\Http\JsonResponse
    {
        // Check file existence
        if (!$request->hasFile('video'))
            return $this->fail(['message' => 'File does not exist']);
        // Check file format (extension)
        $fileExt = strtolower($request->file('video')->getClientOriginalExtension());
        if (!in_array($fileExt, ['mp4']))
            return $this->fail(['message' => 'Incorrect format']);
        try {
            $file             = $request->file('video');
            $FileOriginalName = explode('.', $file->getClientOriginalName());
            $fileName         = $FileOriginalName[0];
            $user             = auth()->user();
            $user_id          = $user->id;
            $resourceData     = [
                'user_id'  => $user_id,
                'src_type' => SrcType::Vod,
                'name'     => $fileName,
                'status'   => 1
            ];

            return $this->success($this->tbaSrv->updateVideo($user_id, $tba_id, $resourceData, $fileName, $file));
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
    }

    //
    public function destroy()
    {

        return $this->success(['message' => 'destroy']);

    }
}
