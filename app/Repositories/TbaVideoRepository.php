<?php

namespace App\Repositories;

use LogicException;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use App\Models\Tba;
use App\Models\TbaVideo;
use App\Models\TbaVideoMap;
use App\Models\User;

class TbaVideoRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function list($page = 1, $perPage = null, $orders = [], $conds = [], $opts = [], $tbaFeatures = [])
    {
        $query = Tba::where(function ($q) {
            $q->where('playlisted', true)->orWhereHas('videos', function ($q) {
                $q->where('playlisted', false);
            });
        });
        
        foreach ($orders as $order) {
            $query->orderBy($order['col'], $order['dir']);
        }
        
        foreach ($conds as $cond) {
            $query->where($cond['col'], $cond['op'], $cond['val']);
        }
        
        if (! empty($opts)) {
            $query->where(function ($q) use ($opts) {
                foreach ($opts as $opt) {
                    $q->orWhere($opt['col'], $opt['op'], $opt['val']);
                }
            });
        }
        
        if (! empty($tbaFeatures)) {
            $query->with(['tbaFeatures' => function ($q) use ($tbaFeatures) {
                $q->whereIn('tba_feature_id', $tbaFeatures);
            }]);
        }
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    
    //
    public function listByUserId($userId, $page = 1)
    {
        return User::findOrFail($userId)->tbas()->where(function ($q) {
            $q->where('playlisted', true)->orWhereHas('videos', function ($q) {
                $q->where('playlisted', false);
            });
        })->paginate(null, ['*'], 'page', $page);
    }
    
    //
    public function getRanks($limit = 1, $orders = [])
    {
        $query = Tba::where(function ($q) {
            $q->where('playlisted', true)->orWhereHas('videos', function ($q) {
                $q->where('playlisted', false);
            });
        })->limit($limit);
        
        foreach ($orders as $order) {
            $query->orderBy($order['col'], $order['dir']);
        }
        
        return $query->get();
    }
    
    //
    public function getTbaVideo($tbaId)
    {
        $tba    = Tba::where(function ($q) {
            $q->where('playlisted', true)->orWhereHas('videos', function ($q) {
                $q->where('playlisted', false);
            });
        })->findOrFail($tbaId);
        $videos = $tba->videos()->orderBy('tbavideo_order')->get()->transform(function ($v) {
            unset($v->pivot);
            return $v;
        });
        
        return [
            'tba'    => $tba,
            'videos' => $videos,
        ];
    }
    
    public function hitTbaVideo($tbaId)
    {
        $tba = Tba::where(function ($q) {
            $q->where('playlisted', true)->orWhereHas('videos', function ($q) {
                $q->where('playlisted', false);
            });
        })->findOrFail($tbaId);
        $tba->hits++;
        $tba->save();
    }
    
    //
    public function getTbaVideoSectMap($tbaId)
    {
        $tba = Tba::has('videos')->findOrFail($tbaId);
        return $tba->videos()->orderBy('tbavideo_order')->with('tbaVideoMaps')->get()->transform(function ($v) {
            $sects = $v->tbaVideoMaps;
            $range = ['min' => $sects->min('tba_start'), 'max' => $sects->max('tba_end')];
            return [
                'range' => $range,
                'sects' => $sects,
            ];
        });
    }
    
    //
    public function createTbaVideoSectMap($tbaId, $sectMap)
    {
        $tba = Tba::findOrFail($tbaId);
        
//        if ($tba->videos()->exists()) {
//            throw new LogicException('videos of tba is already exist');
//        }
        
        TbaVideoMap::where('tba_id', $tbaId)->delete();
        DB::table('tba_video')->where('tba_id', $tbaId)->delete();
        
        $timestamp = date("Y-m-d H:i:s");
        
        $maps  = [];
        $sects = [];
        foreach ($sectMap as $i => $map) {
            
            $videoId = $map['video_id'];
            
            array_push($maps, [
                    'tba_id'         => $tbaId,
                    'video_id'       => $videoId,
                    'tbavideo_order' => $i + 1,
                    'created_at'     => $timestamp,
                    'updated_at'     => $timestamp,
            ]);
            
            foreach ($map['sects'] as $sect) {
                
                $sect['tba_id'    ] = $tbaId;
                $sect['video_id'  ] = $videoId;
                $sect['created_at'] = $timestamp;
                $sect['updated_at'] = $timestamp;
                array_push($sects, $sect);
            }
            
        }
        
        DB::table('tba_video'     )->insert($maps);
        DB::table('tba_video_maps')->insert($sects);
    }
    
    //
    public function getTbaVideoMaps($tbaId)
    {
        return TbaVideoMap::where('tba_id', $tbaId)->get();
    }
    
    //
    public function setTbaVideoMaps($tbaId, $mapDatas)
    {
        $mapDatas = collect($mapDatas);
        
        $mapIds = $mapDatas->map(function ($v) {
            return $v['id'];
        });
        
        $maps = TbaVideoMap::findMany($mapIds);
        if ($maps->count() !== $mapIds->count()) {
            throw new InvalidArgumentException('num of tba video maps is wrong');
        }
        
        foreach ($maps as $i => $map) {
            $data = collect($mapDatas[$i])->only(['tba_start', 'tba_end', 'video_offset'])->toArray();
            $map->fill($data);
            $map->save();
        }

    }
    
}
