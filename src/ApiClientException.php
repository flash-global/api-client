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
     * Return the response body of the exception
     *
     * @param \Exception|null $exception
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function getBadResponse(\Exception $exception = null)
    {
        $previous = is_null($exception) ? $this->getPrevious() : $exception;

        if (is_null($previous)) {
            return null;
        }

        if ($previous instanceof RequestException) {
            return $previous->getResponse();
        }

        return $this->getBadResponse($previous);
    }
}
