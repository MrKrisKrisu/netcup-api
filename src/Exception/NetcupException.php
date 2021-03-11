<?php

namespace Netcup\Exception;

use stdClass;

class NetcupException extends \Exception {

    private stdClass $response;

    public function __construct(stdClass $response = null) {
        if($response != null) {
            $this->response = $response;
        }
    }

    public function getResponse(): ?stdClass {
        return $this->response;
    }
}