<?php

namespace App\Core\Kernel\Http\Exception;

class HttpStatusCodeException extends \RuntimeException
{
    /**
     * @param int $statusCode
     * @param $code
     * @param $message - message shown to the end user
     * @param $previous
     */
    public function __construct(
        public readonly int $statusCode,
        $code = 0,
        $message = "",
        $previous = null
    ) {

        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException(
                'HTTP status code must be between 100 and 599.'
            );
        }

        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
