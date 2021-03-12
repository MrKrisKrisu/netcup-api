<?php

namespace Netcup\Response;

use stdClass;

class Response {

    private stdClass $rawResponse;

    public function __construct(stdClass $rawResponse) {
        $this->rawResponse = $rawResponse;
    }

    public function wasSuccessful(): bool {
        return $this->rawResponse?->status == 'success';
    }

    public function getData(): ?stdClass {
        return $this->rawResponse?->responsedata;
    }
}