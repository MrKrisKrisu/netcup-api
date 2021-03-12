# PHP Library for netcup Domain API

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5eed09a3cf904517974d165260cd3835)](https://www.codacy.com/gh/MrKrisKrisu/netcup-api/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=MrKrisKrisu/netcup-api&amp;utm_campaign=Badge_Grade)

This library is not related to the netcup GmbH, it is provided by a third party.

> :warning: **Warning: This library is currently still WIP!** Please use it only if you agree that the following releases contain breaking changes.

## Installation

You can simply install this library using Composer by running `composer require mrkriskrisu/netcup-api`. You'll need
PHP8.

## Examples

### Login to the API

```php
$api = new \Netcup\API("apiKey", "apiPassword", "123456");
echo "Login " . ($api->isLoggedIn() ? 'successful! :)' : 'not successful! :c') . PHP_EOL;
```

### Domains

#### Get domains and DNS-Records

```php
$domain = $api->infoDomain('k118.de');
print_r($domain->getDnsRecords());
```

#### Create new DNS-Record

```php
$domain = $api->infoDomain('k118.de');
$domain->createNewDnsRecord(new DnsRecord(
    hostname: 'www', 
    type: 'A', 
    destination: '127.0.0.1'
));
```

#### Update existing DNS-Record

```php
$domain = $api->infoDomain('k118.de');
$record = $domain->getDnsRecords()[0];
$record->update(destination: '127.0.0.2');
```

#### Delete DNS-Record

```php
$domain = $api->infoDomain('k118.de');
$record = $domain->getDnsRecords()[0];
$record->delete();
```

### Domain-Handles (Reseller only)

#### Create and update Handle

```php
$handle = $api->createHandle(
    name: 'Edward Keir',
    street: 'Street of God 1',
    postalCode: '12345',
    city: 'Examplecity',
    countryCode: 'DE',
    telephone: '+49.123456789',
    email: 'example@k118.de'
);

$handle->setCity('Kassel'); //this will directly edit the data at the netcup database as well
```

### Logout

You can end the created session. This is optional. If you don't, the token will automatically expire after 15 minutes.

```php
$logoutResult = $api->logout();
```

## Official links

- [netcup Wiki: "DNS API"](https://www.netcup-wiki.de/wiki/DNS_API)
- [netcup Wiki: "CCP API"](https://www.netcup-wiki.de/wiki/CCP_API)
- [netcup Documentation: "API Endpoints"](https://ccp.netcup.net/run/webservice/servers/endpoint.php)
