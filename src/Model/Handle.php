<?php

namespace Netcup\Model;

use Netcup\API;
use Netcup\NetcupException;
use Netcup\NotLoggedInException;
use stdClass;

class Handle {

    private API $api;

    private int    $id;
    private string $type;
    private string $name;
    private string $organisation;
    private string $street;
    private string $postalCode;
    private string $city;
    private string $countryCode;
    private string $telephone;
    private string $email;

    public function __construct(API $api, stdClass $apiResponse) {
        $this->api = $api;
        $this->id = $apiResponse?->responsedata?->id;
        $this->type = $apiResponse?->responsedata?->type;
        $this->name = $apiResponse?->responsedata?->name;
        $this->organisation = $apiResponse?->responsedata?->organisation;
        $this->street = $apiResponse?->responsedata?->street;
        $this->postalCode = $apiResponse?->responsedata?->postalcode;
        $this->city = $apiResponse?->responsedata?->city;
        $this->countryCode = $apiResponse?->responsedata?->countrycode;
        $this->telephone = $apiResponse?->responsedata?->telephone;
        $this->email = $apiResponse?->responsedata?->email;
    }

    public function getID(): ?int {
        return $this->id;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getOrganisation(): ?string {
        return $this->organisation;
    }

    public function getStreet(): ?string {
        return $this->street;
    }

    public function getPostalCode(): ?string {
        return $this->postalCode;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function getCountryCode(): ?string {
        return $this->countryCode;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setType(string $value): void {
        $this->type = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setName(string $value): void {
        $this->name = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setOrganisation(string $value): void {
        $this->organisation = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setStreet(string $value): void {
        $this->street = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setPostalCode(string $value): void {
        $this->postalCode = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setCity(string $value): void {
        $this->city = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setCountryCode(string $value): void {
        $this->countryCode = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setTelephone(string $value): void {
        $this->telephone = $value;
        $this->updateToDatabase();
    }

    /**
     * @param string $value
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function setEmail(string $value): void {
        $this->email = $value;
        $this->updateToDatabase();
    }

    /**
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    private function updateToDatabase(): void {
        $this->api->updateHandle(
            handleId: $this->getID(),
            type: $this->getType() ?? 'person',
            name: $this->getName(),
            organisation: $this->getOrganisation() ?? '',
            street: $this->getStreet(),
            postalCode: $this->getPostalCode(),
            city: $this->getCity(),
            countryCode: $this->getCountryCode(),
            telephone: $this->getTelephone(),
            email: $this->getEmail()
        );
    }

}