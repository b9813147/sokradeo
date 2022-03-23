<?php

namespace App\Repositories\Tba;

use Illuminate\Support\Facades\Lang;
use App\Models\TbaEvaluateEventMode;
use App\Types\Tba\IdentityType;

class EvaluateEventModeRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getEventMode($eventModeId)
    {
        return TbaEvaluateEventMode::findOrFail($eventModeId);
    }
    
    //
    public function getEventModes($conds = [])
    {
        return TbaEvaluateEventMode::where($conds)
            ->whereNotIn('mode', ['EgTkPic', 'VgTkPic'])
            ->orderBy('identity')
            ->orderBy('order')
            ->get()
            ->map(function ($v) {
                $v->mode  = Lang::get('app/tba/evaluate-event-mode')[is_null($v->mode) ? $v->event : $v->mode];
                $v->event = Lang::get('app/tba/evaluate-event'     )[$v->event];
                return $v;
            });
    }
    
    //
    public function getIdentityEventModes($identity)
    {
        return [
                'identity'   => IdentityType::getItem($identity),
                'eventModes' => $this->getEventModes([['identity', $identity]]),
        ];
    }
    
}
