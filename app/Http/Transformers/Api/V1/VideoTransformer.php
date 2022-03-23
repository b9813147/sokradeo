<?php

namespace App\Http\Transformers\Api\V1;

use App\Http\Transformers\Transformer;
use App\Types\Video\MarkType;

class VideoTransformer extends Transformer
{
    public function execute() {
        
        $this->req->merge(['resource' => [
                'src_type' => $this->req->type,
                'name'     => $this->req->video['name'],
                'data'     => $this->req->data,
        ]]);
        
        $this->req->merge([
                'type' => null,
                'data' => null,
        ]);
        
        $this->setIdx();
        $this->setMarkStat();
        
    }
    
    private function setIdx() {
        
        if (! $this->req->exists('idxes') || is_null($this->req->idxes)) {
            return;
        }
        
        $this->req->idxes = collect($this->req->idxes);
        $this->req->idxes->transform(function ($v) {
            $v['thumbnail'] = $v['thum'];
            unset($v['thum']);
            return $v;
        });
        
        $this->req->merge(['idxes' => $this->req->idxes->toArray()]);
    }
    
    private function setMarkStat() {
        
        if (! $this->req->exists('markStat') || is_null($this->req->markStat)) {
            return;
        }
        
        $markStat = [];
        if (isset($this->req->markStat['hard']) || !is_null($this->req->markStat['hard'])) {
            $markStat[MarkType::Hard] = $this->req->markStat['hard'];
        }
        if (isset($this->req->markStat['star']) || !is_null($this->req->markStat['star'])) {
            $markStat[MarkType::Star] = $this->req->markStat['star'];
        }
        
        $this->req->merge(['markStat' => $markStat]);
    }
}
