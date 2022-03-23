<?php

namespace App\Helpers\Api;

trait JsonRpcII
{
    /**
     * @param $id
     * @param $method
     * @param $params
     *
     * @return array object
     */
    public function toJsonRpcIIReq($id, $method, $params)
    {
        if (! isset($params['extraInfo'])) {
            $params['extraInfo'] = (object)[];
        }
        
        return [
                'id'      => $id,
                'jsonrpc' => '2.0',
                'method'  => $method,
                'params'  => $params,
        ];
    }
    
}