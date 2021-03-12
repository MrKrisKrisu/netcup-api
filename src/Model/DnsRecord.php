<?php

namespace Netcup\Model;

class DnsRecord {

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
    ) {
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

}