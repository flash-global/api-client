<?php

namespace Fei\ApiClient;

use GuzzleHttp\Exception\RequestException;

/**
 * Class ApiClientException
 * @package Fei\ApiClient
 */
class ApiClientException extends \Exception
{
    /**
     * @var RequestException
     */
    protected $requestException;

    /**
     * Return the response body of the exception
     *
     * @return null|RequestException
     */
    public function getRequestException()
    {
        if (!is_null($this->requestException)) {
            return $this->requestException;
        }

        $search = function (\Exception $exception = null) use (&$search) {
            $previous = $exception->getPrevious();

            if (is_null($previous)) {
                return null;
            }

            if ($previous instanceof RequestException) {
                return $this->requestException = $previous;
            }

            return $search($previous);
        };

        return $search($this);
    }
}
