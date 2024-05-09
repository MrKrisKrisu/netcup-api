<?php

namespace Netcup\Exception;

use Netcup\Response\Response;
use stdClass;

class NetcupException extends \Exception {

    private ?Response $response;

    public function __construct(null|Response|stdClass $response = null) {
        if ($response instanceof stdClass) {
            // Backward compatibility in case someone out there is creating a NetcupException
            $rawResponse               = new stdClass();
            $rawResponse->responsedata = $response;
            $response                  = new Response($rawResponse);
        }
        $this->response = $response;
        parent::__construct($response->getLongMessage() ?? '');
    }

    /**
     * Provides the `responsedata` entry of the http response
     */
    public function getResponse(): ?stdClass {
        return $this->response->getData();
    }

    /**
     * Provides the complete Response object including error messages
     */
    public function getResponseObject(): ?Response {
        return $this->response;
    }
}