# Object Operations

This guide covers working with objects and customer-object relationships in the AM API.

## Overview

The AM API supports two main entities for object management:
- **Objects**: Standalone entities with custom fields (prefixed with `o_`)
- **Customer Objects**: Links between customers and objects with their own custom fields (prefixed with `co_`)

## Basic Object Operations

### Creating an Object

```php
use Ameax\AmApi\AmApi;
use Ameax\AmApi\Config\Config;

$config = new Config('https://api.example.com', 'username', 'password');
$api = new AmApi($config);

// Create a standalone object (only works for multi-mode objects)
$objectData = [
    'object_id' => 3,
    'title' => 'My Object',
    'state_id' => 3,
    'o_custom_field' => 'value'
];

$indexId = $api->objects()->add($objectData);
```

### Reading an Object

```php
// Get object by object_id and index_id
$object = $api->objects()->get($objectId, $indexId);

// Access custom fields
$customValue = $object['result']['o_custom_field'];
$fileCount = $object['result']['o_upload'];
```

### Updating an Object

```php
$updateData = [
    'object_id' => 3,
    'index_id' => $indexId,
    'title' => 'Updated Title',
    'o_custom_field' => 'new value'
];

$success = $api->objects()->update($updateData);
```

### Deleting an Object

```php
$success = $api->objects()->delete($objectId, $indexId);
```

## Customer Object Operations

Customer objects link customers to objects and are essential for single-mode objects.

### Creating Object with Customer Link

For single-mode objects or when you want to create both at once:

```php
// Access the HTTP client for raw API calls
$reflection = new ReflectionClass($api);
$clientProperty = $reflection->getProperty('client');
$clientProperty->setAccessible(true);
$client = $clientProperty->getValue($api);

// Create object and customer link in one call
$data = [
    'object_id' => 1,
    'customer_id' => 2262,
    'project_id' => 1,
    'title' => 'Customer Object',
    'state_id' => 1,
    'o_kennung' => 'REF-123',
];

$response = $client->post('addObjectAndCustomerobject', [], $data);

if ($response['response']['ack'] === 'ok') {
    $objectDbId = $response['result']['id'];
    $indexId = $response['result']['index_id'];
    $customerObjectId = $response['result']['customer_object_id'];
}
```

### Updating Object and Customer Link

```php
$updateData = [
    'customer_object_id' => $customerObjectId,
    'object_id' => 1,
    'title' => 'Updated Title',
    'o_kennung' => 'REF-456',
];

$response = $client->post('updateObjectAndCustomerobject', [], $updateData);
```

## File Upload to Objects

Objects can have file upload fields (like `o_upload`). Here's how to upload files:

### Important: File Upload Parameters

- **mod**: Use 'object' + object_id (e.g., 'object1' for object_id=1)
- **mod_id**: Use the object's database ID (NOT the index_id!)
- **field**: The upload field name (e.g., 'o_upload')

### Complete File Upload Example

```php
// Step 1: Create object with customer link
$createData = [
    'object_id' => 1,
    'customer_id' => 2262,
    'project_id' => 1,
    'title' => 'Object with Files',
    'state_id' => 1,
    'o_kennung' => 'DOC-' . date('Ymd'),
];

$response = $client->post('addObjectAndCustomerobject', [], $createData);

if ($response['response']['ack'] === 'ok') {
    $objectDbId = $response['result']['id'];        // Database ID (important!)
    $indexId = $response['result']['index_id'];     // Index ID
    $customerObjectId = $response['result']['customer_object_id'];
    
    // Step 2: Upload file to object
    $filePath = '/path/to/document.pdf';
    $uploadUrl = 'https://api.example.com/uploadFile';
    
    $ch = curl_init($uploadUrl);
    $postData = [
        'mod' => 'object1',              // For object_id=1
        'mod_id' => $objectDbId,         // Use database ID, NOT index_id!
        'field' => 'o_upload',
        'file' => new CURLFile($filePath, 'application/pdf', 'document.pdf')
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'username:password');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $uploadResponse = curl_exec($ch);
    curl_close($ch);
    
    $decoded = json_decode($uploadResponse, true);
    if ($decoded['response']['ack'] === 'ok') {
        $fileId = $decoded['result']['file_id'] ?? $decoded['result'];
        echo "File uploaded successfully. File ID: $fileId\n";
        
        // Step 3: Verify upload
        $object = $api->objects()->get(1, $indexId);
        echo "Files in o_upload field: " . $object['result']['o_upload'] . "\n";
    }
}
```

### Using the FileResource Helper

The AM API package also provides a helper method:

```php
try {
    // This method handles the upload but may have permission restrictions
    $fileId = $api->files()->uploadToObject(
        $objectId,
        $indexId,
        'o_upload',
        '/path/to/file.pdf',
        'application/pdf',
        'document.pdf'
    );
} catch (Exception $e) {
    // Fall back to raw API method shown above
}
```

## Object Types and Modes

Objects in AM can be configured in different modes:

1. **Single Mode Objects**: Can only have one instance per customer
   - Require using `addObjectAndCustomerobject` for creation
   - Example: object_id=1

2. **Multi Mode Objects**: Can have multiple instances
   - Support standard `addObject` API
   - Example: object_id=3

## Custom Fields

### Object Custom Fields (o_*)
- `o_upload`: File upload field (auto-counts uploaded files)
- `o_kennung`: Text field for reference numbers
- `o_auswahlfeld_auswahlbutton`: Selection field
- Other custom fields specific to each object type

### Customer Object Custom Fields (co_*)
- Custom fields on the relationship between customer and object
- Examples: `co_status`, `co_priority`, etc.

## Error Handling

Common errors and solutions:

1. **"Diese Funktion kann im objectmode 'single' nicht verwendet werden"**
   - Single mode objects require using `addObjectAndCustomerobject`

2. **"mod_id is invalid or missing permissions"**
   - For file uploads, ensure you're using the database ID, not index_id

3. **"relation_id X ist ungültig"**
   - Check valid relation types for the specific object

## Complete Working Example

```php
<?php
use Ameax\AmApi\AmApi;
use Ameax\AmApi\Config\Config;

// Initialize API
$config = new Config('https://api.example.com', 'username', 'password');
$api = new AmApi($config);

// Get HTTP client for raw API calls
$reflection = new ReflectionClass($api);
$clientProperty = $reflection->getProperty('client');
$clientProperty->setAccessible(true);
$client = $clientProperty->getValue($api);

// Create customer object with file
$data = [
    'object_id' => 1,
    'customer_id' => 2262,
    'project_id' => 1,
    'title' => 'Customer Documentation',
    'state_id' => 1,
    'o_kennung' => 'CUST-' . date('Ymd'),
];

$response = $client->post('addObjectAndCustomerobject', [], $data);

if ($response['response']['ack'] === 'ok') {
    $objectDbId = $response['result']['id'];
    $indexId = $response['result']['index_id'];
    
    echo "Created object: ID=$objectDbId, Index=$indexId\n";
    
    // Upload a file
    $file = '/tmp/customer-doc.pdf';
    file_put_contents($file, 'Sample PDF content');
    
    $ch = curl_init($config->getApiUrl() . 'uploadFile');
    $uploadData = [
        'mod' => 'object1',
        'mod_id' => $objectDbId,  // Important: use database ID!
        'field' => 'o_upload',
        'file' => new CURLFile($file, 'application/pdf', 'customer-doc.pdf')
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $config->getUsername() . ':' . $config->getPassword());
    
    $uploadResult = curl_exec($ch);
    curl_close($ch);
    
    echo "File uploaded: " . $uploadResult . "\n";
    
    // Update the object
    $updateData = [
        'customer_object_id' => $response['result']['customer_object_id'],
        'object_id' => 1,
        'o_kennung' => 'CUST-UPDATED-' . date('Ymd'),
    ];
    
    $updateResponse = $client->post('updateObjectAndCustomerobject', [], $updateData);
    echo "Object updated: " . ($updateResponse['response']['ack'] === 'ok' ? 'Success' : 'Failed') . "\n";
}
```