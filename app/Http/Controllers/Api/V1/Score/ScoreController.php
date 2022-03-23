<?php

namespace App\Http\Controllers\Api\V1\Score;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\Score\ScoreCollection;
use App\Repositories\Group\Channel\TbaContentRepository;
use App\Services\ScoreService;
use App\Types\Tba\ScoreType;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * ScoreController constructor.
     */
    public function __construct(ScoreService $scoreService)
    {
        $this->scoreService = $scoreService;
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $onlyData         = $request->only('user_id', 'group_id', 'tba_id', 'comment', 'score_data');
            $onlyData['flag'] = ScoreType::Valid;

            $scoreCount = $this->scoreService->findWhere([
                'group_id' => $request->input('group_id'),
                'tba_id'   => $request->input('tba_id'),
                'flag'     => 1,
            ])->count();

            if ($scoreCount >= 2) {
                return $this->fail(['message' => 'Reached the upper limit']);
            }

            $scoreInfo = $this->scoreService->updateOrCreate($request->only('user_id', 'group_id', 'tba_id'), $onlyData);
            $this->setContent($request->input('group_id'), $request->input('tba_id'));

        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
        return $this->setStatus(200)->success($scoreInfo);
    }

    /**
     * Display the specified resource.
     *
     * @param int $channel_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $channel_id): \Illuminate\Http\JsonResponse
    {
        try {
            $group_id  = GlobalPlatform::convertChannelIdToGroupId($channel_id);
            $scoreInfo = $this->scoreService->findWhere(['group_id' => $group_id]);

            return $this->success(new ScoreCollection($scoreInfo));
        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $onlyData  = $request->only('user_id', 'group_id', 'tba_id', 'comment', 'score_data');
            $scoreInfo = $this->scoreService->update($id, $onlyData);
            $this->setContent($request->input('group_id'), $request->input('tba_id'));

        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
        return $this->setStatus(204)->success(null);
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

    /**
     * @param int $group_id
     * @param int $tba_id
     * @return bool
     */
    private function setContent(int $group_id, int $tba_id): bool
    {
        $isSuccessful = true;
        try {
            $channel_id = GlobalPlatform::convertGroupIdToChannelId($group_id);
            $score      = $this->scoreService->findWhere(['group_id' => $group_id, 'tba_id' => $tba_id, 'flag' => ScoreType::Valid])->take(2);
            $result     = collect([
                'inno'     => 0,
                'tApp'     => 0,
                'tDesign'  => 0,
                'tEffect'  => 0,
                'tProcess' => 0,
            ]);
            $score->each(function ($q) use ($result) {
                $result['inno']     += json_decode($q)->inno;
                $result['tApp']     += json_decode($q)->tApp;
                $result['tDesign']  += json_decode($q)->tDesign;
                $result['tEffect']  += json_decode($q)->tEffect;
                $result['tProcess'] += json_decode($q)->tProcess;
            });

            // 兩筆才計算平均
            if ($score->count() > 1) {
                $result = $result->map(function ($q) {
                    return $q / 2;
                });
            }
            $tbaContentRepository = new TbaContentRepository($channel_id);
            $tbaContentRepository->setContent($tba_id, ['c_score' => $result->toJson()]);
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }
}
