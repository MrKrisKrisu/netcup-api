<?php

namespace Netcup\Model;

use Netcup\API;
use Netcup\Exception\NetcupException;
use Netcup\Exception\NotLoggedInException;
use stdClass;

class Domain {

    private API $api;

    private stdClass $domainDataRaw;

    public function __construct(API $api, stdClass $responseData) {
        $this->api = $api;
        $this->domainDataRaw = $responseData;
    }

    public function isNetcupNameserver(): bool {
        return $this->domainDataRaw->nameserverentry == 'default nameservers';
    }

    public function getDomainName(): string {
        return $this->domainDataRaw->domainname;
    }

    /**
     * @return array
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function getDnsRecords(): array {
        return $this->api->infoDnsRecords($this->getDomainName());
    }

}