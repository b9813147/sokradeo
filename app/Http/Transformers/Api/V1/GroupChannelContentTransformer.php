<?php

namespace App\Http\Transformers\Api\V1;

use App\Http\Transformers\Transformer;

class GroupChannelContentTransformer extends Transformer
{
    public function execute() {
        
        $content = $this->req->content;
        $this->req->merge(['content' => [
                'content_id'   => $content['id'],
                'content_type' => $content['type'],
        ]]);
    }
}
