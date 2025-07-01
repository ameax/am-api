<?php

declare(strict_types=1);

namespace Ameax\AmApi\Mappers;

class CustomerMapper
{
    private array $fieldMapping = [
        'company_name' => 'name1',
        'postal_code' => 'plz',
        'locality' => 'ort',
        'country' => 'isoland',
        'phone' => 'tel',
    ];

    public function mapToApi(array $data): array
    {
        $result = [];

        // Special handling for street address (combine route and house_number)
        if (isset($data['route'])) {
            $strasse = $data['route'];

            if (isset($data['house_number'])) {
                $strasse .= ' ' . $data['house_number'];
            }

            $result['strasse'] = $strasse;
        }

        // Map fields using the mapping
        foreach ($this->fieldMapping as $from => $to) {
            if (isset($data[$from])) {
                $result[$to] = $data[$from];
            }
        }

        // Copy fields that keep the same name
        foreach (['email', 'fax'] as $directField) {
            if (isset($data[$directField])) {
                $result[$directField] = $data[$directField];
            }
        }

        // Copy any remaining fields that don't need mapping
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) && !array_key_exists($key, $this->fieldMapping) && !in_array($key, ['route', 'house_number'])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function mapFromApi(array $data): array
    {
        $result = [];

        // Reverse mapping
        $reverseMapping = array_flip($this->fieldMapping);

        foreach ($data as $key => $value) {
            if (isset($reverseMapping[$key])) {
                $result[$reverseMapping[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        // Special handling for street address
        if (isset($result['strasse'])) {
            $parts = explode(' ', $result['strasse'], 2);
            $result['route'] = $parts[0];
            if (isset($parts[1])) {
                $result['house_number'] = $parts[1];
            }
            unset($result['strasse']);
        }

        return $result;
    }
}