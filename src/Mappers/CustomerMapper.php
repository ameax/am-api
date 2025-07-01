<?php

declare(strict_types=1);

namespace Ameax\AmApi\Mappers;

class CustomerMapper
{
    private array $data = [];

    private array $fieldMapping = [
        'company_name' => 'name1',
        'postal_code' => 'plz',
        'locality' => 'ort',
        'country' => 'isoland',
        'phone' => 'tel',
    ];

    public static function make(): self
    {
        return new self;
    }

    public function setData(array $data): self
    {
        foreach ($data as $key => $value) {
            // Handle special case for street address
            if ($key === 'route') {
                $this->data['strasse'] = isset($data['house_number'])
                    ? $value.' '.$data['house_number']
                    : $value;

                continue;
            }

            if ($key === 'house_number') {
                continue; // Already handled with route
            }

            // Apply field mapping if exists
            if (isset($this->fieldMapping[$key])) {
                $this->data[$this->fieldMapping[$key]] = $value;
            } else {
                // Pass through unmapped fields
                $this->data[$key] = $value;
            }
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function fromApiResponse(array $apiData): array
    {
        $result = [];
        $reverseMapping = array_flip((new self)->fieldMapping);

        foreach ($apiData as $key => $value) {
            if (isset($reverseMapping[$key])) {
                $result[$reverseMapping[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        // Special handling for street address
        if (isset($apiData['strasse'])) {
            $parts = explode(' ', $apiData['strasse'], 2);
            $result['route'] = $parts[0];
            if (isset($parts[1])) {
                $result['house_number'] = $parts[1];
            }
            unset($result['strasse']);
        }

        return $result;
    }
}
