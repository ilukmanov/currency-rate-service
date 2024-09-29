<?php

namespace app\exception;

use Exception;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * Класс BusinessException
 * @package support\exception
 */
class BusinessException extends Exception
{
    public function render(Request $request): ?Response
    {
        $code = $this->getCode();
        return warning([$this->getMessage()], $code ?: 400);
    }
}
