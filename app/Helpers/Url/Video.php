<?php

namespace App\Helpers\Url;

trait Video
{
    /**
     * @return string
     */
    public static function urlVideo($videoId, $src)
    {
        return url('storage/video/'.$videoId.'/'.$src);
    }
    
    /**
     * @return string
     */
    public static function urlVideoIndex($videoId, $src)
    {
        return url('storage/video/'.$videoId.'/index/'.$src);
    }
    
}