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

    public function getData(): stdClass|array|null {
        if($this->rawResponse?->responsedata === "") {
            return null;
        }
        return $this->rawResponse?->responsedata;
    }

    public function getStatus(): ?string {
        return $this->rawResponse?->status;
    }

    public function getStatusCode(): ?int {
        return $this->rawResponse?->statuscode;
    }

    public function getShortMessage(): ?string {
        return $this->rawResponse?->shortmessage;
    }

    public function getLongMessage(): ?string {
        return $this->rawResponse?->longmessage;
    }
}