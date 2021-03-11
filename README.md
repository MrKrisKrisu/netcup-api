# PHP Library for netcup Domain API

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5eed09a3cf904517974d165260cd3835)](https://www.codacy.com/gh/MrKrisKrisu/netcup-api/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=MrKrisKrisu/netcup-api&amp;utm_campaign=Badge_Grade)

This library is not related to the netcup GmbH, it is provided by a third party.

## Examples

```php
$netcupApi = new \Netcup\API($apiKey, $apiPassword, $customerID);
echo "Login to account with customer number $customerID \r\n";
echo "Login successful? -> " . ($netcupApi->isLoggedIn() ? 'Yes!' : 'No! :(') . "\r\n";

$data = $netcupApi->priceTopleveldomain('com');
echo "The domain will cost " . $data->responsedata->priceperruntime . "\r\n";

$netcupApi->createDomain('github.com');

$logoutResult = $netcupApi->logout();
echo "Logout successful? -> " . ($logoutResult ? 'Yes!' : 'No! :(') . "\r\n";
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