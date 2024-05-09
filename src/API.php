<?php

namespace Netcup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Netcup\Exception\NetcupException;
use Netcup\Exception\NotLoggedInException;
use Netcup\Exception\NotRegisteredAtNetcupException;
use Netcup\Model\DnsRecord;
use Netcup\Model\Domain;
use Netcup\Model\Handle;
use Netcup\Response\Response;
use stdClass;

class API {

    private const API_ENDPOINT = 'https://ccp.netcup.net/run/webservice/servers/endpoint.php?JSON';

    private string $apiKey;
    private string $customerId;
    private string $apiSessionId;
    private bool   $logRequests = false;

    public function __construct(string $apiKey, string $apiPassword, int $customerId, bool $logRequests = false) {
        $this->apiKey = $apiKey;
        $this->customerId = $customerId;
        $this->logRequests = $logRequests;
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
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function ackPoll(int $apiLogId): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('ackpoll', ['apilogid' => $apiLogId]);
    }

    /**
     * Cancel Domain. Current Owner has to allow or deny the termination by clicking a link that is sent to him via e-mail.
     * Process ends after 5 days if not answered.
     * Inclusive domains that were ordered with a hosting product have to be canceled with this product.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function cancelDomain(string $domainName): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('cancelDomain', ['domainname' => $domainName]);
    }

    /**
     * Change Ownerhandle. Current Owner has to allow or deny the ownerchange by clicking a link that is sent to him via e-mail.
     * Process ends after 5 days if not answered.
     * This function is available for domain resellers.
     *
     * @param int $newHandleId
     * @param string $domainName
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function changeOwnerDomain(int $newHandleId, string $domainName): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('changeOwnerDomain', [
            'new_handle_id' => $newHandleId,
            'domainname'    => $domainName
        ]);
    }

    /**
     * Create a new domain for a fee.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @param $contacts
     * @param $nameservers
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function createDomain(string $domainName, $contacts, $nameservers): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('createDomain', [
            'domainname'  => $domainName,
            'contacts'    => $contacts,
            'nameservers' => $nameservers
        ]);
    }

    /**
     * Create a contact handle in data base. Contact handles are mandatory for ordering domains.
     * Fields type, name and organisation can not be changed by an update.
     * Field email can not be changed if domain is used at a global top-level domain.
     * This function is available for domain resellers.
     *
     * @param string $name
     * @param string $street
     * @param string $postalCode
     * @param string $city
     * @param string $countryCode
     * @param string $telephone
     * @param string $email
     * @param string $type "organisation" or "person"
     * @param string $organisation
     * @return Handle
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function createHandle(
        string $name,
        string $street,
        string $postalCode,
        string $city,
        string $countryCode,
        string $telephone,
        string $email,
        string $type = 'person',
        string $organisation = '',
    ): Handle {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('createHandle', [
            'type'         => $type,
            'name'         => $name,
            'organisation' => $organisation,
            'street'       => $street,
            'postalcode'   => $postalCode,
            'city'         => $city,
            'countrycode'  => $countryCode,
            'telephone'    => $telephone,
            'email'        => $email
        ]);
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }
        return new Handle($this, $response->getData());
    }

    /**
     * Delete a contact handle in data base.
     * You can only delete a handle in the netcup database, if it is not used with a domain.
     * This function is available for domain resellers.
     *
     * @param int $handleId
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function deleteHandle(int $handleId): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('deleteHandle', [
            'handle_id' => $handleId
        ]);
    }

    /**
     * Get auth info for domain.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function getAuthcodeDomain(string $domainName): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('getAuthcodeDomain', [
            'domainname' => $domainName
        ]);
    }

    /**
     * Get all records of a zone.
     * Zone must be owned by customer.
     *
     * @param string $domainName
     * @return array
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function infoDnsRecords(string $domainName): array {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('infoDnsRecords', [
            'domainname' => $domainName
        ]);
        if(!$response->wasSuccessful() || !isset($response->getData()->dnsrecords)) {
            throw new NetcupException($response);
        }
        $records = [];
        foreach($response->getData()->dnsrecords as $recordRaw) {
            $records[] = new DnsRecord(
                hostname: $recordRaw->hostname,
                type: $recordRaw->type,
                destination: $recordRaw->destination,
                state: $recordRaw->state,
                priority: $recordRaw->priority,
                id: $recordRaw->id,
                api: $this,
                domainName: $domainName
            );
        }
        return $records;
    }

    /**
     * @param string $domainName
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function infoDnsZone(string $domainName): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('infoDnsZone', [
            'domainname' => $domainName
        ]);
    }

    /**
     * Info Domain. Get Information about domain. All available information for own domains. Status for other domains.
     * This function is available for domain resellers.
     *
     * @param string $domainName
     * @return Domain
     * @throws NotLoggedInException
     * @throws NetcupException
     * @throws NotRegisteredAtNetcupException
     */
    public function infoDomain(string $domainName): Domain {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        try {
            $response = $this->request('infoDomain', ['domainname' => $domainName]);
            if(!$response->wasSuccessful()) {
                throw new NetcupException($response);
            }
            if($response->getData()?->state == 'not registered at netcup') {
                throw new NotRegisteredAtNetcupException();
            }
            return new Domain($this, $response->getData());
        } catch(GuzzleException) {
            throw new NetcupException();
        }
    }


    /**
     * Get Information about a handle.
     * This function is available for domain resellers.
     *
     * @param int $handleId
     * @return Handle
     * @throws NetcupException
     * @throws NotLoggedInException
     */
    public function infoHandle(int $handleId): Handle {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('infoHandle', [
            'handle_id' => $handleId
        ]);
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }
        return new Handle($this, $response->getData());
    }

    /**
     * Get information about all domains that a customer owns. For detailed information please use infoDomain
     * This function is available for domain resellers.
     *
     * @return array
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function listAllDomains(): array {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('listallDomains');
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }

        $domains = [];
        foreach($response->getData() as $recordRaw) {
            $domains[] = new Domain($this, $recordRaw);
        }
        return $domains;
    }

    /**
     * Get ids and name of all handles of a user. If Organisation is set, also value of organisation field.
     * This function is available for domain resellers.
     *
     * @return array
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function listAllHandle(): array {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }

        $response = $this->request('listallHandle');
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }

        $handles = [];
        foreach($response->getData() as $recordRaw) {
            $handles[] = new Handle($this, $recordRaw);
        }
        return $handles;
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
     * @throws NetcupException
     */
    public function logout(): bool {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('logout');
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }

        if(!$response->wasSuccessful()) {
            return false;
        }
        $this->sessionID = null;
        return true;
    }

    /**
     * Get all messages that are not read.
     * This function is available for domain resellers.
     *
     * @param int $messageCount
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function poll(int $messageCount = 10): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }

        return $this->request('poll', [
            'messagecount' => $messageCount
        ]);
    }

    /**
     * Get price for a top-level domain.
     * Current discounts are considered, but can be limited by time or amount.
     * Prices for premium domains can be higher.
     * This function is available for domain resellers.
     * Transfers between netcup customers can result in additional costs for ownerchanges. See customer control panel.
     *
     * @param string $topLevelDomain
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function priceTopleveldomain(string $topLevelDomain): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('priceTopleveldomain', [
            'topleveldomain' => $topLevelDomain
        ]);
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
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     */
    public function updateDnsRecords(string $domainName, $dnsRecordSet): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('updateDnsRecords', [
            'domainname'   => $domainName,
            'dnsrecordset' => $dnsRecordSet
        ]);
    }

    /**
     * Update DNS zone.
     * When DNSSEC is active, the zone is updated in the nameserver with zone resign after a few minutes.
     *
     * @param string $domainName
     * @param $dnsZone
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function updateDnsZone(string $domainName, $dnsZone): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('updateDnsZone', [
            'domainname' => $domainName,
            'dnszone'    => $dnsZone
        ]);
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
     * @return Response
     * @throws NotLoggedInException
     * @throws NetcupException
     * @untested
     */
    public function updateDomain(string $domainName, $contacts, $nameservers, $keepDnsSecRecords, $dnsSecEntries): Response {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        return $this->request('updateDomain', [
            'domainname'        => $domainName,
            'contacts'          => $contacts,
            'nameservers'       => $nameservers,
            'keepdnssecrecords' => $keepDnsSecRecords,
            'dnssecentries'     => $dnsSecEntries
        ]);
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
     * @param string $postalCode
     * @param string $city
     * @param string $countryCode
     * @param string $telephone
     * @param string $email
     * @return Handle
     * @throws NetcupException
     * @throws NotLoggedInException
     * @untested
     */
    public function updateHandle(
        int $handleId,
        string $type,
        string $name,
        string $organisation,
        string $street,
        string $postalCode,
        string $city,
        string $countryCode,
        string $telephone,
        string $email
    ): Handle {
        if(!$this->isLoggedIn()) {
            throw new NotLoggedInException();
        }
        $response = $this->request('updateHandle', [
            'handle_id'    => $handleId,
            'type'         => $type,
            'name'         => $name,
            'organisation' => $organisation,
            'street'       => $street,
            'postalcode'   => $postalCode,
            'city'         => $city,
            'countrycode'  => $countryCode,
            'telephone'    => $telephone,
            'email'        => $email
        ]);
        if(!$response->wasSuccessful()) {
            throw new NetcupException($response);
        }
        return new Handle($this, $response->getData());
    }

    /**
     * @param string $action
     * @param array $param
     * @return Response
     * @throws NetcupException
     */
    private function request(string $action, array $param = []): Response {

        $requestId = uniqid();
        $payload = [
            'action' => $action,
            'param'  => [
                'clientrequestid' => $requestId,
                'apikey'          => $this->apiKey,
                'apisessionid'    => $this->apiSessionId,
                'customernumber'  => $this->customerId
            ]
        ];

        $this->writeToLog($requestId, 'Send ' . $action . ' request');

        foreach($param as $key => $value) {
            $payload['param'][$key] = $value;
        }
        try {
            $client = new Client();
            $guzzleResponse = $client->post(self::API_ENDPOINT, ['json' => $payload]);
            $response = new Response(json_decode($guzzleResponse->getBody()));
            $this->writeToLog($requestId, 'Received "' . $response->getStatus() . '" with message "' . $response->getShortMessage() . '"');
            return $response;
        } catch(GuzzleException) {
            throw new NetcupException();
        }
    }

    private function writeToLog(string $requestID, string $log) {
        if(!$this->logRequests)
            return;

        $data = strtr('[:date] :requestId: :log' . PHP_EOL, [
            ':date'      => date('Y-m-d H:i:s'),
            ':requestId' => $requestID,
            ':log'       => $log
        ]);

        $fp = fopen(dirname(__DIR__) . '/logs/log_' . date('Y_m_d') . '.log', 'a');
        fwrite($fp, $data);
    }
}
