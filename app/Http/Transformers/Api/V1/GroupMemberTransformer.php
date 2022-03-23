<?php

namespace App\Http\Transformers\Api\V1;

use App\Http\Transformers\Transformer;
use App\Types\Group\DutyType;

class GroupMemberTransformer extends Transformer
{
    public function execute() {
        
        /*
         * member_status:
         * 0: 無效
         * 1: 有效
         * 2: 待審核
         * */
        $status = $this->req->exists('member_status') ? $this->req->member_status : 2;
        $this->req->merge([
                'member_status' => $status,
                'member_duty'   => DutyType::General,  // 限制:安全考量 成員角色只能是一般成員
        ]);
    }
    
}
