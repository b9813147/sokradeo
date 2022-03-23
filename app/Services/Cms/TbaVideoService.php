<?php

namespace App\Services\Cms;

use Exception;
use App\Repositories\TbaVideoRepository;
use App\Repositories\Tba\HistoryRepository as TbaHistoryRepository;
use App\Repositories\Tba\FavoriteRepository as TbaFavoriteRepository;
use App\Repositories\Tba\PlaylistTrackRepository as TbaPlaylistTrackRepository;

class TbaVideoService
{
    private $tbaVideoRepo = null;
    private $tbaHistRepo = null;
    private $tbaFavRepo = null;

    //
    public function __construct(
        TbaVideoRepository $tbaVideoRepo,
        TbaHistoryRepository $tbaHistRepo,
        TbaFavoriteRepository $tbaFavRepo
    ) {
        $this->tbaVideoRepo = $tbaVideoRepo;
        $this->tbaHistRepo  = $tbaHistRepo;
        $this->tbaFavRepo   = $tbaFavRepo;
    }

    //
    public function list($page)
    {
        return $this->tbaVideoRepo->list($page);
    }

    public function filters($page, $perPage, $orders = [], $conds = [], $search = '', $tbaFeatures = [])
    {
        $orders = collect($orders)->map(function ($v, $k) {
            return ['col' => $k, 'dir' => $v];
        })->toArray();

        $conds = collect($conds)->map(function ($v, $k) {
            return ['col' => $k, 'op' => '=', 'val' => $v];
        })->toArray();

        $opts = [];
        if (!empty($search)) {
            $opts = [
                ['col' => 'name', 'op' => 'like', 'val' => '%' . $search . '%'],
                ['col' => 'description', 'op' => 'like', 'val' => '%' . $search . '%'],
            ];
        }

        return $this->tbaVideoRepo->list($page, $perPage, $orders, $conds, $opts, $tbaFeatures);
    }

    //
    public function listByUserId($userId, $page)
    {
        return $this->tbaVideoRepo->listByUserId($userId, $page);
    }

    //
    public function getRanks($limit, $orders)
    {
        return $this->tbaVideoRepo->getRanks($limit, $orders);
    }

    //
    public function getTbaVideo($tbaId)
    {
        return $this->tbaVideoRepo->getTbaVideo($tbaId);
    }

    //
    public function hitTbaVideo($tbaId, $userId = null, $url = null)
    {
        $this->tbaVideoRepo->hitTbaVideo($tbaId);
        if (is_null($userId)) {
            return;
        }
        $this->tbaHistRepo->createHist($userId, $tbaId, $url);
    }

    //
    public function createTbaVideo()
    {
        throw new Exception('please implement');
    }

    //
    public function getTbaVideoPlaylist($tbaVideo)
    {
        // 單部
        if ($tbaVideo['tba']->playlisted === 0) {
            $tbaVideo['videoSrcType'] = 'reference';
            return [$tbaVideo];
        }

        // 清單
        // 說明:可以試著優化此段程式 減少搜尋資料庫次數(尤其是當切片都屬於同部蘇格拉底影片時 效果最為顯著)
        $playlist = [];
        $tracks   = (new TbaPlaylistTrackRepository())->getPlaylistTracks($tbaVideo['tba']->id);
        foreach ($tracks as $track) {
            try {
                $refTbaVideo                 = $this->tbaVideoRepo->getTbaVideo($track['ref_tba_id']);
                $refTbaVideo['videoSrcType'] = 'reference';
                if (!is_null($track['time_start']) && !is_null($track['time_end'])) { // 切片成立條件
                    $refTbaVideo['tba']['frag'] = [
                        'name'        => $track['frag_name'],
                        'description' => $track['frag_description'],
                        'start'       => $track['time_start'],
                        'end'         => $track['time_end']
                    ];
                }
                array_push($playlist, $refTbaVideo);
            } catch (Exception $e) {
                continue;
            }
        }
        return $playlist;
    }

    /**
     * Get Watch History
     * @param int $userId
     * @param int $paginate
     * @return TbaHistory[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getTbaVideoHists($userId, $paginate)
    {
        return $this->tbaHistRepo->getHists($userId, $paginate);
    }

    /**
     * Delete Watch History
     * @param int $userId
     * @return Bool
     */
    public function deleteTbaVideoHists($userId)
    {
        return $this->tbaHistRepo->deleteHists($userId);
    }

    //
    public function getTbaVideoSectMap($tbaId)
    {
        return $this->tbaVideoRepo->getTbaVideoSectMap($tbaId);
    }

    //
    public function createTbaVideoSectMap($tbaId, $sectMap)
    {
        return $this->tbaVideoRepo->createTbaVideoSectMap($tbaId, $sectMap);
    }

    //
    public function getTbaVideoMaps($tbaId)
    {
        return $this->tbaVideoRepo->getTbaVideoMaps($tbaId);
    }

    //
    public function setTbaVideoMaps($tbaId, $maps)
    {
        return $this->tbaVideoRepo->setTbaVideoMaps($tbaId, $maps);
    }
}
