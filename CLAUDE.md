# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP package for interacting with the AM API. It provides a framework-agnostic, modular approach where each API endpoint is implemented as a separate class that can be loaded dynamically.

## Package Architecture

The package follows a fluent resource-based design pattern:

```
src/
├── AmApi.php                      # Main API client with resource accessors
├── Resources/                     # Resource classes for each API endpoint group
│   ├── AccountResource.php        # Account operations
│   ├── CustomerResource.php       # Customer operations
│   ├── PersonResource.php         # Person operations
│   ├── RelationResource.php       # Customer relation operations
│   └── UserResource.php           # User operations
├── Config/
│   └── Config.php                 # Configuration management
├── Exceptions/
│   └── ApiException.php           # Custom exception for API errors
├── Http/
│   └── AmApiClient.php            # HTTP client wrapper
├── Mappers/                       # Field mapping classes
│   ├── CustomerMapper.php
│   └── PersonMapper.php
├── Services/                      # Shared services
│   └── ResponseParser.php
└── Traits/                        # Shared functionality
    └── HandlesApiResponses.php
```

## Development Commands

```bash
# Install dependencies
composer install

# Run linting
composer lint

# Run static analysis
composer test:types

# Run all quality checks
composer test
```

## Key Design Principles

1. **Framework Agnostic**: No Laravel or other framework dependencies. Pure PHP implementation.
2. **Fluent Interface**: Natural method chaining: `$api->customers()->add(...)`
3. **Strong Typing**: No mixed return types - only primitives (int, bool, array, ?int, etc.)
4. **Lazy Loading**: Resources are instantiated only when accessed.
5. **Configuration-Based**: All settings (URL, auth) passed via configuration.
6. **Error Handling**: Consistent error handling with custom exceptions.
7. **Separation of Concerns**: Mappers handle field transformations, traits provide shared functionality.

## Implementation Guidelines

### Adding New API Methods

1. Add the method to the appropriate Resource class in `src/Resources/`
2. Define explicit return types (int, bool, array, etc.)
3. Use traits for common functionality
4. If field mapping is required, create or update the appropriate Mapper class

### Resource Class Structure

```php
namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class CustomerResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {
    }

    public function add(array $data): int
    {
        $response = $this->client->post('addCustomer', [], $data);
        
        $this->checkForErrors($response);
        return (int) $this->extractResult($response);
    }

    public function get(int $customerId): array
    {
        return $this->client->get('getCustomer', [
            'customer_id' => $customerId
        ]);
    }
}
```

### Using Mappers

```php
use Ameax\AmApi\Mappers\CustomerMapper;

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

$customerId = $api->customers()->add($apiData);

// Map response data back to your format
$apiResponse = $api->customers()->get($customerId);
$yourData = CustomerMapper::fromApiResponse($apiResponse);
```

### Response Handling

The API may return responses in different formats:
- Direct format: `['ack' => 'ok', 'result' => ...]`
- Nested format: `['response' => ['ack' => 'ok', 'result' => ...]]`

Always check both formats when parsing responses.

## Common Patterns

### Field Mapping

Some endpoints require field mapping between internal names and API names:
- `firstname` → `vorname`
- `lastname` → `nachname`
- `phone` → `tel`

### Error Handling

Always throw `ApiException` with descriptive messages when API calls fail.

### Authentication

Basic auth credentials are passed via configuration and used in all requests.