<?php

namespace App\Libraries\Meta;

use Illuminate\Support\Facades\Route;


class Meta
{
    protected $routeName;

    public function __construct()
    {
        $this->routeName = Route::currentRouteName();
    }

    /**
     * Get current route name
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Get Base Meta Title
     * @return string
     */
    public function getBaseMetaTitle()
    {
        return __('app/base.sokradeo');
    }

    /**
     * Get TbaPlayer Meta Title
     * @return string
     */
    public function getTbaPlayerMetaTitle()
    {
        return __('app/base.tba-player');
    }

    /**
     * Get custom meta title based on Route
     * @return string
     */
    public function getCustomMetaTitle()
    {
        $title = $this->getBaseMetaTitle(); // default title

        // If route name includes 'watch'
        // then set title based on 'app/base.tba-player'
        if (strpos($this->routeName, 'watch') !== false) {
            $title = $this->getTbaPlayerMetaTitle();
        }

        return $title;
    }
}
