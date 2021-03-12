<?php

namespace Netcup\Model;

use Netcup\API;
use Netcup\Exception\MissingArgumentsException;
use Netcup\Exception\NetcupException;
use Netcup\Exception\NotLoggedInException;

class DnsRecord {

    private API|null    $api;
    private string|null $domainName;

    private int|null    $id;
    private string      $hostname;
    private string      $type;
    private int         $priority;
    private string      $destination;
    private string|null $state;

    public function __construct(
        string $hostname,
        string $type,
        string $destination,
        string $state = null,
        int $priority = 0,
        int $id = null,
        API $api = null,
        string $domainName = null
    ) {
        $this->api = $api;
        $this->domainName = $domainName;
        $this->id = $id;
        $this->hostname = $hostname;
        $this->type = $type;
        $this->priority = $priority;
        $this->destination = $destination;
        $this->state = $state;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getHostname(): string {
        return $this->hostname;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getPriority(): int {
        return $this->priority ?? 0;
    }

    public function getDestination(): string {
        return $this->destination;
    }

    public function getState(): string {
        return $this->state;
    }

    /**
     * @param string|null $hostname
     * @param string|null $type
     * @param int|null $priority
     * @param string|null $destination
     * @return bool
     * @throws NetcupException
     * @throws NotLoggedInException
     * @throws MissingArgumentsException
     */
    public function update(string $hostname = null, string $type = null, int $priority = null, string $destination = null): bool {
        if($this->api == null || $this->id == null || $this->domainName == null) {
            throw new MissingArgumentsException();
        }
        $this->hostname = $hostname ?? $this->hostname;
        $this->type = $type ?? $this->type;
        $this->priority = $priority ?? $this->priority;
        $this->destination = $destination ?? $this->destination;

        $response = $this->api->updateDnsRecords($this->domainName,
                                                 [
                                                     'dnsrecords' => [
                                                         [
                                                             'id'          => $this->getID(),
                                                             'hostname'    => $this->getHostname(),
                                                             'type'        => $this->getType(),
                                                             'priority'    => $this->getPriority(),
                                                             'destination' => $this->getDestination()
                                                         ]
                                                     ]
                                                 ]);
        return $response->wasSuccessful();
    }

    /**
     * @return bool
     * @throws MissingArgumentsException
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function delete(): bool {
        if($this->api == null || $this->id == null || $this->domainName == null) {
            throw new MissingArgumentsException();
        }
        $response = $this->api->updateDnsRecords($this->domainName,
                                                 [
                                                     'dnsrecords' => [
                                                         [
                                                             'id'           => $this->getID(),
                                                             'hostname'     => $this->getHostname(),
                                                             'type'         => $this->getType(),
                                                             'priority'     => $this->getPriority(),
                                                             'destination'  => $this->getDestination(),
                                                             'deleterecord' => true
                                                         ]
                                                     ]
                                                 ]);
        return $response->wasSuccessful();
    }

}