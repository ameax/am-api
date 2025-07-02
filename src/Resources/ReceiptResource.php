<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ReceiptResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addReceipt', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with receipt_id field
        if (is_array($result) && isset($result['receipt_id'])) {
            return (int) $result['receipt_id'];
        }

        return (int) $result;
    }

    public function get(int $receiptId, bool $includePositions = false, bool $includeTotal = false): array
    {
        $params = [
            'receipt_id' => $receiptId,
        ];

        if ($includePositions) {
            $params['receiptpos'] = 1;
        }
        if ($includeTotal) {
            $params['receipttotal'] = 1;
        }

        return $this->client->post('searchReceipt', $params, []);
    }

    public function update(int $receiptId, array $data = []): bool
    {
        $response = $this->client->post('updateReceipt', ['receipt_id' => $receiptId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $receiptId): bool
    {
        $response = $this->client->get('delReceipt', [
            'receipt_id' => $receiptId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function search(array $filters = []): array
    {
        $response = $this->client->post('searchReceipt', $filters, []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function addPosition(int $receiptId, array $positionData): int
    {
        $positionData['receipt_id'] = $receiptId;

        $response = $this->client->post('addReceiptPos', [], $positionData);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with receiptpos_id field
        if (is_array($result) && isset($result['receiptpos_id'])) {
            return (int) $result['receiptpos_id'];
        }

        return (int) $result;
    }

    public function addArticlePosition(int $receiptId, int $articleId, int $quantity = 1, ?float $discountPercent = null, ?float $priceBase = null): int
    {
        $data = [
            'receipt_id' => $receiptId,
            'article_id' => $articleId,
            'qty' => $quantity,
        ];

        if ($discountPercent !== null) {
            $data['discount_percent'] = $discountPercent;
        }
        if ($priceBase !== null) {
            $data['price_base'] = $priceBase;
        }

        return $this->addPosition($receiptId, $data);
    }

    public function addTextPosition(int $receiptId, string $text, string $textType = 'plain'): int
    {
        return $this->addPosition($receiptId, [
            'postype' => 'text',
            'text_type' => $textType,
            'article' => $text,
        ]);
    }

    public function deletePosition(int $receiptId, int $positionId): bool
    {
        $response = $this->client->post('delReceiptPos', [], [
            'receipt_id' => $receiptId,
            'receiptpos_id' => $positionId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function addFile(int $receiptId, string $filePath, string $fileName = ''): int
    {
        $response = $this->client->post('addReceiptFile', [], [
            'receipt_id' => $receiptId,
            'file' => $filePath,
            'filename' => $fileName,
        ]);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with file_id field
        if (is_array($result) && isset($result['file_id'])) {
            return (int) $result['file_id'];
        }

        return (int) $result;
    }

    public function deleteFile(int $receiptId, int $fileId): bool
    {
        $response = $this->client->post('delReceiptFile', [], [
            'receipt_id' => $receiptId,
            'file_id' => $fileId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function setCustomerDefaults(int $customerId, array $defaults): bool
    {
        $defaults['customer_id'] = $customerId;

        $response = $this->client->post('addCustomerReceipt', [], $defaults);

        $this->checkForErrors($response);

        return true;
    }

    public function updateCustomerDefaults(int $customerId, array $defaults): bool
    {
        $defaults['customer_id'] = $customerId;

        $response = $this->client->post('updateCustomerReceipt', [], $defaults);

        $this->checkForErrors($response);

        return true;
    }

    public function sendEmail(int $receiptId, array $emailData = []): bool
    {
        $data = array_merge(['receipt_id' => $receiptId], $emailData);

        $response = $this->client->post('executeReceiptSendMail', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function sendOnlineBrief24(int $receiptId, array $sendData = []): bool
    {
        $data = array_merge(['receipt_id' => $receiptId], $sendData);

        $response = $this->client->post('executeReceiptSendOB24', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function updateStatus(int $receiptId, string $status): bool
    {
        $response = $this->client->post('updateReceiptStatus', [], [
            'receipt_id' => $receiptId,
            'receiptstatus' => $status,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function finishOrderStatus(int $receiptId): bool
    {
        $response = $this->client->post('finishReceiptOrderStatus', [], [
            'receipt_id' => $receiptId,
        ]);

        $this->checkForErrors($response);

        return true;
    }
}
