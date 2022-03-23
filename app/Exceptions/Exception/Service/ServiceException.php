<?php

namespace App\Exceptions\Exception\Service;

use App\Exceptions\Exception\AppException;

class ServiceException extends AppException
{
    public function __construct($desc = null, $meta = null, $code = null, $previous = null)
    {
        $this->message = 'Service Exception';
        parent::__construct($desc, $meta, $code, $previous);
    }
}
