<?php

namespace App\Http\Controllers\Api\V1\Favorite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Helpers\Custom\GlobalPlatform;
use App\Services\Tba\FavoriteService;



class FavoriteController extends Controller
{
    protected $tbaFavoriteService;

    public function __construct(FavoriteService $tbaFavoriteService)
    {
        $this->tbaFavoriteService = $tbaFavoriteService;
    }

    /**
     * Get Favorite Videos
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $req): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = auth()->id();
            $paginate = $req->input('size', 0);
            $result = $this->tbaFavoriteService->getTbaVideoFavs($userId, $paginate);
            return Response::json([
                'status' => true,
                'data'   => $result
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'data'   => $exception
            ]);
        }
    }

    /**
     * Update or Create Favorite Video
     * @param Request $req
     * @param int $channelId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $req, int $channelId): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = auth()->id();
            $groupId = GlobalPlatform::convertChannelIdToGroupId($channelId);
            $tbaId = $req->input('contentId');
            $result = $this->tbaFavoriteService->updateOrCreateTbaVideoFav($userId, $channelId, $groupId, $tbaId);
            return Response::json([
                'status' => true,
                'data' => $result
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false,
                'message' => $exception
            ]);
        }
    }

    /**
     * Delete Favorite Video
     * @param Request $req
     * @param int $channelId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $req, int $channelId): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = auth()->id();
            $groupId = GlobalPlatform::convertChannelIdToGroupId($channelId);
            $tbaId = $req->input('contentId');
            $isSuccessful = $this->tbaFavoriteService->deleteTbaVideoFav($userId, $channelId, $groupId, $tbaId);
            if (!$isSuccessful) throw new Exception();
            return Response::json([
                'status' => true
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'status' => false
            ]);
        }
    }
}
