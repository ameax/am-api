# AM API PHP Client

A framework-agnostic PHP client for the AM API with a fluent interface and strong typing.

## Requirements

- PHP 8.3 or higher
- Composer

## Installation

```bash
composer require ameax/am-api
```

## Configuration

```php
use Ameax\AmApi\AmApi;
use Ameax\AmApi\Config\Config;

$config = new Config(
    apiUrl: 'https://api.example.com',
    username: 'your-username',
    password: 'your-password'
);

$api = new AmApi($config);
```

## Usage Examples

### Creating a Customer

```php
use Ameax\AmApi\Mappers\CustomerMapper;

// Using mapper to transform your field names to API format
$customerData = CustomerMapper::make()
    ->setData([
        'company_name' => 'Example Company GmbH',
        'route' => 'Hauptstraße',
        'house_number' => '123',
        'postal_code' => '10115',
        'locality' => 'Berlin',
        'country' => 'DE',
        'phone' => '+49 30 12345678',
        'email' => 'info@example.com',
        'fax' => '+49 30 12345679'
    ])
    ->getData();

try {
    $customerId = $api->customers()->add($customerData);
    echo "Customer created with ID: {$customerId}\n";
} catch (\Exception $e) {
    echo "Error creating customer: " . $e->getMessage();
}

// Or directly use API field names without mapping
$customerId = $api->customers()->add([
    'name1' => 'Example Company GmbH',
    'strasse' => 'Hauptstraße 123',
    'plz' => '10115',
    'ort' => 'Berlin',
    'isoland' => 'DE',
    'tel' => '+49 30 12345678',
    'email' => 'info@example.com'
]);
```

### Updating a Customer

```php
use Ameax\AmApi\Mappers\CustomerMapper;

// Using mapper for updates
$updateData = CustomerMapper::make()
    ->setData([
        'company_name' => 'Updated Company Name GmbH',
        'phone' => '+49 30 98765432',
        'email' => 'newemail@example.com'
    ])
    ->getData();

try {
    $success = $api->customers()->update(12345, $updateData);
    
    if ($success) {
        echo "Customer updated successfully\n";
    }
} catch (\Exception $e) {
    echo "Error updating customer: " . $e->getMessage();
}
```

### More Examples

```php
// Get customer with related data - returns array
$customer = $api->customers()->get(12345, ['files', 'person', 'project']);

// Search customers
$results = $api->customers()->search([
    'name1_like' => 'Example',
    'add_dt_from' => '2024-01-01',
    'add_dt_till' => '2024-12-31'
]);

// Load customer (alternative search method)
$results = $api->customers()->load([
    'name1_like' => 'Example',
    'cat' => 1,
    'files' => 1,
    'images' => 1
]);

// Delete customer - returns bool
$deleted = $api->customers()->delete(12345);

// Account operations
$accountId = $api->accounts()->add(['email' => 'user@example.com', 'pw' => 'password']);
$account = $api->accounts()->get($accountId);
$api->accounts()->changePassword($accountId, 'newPassword');
$results = $api->accounts()->searchByEmail('user@example.com');

// Person operations with mapper
use Ameax\AmApi\Mappers\PersonMapper;

$personData = PersonMapper::make()
    ->setData([
        'customer_id' => 12345,
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@example.com',
        'gender' => 'male',
        'phone' => '+49 30 12345678'
    ])
    ->getData();

$personId = $api->persons()->add($personData);

// Relation operations
$relationId = $api->relations()->add(12345, 67890, 'invoiceaddress');
$relations = $api->relations()->get(12345);

// Object operations with file upload
$reflection = new ReflectionClass($api);
$clientProperty = $reflection->getProperty('client');
$clientProperty->setAccessible(true);
$client = $clientProperty->getValue($api);

// Create object with customer link
$response = $client->post('addObjectAndCustomerobject', [], [
    'object_id' => 1,
    'customer_id' => 12345,
    'project_id' => 1,
    'o_kennung' => 'REF-2024-001'
]);

if ($response['response']['ack'] === 'ok') {
    $objectDbId = $response['result']['id'];
    $indexId = $response['result']['index_id'];
    
    // Upload file to object (use database ID, not index_id!)
    $fileId = $api->files()->uploadToObject(1, $objectDbId, 'o_upload', 
        '/path/to/file.pdf', 'application/pdf', 'document.pdf');
}
```

## Field Mapping

The package provides optional mapper classes to transform between your application's field names and the AM API field names:

```php
use Ameax\AmApi\Mappers\CustomerMapper;
use Ameax\AmApi\Mappers\PersonMapper;

// Map your data to API format
$apiData = CustomerMapper::make()
    ->setData([
        'company_name' => 'ACME Corp',
        'route' => 'Main Street',
        'house_number' => '42',
        'postal_code' => '10115',
        'locality' => 'Berlin'
    ])
    ->getData();

// Map API response back to your format
$apiResponse = $api->customers()->get(123);
$yourData = CustomerMapper::fromApiResponse($apiResponse);
```

### Customer Field Mappings
| Your Field | API Field | Notes |
|------------|-----------|-------|
| `company_name` | `name1` | |
| `route` + `house_number` | `strasse` | Combined automatically |
| `postal_code` | `plz` | |
| `locality` | `ort` | |
| `country` | `isoland` | |
| `phone` | `tel` | |

### Person Field Mappings
| Your Field | API Field | Notes |
|------------|-----------|-------|
| `firstname` | `vorname` | |
| `lastname` | `nachname` | |
| `phone` | `tel` | |
| `mobile` | `mobil` | |
| `department` | `abteilung` | |
| `gender` | `anrede` | Values: male→Herr, female→Frau |

Fields like `email` and `fax` are passed through without mapping. You can also use the API field names directly without any mapping.

### Custom Fields

The API supports custom fields prefixed with module codes:
- `xcu_` - Customer custom fields
- `xpe_` - Person custom fields
- Other module-specific prefixes

Example:
```php
$customerData = [
    'name1' => 'Company Name',
    'xcu_eine_weitere_adresse__street' => 'Custom Street',
    'xcu_eine_weitere_adresse__zipcode' => '12345',
    'xcu_eine_weitere_adresse__city' => 'Custom City'
];
```

## Architecture

This package follows a fluent resource-based architecture with strong typing:

- **Resource Classes**: Each API resource (customers, accounts, etc.) has its own class
- **Fluent Interface**: Clean method chaining: `$api->customers()->add(...)`
- **Strong Typing**: All methods have explicit return types (int, bool, array) - no mixed types
- **Optional Mappers**: Transform between your field names and API field names when needed
- **Trait-based Sharing**: Common functionality shared via traits instead of inheritance

## Development

```bash
# Install dependencies
composer install

# Run linting
composer lint

# Run static analysis
composer test:types
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).