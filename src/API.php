<?php

namespace Netcup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class API {

    private const API_ENDPOINT = 'https://ccp.netcup.net/run/webservice/servers/endpoint.php?JSON';

    private $apiKey;
    private $customerId;
    private $apiSessionId;

    public function __construct(string $apiKey, string $apiPassword, int $customerId) {
        $this->apiKey = $apiKey;
        $this->customerId = $customerId;
        $this->login($apiPassword);
    }

    public function isLoggedIn(): bool {
        return $this->apiSessionId !== null;
    }

    /**
     * Acknowledge log message from call made via API.
     * This function is available for domain resellers.
     *
     * @param int $apiLogId
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function ackPoll(int $apiLogId): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'ackpoll',
                'param'  => [
                    'apilogid'       => $apiLogId,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Cancel Domain. Current Owner has to allow or deny the termination by clicking a link that is sent to him via e-mail.
     * Process ends after 5 days if not answered.
     * Inclusive domains that were ordered with a hosting product have to be canceled with this product.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function cancelDomain(string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'cancelDomain',
                'param'  => [
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Change Ownerhandle. Current Owner has to allow or deny the ownerchange by clicking a link that is sent to him via e-mail.
     * Process ends after 5 days if not answered.
     * This function is available for domain resellers.
     *
     * @param int $newHandleId
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function changeOwnerDomain(int $newHandleId, string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'changeOwnerDomain',
                'param'  => [
                    'new_handle_id'  => $newHandleId,
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Create a new domain for a fee.
     * This function is avaliable for domain resellers.
     *
     * @param string $domainName
     * @param $contacts
     * @param $nameservers
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function createDomain(string $domainName, $contacts, $nameservers): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'createDomain',
                'param'  => [
                    'domainname'     => $domainName,
                    'contacts'       => $contacts,
                    'nameservers'    => $nameservers,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Create a contact handle in data base. Contact handles are mandatory for ordering domains.
     * Fields type, name and organisation can not be changed by an update.
     * Field email can not be changed if domain is used at a global top-level domain.
     * This function is available for domain resellers.
     *
     * @param string $type "organisation" or "person"
     * @param string $name
     * @param string $organisation
     * @param string $street
     * @param string $postalcode
     * @param string $city
     * @param string $countrycode
     * @param string $telephone
     * @param string $email
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function createHandle(string $type, string $name, string $organisation, string $street, string $postalcode, string $city, string $countrycode, string $telephone, string $email): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'createHandle',
                'param'  => [
                    'type'           => $type,
                    'name'           => $name,
                    'organisation'   => $organisation,
                    'street'         => $street,
                    'postalcode'     => $postalcode,
                    'city'           => $city,
                    'countrycode'    => $countrycode,
                    'telephone'      => $telephone,
                    'email'          => $email,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Delete a contact handle in data base.
     * You can only delete a handle in the netcup database, if it is not used with a domain.
     * This function is available for domain resellers.
     *
     * @param int $handleId
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function deleteHandle(int $handleId): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'deleteHandle',
                'param'  => [
                    'handle_id'      => $handleId,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get auth info for domain.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function getAuthcodeDomain(string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'getAuthcodeDomain',
                'param'  => [
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get all records of a zone.
     * Zone must be owned by customer.
     *
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function infoDnsRecords(string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'infoDnsRecords',
                'param'  => [
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function infoDnsZone(string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'infoDnsZone',
                'param'  => [
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Info Domain. Get Information about domain. All available information for own domains. Status for other domains.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function infoDomain(string $domainName): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'infoDomain',
                'param'  => [
                    'domainname'     => $domainName,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get Information about a handle.
     * This function is available for domain resellers.
     *
     * @param int $handleId
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function infoHandle(int $handleId): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'infoHandle',
                'param'  => [
                    'handle_id'      => $handleId,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId,
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get information about all domains that a customer owns. For detailed information please use infoDomain
     * This function is available for domain resellers.
     *
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function listAllDomains(): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'listallDomains',
                'param'  => [
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get ids and name of all handles of a user. If Organisation is set, also value of organisation field.
     * This function is available for domain resellers.
     *
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function listAllHandle(): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'listallHandle',
                'param'  => [
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * @param string $apiPassword
     * @return bool
     * @throws GuzzleException
     */
    private function login(string $apiPassword): bool {
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'login',
                'param'  => [
                    'apikey'         => $this->apiKey,
                    'apipassword'    => $apiPassword,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        $json = json_decode($response->getBody());
        if(isset($json->responsedata->apisessionid)) {
            $this->apiSessionId = $json->responsedata->apisessionid;
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws NotLoggedInException
     * @throws GuzzleException
     */
    public function logout(): bool {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'logout',
                'param'  => [
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        $json = json_decode($response->getBody());
        if(isset($json->status) && $json->status == "success") {
            $this->sessionID = null;
            return true;
        }
        return false;
    }

    /**
     * Get all messages that are not read.
     * This function is available for domain resellers.
     *
     * @param int $messageCount
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     */
    public function poll(int $messageCount = 10): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'poll',
                'param'  => [
                    'messagecount'   => $messageCount,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Get price for a top-level domain.
     * Current discounts are considered, but can be limited by time or amount.
     * Prices for premium domains can be higher.
     * This function is available for domain resellers.
     * Transfers between netcup customers can result in additional costs for ownerchanges. See customer control panel.
     *
     * @param string $topLevelDomain
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function priceTopleveldomain(string $topLevelDomain): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'priceTopleveldomain',
                'param'  => [
                    'topleveldomain' => $topLevelDomain,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * @return stdClass
     * @stub
     */
    public function transferDomain(): stdClass {
    }

    /**
     * Update DNS records of a zone. Deletion of other records is optional.
     * When DNSSEC is active, the zone is updated in the nameserver with zone resign after a few minutes.
     *
     * @param string $domainName
     * @param $dnsRecordSet
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function updateDnsRecords(string $domainName, $dnsRecordSet): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'updateDnsRecords',
                'param'  => [
                    'domainname'     => $domainName,
                    'dnsrecordset'   => $dnsRecordSet,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Update DNS zone.
     * When DNSSEC is active, the zone is updated in the nameserver with zone resign after a few minutes.
     *
     * @param string $domainName
     * @param $dnsZone
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function updateDnsZone(string $domainName, $dnsZone): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'updateDnsZone',
                'param'  => [
                    'domainname'     => $domainName,
                    'dnszone'        => $dnsZone,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Update a domain contacts and nameserver settings.
     * For updating owner handle use changeOwnerDomain.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @param $contacts
     * @param $nameservers
     * @param $keepDnsSecRecords
     * @param $dnsSecEntries
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function updateDomain(string $domainName, $contacts, $nameservers, $keepDnsSecRecords, $dnsSecEntries): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'updateDomain',
                'param'  => [
                    'domainname'        => $domainName,
                    'contacts'          => $contacts,
                    'nameservers'       => $nameservers,
                    'keepdnssecrecords' => $keepDnsSecRecords,
                    'dnssecentries'     => $dnsSecEntries,
                    'apikey'            => $this->apiKey,
                    'apisessionid'      => $this->apiSessionId,
                    'customernumber'    => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }

    /**
     * Update a existing contact handle in data base and at registries where it is used.
     * Handle is created at a registry as soon as it is used.
     * This function is available for domain resellers.
     *
     * @param int $handleId
     * @param string $type
     * @param string $name
     * @param string $organisation
     * @param string $street
     * @param string $postalcode
     * @param string $city
     * @param string $countrycode
     * @param string $telephone
     * @param string $email
     * @return stdClass
     * @throws GuzzleException
     * @throws NotLoggedInException
     * @untested
     */
    public function updateHandle(int $handleId, string $type, string $name, string $organisation, string $street, string $postalcode, string $city, string $countrycode, string $telephone, string $email): stdClass {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $client = new Client();
        $response = $client->post(self::API_ENDPOINT, [
            'json' => [
                'action' => 'updateHandle',
                'param'  => [
                    'handle_id'      => $handleId,
                    'type'           => $type,
                    'name'           => $name,
                    'organisation'   => $organisation,
                    'street'         => $street,
                    'postalcode'     => $postalcode,
                    'city'           => $city,
                    'countrycode'    => $countrycode,
                    'telephone'      => $telephone,
                    'email'          => $email,
                    'apikey'         => $this->apiKey,
                    'apisessionid'   => $this->apiSessionId,
                    'customernumber' => $this->customerId
                ]
            ]
        ]);
        return json_decode($response->getBody());
    }
}
