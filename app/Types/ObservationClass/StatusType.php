<?php

namespace App\Types\ObservationClass;

abstract class StatusType
{
    //
    const STANDBY = 'standby'; // ready
    const STARTING = 'starting';
    const ENDED = 'ended';

    const STANDBY_VAL = 'R';
    const STARTING_VAL = 'S';
    const ENDED_VAL = 'E';

    const STANDBY_ACTION = 'set';
    const STARTING_ACTION = 'start';
    const ENDED_ACTION = 'end';

    // extra action(s)
    const ADD_EXTRA_TIME_ACTION = 'et';

    //
    public static function check($type)
    {
        switch ($type) {
            case StatusType::STANDBY:
                return true;
            case StatusType::STARTING:
                return true;
            case StatusType::ENDED:
                return true;
            default:
                return false;
        }
    }

    //
    public static function list()
    {
        return [
            ['type' => StatusType::STANDBY, 'text' => 'standby', 'value' => StatusType::STANDBY_VAL, 'action' => StatusType::STANDBY_ACTION],
            ['type' => StatusType::STARTING, 'text' => 'starting', 'value' => StatusType::STARTING_VAL, 'action' => StatusType::STARTING_ACTION],
            ['type' => StatusType::ENDED, 'text' => 'ended', 'value' => StatusType::ENDED_VAL, 'action' => StatusType::ENDED_ACTION],
        ];
    }

    public static function getStatusValue($type)
    {
        switch ($type) {
            case StatusType::STANDBY:
                return StatusType::STANDBY_VAL;
            case StatusType::STARTING:
                return StatusType::STARTING_VAL;
            case StatusType::ENDED:
                return StatusType::ENDED_VAL;
            default:
                return StatusType::STANDBY_VAL;
        }
    }

    public static function getStatusAction($type)
    {
        switch ($type) {
            case StatusType::STANDBY:
                return StatusType::STANDBY_ACTION;
            case StatusType::STARTING:
                return StatusType::STARTING_ACTION;
            case StatusType::ENDED:
                return StatusType::ENDED_ACTION;
            default:
                return StatusType::STANDBY_ACTION;
        }
    }
}
