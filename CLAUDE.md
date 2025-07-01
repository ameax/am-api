# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP package for interacting with the AM API. It provides a framework-agnostic, modular approach where each API endpoint is implemented as a separate class that can be loaded dynamically.

## Package Architecture

The package follows a modular design pattern with one class per API action:

```
src/
├── AmApi.php                      # Main API client class that loads actions
├── Actions/                       # One class per API action
│   ├── AbstractAction.php         # Base action class
│   ├── Account/
│   │   ├── AddAccount.php
│   │   ├── GetAccount.php
│   │   ├── UpdateAccount.php
│   │   ├── DeleteAccount.php
│   │   ├── SearchAccountByEmail.php
│   │   └── ExecuteAccountLogin.php
│   ├── Customer/
│   │   ├── AddCustomer.php
│   │   ├── GetCustomer.php
│   │   ├── UpdateCustomer.php
│   │   └── DeleteCustomer.php
│   ├── Person/
│   │   ├── AddPerson.php
│   │   ├── GetPerson.php
│   │   ├── UpdatePerson.php
│   │   └── DeletePerson.php
│   └── Relation/
│       ├── AddCustomerRelation.php
│       ├── GetCustomerRelation.php
│       └── DeleteCustomerRelation.php
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
2. **Single Responsibility**: Each API action has its own class.
3. **Lazy Loading**: Actions are loaded on-demand when accessed.
4. **Configuration-Based**: All settings (URL, auth) passed via configuration, not pulled from environment.
5. **Type Safety**: Use PHP 8.3+ features like typed properties and return types.
6. **Error Handling**: Consistent error handling with custom exceptions.
7. **Separation of Concerns**: Mappers handle field transformations, traits provide shared functionality.

## Implementation Guidelines

### Adding New Actions

1. Create a new class in the appropriate `src/Actions/` subdirectory extending `AbstractAction`
2. Implement the `execute()` method for the specific API call
3. Register the action in `AmApi::registerActions()` if needed
4. If field mapping is required, create or update the appropriate Mapper class

### Action Class Structure

```php
namespace Ameax\AmApi\Actions\Customer;

use Ameax\AmApi\Actions\AbstractAction;
use Ameax\AmApi\Mappers\CustomerMapper;

class AddCustomer extends AbstractAction
{
    public function __construct(
        AmApiClient $client,
        private readonly CustomerMapper $mapper
    ) {
        parent::__construct($client);
    }

    public function execute(array $data): int
    {
        $mappedData = $this->mapper->mapToApi($data);
        $response = $this->post('addCustomer', [], $mappedData);
        
        $this->checkForErrors($response);
        return $this->extractResult($response);
    }
}
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